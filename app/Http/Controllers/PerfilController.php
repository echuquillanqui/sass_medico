<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PerfilController extends Controller
{
    public function edit()
    {
        return view('perfil.edit', ['usuario' => auth()->user()]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'telefono' => ['nullable', 'string', 'max:30'],
            'cmp' => ['nullable', 'string', 'max:30'],
            'titulo_profesional' => ['nullable', 'string', 'max:40'],
            'firma' => ['nullable', 'string'],
        ]);
        $user->update($data);

        return back()->with('ok', 'Perfil actualizado.');
    }

    public function password(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'actual' => ['required'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        if (! Hash::check($data['actual'], $user->password)) {
            throw ValidationException::withMessages(['actual' => 'La contraseña actual no es correcta.']);
        }

        $user->update(['password' => Hash::make($data['password'])]);

        return back()->with('ok', 'Contraseña actualizada.');
    }
}
