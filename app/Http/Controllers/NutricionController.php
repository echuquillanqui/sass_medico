<?php

namespace App\Http\Controllers;

use App\Models\Especialidad;
use App\Models\EvaluacionNutricion;
use App\Models\Paciente;
use Illuminate\Http\Request;

class NutricionController extends Controller
{
    private function empresaId(): int
    {
        return (int) auth()->user()->empresa_id;
    }

    public function index(Request $request)
    {
        $esp = Especialidad::where('slug', 'nutricion')->first();

        $q = Paciente::where('empresa_id', $this->empresaId())->withCount('evaluacionesNutricion')
            ->with(['evaluacionesNutricion' => fn ($r) => $r->latest('fecha')->limit(1)]);
        if ($esp) {
            $q->where(function ($w) use ($esp) {
                $w->where('especialidad_id', $esp->id)->orHas('evaluacionesNutricion');
            });
        }
        if ($buscar = trim((string) $request->get('q'))) {
            $q->where(fn ($w) => $w->where('nombres', 'like', "%$buscar%")
                ->orWhere('apellidos', 'like', "%$buscar%")->orWhere('documento', 'like', "%$buscar%"));
        }

        return view('nutricion.index', [
            'pacientes' => $q->orderBy('apellidos')->paginate(12)->withQueryString(),
            'buscar' => $buscar ?? '',
        ]);
    }

    public function show(Paciente $paciente)
    {
        abort_unless($paciente->empresa_id === $this->empresaId(), 403);
        $evaluaciones = $paciente->evaluacionesNutricion()->orderByDesc('fecha')->orderByDesc('id')->get();

        return view('nutricion.show', [
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
            'peso' => ['nullable', 'numeric', 'min:0', 'max:400'],
            'talla' => ['nullable', 'numeric', 'min:0', 'max:250'],
            'grasa' => ['nullable', 'numeric', 'min:0', 'max:80'],
            'cintura' => ['nullable', 'numeric', 'min:0', 'max:250'],
            'cadera' => ['nullable', 'numeric', 'min:0', 'max:250'],
            'musculo' => ['nullable', 'numeric', 'min:0', 'max:80'],
            'objetivo_kcal' => ['nullable', 'integer', 'min:0', 'max:8000'],
            'peso_objetivo' => ['nullable', 'numeric', 'min:0', 'max:400'],
            'plan' => ['nullable', 'string'],
            'observaciones' => ['nullable', 'string'],
        ]);

        // IMC = peso / (talla_m^2)
        $imc = null;
        if (! empty($data['peso']) && ! empty($data['talla'])) {
            $m = $data['talla'] / 100;
            $imc = $m > 0 ? round($data['peso'] / ($m * $m), 2) : null;
        }

        $paciente->evaluacionesNutricion()->create(array_merge($data, [
            'empresa_id' => $this->empresaId(),
            'user_id' => auth()->id(),
            'imc' => $imc,
        ]));

        return redirect()->route('nutricion.show', $paciente)->with('ok', 'Evaluación nutricional registrada.');
    }

    public function destroy(EvaluacionNutricion $evaluacion)
    {
        abort_unless($evaluacion->empresa_id === $this->empresaId(), 403);
        $paciente = $evaluacion->paciente;
        $evaluacion->delete();

        return redirect()->route('nutricion.show', $paciente)->with('ok', 'Evaluación eliminada.');
    }
}
