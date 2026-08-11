<?php

namespace App\Http\Controllers;

use App\Mail\CitaMail;
use App\Models\Cita;
use App\Models\Notificacion;
use App\Models\Paciente;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class CitaController extends Controller
{
    private function empresaId(): int
    {
        return (int) auth()->user()->empresa_id;
    }

    public function index(Request $request)
    {
        $estado = $request->get('estado');
        $citas = Cita::where('empresa_id', $this->empresaId())
            ->when($estado, fn ($q) => $q->where('estado', $estado))
            ->with(['paciente', 'medico', 'especialidad'])
            ->orderBy('fecha', 'desc')->orderBy('hora')
            ->paginate(12)->withQueryString();

        return view('citas.index', compact('citas', 'estado'));
    }

    public function create()
    {
        return view('citas.form', [
            'cita' => new Cita(['fecha' => now()->toDateString(), 'hora' => '09:00']),
        ] + $this->opciones());
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['empresa_id'] = $this->empresaId();
        $cita = Cita::create($data);

        // Enviar confirmación por correo si el paciente tiene email
        $cita->load(['paciente', 'especialidad', 'medico', 'empresa']);
        if ($cita->paciente && $cita->paciente->email) {
            try {
                Mail::to($cita->paciente->email)->send(new CitaMail($cita, 'confirmacion'));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        Notificacion::crear($cita->empresa_id, 'Nueva cita agendada', [
            'tipo' => 'cita', 'icono' => 'fa-calendar-plus',
            'mensaje' => $cita->paciente->nombre_completo.' · '.$cita->fecha->format('d/m/Y').' '.substr((string) $cita->hora, 0, 5),
            'url' => route('citas.index'),
        ]);

        return redirect()->route('citas.index')->with('ok', 'Cita agendada correctamente.');
    }

    public function edit(Cita $cita)
    {
        abort_unless($cita->empresa_id === $this->empresaId(), 403);
        return view('citas.form', ['cita' => $cita] + $this->opciones());
    }

    public function update(Request $request, Cita $cita)
    {
        abort_unless($cita->empresa_id === $this->empresaId(), 403);
        $cita->update($this->validated($request));

        return redirect()->route('citas.index')->with('ok', 'Cita actualizada.');
    }

    public function destroy(Cita $cita)
    {
        abort_unless($cita->empresa_id === $this->empresaId(), 403);
        $cita->delete();

        return redirect()->route('citas.index')->with('ok', 'Cita eliminada.');
    }

    private function opciones(): array
    {
        $empresa = auth()->user()->empresa;
        return [
            'pacientes' => Paciente::where('empresa_id', $this->empresaId())->orderBy('apellidos')->get(),
            'medicos' => User::where('empresa_id', $this->empresaId())->where('role', 'medico')->get(),
            'especialidades' => $empresa?->especialidadesActivas()->get() ?? collect(),
        ];
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'paciente_id' => ['required', 'exists:pacientes,id'],
            'medico_id' => ['nullable', 'exists:users,id'],
            'especialidad_id' => ['nullable', 'exists:especialidades,id'],
            'fecha' => ['required', 'date'],
            'hora' => ['required'],
            'duracion' => ['nullable', 'integer', 'min:5', 'max:240'],
            'estado' => ['required', 'in:pendiente,confirmada,atendida,cancelada,no_asistio'],
            'motivo' => ['nullable', 'string', 'max:200'],
            'notas' => ['nullable', 'string'],
            'es_teleconsulta' => ['nullable', 'boolean'],
        ]);
    }
}
