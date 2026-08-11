<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LabExamen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class LabExamenController extends Controller
{
    private function empresaId(): int
    {
        return (int) auth()->user()->empresa_id;
    }

    public function index()
    {
        $examenes = LabExamen::with('componentes')->where('empresa_id', $this->empresaId())
            ->whereNull('padre_id')->orderBy('categoria')->orderBy('nombre')->get();
        $examenesPrincipales = LabExamen::where('empresa_id', $this->empresaId())
            ->whereNull('padre_id')->orderBy('nombre')->get();

        return view('admin.lab_examenes.index', compact('examenes', 'examenesPrincipales'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $componentes = $data['componentes'] ?? [];
        unset($data['componentes'], $data['modo']);
        $data['empresa_id'] = $this->empresaId();

        DB::transaction(function () use ($data, $componentes) {
            $examen = LabExamen::create($data);

            foreach ($componentes as $componente) {
                $examen->componentes()->create([
                    ...$componente,
                    'empresa_id' => $this->empresaId(),
                    'categoria' => $componente['categoria'] ?? $examen->categoria,
                    'precio' => $componente['precio'] ?? 0,
                ]);
            }
        });

        $mensaje = count($componentes)
            ? 'Examen agrupado y sus componentes agregados al catálogo.'
            : 'Examen agregado al catálogo.';

        return back()->with('ok', $mensaje);
    }

    public function update(Request $request, LabExamen $examen)
    {
        abort_unless($examen->empresa_id === $this->empresaId(), 403);

        $data = $this->validated($request);
        $componentes = $data['componentes'] ?? null;
        unset($data['componentes'], $data['modo'], $data['editando_id']);

        DB::transaction(function () use ($examen, $data, $componentes) {
            $examen->update($data);

            if ($componentes === null) {
                return;
            }

            $idsConservados = [];
            foreach ($componentes as $componente) {
                $id = $componente['id'] ?? null;
                unset($componente['id']);
                $componente['empresa_id'] = $this->empresaId();
                $componente['categoria'] = $componente['categoria'] ?? $examen->categoria;
                $componente['precio'] = $componente['precio'] ?? 0;

                if ($id) {
                    $hijo = $examen->componentes()->whereKey($id)->firstOrFail();
                    $hijo->update($componente);
                    $idsConservados[] = $hijo->id;
                } else {
                    $idsConservados[] = $examen->componentes()->create($componente)->id;
                }
            }

            $examen->componentes()->whereNotIn('id', $idsConservados)->delete();
        });

        return back()->with('ok', 'Examen agrupado actualizado.');
    }

    public function destroy(LabExamen $examen)
    {
        abort_unless($examen->empresa_id === $this->empresaId(), 403);
        DB::transaction(function () use ($examen) {
            $examen->componentes()->delete();
            $examen->delete();
        });

        return back()->with('ok', 'Examen eliminado.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:120'],
            'padre_id' => [
                'nullable',
                'integer',
                Rule::exists('lab_examenes', 'id')->where(fn ($query) => $query
                    ->where('empresa_id', $this->empresaId())->whereNull('padre_id')),
            ],
            'categoria' => ['nullable', 'string', 'max:60'],
            'unidad' => ['nullable', 'string', 'max:30'],
            'valor_referencia' => ['nullable', 'string', 'max:60'],
            'precio' => ['nullable', 'numeric', 'min:0'],
            'modo' => ['nullable', Rule::in(['grupo'])],
            'editando_id' => ['nullable', 'integer'],
            'componentes' => ['exclude_unless:modo,grupo', 'required_if:modo,grupo', 'array', 'min:1'],
            'componentes.*.id' => ['nullable', 'integer'],
            'componentes.*.nombre' => ['required', 'string', 'max:120'],
            'componentes.*.categoria' => ['nullable', 'string', 'max:60'],
            'componentes.*.unidad' => ['nullable', 'string', 'max:30'],
            'componentes.*.valor_referencia' => ['nullable', 'string', 'max:60'],
            'componentes.*.precio' => ['nullable', 'numeric', 'min:0'],
        ]);
    }
}
