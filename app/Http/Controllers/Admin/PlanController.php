<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PlanController extends Controller
{
    public function index()
    {
        return view('admin.planes.index', [
            'planes' => Plan::withCount('empresas')->orderBy('orden')->orderBy('precio')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = $this->slugUnico($data['nombre']);
        Plan::create($data);

        return back()->with('ok', 'Plan creado.');
    }

    public function update(Request $request, Plan $plan)
    {
        $plan->update($this->validated($request));

        return back()->with('ok', 'Plan actualizado.');
    }

    public function destroy(Plan $plan)
    {
        if ($plan->empresas()->exists()) {
            return back()->with('error', 'No se puede eliminar: hay empresas con este plan.');
        }
        $plan->delete();

        return back()->with('ok', 'Plan eliminado.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:80'],
            'precio' => ['required', 'numeric', 'min:0', 'max:99999'],
            'ciclo' => ['required', 'in:mensual,anual'],
            'descripcion' => ['nullable', 'string', 'max:160'],
            'limite_especialidades' => ['nullable', 'integer', 'min:0', 'max:100'],
            'limite_usuarios' => ['nullable', 'integer', 'min:0', 'max:1000'],
            'destacado' => ['nullable', 'boolean'],
            'activo' => ['nullable', 'boolean'],
            'orden' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);
    }

    private function slugUnico(string $nombre): string
    {
        $base = Str::slug($nombre) ?: 'plan';
        $slug = $base;
        $i = 2;
        while (Plan::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
