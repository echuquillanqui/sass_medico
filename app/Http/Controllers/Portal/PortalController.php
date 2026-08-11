<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PortalController extends Controller
{
    private function paciente()
    {
        return Auth::guard('paciente')->user();
    }

    public function dashboard()
    {
        $p = $this->paciente();
        $proximas = $p->citas()->with('especialidad', 'medico')
            ->whereDate('fecha', '>=', now()->toDateString())
            ->orderBy('fecha')->orderBy('hora')->get();

        // Cita atendida pendiente de calificar
        $porCalificar = $p->citas()->with('especialidad', 'medico')
            ->where('estado', 'atendida')
            ->whereDoesntHave('encuesta')
            ->latest('fecha')->first();

        return view('portal.dashboard', [
            'paciente' => $p,
            'proximas' => $proximas,
            'porCalificar' => $porCalificar,
            'empresa' => $p->empresa,
        ]);
    }

    public function historia()
    {
        $p = $this->paciente();
        $consultas = $p->consultas()->with('medico', 'especialidad')->orderByDesc('fecha')->get();

        return view('portal.historia', ['paciente' => $p, 'consultas' => $consultas]);
    }

    public function pagos()
    {
        $p = $this->paciente();
        $pagos = $p->pagos()->orderByDesc('fecha')->get();

        return view('portal.pagos', ['paciente' => $p, 'pagos' => $pagos, 'empresa' => $p->empresa]);
    }
}
