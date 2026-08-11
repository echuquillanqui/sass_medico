<?php

namespace App\Http\Controllers;

use App\Models\LabExamen;
use App\Models\LabOrden;
use App\Models\Paciente;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LaboratorioController extends Controller
{
    private function empresaId(): int
    {
        return (int) auth()->user()->empresa_id;
    }

    public function index(Request $request)
    {
        $estado = $request->get('estado');
        $ordenes = LabOrden::with(['paciente', 'medico', 'items'])
            ->where('empresa_id', $this->empresaId())
            ->when($estado, fn ($q) => $q->where('estado', $estado))
            ->orderByDesc('fecha')->orderByDesc('id')
            ->paginate(12)->withQueryString();

        return view('laboratorio.index', compact('ordenes', 'estado'));
    }

    public function create(Request $request)
    {
        return view('laboratorio.form', $this->opciones() + ['pacienteSel' => $request->get('paciente_id')]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'paciente_id' => ['required', 'exists:pacientes,id'],
            'medico_id' => ['nullable', 'exists:users,id'],
            'fecha' => ['required', 'date'],
            'observaciones' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.nombre' => ['nullable', 'string', 'max:120'],
            'items.*.lab_examen_id' => [
                'nullable',
                'integer',
                Rule::exists('lab_examenes', 'id')->where('empresa_id', $this->empresaId()),
            ],
            'items.*.grupo' => ['nullable', 'string', 'max:120'],
            'items.*.unidad' => ['nullable', 'string', 'max:30'],
            'items.*.valor_referencia' => ['nullable', 'string', 'max:60'],
        ]);

        $orden = LabOrden::create([
            'empresa_id' => $this->empresaId(),
            'paciente_id' => $data['paciente_id'],
            'medico_id' => $data['medico_id'] ?? auth()->id(),
            'fecha' => $data['fecha'],
            'estado' => 'solicitada',
            'observaciones' => $data['observaciones'] ?? null,
        ]);

        foreach ($data['items'] as $it) {
            if (! empty($it['nombre'])) {
                $orden->items()->create([
                    'lab_examen_id' => $it['lab_examen_id'] ?? null,
                    'grupo' => $it['grupo'] ?? null,
                    'nombre' => $it['nombre'],
                    'unidad' => $it['unidad'] ?? null,
                    'valor_referencia' => $it['valor_referencia'] ?? null,
                ]);
            }
        }

        return redirect()->route('laboratorio.show', $orden)->with('ok', 'Orden creada. Ahora puedes capturar resultados.');
    }

    public function show(LabOrden $orden)
    {
        abort_unless($orden->empresa_id === $this->empresaId(), 403);
        $orden->load(['paciente', 'medico', 'items']);

        return view('laboratorio.show', compact('orden'));
    }

    public function guardarResultados(Request $request, LabOrden $orden)
    {
        abort_unless($orden->empresa_id === $this->empresaId(), 403);

        $items = $request->input('items', []);
        foreach ($orden->items as $item) {
            if (isset($items[$item->id])) {
                $item->update([
                    'resultado' => $items[$item->id]['resultado'] ?? null,
                    'fuera_rango' => ! empty($items[$item->id]['fuera_rango']),
                    'notas' => $items[$item->id]['notas'] ?? null,
                ]);
            }
        }

        // Recalcular estado
        $orden->refresh()->load('items');
        $total = $orden->items->count();
        $conResultado = $orden->items->filter(fn ($i) => filled($i->resultado))->count();
        $estado = $conResultado === 0 ? 'solicitada' : ($conResultado < $total ? 'en_proceso' : 'completada');
        $orden->update(['estado' => $estado]);

        return back()->with('ok', 'Resultados guardados.');
    }

    public function entregar(LabOrden $orden)
    {
        abort_unless($orden->empresa_id === $this->empresaId(), 403);
        $orden->update(['estado' => 'entregada']);

        return back()->with('ok', 'Orden marcada como entregada.');
    }

    public function destroy(LabOrden $orden)
    {
        abort_unless($orden->empresa_id === $this->empresaId(), 403);
        $orden->delete();

        return redirect()->route('laboratorio.index')->with('ok', 'Orden eliminada.');
    }

    public function pdf(LabOrden $orden)
    {
        abort_unless($orden->empresa_id === $this->empresaId(), 403);
        $orden->load(['paciente', 'medico', 'items']);

        $pdf = Pdf::loadView('laboratorio.pdf', [
            'orden' => $orden,
            'empresa' => auth()->user()->empresa,
        ])->setPaper('a4');

        return $pdf->stream('laboratorio-'.$orden->id.'.pdf');
    }

    private function opciones(): array
    {
        return [
            'orden' => new LabOrden(['fecha' => now()->toDateString()]),
            'pacientes' => Paciente::where('empresa_id', $this->empresaId())->orderBy('apellidos')->get(),
            'medicos' => User::where('empresa_id', $this->empresaId())->where('role', 'medico')->get(),
            'examenes' => LabExamen::with(['componentes' => fn ($q) => $q->where('activo', true)])
                ->where('empresa_id', $this->empresaId())->where('activo', true)
                ->whereNull('padre_id')->orderBy('nombre')->get(),
        ];
    }
}
