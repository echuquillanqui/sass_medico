<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmpresaConfigController extends Controller
{
    public function edit()
    {
        $empresa = auth()->user()->empresa;
        abort_unless($empresa, 403);

        return view('admin.empresa.edit', compact('empresa'));
    }

    public function update(Request $request)
    {
        $empresa = auth()->user()->empresa;
        abort_unless($empresa, 403);

        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'ruc' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:120'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'direccion' => ['nullable', 'string', 'max:200'],
            'sitio_web' => ['nullable', 'string', 'max:120'],
            'color_primario' => ['nullable', 'string', 'max:9'],
            'moneda' => ['nullable', 'string', 'max:5'],
            'separador_decimal' => ['nullable', 'string', 'max:1'],
            'separador_miles' => ['nullable', 'string', 'max:1'],
            'decimales' => ['nullable', 'integer', 'min:0', 'max:4'],
            'moneda_posicion' => ['nullable', 'in:antes,despues'],
            'horario_inicio' => ['nullable', 'string', 'max:10'],
            'horario_fin' => ['nullable', 'string', 'max:10'],
            'dias_atencion' => ['nullable', 'string', 'max:60'],
        ]);

        // Sanea separadores a un conjunto permitido.
        $sepDec = in_array($data['separador_decimal'] ?? '.', ['.', ',']) ? $data['separador_decimal'] : '.';
        $sepMil = in_array($data['separador_miles'] ?? ',', ['.', ',', ' ', "'"]) ? $data['separador_miles'] : ',';
        if ($sepDec === $sepMil) {
            return back()->withInput()->with('error', 'El separador de decimales y el de miles no pueden ser iguales.');
        }
        $data['separador_decimal'] = $sepDec;
        $data['separador_miles'] = $sepMil;

        if ($request->hasFile('logo')) {
            $request->validate(['logo' => ['image', 'max:2048']]);
            $path = $request->file('logo')->store('logos', 'public');
            $data['logo'] = $path;
        }

        $empresa->update($data);

        return back()->with('ok', 'Configuración de la empresa actualizada.');
    }
}
