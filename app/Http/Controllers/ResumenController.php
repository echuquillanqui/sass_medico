<?php

namespace App\Http\Controllers;

use App\Models\Resumen;
use App\Support\Facturacion;
use Illuminate\Http\Request;

/**
 * Resumen Diario de Boletas (RC): reporta a SUNAT en lote las boletas del día
 * (altas) y comunica la baja de boletas ya reportadas.
 */
class ResumenController extends Controller
{
    private function empresaId(): int
    {
        return (int) auth()->user()->empresa_id;
    }

    public function index()
    {
        $empresaId = $this->empresaId();

        return view('resumenes.index', [
            'pendientes' => Facturacion::pendientesResumen($empresaId),
            'resumenes' => Resumen::where('empresa_id', $empresaId)
                ->withCount('comprobantes')->orderByDesc('id')->paginate(15),
            'estados' => Resumen::ESTADOS,
        ]);
    }

    /** Genera el resumen de una fecha y lo envía a SUNAT. */
    public function generar(Request $request)
    {
        $data = $request->validate([
            'fecha' => ['required', 'date'],
        ]);

        $config = Facturacion::config($this->empresaId());
        $creado = Facturacion::crearResumen($config, $data['fecha']);

        if (! $creado['ok']) {
            return back()->with('error', $creado['mensaje']);
        }

        $envio = Facturacion::enviarResumen($config, $creado['resumen']);

        return back()->with($envio['ok'] ? 'ok' : 'error', $creado['mensaje'].' '.$envio['mensaje']);
    }

    /** Consulta a SUNAT el estado (ticket) de un resumen enviado. */
    public function consultar(Resumen $resumen)
    {
        abort_unless($resumen->empresa_id === $this->empresaId(), 403);

        $config = Facturacion::config($this->empresaId());
        $res = Facturacion::consultarResumen($config, $resumen);

        return back()->with($res['ok'] ? 'ok' : 'error', $res['mensaje']);
    }

    /** Reenvía un resumen que quedó pendiente por un error de envío (conserva su detalle). */
    public function reenviar(Resumen $resumen)
    {
        abort_unless($resumen->empresa_id === $this->empresaId(), 403);
        abort_unless($resumen->estado === 'pendiente', 400, 'Solo se pueden reenviar resúmenes pendientes.');

        $config = Facturacion::config($this->empresaId());
        $res = Facturacion::enviarResumen($config, $resumen);

        return back()->with($res['ok'] ? 'ok' : 'error', $res['mensaje']);
    }
}
