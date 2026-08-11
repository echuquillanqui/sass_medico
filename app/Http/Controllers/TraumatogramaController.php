<?php

namespace App\Http\Controllers;

use App\Models\Especialidad;
use App\Models\Paciente;
use App\Models\Traumatograma;
use Illuminate\Http\Request;

class TraumatogramaController extends Controller
{
    private function empresaId(): int
    {
        return (int) auth()->user()->empresa_id;
    }

    public function index(Request $request)
    {
        $esp = Especialidad::where('slug', 'traumatologia')->first();

        $q = Paciente::where('empresa_id', $this->empresaId())->with('traumatograma');
        if ($esp) {
            $q->where(function ($w) use ($esp) {
                $w->where('especialidad_id', $esp->id)->orWhereHas('traumatograma');
            });
        }
        if ($buscar = trim((string) $request->get('q'))) {
            $q->where(fn ($w) => $w->where('nombres', 'like', "%$buscar%")
                ->orWhere('apellidos', 'like', "%$buscar%")->orWhere('documento', 'like', "%$buscar%"));
        }

        return view('traumatograma.index', [
            'pacientes' => $q->orderBy('apellidos')->paginate(12)->withQueryString(),
            'buscar' => $buscar ?? '',
        ]);
    }

    public function edit(Paciente $paciente)
    {
        abort_unless($paciente->empresa_id === $this->empresaId(), 403);
        $trauma = Traumatograma::firstOrNew(['paciente_id' => $paciente->id]);

        return view('traumatograma.edit', [
            'paciente' => $paciente,
            'trauma' => $trauma,
            'tipos' => Traumatograma::TIPOS,
            'lesiones' => $trauma->lesiones ?? [],
        ]);
    }

    public function update(Request $request, Paciente $paciente)
    {
        abort_unless($paciente->empresa_id === $this->empresaId(), 403);

        $data = $request->validate([
            'lesiones' => ['nullable', 'string'],
            'notas' => ['nullable', 'string'],
        ]);

        $lesiones = json_decode($data['lesiones'] ?? '[]', true);
        if (! is_array($lesiones)) {
            $lesiones = [];
        }

        $lesiones = array_values(array_filter(array_map(function ($l) {
            if (! is_array($l)) {
                return null;
            }

            return [
                'vista' => in_array($l['vista'] ?? '', ['frente', 'espalda']) ? $l['vista'] : 'frente',
                'x' => round((float) ($l['x'] ?? 0), 2),
                'y' => round((float) ($l['y'] ?? 0), 2),
                'tipo' => array_key_exists($l['tipo'] ?? '', Traumatograma::TIPOS) ? $l['tipo'] : 'contusion',
                'descripcion' => mb_substr((string) ($l['descripcion'] ?? ''), 0, 160),
            ];
        }, $lesiones)));

        Traumatograma::updateOrCreate(
            ['paciente_id' => $paciente->id],
            [
                'empresa_id' => $this->empresaId(),
                'user_id' => auth()->id(),
                'lesiones' => $lesiones,
                'notas' => $data['notas'] ?? null,
            ]
        );

        return redirect()->route('traumatograma.edit', $paciente)->with('ok', 'Mapa de lesiones guardado.');
    }
}
