<?php

namespace App\Http\Controllers;

use App\Models\Especialidad;
use App\Models\Paciente;
use App\Models\SesionPsicologica;
use Illuminate\Http\Request;

class PsicologiaController extends Controller
{
    private function empresaId(): int
    {
        return (int) auth()->user()->empresa_id;
    }

    public function index(Request $request)
    {
        $esp = Especialidad::where('slug', 'psicologia')->first();

        $q = Paciente::where('empresa_id', $this->empresaId())->withCount('sesionesPsico')
            ->with(['sesionesPsico' => fn ($r) => $r->latest('fecha')->limit(1)]);
        if ($esp) {
            $q->where(function ($w) use ($esp) {
                $w->where('especialidad_id', $esp->id)->orHas('sesionesPsico');
            });
        }
        if ($buscar = trim((string) $request->get('q'))) {
            $q->where(fn ($w) => $w->where('nombres', 'like', "%$buscar%")
                ->orWhere('apellidos', 'like', "%$buscar%")->orWhere('documento', 'like', "%$buscar%"));
        }

        return view('psicologia.index', [
            'pacientes' => $q->orderBy('apellidos')->paginate(12)->withQueryString(),
            'buscar' => $buscar ?? '',
        ]);
    }

    public function show(Paciente $paciente)
    {
        abort_unless($paciente->empresa_id === $this->empresaId(), 403);
        $sesiones = $paciente->sesionesPsico()->orderByDesc('fecha')->orderByDesc('id')->get();

        return view('psicologia.show', [
            'paciente' => $paciente,
            'sesiones' => $sesiones,
            'ultima' => $sesiones->first(),
            'nextNum' => ($sesiones->max('numero') ?? 0) + 1,
        ]);
    }

    public function store(Request $request, Paciente $paciente)
    {
        abort_unless($paciente->empresa_id === $this->empresaId(), 403);

        $data = $request->validate([
            'fecha' => ['required', 'date'],
            'numero' => ['nullable', 'integer', 'min:1', 'max:999'],
            'motivo' => ['nullable', 'string', 'max:180'],
            'enfoque' => ['nullable', 'string', 'max:80'],
            'desarrollo' => ['nullable', 'string'],
            'tareas' => ['nullable', 'string'],
            'estado_animo' => ['nullable', 'integer', 'min:1', 'max:10'],
            'progreso' => ['nullable', 'integer', 'min:0', 'max:100'],
            'proxima_cita' => ['nullable', 'date'],
        ]);

        $paciente->sesionesPsico()->create(array_merge($data, [
            'empresa_id' => $this->empresaId(),
            'user_id' => auth()->id(),
        ]));

        return redirect()->route('psicologia.show', $paciente)->with('ok', 'Sesión registrada.');
    }

    public function destroy(SesionPsicologica $sesion)
    {
        abort_unless($sesion->empresa_id === $this->empresaId(), 403);
        $paciente = $sesion->paciente;
        $sesion->delete();

        return redirect()->route('psicologia.show', $paciente)->with('ok', 'Sesión eliminada.');
    }
}
