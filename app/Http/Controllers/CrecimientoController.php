<?php

namespace App\Http\Controllers;

use App\Models\Especialidad;
use App\Models\Paciente;
use Illuminate\Http\Request;

class CrecimientoController extends Controller
{
    private function empresaId(): int
    {
        return (int) auth()->user()->empresa_id;
    }

    /** Selector de pacientes pediátricos para ver su curva de crecimiento OMS. */
    public function index(Request $request)
    {
        $esp = Especialidad::where('slug', 'pediatria')->first();

        $q = Paciente::where('empresa_id', $this->empresaId());
        if ($esp) {
            $q->where('especialidad_id', $esp->id);
        }
        if ($buscar = trim((string) $request->get('q'))) {
            $q->where(function ($w) use ($buscar) {
                $w->where('nombres', 'like', "%$buscar%")
                  ->orWhere('apellidos', 'like', "%$buscar%")
                  ->orWhere('documento', 'like', "%$buscar%");
            });
        }

        $pacientes = $q->orderBy('apellidos')->paginate(12)->withQueryString();

        return view('crecimiento.index', [
            'pacientes' => $pacientes,
            'especialidad' => $esp,
            'buscar' => $buscar ?? '',
        ]);
    }
}
