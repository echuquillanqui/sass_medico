<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use Illuminate\Http\Request;

class AgendaController extends Controller
{
    private function empresaId(): int
    {
        return (int) auth()->user()->empresa_id;
    }

    public function index()
    {
        return view('agenda.index');
    }

    public function eventos(Request $request)
    {
        $colores = [
            'pendiente' => '#f59e0b', 'confirmada' => '#3b82f6', 'atendida' => '#22c55e',
            'cancelada' => '#ef4444', 'no_asistio' => '#94a3b8',
        ];

        $citas = Cita::with(['paciente', 'especialidad', 'medico'])
            ->where('empresa_id', $this->empresaId())
            ->when($request->start, fn ($q) => $q->whereDate('fecha', '>=', substr($request->start, 0, 10)))
            ->when($request->end, fn ($q) => $q->whereDate('fecha', '<=', substr($request->end, 0, 10)))
            ->get();

        $etiquetas = [
            'pendiente' => 'Pendiente', 'confirmada' => 'Confirmada', 'atendida' => 'Atendida',
            'cancelada' => 'Cancelada', 'no_asistio' => 'No asistió',
        ];

        $eventos = $citas->map(function ($c) use ($colores, $etiquetas) {
            $hora = substr((string) $c->hora, 0, 5);
            $inicio = $c->fecha->format('Y-m-d').'T'.$hora.':00';
            $color = $colores[$c->estado] ?? '#7c3aed';

            return [
                'id' => $c->id,
                'title' => $c->paciente->nombre_completo,
                'start' => $inicio,
                'color' => $color,
                'borderColor' => $color,
                'url' => route('citas.edit', $c),
                'extendedProps' => [
                    'estado' => $c->estado,
                    'estadoLabel' => $etiquetas[$c->estado] ?? ucfirst($c->estado),
                    'hora' => $hora,
                    'especialidad' => $c->especialidad->nombre ?? 'General',
                    'medico' => $c->medico->name ?? null,
                ],
            ];
        });

        return response()->json($eventos);
    }

    public function mover(Request $request, Cita $cita)
    {
        abort_unless($cita->empresa_id === $this->empresaId(), 403);
        $data = $request->validate([
            'fecha' => ['required', 'date'],
            'hora' => ['nullable'],
        ]);
        $cita->update([
            'fecha' => $data['fecha'],
            'hora' => $data['hora'] ?? $cita->hora,
        ]);

        return response()->json(['ok' => true]);
    }
}
