<?php

namespace App\Http\Controllers;

use App\Models\Especialidad;
use App\Models\EvaluacionOftalmo;
use App\Models\Paciente;
use Illuminate\Http\Request;

class OftalmoController extends Controller
{
    private function empresaId(): int
    {
        return (int) auth()->user()->empresa_id;
    }

    public function index(Request $request)
    {
        $esp = Especialidad::where('slug', 'oftalmologia')->first();

        $q = Paciente::where('empresa_id', $this->empresaId())->withCount('evaluacionesOftalmo')
            ->with(['evaluacionesOftalmo' => fn ($r) => $r->latest('fecha')->limit(1)]);
        if ($esp) {
            $q->where(function ($w) use ($esp) {
                $w->where('especialidad_id', $esp->id)->orHas('evaluacionesOftalmo');
            });
        }
        if ($buscar = trim((string) $request->get('q'))) {
            $q->where(fn ($w) => $w->where('nombres', 'like', "%$buscar%")
                ->orWhere('apellidos', 'like', "%$buscar%")->orWhere('documento', 'like', "%$buscar%"));
        }

        return view('oftalmo.index', [
            'pacientes' => $q->orderBy('apellidos')->paginate(12)->withQueryString(),
            'buscar' => $buscar ?? '',
        ]);
    }

    public function show(Paciente $paciente)
    {
        abort_unless($paciente->empresa_id === $this->empresaId(), 403);
        $evaluaciones = $paciente->evaluacionesOftalmo()->orderByDesc('fecha')->orderByDesc('id')->get();

        return view('oftalmo.show', [
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
            'od_av' => ['nullable', 'string', 'max:12'],
            'od_esfera' => ['nullable', 'numeric', 'between:-30,30'],
            'od_cilindro' => ['nullable', 'numeric', 'between:-15,15'],
            'od_eje' => ['nullable', 'integer', 'min:0', 'max:180'],
            'od_pio' => ['nullable', 'numeric', 'min:0', 'max:80'],
            'os_av' => ['nullable', 'string', 'max:12'],
            'os_esfera' => ['nullable', 'numeric', 'between:-30,30'],
            'os_cilindro' => ['nullable', 'numeric', 'between:-15,15'],
            'os_eje' => ['nullable', 'integer', 'min:0', 'max:180'],
            'os_pio' => ['nullable', 'numeric', 'min:0', 'max:80'],
            'diagnostico' => ['nullable', 'string', 'max:180'],
            'observaciones' => ['nullable', 'string'],
        ]);

        $paciente->evaluacionesOftalmo()->create(array_merge($data, [
            'empresa_id' => $this->empresaId(),
            'user_id' => auth()->id(),
        ]));

        return redirect()->route('oftalmo.show', $paciente)->with('ok', 'Evaluación oftalmológica registrada.');
    }

    public function destroy(EvaluacionOftalmo $evaluacion)
    {
        abort_unless($evaluacion->empresa_id === $this->empresaId(), 403);
        $paciente = $evaluacion->paciente;
        $evaluacion->delete();

        return redirect()->route('oftalmo.show', $paciente)->with('ok', 'Evaluación eliminada.');
    }
}
