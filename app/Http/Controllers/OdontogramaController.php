<?php

namespace App\Http\Controllers;

use App\Models\Especialidad;
use App\Models\Odontograma;
use App\Models\Paciente;
use Illuminate\Http\Request;

class OdontogramaController extends Controller
{
    private function empresaId(): int
    {
        return (int) auth()->user()->empresa_id;
    }

    private function especialidad(): ?Especialidad
    {
        return Especialidad::where('slug', 'odontologia')->first();
    }

    /** Lista de pacientes de odontología con estado de su odontograma. */
    public function index(Request $request)
    {
        $esp = $this->especialidad();

        $q = Paciente::where('empresa_id', $this->empresaId())
            ->with('odontograma');

        // Prioriza pacientes de la especialidad odontología, pero permite buscar cualquiera.
        if ($esp) {
            $q->where(function ($w) use ($esp) {
                $w->where('especialidad_id', $esp->id)
                  ->orWhereHas('odontograma');
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

        return view('odontograma.index', [
            'pacientes' => $pacientes,
            'especialidad' => $esp,
            'buscar' => $buscar ?? '',
        ]);
    }

    /** Editor del tablero dental de un paciente. */
    public function edit(Paciente $paciente)
    {
        abort_unless($paciente->empresa_id === $this->empresaId(), 403);

        $odo = Odontograma::firstOrNew(['paciente_id' => $paciente->id]);

        return view('odontograma.edit', [
            'paciente' => $paciente,
            'odo' => $odo,
            'estados' => Odontograma::ESTADOS,
            'dientes' => $odo->dientes ?? [],
            'plan' => $odo->plan ?? [],
        ]);
    }

    /** Guarda dientes, plan de tratamiento y notas. */
    public function update(Request $request, Paciente $paciente)
    {
        abort_unless($paciente->empresa_id === $this->empresaId(), 403);

        $data = $request->validate([
            'dientes' => ['nullable', 'string'],   // JSON
            'notas' => ['nullable', 'string'],
            'plan' => ['nullable', 'array'],
            'plan.*.pieza' => ['nullable', 'string', 'max:10'],
            'plan.*.procedimiento' => ['nullable', 'string', 'max:160'],
            'plan.*.estado' => ['nullable', 'string', 'max:20'],
            'plan.*.costo' => ['nullable', 'numeric', 'min:0'],
        ]);

        $dientes = json_decode($data['dientes'] ?? '{}', true);
        if (! is_array($dientes)) {
            $dientes = [];
        }

        // Limpia filas del plan vacías.
        $plan = collect($data['plan'] ?? [])
            ->filter(fn ($r) => filled($r['pieza'] ?? null) || filled($r['procedimiento'] ?? null))
            ->map(fn ($r) => [
                'pieza' => $r['pieza'] ?? '',
                'procedimiento' => $r['procedimiento'] ?? '',
                'estado' => $r['estado'] ?? 'pendiente',
                'costo' => isset($r['costo']) && $r['costo'] !== '' ? (float) $r['costo'] : null,
            ])
            ->values()
            ->all();

        Odontograma::updateOrCreate(
            ['paciente_id' => $paciente->id],
            [
                'empresa_id' => $this->empresaId(),
                'user_id' => auth()->id(),
                'dientes' => $dientes,
                'plan' => $plan,
                'notas' => $data['notas'] ?? null,
            ]
        );

        return redirect()
            ->route('odontograma.edit', $paciente)
            ->with('ok', 'Odontograma guardado correctamente.');
    }
}
