<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use App\Models\Notificacion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservaController extends Controller
{
    private function paciente()
    {
        return Auth::guard('paciente')->user();
    }

    /** Genera franjas horarias según el horario de la empresa. */
    private function franjas($empresa): array
    {
        $ini = (int) substr($empresa->horario_inicio ?? '08:00', 0, 2);
        $fin = (int) substr($empresa->horario_fin ?? '18:00', 0, 2);
        $slots = [];
        for ($h = $ini; $h < $fin; $h++) {
            $slots[] = sprintf('%02d:00', $h);
            $slots[] = sprintf('%02d:30', $h);
        }
        return $slots;
    }

    public function create()
    {
        $p = $this->paciente();
        $empresa = $p->empresa;

        return view('portal.reservar', [
            'paciente' => $p,
            'empresa' => $empresa,
            'especialidades' => $empresa?->especialidadesActivas()->get() ?? collect(),
            'medicos' => User::where('empresa_id', $empresa->id)->where('role', 'medico')->where('activo', true)->get(),
            'franjas' => $this->franjas($empresa),
        ]);
    }

    public function store(Request $request)
    {
        $p = $this->paciente();
        $empresa = $p->empresa;

        $data = $request->validate([
            'especialidad_id' => ['nullable', 'exists:especialidades,id'],
            'medico_id' => ['nullable', 'exists:users,id'],
            'fecha' => ['required', 'date', 'after_or_equal:today'],
            'hora' => ['required', 'string'],
            'motivo' => ['nullable', 'string', 'max:200'],
        ]);

        if (! $this->medicoDisponible($data['medico_id'] ?? null, $data['fecha'], $data['hora'])) {
            return back()->withErrors(['hora' => 'El medico no atiende en ese horario. Elige otro.'])->withInput();
        }

        // Verificar disponibilidad si se eligió médico
        if (! empty($data['medico_id'])) {
            $ocupado = Cita::where('empresa_id', $empresa->id)
                ->where('medico_id', $data['medico_id'])
                ->whereDate('fecha', $data['fecha'])
                ->where('hora', $data['hora'].':00')
                ->whereNotIn('estado', ['cancelada', 'no_asistio'])
                ->exists();

            if ($ocupado) {
                return back()->withErrors(['hora' => 'Ese horario ya está ocupado. Elige otro.'])->withInput();
            }
        }

        $cita = Cita::create([
            'empresa_id' => $empresa->id,
            'paciente_id' => $p->id,
            'medico_id' => $data['medico_id'] ?? null,
            'especialidad_id' => $data['especialidad_id'] ?? $p->especialidad_id,
            'fecha' => $data['fecha'],
            'hora' => $data['hora'].':00',
            'duracion' => 30,
            'estado' => 'pendiente',
            'motivo' => $data['motivo'] ?? 'Reserva online',
        ]);

        Notificacion::crear($empresa->id, 'Reserva online del paciente', [
            'tipo' => 'cita', 'icono' => 'fa-globe',
            'mensaje' => $p->nombre_completo.' reservó para el '.$cita->fecha->format('d/m/Y').' '.$data['hora'],
            'url' => route('citas.index'),
        ]);

        return redirect()->route('portal.dashboard')->with('ok', 'Tu cita fue solicitada. La clínica la confirmará pronto.');
    }

    public function editar(\App\Models\Cita $cita)
    {
        $this->ownCita($cita);
        $empresa = $this->paciente()->empresa;

        return view('portal.cita-editar', [
            'cita' => $cita,
            'empresa' => $empresa,
            'medicos' => User::where('empresa_id', $empresa->id)->where('role', 'medico')->where('activo', true)->get(),
            'franjas' => $this->franjas($empresa),
        ]);
    }

    public function actualizar(Request $request, \App\Models\Cita $cita)
    {
        $this->ownCita($cita);
        $data = $request->validate([
            'fecha' => ['required', 'date', 'after_or_equal:today'],
            'hora' => ['required', 'string'],
        ]);

        if ($cita->medico_id) {
            $ocupado = \App\Models\Cita::where('empresa_id', $cita->empresa_id)
                ->where('medico_id', $cita->medico_id)->whereKeyNot($cita->id)
                ->whereDate('fecha', $data['fecha'])->where('hora', $data['hora'].':00')
                ->whereNotIn('estado', ['cancelada', 'no_asistio'])->exists();
            if ($ocupado) {
                return back()->withErrors(['hora' => 'Ese horario ya esta ocupado.'])->withInput();
            }
        }

        $cita->update(['fecha' => $data['fecha'], 'hora' => $data['hora'].':00', 'estado' => 'pendiente']);

        \App\Models\Notificacion::crear($cita->empresa_id, 'Cita reprogramada por el paciente', [
            'tipo' => 'cita', 'icono' => 'fa-calendar-day',
            'mensaje' => $this->paciente()->nombre_completo.' movio su cita al '.$cita->fecha->format('d/m/Y').' '.$data['hora'],
            'url' => route('citas.index'),
        ]);

        return redirect()->route('portal.dashboard')->with('ok', 'Tu cita fue reprogramada.');
    }

    public function cancelar(\App\Models\Cita $cita)
    {
        $this->ownCita($cita);
        $cita->update(['estado' => 'cancelada']);

        \App\Models\Notificacion::crear($cita->empresa_id, 'Cita cancelada por el paciente', [
            'tipo' => 'alerta', 'icono' => 'fa-calendar-xmark',
            'mensaje' => $this->paciente()->nombre_completo.' cancelo su cita del '.$cita->fecha->format('d/m/Y'),
            'url' => route('citas.index'),
        ]);

        return redirect()->route('portal.dashboard')->with('ok', 'Tu cita fue cancelada.');
    }

    private function ownCita(\App\Models\Cita $cita): void
    {
        abort_unless($cita->paciente_id === $this->paciente()->id && $cita->estado === 'pendiente', 403);
    }


    private function medicoDisponible(?int $medicoId, string $fecha, string $hora): bool
    {
        if (! $medicoId) return true;
        $horarios = \App\Models\HorarioMedico::where('user_id', $medicoId)->where('activo', true)->get();
        if ($horarios->isEmpty()) return true; // sin horarios => usa horario general
        $dow = (int) \Illuminate\Support\Carbon::parse($fecha)->dayOfWeek; // 0=domingo
        foreach ($horarios->where('dia_semana', $dow) as $h) {
            $ini = substr($h->hora_inicio, 0, 5);
            $fin = substr($h->hora_fin, 0, 5);
            if ($hora >= $ini && $hora < $fin) return true;
        }
        return false;
    }


    public function confirmar(\App\Models\Cita $cita)
    {
        $this->ownCita($cita);
        $cita->update(['estado' => 'confirmada']);

        \App\Models\Notificacion::crear($cita->empresa_id, 'Cita confirmada por el paciente', [
            'tipo' => 'cita', 'icono' => 'fa-circle-check',
            'mensaje' => $this->paciente()->nombre_completo.' confirmo su cita del '.$cita->fecha->format('d/m/Y'),
            'url' => route('citas.index'),
        ]);

        return back()->with('ok', 'Confirmaste tu asistencia. ¡Te esperamos!');
    }

    public function encuestar(\App\Models\Cita $cita)
    {
        abort_unless($cita->paciente_id === $this->paciente()->id && $cita->estado === 'atendida', 403);
        abort_if($cita->encuesta()->exists(), 403);

        return view('portal.encuesta', ['cita' => $cita]);
    }

    public function guardarEncuesta(Request $request, \App\Models\Cita $cita)
    {
        abort_unless($cita->paciente_id === $this->paciente()->id && $cita->estado === 'atendida', 403);
        abort_if($cita->encuesta()->exists(), 403);

        $data = $request->validate([
            'puntuacion' => ['required', 'integer', 'between:1,5'],
            'comentario' => ['nullable', 'string', 'max:500'],
        ]);

        \App\Models\Encuesta::create([
            'empresa_id' => $cita->empresa_id,
            'paciente_id' => $cita->paciente_id,
            'cita_id' => $cita->id,
            'puntuacion' => $data['puntuacion'],
            'comentario' => $data['comentario'] ?? null,
        ]);

        \App\Models\Notificacion::crear($cita->empresa_id, 'Nueva encuesta de satisfacción', [
            'tipo' => 'info', 'icono' => 'fa-star',
            'mensaje' => $this->paciente()->nombre_completo.' calificó su atención con '.$data['puntuacion'].'/5',
            'url' => route('reportes.clinico'),
        ]);

        return redirect()->route('portal.dashboard')->with('ok', 'Gracias por tu opinión.');
    }

}
