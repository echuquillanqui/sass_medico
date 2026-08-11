<?php

namespace App\Http\Controllers;

use App\Models\Dermatograma;
use App\Models\Especialidad;
use App\Models\Paciente;
use Illuminate\Http\Request;

class DermatogramaController extends Controller
{
    private function empresaId(): int
    {
        return (int) auth()->user()->empresa_id;
    }

    public function index(Request $request)
    {
        $esp = Especialidad::where('slug', 'dermatologia')->first();

        $q = Paciente::where('empresa_id', $this->empresaId())->with('dermatograma');
        if ($esp) {
            $q->where(function ($w) use ($esp) {
                $w->where('especialidad_id', $esp->id)->orWhereHas('dermatograma');
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

        return view('dermatograma.index', [
            'pacientes' => $pacientes,
            'buscar' => $buscar ?? '',
        ]);
    }

    public function edit(Paciente $paciente)
    {
        abort_unless($paciente->empresa_id === $this->empresaId(), 403);

        $derma = Dermatograma::firstOrNew(['paciente_id' => $paciente->id]);

        return view('dermatograma.edit', [
            'paciente' => $paciente,
            'derma' => $derma,
            'tipos' => Dermatograma::TIPOS,
            'lesiones' => $derma->lesiones ?? [],
        ]);
    }

    public function update(Request $request, Paciente $paciente)
    {
        abort_unless($paciente->empresa_id === $this->empresaId(), 403);

        $data = $request->validate([
            'lesiones' => ['nullable', 'string'],  // JSON
            'notas' => ['nullable', 'string'],
        ]);

        $lesiones = json_decode($data['lesiones'] ?? '[]', true);
        if (! is_array($lesiones)) {
            $lesiones = [];
        }

        // Sanea cada lesión.
        $lesiones = array_values(array_filter(array_map(function ($l) {
            if (! is_array($l)) {
                return null;
            }

            return [
                'vista' => in_array($l['vista'] ?? '', ['frente', 'espalda']) ? $l['vista'] : 'frente',
                'x' => round((float) ($l['x'] ?? 0), 2),
                'y' => round((float) ($l['y'] ?? 0), 2),
                'tipo' => array_key_exists($l['tipo'] ?? '', Dermatograma::TIPOS) ? $l['tipo'] : 'macula',
                'descripcion' => mb_substr((string) ($l['descripcion'] ?? ''), 0, 160),
            ];
        }, $lesiones)));

        Dermatograma::updateOrCreate(
            ['paciente_id' => $paciente->id],
            [
                'empresa_id' => $this->empresaId(),
                'user_id' => auth()->id(),
                'lesiones' => $lesiones,
                'notas' => $data['notas'] ?? null,
            ]
        );

        return redirect()->route('dermatograma.edit', $paciente)->with('ok', 'Mapa de lesiones guardado.');
    }
}
