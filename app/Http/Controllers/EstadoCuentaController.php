<?php

namespace App\Http\Controllers;

use App\Models\Pago;

class EstadoCuentaController extends Controller
{
    public function index()
    {
        $eid = (int) auth()->user()->empresa_id;

        $deudores = Pago::with('paciente')
            ->where('empresa_id', $eid)->where('estado', 'pendiente')
            ->selectRaw('paciente_id, SUM(monto) deuda, COUNT(*) items, MIN(fecha) desde')
            ->groupBy('paciente_id')
            ->orderByDesc('deuda')
            ->get();

        $totalDeuda = $deudores->sum('deuda');

        return view('pagos.estados', compact('deudores', 'totalDeuda'));
    }
}
