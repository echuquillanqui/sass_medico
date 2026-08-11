<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\Triaje;
use App\Models\User;
use Illuminate\Http\Request;

class TriajeController extends Controller
{
    private function empresaId(): int
    {
        return (int) auth()->user()->empresa_id;
    }

    public function index()
    {
        // Cola de emergencias: primero por nivel (1 mas urgente), luego por llegada
        $cola = Triaje::with(['paciente', 'medico'])
            ->where('empresa_id', $this->empresaId())
            ->whereIn('estado', ['en_espera', 'en_atencion'])
            ->orderBy('nivel')->orderBy('hora_llegada')->get();

        $atendidosHoy = Triaje::where('empresa_id', $this->empresaId())
            ->where('estado', 'atendido')->whereDate('hora_llegada', today())->count();

        return view('triaje.index', [
            'cola' => $cola,
            'niveles' => Triaje::NIVELES,
            'enEspera' => $cola->where('estado', 'en_espera')->count(),
            'enAtencion' => $cola->where('estado', 'en_atencion')->count(),
            'atendidosHoy' => $atendidosHoy,
        ]);
    }

    public function create()
    {
        return view('triaje.form', [
            'pacientes' => Paciente::where('empresa_id', $this->empresaId())->orderBy('apellidos')->get(),
            'niveles' => Triaje::NIVELES,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'paciente_id' => ['required', 'exists:pacientes,id'],
            'nivel' => ['required', 'integer', 'between:1,5'],
            'motivo' => ['required', 'string', 'max:200'],
            'presion_arterial' => ['nullable', 'string', 'max:20'],
            'frecuencia_cardiaca' => ['nullable', 'integer'],
            'frecuencia_respiratoria' => ['nullable', 'integer'],
            'temperatura' => ['nullable', 'numeric'],
            'saturacion' => ['nullable', 'string', 'max:10'],
            'dolor' => ['nullable', 'integer', 'between:0,10'],
            'observaciones' => ['nullable', 'string'],
        ]);
        $data['empresa_id'] = $this->empresaId();
        $data['user_id'] = auth()->id();
        $data['estado'] = 'en_espera';
        $data['hora_llegada'] = now();
        Triaje::create($data);

        return redirect()->route('triaje.index')->with('ok', 'Paciente registrado en emergencias.');
    }

    public function atender(Triaje $triaje)
    {
        abort_unless($triaje->empresa_id === $this->empresaId(), 403);
        $triaje->update(['estado' => 'en_atencion', 'medico_id' => auth()->id(), 'hora_atencion' => now()]);

        return back()->with('ok', 'Paciente en atención.');
    }

    public function finalizar(Triaje $triaje)
    {
        abort_unless($triaje->empresa_id === $this->empresaId(), 403);
        $triaje->update(['estado' => 'atendido']);

        return back()->with('ok', 'Atención finalizada.');
    }

    public function destroy(Triaje $triaje)
    {
        abort_unless($triaje->empresa_id === $this->empresaId(), 403);
        $triaje->delete();

        return back()->with('ok', 'Registro eliminado.');
    }
}
