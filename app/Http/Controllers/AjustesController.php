<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AjustesController extends Controller
{
    public function edit()
    {
        return view('ajustes.edit', ['usuario' => auth()->user()]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'tema' => ['required', 'in:auto,claro,oscuro'],
            'densidad' => ['required', 'in:comodo,compacto'],
            'items_por_pagina' => ['required', 'integer', 'in:10,15,25,50'],
            'notif_citas' => ['nullable', 'boolean'],
            'notif_pagos' => ['nullable', 'boolean'],
        ]);

        $user = $request->user();
        $user->update([
            'preferencias' => [
                'tema' => $data['tema'],
                'densidad' => $data['densidad'],
                'items_por_pagina' => (int) $data['items_por_pagina'],
                'notif_citas' => $request->boolean('notif_citas'),
                'notif_pagos' => $request->boolean('notif_pagos'),
            ],
        ]);

        return back()->with('ok', 'Preferencias guardadas.');
    }
}
