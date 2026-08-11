<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use Illuminate\Support\Carbon;

class ColaController extends Controller
{
    private function empresaId(): int
    {
        return (int) auth()->user()->empresa_id;
    }

    public function index()
    {
        $hoy = Carbon::today();
        $base = Cita::with(['paciente', 'especialidad', 'medico'])
            ->where('empresa_id', $this->empresaId())
            ->whereDate('fecha', $hoy)
            ->whereNotIn('estado', ['cancelada', 'no_asistio']);

        $citas = (clone $base)->orderBy('hora')->get();

        return view('cola.index', [
            'porLlegar' => $citas->where('estado_sala', 'sin_llegar'),
            'esperando' => $citas->where('estado_sala', 'esperando')->sortBy('hora_llegada'),
            'enAtencion' => $citas->where('estado_sala', 'en_atencion'),
            'atendidos' => $citas->where('estado_sala', 'atendido'),
        ]);
    }

    public function llegada(Cita $cita)
    {
        $this->own($cita);
        $cita->update(['estado_sala' => 'esperando', 'hora_llegada' => now()]);

        return back();
    }

    public function iniciar(Cita $cita)
    {
        $this->own($cita);
        $cita->update(['estado_sala' => 'en_atencion', 'hora_atencion' => now(), 'estado' => 'confirmada']);

        return back();
    }

    public function finalizar(Cita $cita)
    {
        $this->own($cita);
        $cita->update(['estado_sala' => 'atendido', 'estado' => 'atendida']);

        return back();
    }

    private function own(Cita $cita): void
    {
        abort_unless($cita->empresa_id === $this->empresaId(), 403);
    }
}
