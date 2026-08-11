<?php

namespace App\Http\Controllers;

use App\Models\ControlPrenatal;
use App\Models\Embarazo;
use App\Models\Especialidad;
use App\Models\Paciente;
use Illuminate\Http\Request;

class PrenatalController extends Controller
{
    private function empresaId(): int
    {
        return (int) auth()->user()->empresa_id;
    }

    /** Selector de gestantes (ginecología / obstetricia). */
    public function index(Request $request)
    {
        $slugs = ['ginecologia', 'obstetricia'];
        $espIds = Especialidad::whereIn('slug', $slugs)->pluck('id');

        $q = Paciente::where('empresa_id', $this->empresaId())->with('embarazo')
            ->where('sexo', 'F');
        $q->where(function ($w) use ($espIds) {
            $w->whereIn('especialidad_id', $espIds)->orWhereHas('embarazo');
        });
        if ($buscar = trim((string) $request->get('q'))) {
            $q->where(function ($w) use ($buscar) {
                $w->where('nombres', 'like', "%$buscar%")
                  ->orWhere('apellidos', 'like', "%$buscar%")
                  ->orWhere('documento', 'like', "%$buscar%");
            });
        }

        $pacientes = $q->orderBy('apellidos')->paginate(12)->withQueryString();

        return view('prenatal.index', [
            'pacientes' => $pacientes,
            'buscar' => $buscar ?? '',
        ]);
    }

    /** Ficha de control prenatal de una paciente. */
    public function show(Paciente $paciente)
    {
        abort_unless($paciente->empresa_id === $this->empresaId(), 403);

        $embarazo = Embarazo::with('controles')->firstOrNew(['paciente_id' => $paciente->id]);

        return view('prenatal.show', [
            'paciente' => $paciente,
            'embarazo' => $embarazo,
        ]);
    }

    /** Crea/actualiza el embarazo actual de la paciente. */
    public function guardarEmbarazo(Request $request, Paciente $paciente)
    {
        abort_unless($paciente->empresa_id === $this->empresaId(), 403);

        $data = $request->validate([
            'fum' => ['nullable', 'date'],
            'fpp' => ['nullable', 'date'],
            'gestas' => ['nullable', 'integer', 'min:0', 'max:30'],
            'partos' => ['nullable', 'integer', 'min:0', 'max:30'],
            'abortos' => ['nullable', 'integer', 'min:0', 'max:30'],
            'cesareas' => ['nullable', 'integer', 'min:0', 'max:30'],
            'grupo_sanguineo' => ['nullable', 'string', 'max:6'],
            'riesgo_alto' => ['nullable', 'boolean'],
            'estado' => ['nullable', 'in:activo,finalizado'],
            'antecedentes' => ['nullable', 'string'],
        ]);

        // FPP automática (Naegele) si hay FUM y no se indicó manualmente.
        if (empty($data['fpp']) && ! empty($data['fum'])) {
            $data['fpp'] = \Illuminate\Support\Carbon::parse($data['fum'])->addDays(280)->toDateString();
        }

        Embarazo::updateOrCreate(
            ['paciente_id' => $paciente->id],
            array_merge($data, [
                'empresa_id' => $this->empresaId(),
                'user_id' => auth()->id(),
                'riesgo_alto' => (bool) ($data['riesgo_alto'] ?? false),
                'estado' => $data['estado'] ?? 'activo',
            ])
        );

        return redirect()->route('prenatal.show', $paciente)->with('ok', 'Datos del embarazo guardados.');
    }

    /** Registra un control prenatal. */
    public function guardarControl(Request $request, Paciente $paciente)
    {
        abort_unless($paciente->empresa_id === $this->empresaId(), 403);
        $embarazo = Embarazo::where('paciente_id', $paciente->id)->firstOrFail();

        $data = $request->validate([
            'fecha' => ['required', 'date'],
            'peso' => ['nullable', 'numeric', 'min:0', 'max:300'],
            'presion_arterial' => ['nullable', 'string', 'max:12'],
            'altura_uterina' => ['nullable', 'numeric', 'min:0', 'max:60'],
            'fcf' => ['nullable', 'integer', 'min:0', 'max:250'],
            'presentacion' => ['nullable', 'string', 'max:30'],
            'movimientos_fetales' => ['nullable', 'boolean'],
            'edema' => ['nullable', 'boolean'],
            'observaciones' => ['nullable', 'string'],
        ]);

        $data['semanas'] = $embarazo->semanasA($data['fecha']);
        $data['user_id'] = auth()->id();
        $data['movimientos_fetales'] = (bool) ($data['movimientos_fetales'] ?? false);
        $data['edema'] = (bool) ($data['edema'] ?? false);

        $embarazo->controles()->create($data);

        return redirect()->route('prenatal.show', $paciente)->with('ok', 'Control prenatal registrado.');
    }

    public function eliminarControl(ControlPrenatal $control)
    {
        $paciente = $control->embarazo->paciente;
        abort_unless($paciente->empresa_id === $this->empresaId(), 403);
        $control->delete();

        return back()->with('ok', 'Control eliminado.');
    }
}
