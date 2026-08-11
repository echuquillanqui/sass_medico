<?php

namespace App\Http\Controllers;

use App\Models\Cama;
use App\Models\Hospitalizacion;
use App\Models\Paciente;
use App\Models\User;
use Illuminate\Http\Request;

class HospitalizacionController extends Controller
{
    private function empresaId(): int
    {
        return (int) auth()->user()->empresa_id;
    }

    public function index()
    {
        $activas = Hospitalizacion::with(['paciente', 'cama', 'medico', 'especialidad'])
            ->where('empresa_id', $this->empresaId())->where('estado', 'activa')
            ->orderBy('fecha_ingreso')->get();

        $camas = Cama::where('empresa_id', $this->empresaId())->where('activo', true)
            ->withCount(['hospitalizaciones as ocupada_count' => fn ($q) => $q->where('estado', 'activa')])
            ->orderBy('nombre')->get();

        return view('hospitalizacion.index', [
            'activas' => $activas,
            'camas' => $camas,
            'totalCamas' => $camas->count(),
            'ocupadas' => $camas->where('ocupada_count', '>', 0)->count(),
        ]);
    }

    public function create()
    {
        return view('hospitalizacion.form', $this->opciones());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'paciente_id' => ['required', 'exists:pacientes,id'],
            'cama_id' => ['nullable', 'exists:camas,id'],
            'medico_id' => ['nullable', 'exists:users,id'],
            'especialidad_id' => ['nullable', 'exists:especialidades,id'],
            'fecha_ingreso' => ['required', 'date'],
            'motivo_ingreso' => ['nullable', 'string'],
            'diagnostico_ingreso' => ['nullable', 'string'],
        ]);

        // Verificar cama libre
        if (! empty($data['cama_id'])) {
            $ocupada = Hospitalizacion::where('cama_id', $data['cama_id'])->where('estado', 'activa')->exists();
            if ($ocupada) {
                return back()->withErrors(['cama_id' => 'La cama seleccionada ya está ocupada.'])->withInput();
            }
        }

        $data['empresa_id'] = $this->empresaId();
        $data['estado'] = 'activa';
        $h = Hospitalizacion::create($data);

        return redirect()->route('hospitalizacion.show', $h)->with('ok', 'Paciente ingresado.');
    }

    public function show(Hospitalizacion $hospitalizacion)
    {
        abort_unless($hospitalizacion->empresa_id === $this->empresaId(), 403);
        $hospitalizacion->load(['paciente', 'cama', 'medico', 'especialidad', 'evoluciones.user']);

        return view('hospitalizacion.show', compact('hospitalizacion'));
    }

    public function alta(Request $request, Hospitalizacion $hospitalizacion)
    {
        abort_unless($hospitalizacion->empresa_id === $this->empresaId(), 403);
        $data = $request->validate(['resumen_alta' => ['nullable', 'string']]);

        $hospitalizacion->update([
            'estado' => 'alta',
            'fecha_alta' => now(),
            'resumen_alta' => $data['resumen_alta'] ?? null,
        ]);

        return redirect()->route('hospitalizacion.index')->with('ok', 'Alta registrada. La cama quedó libre.');
    }

    public function destroy(Hospitalizacion $hospitalizacion)
    {
        abort_unless($hospitalizacion->empresa_id === $this->empresaId(), 403);
        $hospitalizacion->delete();

        return redirect()->route('hospitalizacion.index')->with('ok', 'Hospitalización eliminada.');
    }


    public function agregarEvolucion(Request $request, Hospitalizacion $hospitalizacion)
    {
        abort_unless($hospitalizacion->empresa_id === $this->empresaId(), 403);
        abort_unless($hospitalizacion->estado === 'activa', 403, 'La hospitalizacion ya tiene alta.');

        $data = $request->validate([
            'nota' => ['required', 'string'],
            'presion_arterial' => ['nullable', 'string', 'max:20'],
            'frecuencia_cardiaca' => ['nullable', 'integer'],
            'temperatura' => ['nullable', 'numeric'],
            'saturacion' => ['nullable', 'string', 'max:10'],
        ]);
        $data['user_id'] = auth()->id();
        $data['fecha'] = now();
        $hospitalizacion->evoluciones()->create($data);

        return back()->with('ok', 'Evolucion registrada.');
    }

    private function opciones(): array
    {
        $eid = $this->empresaId();
        $camasOcupadas = Hospitalizacion::where('empresa_id', $eid)->where('estado', 'activa')->pluck('cama_id')->filter()->all();
        $empresa = auth()->user()->empresa;

        return [
            'hospitalizacion' => new Hospitalizacion(['fecha_ingreso' => now()->format('Y-m-d\TH:i')]),
            'pacientes' => Paciente::where('empresa_id', $eid)->orderBy('apellidos')->get(),
            'camasLibres' => Cama::where('empresa_id', $eid)->where('activo', true)->whereNotIn('id', $camasOcupadas)->orderBy('nombre')->get(),
            'medicos' => User::where('empresa_id', $eid)->where('role', 'medico')->get(),
            'especialidades' => $empresa?->especialidadesActivas()->get() ?? collect(),
        ];
    }
}
