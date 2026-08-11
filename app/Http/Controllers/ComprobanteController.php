<?php

namespace App\Http\Controllers;

use App\Models\Comprobante;
use App\Support\Facturacion;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ComprobanteController extends Controller
{
    private function empresaId(): int
    {
        return (int) auth()->user()->empresa_id;
    }

    public function index(Request $request)
    {
        $q = Comprobante::with('paciente')->where('empresa_id', $this->empresaId());
        if ($tipo = $request->get('tipo')) {
            $q->where('tipo', $tipo);
        }
        if ($estado = $request->get('estado')) {
            $q->where('estado', $estado);
        }

        $comprobantes = $q->orderByDesc('id')->paginate(20)->withQueryString();

        return view('comprobantes.index', [
            'comprobantes' => $comprobantes,
            'tipos' => Comprobante::TIPOS,
            'resumen' => Comprobante::where('empresa_id', $this->empresaId())
                ->selectRaw('estado, COUNT(*) n, SUM(total) total')->groupBy('estado')->get()->keyBy('estado'),
        ]);
    }

    public function pdf(Comprobante $comprobante)
    {
        abort_unless($comprobante->empresa_id === $this->empresaId(), 403);
        $comprobante->load('paciente', 'empresa.facturacionConfig');

        $ruc = $comprobante->empresa->facturacionConfig->ruc ?? '';
        $qr = $ruc ? $this->qrSvg($comprobante->qrContenido($ruc)) : null;

        $pdf = Pdf::loadView('comprobantes.pdf', ['c' => $comprobante, 'qr' => $qr])->setPaper('a5');

        return $pdf->stream('comprobante-'.$comprobante->numero.'.pdf');
    }

    /** Genera un código QR (SVG puro, sin dependencias de imagen) para el PDF. */
    private function qrSvg(string $contenido): string
    {
        $renderer = new \BaconQrCode\Renderer\ImageRenderer(
            new \BaconQrCode\Renderer\RendererStyle\RendererStyle(220, 1),
            new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
        );

        return (new \BaconQrCode\Writer($renderer))->writeString($contenido);
    }

    /** Descarga el XML firmado enviado a SUNAT. */
    public function xml(Comprobante $comprobante)
    {
        abort_unless($comprobante->empresa_id === $this->empresaId(), 403);
        abort_unless($comprobante->xml_path && Storage::disk('local')->exists($comprobante->xml_path), 404, 'XML no disponible.');

        return Storage::disk('local')->download($comprobante->xml_path, $comprobante->numero.'.xml');
    }

    /** Descarga el CDR (Constancia de Recepción · ZIP) devuelto por SUNAT. */
    public function cdr(Comprobante $comprobante)
    {
        abort_unless($comprobante->empresa_id === $this->empresaId(), 403);
        abort_unless($comprobante->cdr_path && Storage::disk('local')->exists($comprobante->cdr_path), 404, 'CDR no disponible.');

        return Storage::disk('local')->download($comprobante->cdr_path, 'R-'.$comprobante->numero.'.zip');
    }

    public function emitir(Comprobante $comprobante)
    {
        abort_unless($comprobante->empresa_id === $this->empresaId(), 403);
        $config = Facturacion::config($this->empresaId());
        $res = Facturacion::emitir($config, $comprobante);

        return back()->with($res['ok'] ? 'ok' : 'error', $res['mensaje']);
    }

    public function notaCredito(Request $request, Comprobante $comprobante)
    {
        abort_unless($comprobante->empresa_id === $this->empresaId(), 403);
        abort_if(in_array($comprobante->tipo, ['nota_credito']), 400, 'No se puede crear una nota sobre otra nota.');

        $data = $request->validate([
            'tipo_nota' => ['required', 'string', 'max:2'],
            'motivo' => ['nullable', 'string', 'max:150'],
            'total' => ['nullable', 'numeric', 'min:0'],
        ]);

        $config = Facturacion::config($this->empresaId());
        $nota = Facturacion::crearNotaCredito($config, $comprobante, $data);
        $res = Facturacion::emitir($config, $nota);

        return back()->with('ok', 'Nota de crédito '.$nota->numero.' creada ('.$nota->estado.'). '.($res['mensaje'] ?? ''));
    }

    public function anular(Request $request, Comprobante $comprobante)
    {
        abort_unless($comprobante->empresa_id === $this->empresaId(), 403);
        $motivo = $request->input('motivo', 'Anulación');

        $config = Facturacion::config($this->empresaId());
        $res = Facturacion::anular($config, $comprobante, $motivo);

        return back()->with($res['ok'] ? 'ok' : 'error', $res['mensaje']);
    }

    /** Consulta a SUNAT el ticket de una comunicación de baja de factura. */
    public function consultarBaja(Comprobante $comprobante)
    {
        abort_unless($comprobante->empresa_id === $this->empresaId(), 403);

        $config = Facturacion::config($this->empresaId());
        $res = Facturacion::consultarBaja($config, $comprobante);

        return back()->with($res['ok'] ? 'ok' : 'error', $res['mensaje']);
    }
}
