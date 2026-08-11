<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LabExamen;
use Illuminate\Http\Request;

class LabExamenController extends Controller
{
    private function empresaId(): int
    {
        return (int) auth()->user()->empresa_id;
    }

    public function index()
    {
        $examenes = LabExamen::where('empresa_id', $this->empresaId())
            ->orderBy('categoria')->orderBy('nombre')->get();

        return view('admin.lab_examenes.index', compact('examenes'));
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
            'categoria' => ['nullable', 'string', 'max:60'],
            'unidad' => ['nullable', 'string', 'max:30'],
            'valor_referencia' => ['nullable', 'string', 'max:60'],
            'precio' => ['nullable', 'numeric', 'min:0'],
        ]);
    }
}
