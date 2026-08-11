<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Servicio;
use Illuminate\Http\Request;

class ServicioController extends Controller
{
    private function empresaId(): int
    {
        return (int) auth()->user()->empresa_id;
    }

    public function index()
    {
        $servicios = Servicio::with('especialidad')
            ->where('empresa_id', $this->empresaId())->orderBy('nombre')->get();

        $especialidades = auth()->user()->empresa?->especialidadesActivas()->get() ?? collect();

        return view('admin.servicios.index', compact('servicios', 'especialidades'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['empresa_id'] = $this->empresaId();
        Servicio::create($data);

        return back()->with('ok', 'Servicio agregado.');
    }

    public function update(Request $request, Servicio $servicio)
    {
        abort_unless($servicio->empresa_id === $this->empresaId(), 403);
        $servicio->update($this->validated($request));

        return back()->with('ok', 'Servicio actualizado.');
    }

    public function destroy(Servicio $servicio)
    {
        abort_unless($servicio->empresa_id === $this->empresaId(), 403);
        $servicio->delete();

        return back()->with('ok', 'Servicio eliminado.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:120'],
            'precio' => ['required', 'numeric', 'min:0'],
            'especialidad_id' => ['nullable', 'exists:especialidades,id'],
        ]);
    }
}
