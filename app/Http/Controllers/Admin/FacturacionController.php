<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Facturacion;
use Illuminate\Http\Request;

class FacturacionController extends Controller
{
    private function empresa()
    {
        $empresa = auth()->user()->empresa;
        abort_unless($empresa, 403);

        return $empresa;
    }

    public function configuracion()
    {
        $empresa = $this->empresa();
        $config = Facturacion::config((int) $empresa->id);

        return view('facturacion.configuracion', [
            'empresa' => $empresa,
            'config' => $config,
            'estado' => Facturacion::estado($config),
            'prueba' => session('prueba'),
        ]);
    }

    public function guardar(Request $request)
    {
        $empresa = $this->empresa();

        $data = $request->validate([
            'habilitada' => ['nullable', 'boolean'],
            'emitir_automatico' => ['nullable', 'boolean'],
            'driver' => ['required', 'in:ninguno,greenter'],
            'entorno' => ['required', 'in:beta,produccion'],
            'ruc' => ['nullable', 'digits:11'],
            'razon_social' => ['nullable', 'string', 'max:160'],
            'nombre_comercial' => ['nullable', 'string', 'max:160'],
            'direccion_fiscal' => ['nullable', 'string', 'max:200'],
            'ubigeo' => ['nullable', 'string', 'max:6'],
            'departamento' => ['nullable', 'string', 'max:60'],
            'provincia' => ['nullable', 'string', 'max:60'],
            'distrito' => ['nullable', 'string', 'max:60'],
            'sol_usuario' => ['nullable', 'string', 'max:60'],
            'sol_clave' => ['nullable', 'string', 'max:120'],
            'certificado_ruta' => ['nullable', 'string', 'max:255'],
            'serie_boleta' => ['nullable', 'string', 'max:4'],
            'serie_factura' => ['nullable', 'string', 'max:4'],
            'serie_nota' => ['nullable', 'string', 'max:4'],
            'serie_nota_boleta' => ['nullable', 'string', 'max:4'],
            'igv_porcentaje' => ['nullable', 'numeric', 'min:0', 'max:30'],
            'afectacion_igv' => ['required', 'in:10,20,30'],
        ]);

        $config = Facturacion::config((int) $empresa->id);
        $config->fill(array_merge($data, [
            'empresa_id' => $empresa->id,
            'habilitada' => (bool) ($data['habilitada'] ?? false),
            'emitir_automatico' => (bool) ($data['emitir_automatico'] ?? false),
        ]));

        // No sobrescribir la clave SOL si el campo llegó vacío.
        if (blank($data['sol_clave'] ?? null)) {
            $config->sol_clave = $config->getOriginal('sol_clave') ?? $config->sol_clave;
        }

        $config->save();

        return redirect()->route('admin.facturacion.configuracion')->with('ok', 'Configuración de facturación electrónica guardada.');
    }

    public function probar(Request $request)
    {
        $empresa = $this->empresa();

        // Guarda primero para probar con los datos actuales.
        $this->guardar($request);

        $config = Facturacion::config((int) $empresa->id);

        return redirect()->route('admin.facturacion.configuracion')->with('prueba', Facturacion::probar($config));
    }
}
