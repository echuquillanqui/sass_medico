<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\TenantData;
use Illuminate\Http\Request;

class MantenimientoController extends Controller
{
    private function empresa()
    {
        $empresa = auth()->user()->empresa;
        abort_unless($empresa, 403);

        return $empresa;
    }

    public function index()
    {
        $empresa = $this->empresa();
        $resumen = TenantData::resumen((int) $empresa->id);

        return view('admin.mantenimiento.index', [
            'empresa' => $empresa,
            'resumen' => $resumen,
            'total' => array_sum($resumen),
        ]);
    }

    /** Descarga un archivo de copia de seguridad (JSON) con los datos de la empresa. */
    public function backup()
    {
        $empresa = $this->empresa();
        $data = TenantData::export((int) $empresa->id);
        // Incluye la configuración de la empresa como referencia (no se restaura la identidad).
        $data['empresa'] = $empresa->only([
            'nombre', 'ruc', 'email', 'telefono', 'direccion', 'moneda',
            'separador_decimal', 'separador_miles', 'decimales', 'moneda_posicion', 'color_primario',
        ]);

        $nombre = 'backup-'.($empresa->slug ?: 'empresa').'-'.now()->format('Y_m_d_His').'.json';
        $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        return response($json, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="'.$nombre.'"',
        ]);
    }

    /** Restaura una copia de seguridad previamente descargada de ESTA empresa. */
    public function restore(Request $request)
    {
        $empresa = $this->empresa();
        $request->validate([
            'archivo' => ['required', 'file', 'mimetypes:application/json,text/plain', 'max:51200'],
            'confirmar' => ['accepted'],
        ]);

        $contenido = file_get_contents($request->file('archivo')->getRealPath());
        $data = json_decode($contenido, true);

        if (! is_array($data) || ($data['meta']['tipo'] ?? null) !== 'backup-empresa' || ! isset($data['tablas'])) {
            return back()->with('error', 'El archivo no es una copia de seguridad válida del sistema.');
        }

        try {
            $conteo = TenantData::import((int) $empresa->id, $data);
        } catch (\Throwable $e) {
            return back()->with('error', 'No se pudo restaurar la copia: '.$e->getMessage());
        }

        return back()->with('ok', 'Copia restaurada correctamente. Registros restaurados: '.array_sum($conteo).'.');
    }

    /** Reinicia (borra) todos los datos operativos de la empresa. Conserva usuarios y configuración. */
    public function reset(Request $request)
    {
        $empresa = $this->empresa();
        $request->validate(['confirmacion' => ['required', 'string']]);

        if (trim($request->input('confirmacion')) !== trim($empresa->nombre)) {
            return back()->with('error', 'La confirmación no coincide con el nombre de la empresa. No se realizó ningún cambio.');
        }

        TenantData::reset((int) $empresa->id);

        return back()->with('ok', 'El sistema fue reiniciado. La empresa quedó lista como nueva (usuarios y configuración conservados).');
    }
}
