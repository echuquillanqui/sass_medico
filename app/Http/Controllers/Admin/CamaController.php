<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cama;
use Illuminate\Http\Request;

class CamaController extends Controller
{
    private function empresaId(): int
    {
        return (int) auth()->user()->empresa_id;
    }

    public function index()
    {
        $camas = Cama::where('empresa_id', $this->empresaId())
            ->withCount(['hospitalizaciones as ocupada_count' => fn ($q) => $q->where('estado', 'activa')])
            ->orderBy('area')->orderBy('nombre')->get();

        return view('admin.camas.index', compact('camas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:60'],
            'area' => ['nullable', 'string', 'max:60'],
        ]);
        $data['empresa_id'] = $this->empresaId();
        Cama::create($data);

        return back()->with('ok', 'Cama agregada.');
    }

    public function destroy(Cama $cama)
    {
        abort_unless($cama->empresa_id === $this->empresaId(), 403);
        if ($cama->ocupada) {
            return back()->with('ok', 'No se puede eliminar: la cama está ocupada.');
        }
        $cama->delete();

        return back()->with('ok', 'Cama eliminada.');
    }
}
