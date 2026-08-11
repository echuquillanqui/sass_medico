<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    public function index()
    {
        $empresaId = (int) auth()->user()->empresa_id;
        $notificaciones = Notificacion::where('empresa_id', $empresaId)
            ->orderByDesc('id')->paginate(20);

        // Marcar como leídas al ver la bandeja
        Notificacion::where('empresa_id', $empresaId)->where('leido', false)->update(['leido' => true]);

        return view('notificaciones.index', compact('notificaciones'));
    }

    public function marcarTodas()
    {
        Notificacion::where('empresa_id', (int) auth()->user()->empresa_id)
            ->update(['leido' => true]);

        return back()->with('ok', 'Notificaciones marcadas como leídas.');
    }
}
