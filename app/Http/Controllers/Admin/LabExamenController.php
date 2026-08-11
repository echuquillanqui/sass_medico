<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LabExamen;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LabExamenController extends Controller
{
    private function empresaId(): int
    {
        return (int) auth()->user()->empresa_id;
    }

    public function index()
    {
        $examenes = LabExamen::with('padre')->where('empresa_id', $this->empresaId())
            ->orderByRaw('padre_id is not null')->orderBy('categoria')->orderBy('nombre')->get();
        $examenesPrincipales = LabExamen::where('empresa_id', $this->empresaId())
            ->whereNull('padre_id')->orderBy('nombre')->get();

        return view('admin.lab_examenes.index', compact('examenes', 'examenesPrincipales'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['empresa_id'] = $this->empresaId();
        LabExamen::create($data);

        return back()->with('ok', 'Examen agregado al catálogo.');
    }

    public function update(Request $request, LabExamen $examen)
    {
        abort_unless($examen->empresa_id === $this->empresaId(), 403);
        $examen->update($this->validated($request));

        return back()->with('ok', 'Examen actualizado.');
    }

    public function destroy(LabExamen $examen)
    {
        abort_unless($examen->empresa_id === $this->empresaId(), 403);
        $examen->delete();

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
        ]);
    }
}
