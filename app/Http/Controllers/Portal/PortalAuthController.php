<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PortalAuthController extends Controller
{
    public function show()
    {
        if (Auth::guard('paciente')->check()) {
            return redirect()->route('portal.dashboard');
        }
        return view('portal.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $paciente = \App\Models\Paciente::where('email', $credentials['email'])->first();

        if (! Auth::guard('paciente')->attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Credenciales incorrectas o portal no habilitado.',
            ]);
        }

        if (! $paciente || ! $paciente->acceso_portal) {
            Auth::guard('paciente')->logout();
            throw ValidationException::withMessages([
                'email' => 'Tu acceso al portal no está habilitado. Contacta a tu clínica.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->route('portal.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('paciente')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('portal.login');
    }
}
