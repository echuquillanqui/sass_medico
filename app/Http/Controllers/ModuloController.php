<?php

namespace App\Http\Controllers;

use App\Models\Especialidad;
use App\Models\Paciente;

class ModuloController extends Controller
{
    public function show(string $slug)
    {
        $especialidad = Especialidad::where('slug', $slug)->firstOrFail();
        $empresaId = auth()->user()->empresa_id;

        $pacientes = Paciente::where('empresa_id', $empresaId)
            ->where('especialidad_id', $especialidad->id)
            ->orderBy('apellidos')
            ->paginate(10);

        return view('modulos.show', compact('especialidad', 'pacientes'));
    }
}
