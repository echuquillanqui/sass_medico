<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Especialidad;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegistroController extends Controller
{
    public function show()
    {
        return view('auth.registro', [
            'especialidades' => Especialidad::where('activo', true)->orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'empresa' => ['required', 'string', 'max:150'],
            'ruc' => ['nullable', 'string', 'max:20'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'plan' => ['required', 'in:basico,profesional,enterprise'],
            'especialidades' => ['required', 'array', 'min:1'],
            'especialidades.*' => ['exists:especialidades,id'],
            'admin_nombre' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:120', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        DB::transaction(function () use ($data, $request) {
            $empresa = Empresa::create([
                'nombre' => $data['empresa'],
                'slug' => Str::slug($data['empresa']).'-'.Str::lower(Str::random(4)),
                'ruc' => $data['ruc'] ?? null,
                'telefono' => $data['telefono'] ?? null,
                'email' => $data['email'],
                'plan' => $data['plan'],
                'moneda' => 'S/',
                'activo' => true,
            ]);

            $sync = collect($data['especialidades'])->mapWithKeys(fn ($id) => [$id => ['activo' => true]])->all();
            $empresa->especialidades()->sync($sync);

            User::create([
                'empresa_id' => $empresa->id,
                'name' => $data['admin_nombre'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => 'admin',
                'activo' => true,
            ]);
        });

        return redirect()->route('login')->with('ok', '¡Tu clínica fue registrada! Ingresa con tu correo y contraseña.');
    }
}
