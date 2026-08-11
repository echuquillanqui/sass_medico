<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Paciente;
use Illuminate\Http\Request;

class BuscadorController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q'));
        $empresaId = (int) auth()->user()->empresa_id;

        $pacientes = collect();
        $citas = collect();

        if (strlen($q) >= 2 && $empresaId) {
            $pacientes = Paciente::where('empresa_id', $empresaId)
                ->where(function ($x) use ($q) {
                    $x->where('nombres', 'like', "%{$q}%")
                        ->orWhere('apellidos', 'like', "%{$q}%")
                        ->orWhere('documento', 'like', "%{$q}%")
                        ->orWhere('telefono', 'like', "%{$q}%");
                })->limit(20)->get();

            $citas = Cita::with(['paciente', 'especialidad'])
                ->where('empresa_id', $empresaId)
                ->whereHas('paciente', fn ($p) => $p->where('nombres', 'like', "%{$q}%")->orWhere('apellidos', 'like', "%{$q}%"))
                ->orderByDesc('fecha')->limit(20)->get();
        }

        return view('buscador.index', compact('q', 'pacientes', 'citas'));
    }
}
