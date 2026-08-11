<?php

namespace App\Http\Controllers;

use App\Models\Especialidad;
use App\Models\EvaluacionEspecialidad;
use App\Models\Paciente;
use Illuminate\Http\Request;

class EvaluacionEspecialidadController extends Controller
{
    private function empresaId(): int
    {
        return (int) auth()->user()->empresa_id;
    }

    private function config(string $slug): array
    {
        $cfg = config("evaluaciones.$slug");
        abort_if(! $cfg, 404, 'Esta especialidad no tiene un proceso de evaluación configurado.');

        return $cfg;
    }

    public function index(string $slug, Request $request)
    {
        $cfg = $this->config($slug);
        $esp = Especialidad::where('slug', $slug)->first();

        $q = Paciente::where('empresa_id', $this->empresaId())
            ->withCount(['evaluacionesEspecialidad as evals_count' => fn ($r) => $r->where('especialidad_slug', $slug)])
            ->withMax(['evaluacionesEspecialidad as ultima_fecha' => fn ($r) => $r->where('especialidad_slug', $slug)], 'fecha');

        if ($esp) {
            $q->where(function ($w) use ($esp, $slug) {
                $w->where('especialidad_id', $esp->id)
                  ->orWhereHas('evaluacionesEspecialidad', fn ($r) => $r->where('especialidad_slug', $slug));
            });
        }
        if ($buscar = trim((string) $request->get('q'))) {
            $q->where(fn ($w) => $w->where('nombres', 'like', "%$buscar%")
                ->orWhere('apellidos', 'like', "%$buscar%")->orWhere('documento', 'like', "%$buscar%"));
        }

        return view('evaluaciones.index', [
            'slug' => $slug, 'cfg' => $cfg, 'especialidad' => $esp,
            'pacientes' => $q->orderBy('apellidos')->paginate(12)->withQueryString(),
            'buscar' => $buscar ?? '',
        ]);
    }

    public function show(string $slug, Paciente $paciente)
    {
        $cfg = $this->config($slug);
        abort_unless($paciente->empresa_id === $this->empresaId(), 403);

        $evaluaciones = $paciente->evaluacionesEspecialidad()
            ->where('especialidad_slug', $slug)
            ->orderByDesc('fecha')->orderByDesc('id')->get();

        return view('evaluaciones.show', [
            'slug' => $slug, 'cfg' => $cfg, 'paciente' => $paciente,
            'evaluaciones' => $evaluaciones, 'ultima' => $evaluaciones->first(),
        ]);
    }

    public function store(string $slug, Paciente $paciente, Request $request)
    {
        $cfg = $this->config($slug);
        abort_unless($paciente->empresa_id === $this->empresaId(), 403);

        $request->validate([
            'fecha' => ['required', 'date'],
            'notas' => ['nullable', 'string'],
        ]);

        // Recoge solo los campos definidos en la config de la especialidad.
        $datos = [];
        foreach ($cfg['campos'] as $campo) {
            $val = $request->input('datos.'.$campo['name']);
            if ($val !== null && $val !== '') {
                $datos[$campo['name']] = is_string($val) ? mb_substr($val, 0, 2000) : $val;
            }
        }

        $paciente->evaluacionesEspecialidad()->create([
            'empresa_id' => $this->empresaId(),
            'user_id' => auth()->id(),
            'especialidad_slug' => $slug,
            'fecha' => $request->date('fecha'),
            'datos' => $datos,
            'notas' => $request->input('notas'),
        ]);

        return redirect()->route('evaluacion.show', [$slug, $paciente])->with('ok', 'Evaluación registrada.');
    }

    public function destroy(EvaluacionEspecialidad $evaluacion)
    {
        abort_unless($evaluacion->empresa_id === $this->empresaId(), 403);
        $slug = $evaluacion->especialidad_slug;
        $paciente = $evaluacion->paciente;
        $evaluacion->delete();

        return redirect()->route('evaluacion.show', [$slug, $paciente])->with('ok', 'Evaluación eliminada.');
    }
}
