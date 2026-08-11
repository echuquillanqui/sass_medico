<?php

namespace App\Http\Controllers;

use App\Models\Insumo;
use App\Models\Notificacion;
use App\Models\MovimientoInsumo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InsumoController extends Controller
{
    private function empresaId(): int
    {
        return (int) auth()->user()->empresa_id;
    }

    public function index(Request $request)
    {
        $q = $request->get('q');
        $insumos = Insumo::where('empresa_id', $this->empresaId())
            ->when($q, fn ($x) => $x->where('nombre', 'like', "%{$q}%"))
            ->orderBy('nombre')->paginate(12)->withQueryString();

        $bajoStock = Insumo::where('empresa_id', $this->empresaId())
            ->whereColumn('stock', '<=', 'stock_minimo')->count();

        $valorTotal = Insumo::where('empresa_id', $this->empresaId())
            ->select(DB::raw('SUM(stock * precio) as v'))->value('v') ?? 0;

        return view('insumos.index', compact('insumos', 'q', 'bajoStock', 'valorTotal'));
    }

    public function create()
    {
        return view('insumos.form', ['insumo' => new Insumo(['unidad' => 'unidad'])]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['empresa_id'] = $this->empresaId();
        Insumo::create($data);

        return redirect()->route('insumos.index')->with('ok', 'Insumo registrado.');
    }

    public function edit(Insumo $insumo)
    {
        abort_unless($insumo->empresa_id === $this->empresaId(), 403);
        return view('insumos.form', compact('insumo'));
    }

    public function update(Request $request, Insumo $insumo)
    {
        abort_unless($insumo->empresa_id === $this->empresaId(), 403);
        $insumo->update($this->validated($request));

        return redirect()->route('insumos.index')->with('ok', 'Insumo actualizado.');
    }

    public function destroy(Insumo $insumo)
    {
        abort_unless($insumo->empresa_id === $this->empresaId(), 403);
        $insumo->delete();

        return redirect()->route('insumos.index')->with('ok', 'Insumo eliminado.');
    }

    public function movimientos(Insumo $insumo)
    {
        abort_unless($insumo->empresa_id === $this->empresaId(), 403);
        $movs = $insumo->movimientos()->with('user')->orderByDesc('fecha')->orderByDesc('id')->paginate(15);

        return view('insumos.movimientos', compact('insumo', 'movs'));
    }

    public function registrarMovimiento(Request $request, Insumo $insumo)
    {
        abort_unless($insumo->empresa_id === $this->empresaId(), 403);

        $data = $request->validate([
            'tipo' => ['required', 'in:entrada,salida'],
            'cantidad' => ['required', 'numeric', 'min:0.01'],
            'motivo' => ['nullable', 'string', 'max:150'],
            'fecha' => ['required', 'date'],
        ]);

        if ($data['tipo'] === 'salida' && $data['cantidad'] > $insumo->stock) {
            return back()->withErrors(['cantidad' => 'No hay stock suficiente. Disponible: '.$insumo->stock])->withInput();
        }

        DB::transaction(function () use ($insumo, $data) {
            MovimientoInsumo::create([
                'empresa_id' => $this->empresaId(),
                'insumo_id' => $insumo->id,
                'user_id' => auth()->id(),
                'tipo' => $data['tipo'],
                'cantidad' => $data['cantidad'],
                'motivo' => $data['motivo'] ?? null,
                'fecha' => $data['fecha'],
            ]);

            $insumo->stock += $data['tipo'] === 'entrada' ? $data['cantidad'] : -$data['cantidad'];
            $insumo->save();
        });

        $insumo->refresh();
        if ($insumo->bajo_stock) {
            Notificacion::crear($insumo->empresa_id, 'Stock bajo: '.$insumo->nombre, [
                'tipo' => 'alerta', 'icono' => 'fa-triangle-exclamation',
                'mensaje' => 'Quedan '.rtrim(rtrim(number_format($insumo->stock, 2), '0'), '.').' '.$insumo->unidad,
                'url' => route('insumos.index'),
            ]);
        }

        return redirect()->route('insumos.movimientos', $insumo)->with('ok', 'Movimiento registrado.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'categoria' => ['nullable', 'string', 'max:80'],
            'unidad' => ['nullable', 'string', 'max:30'],
            'stock' => ['nullable', 'numeric', 'min:0'],
            'stock_minimo' => ['nullable', 'numeric', 'min:0'],
            'precio' => ['nullable', 'numeric', 'min:0'],
            'codigo' => ['nullable', 'string', 'max:50'],
        ]);
    }
}
