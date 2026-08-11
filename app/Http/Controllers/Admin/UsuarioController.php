<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    private function empresaId(): int
    {
        return (int) auth()->user()->empresa_id;
    }

    public function index()
    {
        $usuarios = User::where('empresa_id', $this->empresaId())
            ->with('especialidad')->orderBy('name')->paginate(10);

        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        return view('admin.usuarios.form', [
            'usuario' => new User(),
            'especialidades' => $this->especialidades(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:120', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'in:admin,medico,recepcion'],
            'especialidad_id' => ['nullable', 'exists:especialidades,id'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'cmp' => ['nullable', 'string', 'max:30'],
            'titulo_profesional' => ['nullable', 'string', 'max:40'],
        ]);
        $data['empresa_id'] = $this->empresaId();
        $data['password'] = Hash::make($data['password']);
        $data['activo'] = true;
        User::create($data);

        return redirect()->route('admin.usuarios.index')->with('ok', 'Usuario creado.');
    }

    public function edit(User $usuario)
    {
        abort_unless($usuario->empresa_id === $this->empresaId(), 403);
        return view('admin.usuarios.form', [
            'usuario' => $usuario,
            'especialidades' => $this->especialidades(),
        ]);
    }

    public function update(Request $request, User $usuario)
    {
        abort_unless($usuario->empresa_id === $this->empresaId(), 403);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:120', Rule::unique('users', 'email')->ignore($usuario->id)],
            'password' => ['nullable', 'string', 'min:6'],
            'role' => ['required', 'in:admin,medico,recepcion'],
            'especialidad_id' => ['nullable', 'exists:especialidades,id'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'cmp' => ['nullable', 'string', 'max:30'],
            'titulo_profesional' => ['nullable', 'string', 'max:40'],
            'activo' => ['nullable', 'boolean'],
        ]);
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }
        $usuario->update($data);

        return redirect()->route('admin.usuarios.index')->with('ok', 'Usuario actualizado.');
    }

    public function destroy(User $usuario)
    {
        abort_unless($usuario->empresa_id === $this->empresaId(), 403);
        $usuario->delete();

        return redirect()->route('admin.usuarios.index')->with('ok', 'Usuario eliminado.');
    }

    private function especialidades()
    {
        return auth()->user()->empresa?->especialidadesActivas()->get() ?? collect();
    }
}
