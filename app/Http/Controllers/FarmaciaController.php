<?php

namespace App\Http\Controllers;

use App\Models\Dispensacion;
use App\Models\Insumo;
use App\Models\MovimientoInsumo;
use App\Models\Paciente;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FarmaciaController extends Controller
{
    private function empresaId(): int
    {
        return (int) auth()->user()->empresa_id;
    }

    public function index(Request $request)
    {
        $desde = $request->get('desde', now()->startOfMonth()->toDateString());
        $hasta = $request->get('hasta', now()->endOfMonth()->toDateString());

        $dispensaciones = Dispensacion::with(['paciente', 'items'])
            ->where('empresa_id', $this->empresaId())
            ->whereBetween('fecha', [$desde, $hasta])
            ->orderByDesc('fecha')->orderByDesc('id')
            ->paginate(15)->withQueryString();

        $total = Dispensacion::where('empresa_id', $this->empresaId())
            ->whereBetween('fecha', [$desde, $hasta])->sum('total');

        return view('farmacia.index', compact('dispensaciones', 'total', 'desde', 'hasta'));
    }

    public function create(Request $request)
    {
        return view('farmacia.form', [
            'pacientes' => Paciente::where('empresa_id', $this->empresaId())->orderBy('apellidos')->get(),
            'insumos' => Insumo::where('empresa_id', $this->empresaId())->where('activo', true)->orderBy('nombre')->get(),
            'pacienteSel' => $request->get('paciente_id'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'paciente_id' => ['nullable', 'exists:pacientes,id'],
            'fecha' => ['required', 'date'],
            'observaciones' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.insumo_id' => ['required', 'exists:insumos,id'],
            'items.*.cantidad' => ['required', 'numeric', 'min:0.01'],
            'items.*.indicaciones' => ['nullable', 'string', 'max:150'],
        ]);

        // Verificar stock suficiente
        foreach ($data['items'] as $it) {
            $insumo = Insumo::where('empresa_id', $this->empresaId())->find($it['insumo_id']);
            if (! $insumo || $it['cantidad'] > $insumo->stock) {
                $nombre = $insumo->nombre ?? 'insumo';
                return back()->withErrors(['items' => "Stock insuficiente de {$nombre}. Disponible: ".($insumo->stock ?? 0)])->withInput();
            }
        }

        $dispensacion = DB::transaction(function () use ($data) {
            $disp = Dispensacion::create([
                'empresa_id' => $this->empresaId(),
                'paciente_id' => $data['paciente_id'] ?? null,
                'user_id' => auth()->id(),
                'fecha' => $data['fecha'],
                'observaciones' => $data['observaciones'] ?? null,
                'total' => 0,
            ]);

            $total = 0;
            foreach ($data['items'] as $it) {
                $insumo = Insumo::where('empresa_id', $this->empresaId())->find($it['insumo_id']);
                $subtotal = (float) $insumo->precio * (float) $it['cantidad'];
                $total += $subtotal;

                $disp->items()->create([
                    'insumo_id' => $insumo->id,
                    'nombre' => $insumo->nombre,
                    'cantidad' => $it['cantidad'],
                    'precio' => $insumo->precio,
                    'indicaciones' => $it['indicaciones'] ?? null,
                ]);

                // Descontar stock + registrar movimiento de salida
                $insumo->decrement('stock', $it['cantidad']);
                MovimientoInsumo::create([
                    'empresa_id' => $this->empresaId(),
                    'insumo_id' => $insumo->id,
                    'user_id' => auth()->id(),
                    'tipo' => 'salida',
                    'cantidad' => $it['cantidad'],
                    'motivo' => 'Dispensacion farmacia',
                    'fecha' => $data['fecha'],
                ]);
            }

            $disp->update(['total' => $total]);
            return $disp;
        });

        return redirect()->route('farmacia.index')->with('ok', 'Dispensación registrada y stock actualizado.');
    }

    public function comprobante(Dispensacion $dispensacion)
    {
        abort_unless($dispensacion->empresa_id === $this->empresaId(), 403);
        $dispensacion->load(['paciente', 'items', 'user']);

        $pdf = Pdf::loadView('farmacia.comprobante', [
            'd' => $dispensacion,
            'empresa' => auth()->user()->empresa,
        ])->setPaper('a5');

        return $pdf->stream('dispensacion-'.$dispensacion->id.'.pdf');
    }

    public function destroy(Dispensacion $dispensacion)
    {
        abort_unless($dispensacion->empresa_id === $this->empresaId(), 403);

        DB::transaction(function () use ($dispensacion) {
            // Reponer stock de cada item
            foreach ($dispensacion->items as $item) {
                if ($item->insumo_id) {
                    Insumo::where('id', $item->insumo_id)->increment('stock', $item->cantidad);
                    MovimientoInsumo::create([
                        'empresa_id' => $this->empresaId(),
                        'insumo_id' => $item->insumo_id,
                        'user_id' => auth()->id(),
                        'tipo' => 'entrada',
                        'cantidad' => $item->cantidad,
                        'motivo' => 'Anulacion dispensacion',
                        'fecha' => now()->toDateString(),
                    ]);
                }
            }
            $dispensacion->delete();
        });

        return back()->with('ok', 'Dispensación anulada y stock repuesto.');
    }
}
