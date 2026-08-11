<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Especialidad;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class EspecialidadController extends Controller
{
    public function index()
    {
        $especialidades = Especialidad::withCount('empresas')->orderBy('nombre')->get();

        return view('admin.especialidades.index', compact('especialidades'));
    }

    public function create()
    {
        return view('admin.especialidades.form', ['especialidad' => new Especialidad(['icono' => 'fa-stethoscope', 'color' => '#7c3aed', 'activo' => true])]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = Str::slug($data['nombre']);
        Especialidad::create($data);

        return redirect()->route('admin.especialidades.index')->with('ok', 'Especialidad creada.');
    }

    public function edit(Especialidad $especialidad)
    {
        return view('admin.especialidades.form', compact('especialidad'));
    }

    public function update(Request $request, Especialidad $especialidad)
    {
        $especialidad->update($this->validated($request, $especialidad->id));

        return redirect()->route('admin.especialidades.index')->with('ok', 'Especialidad actualizada.');
    }

    public function destroy(Especialidad $especialidad)
    {
        if ($especialidad->empresas()->exists()) {
            return back()->with('ok', 'No se puede eliminar: hay empresas usando esta especialidad. Desactívala en su lugar.');
        }
        $especialidad->delete();

        return redirect()->route('admin.especialidades.index')->with('ok', 'Especialidad eliminada.');
    }

    private function validated(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:80', Rule::unique('especialidades', 'nombre')->ignore($id)],
            'icono' => ['required', 'string', 'max:40'],
            'color' => ['required', 'string', 'max:9'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'activo' => ['nullable', 'boolean'],
        ]);
    }
}
