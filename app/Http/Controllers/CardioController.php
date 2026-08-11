<?php

namespace App\Http\Controllers;

use App\Models\Especialidad;
use App\Models\EvaluacionCardio;
use App\Models\Paciente;
use Illuminate\Http\Request;

class CardioController extends Controller
{
    private function empresaId(): int
    {
        return (int) auth()->user()->empresa_id;
    }

    public function index(Request $request)
    {
        $esp = Especialidad::where('slug', 'cardiologia')->first();

        $q = Paciente::where('empresa_id', $this->empresaId())
            ->withCount('evaluacionesCardio')
            ->with(['evaluacionesCardio' => fn ($r) => $r->latest('fecha')->limit(1)]);
        if ($esp) {
            $q->where(function ($w) use ($esp) {
                $w->where('especialidad_id', $esp->id)->orHas('evaluacionesCardio');
            });
        }
        if ($buscar = trim((string) $request->get('q'))) {
            $q->where(function ($w) use ($buscar) {
                $w->where('nombres', 'like', "%$buscar%")
                  ->orWhere('apellidos', 'like', "%$buscar%")
                  ->orWhere('documento', 'like', "%$buscar%");
            });
        }

        $pacientes = $q->orderBy('apellidos')->paginate(12)->withQueryString();

        return view('cardio.index', [
            'pacientes' => $pacientes,
            'buscar' => $buscar ?? '',
        ]);
    }

    public function show(Paciente $paciente)
    {
        abort_unless($paciente->empresa_id === $this->empresaId(), 403);

        $evaluaciones = $paciente->evaluacionesCardio()->orderByDesc('fecha')->orderByDesc('id')->get();

        return view('cardio.show', [
            'paciente' => $paciente,
            'evaluaciones' => $evaluaciones,
            'ultima' => $evaluaciones->first(),
        ]);
    }

    public function store(Request $request, Paciente $paciente)
    {
        abort_unless($paciente->empresa_id === $this->empresaId(), 403);

        $data = $request->validate([
            'fecha' => ['required', 'date'],
            'pa_sistolica' => ['nullable', 'integer', 'min:0', 'max:300'],
            'pa_diastolica' => ['nullable', 'integer', 'min:0', 'max:200'],
            'fc' => ['nullable', 'integer', 'min:0', 'max:300'],
            'colesterol_total' => ['nullable', 'integer', 'min:0', 'max:600'],
            'hdl' => ['nullable', 'integer', 'min:0', 'max:200'],
            'ldl' => ['nullable', 'integer', 'min:0', 'max:400'],
            'trigliceridos' => ['nullable', 'integer', 'min:0', 'max:1500'],
            'glucosa' => ['nullable', 'integer', 'min:0', 'max:800'],
            'fumador' => ['nullable', 'boolean'],
            'diabetes' => ['nullable', 'boolean'],
            'ecg_ritmo' => ['nullable', 'string', 'max:60'],
            'ecg_hallazgos' => ['nullable', 'string'],
            'observaciones' => ['nullable', 'string'],
        ]);

        $data['fumador'] = (bool) ($data['fumador'] ?? false);
        $data['diabetes'] = (bool) ($data['diabetes'] ?? false);

        $riesgo = EvaluacionCardio::estimarRiesgo($data, $paciente->edad, $paciente->sexo);

        $paciente->evaluacionesCardio()->create(array_merge($data, [
            'empresa_id' => $this->empresaId(),
            'user_id' => auth()->id(),
            'riesgo_pct' => $riesgo['pct'],
            'riesgo_nivel' => $riesgo['nivel'],
        ]));

        return redirect()->route('cardio.show', $paciente)->with('ok', 'Evaluación cardiovascular registrada.');
    }

    public function destroy(EvaluacionCardio $evaluacion)
    {
        abort_unless($evaluacion->empresa_id === $this->empresaId(), 403);
        $paciente = $evaluacion->paciente;
        $evaluacion->delete();

        return redirect()->route('cardio.show', $paciente)->with('ok', 'Evaluación eliminada.');
    }
}
