<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Auditoria;
use Illuminate\Http\Request;

class AuditoriaController extends Controller
{
    public function index(Request $request)
    {
        $empresaId = (int) auth()->user()->empresa_id;

        $registros = Auditoria::with('user')
            ->where('empresa_id', $empresaId)
            ->when($request->modelo, fn ($q) => $q->where('modelo', $request->modelo))
            ->when($request->accion, fn ($q) => $q->where('accion', $request->accion))
            ->orderByDesc('id')
            ->paginate(20)->withQueryString();

        $modelos = Auditoria::where('empresa_id', $empresaId)->distinct()->pluck('modelo');

        return view('admin.auditoria.index', compact('registros', 'modelos'));
    }
}
