<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Especialidad;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EmpresaController extends Controller
{
    public function index()
    {
        $empresas = Empresa::withCount(['usuarios', 'pacientes'])
            ->with('especialidadesActivas')->latest()->paginate(10);

        return view('admin.empresas.index', compact('empresas'));
    }

    public function create()
    {
        return view('admin.empresas.form', [
            'empresa' => new Empresa(),
            'especialidades' => Especialidad::where('activo', true)->orderBy('nombre')->get(),
            'planes' => Plan::where('activo', true)->orderBy('orden')->orderBy('precio')->get(),
            'asignadas' => [],
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        // Datos del usuario administrador de la empresa
        $admin = $request->validate([
            'admin_nombre' => ['required', 'string', 'max:120'],
            'admin_email' => ['required', 'email', 'max:120', 'unique:users,email'],
            'admin_password' => ['required', 'string', 'min:6'],
        ]);

        $data['slug'] = Str::slug($data['nombre']).'-'.Str::lower(Str::random(4));
        $empresa = Empresa::create($data);
        $this->syncEspecialidades($empresa, $request);

        User::create([
            'empresa_id' => $empresa->id,
            'name' => $admin['admin_nombre'],
            'email' => $admin['admin_email'],
            'password' => Hash::make($admin['admin_password']),
            'role' => 'admin',
            'activo' => true,
        ]);

        return redirect()->route('admin.empresas.index')
            ->with('ok', 'Empresa creada con su administrador ('.$admin['admin_email'].').');
    }

    public function edit(Empresa $empresa)
    {
        return view('admin.empresas.form', [
            'empresa' => $empresa,
            'especialidades' => Especialidad::where('activo', true)->orderBy('nombre')->get(),
            'planes' => Plan::where('activo', true)->orderBy('orden')->orderBy('precio')->get(),
            'asignadas' => $empresa->especialidades()->pluck('especialidades.id')->all(),
        ]);
    }

    public function update(Request $request, Empresa $empresa)
    {
        $empresa->update($this->validated($request));
        $this->syncEspecialidades($empresa, $request);

        return redirect()->route('admin.empresas.index')->with('ok', 'Empresa actualizada.');
    }

    public function destroy(Empresa $empresa)
    {
        $empresa->delete();
        return redirect()->route('admin.empresas.index')->with('ok', 'Empresa eliminada.');
    }

    private function syncEspecialidades(Empresa $empresa, Request $request): void
    {
        $ids = collect($request->input('especialidades', []));
        $sync = $ids->mapWithKeys(fn ($id) => [$id => ['activo' => true]])->all();
        $empresa->especialidades()->sync($sync);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'ruc' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:120'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'direccion' => ['nullable', 'string', 'max:200'],
            'plan_id' => ['nullable', 'exists:planes,id'],
            'activo' => ['nullable', 'boolean'],
        ]);
    }
}
