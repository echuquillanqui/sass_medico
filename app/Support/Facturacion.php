<?php

namespace App\Support;

use App\Models\Comprobante;
use App\Models\FacturacionConfig;
use App\Models\Resumen;

/**
 * Servicio de Facturación Electrónica (SUNAT · Perú · UBL 2.1).
 *
 * Diseñado con un concepto de "driver" para poder conectar la emisión real:
 *   - 'ninguno'  : no envía a SUNAT; el comprobante queda PENDIENTE (útil para
 *                  registrar el correlativo y emitir después).
 *   - 'greenter' : emisión real firmando el XML y enviándolo a SUNAT mediante la
 *                  librería Greenter (greenter/greenter). Requiere instalarla por
 *                  Composer y cargar un certificado digital .pem válido.
 *
 * Este servicio NO simula respuestas de SUNAT: si el driver o el certificado no
 * están listos, lo informa con claridad en "probar()".
 */
class Facturacion
{
    /** Config de la empresa (nueva si no existe). */
    public static function config(int $empresaId): FacturacionConfig
    {
        return FacturacionConfig::firstOrNew(['empresa_id' => $empresaId]);
    }

    /** ¿Está instalada la librería Greenter? */
    public static function greenterDisponible(): bool
    {
        return class_exists('Greenter\\See') || class_exists('Greenter\\Api');
    }

    /** Chips de estado para el encabezado de la vista. */
    public static function estado(FacturacionConfig $c): array
    {
        return [
            'habilitada' => (bool) $c->habilitada,
            'driver' => $c->driver ?: 'ninguno',
            'entorno' => $c->entorno ?: 'beta',
            'certificado' => $c->certificadoExiste(),
        ];
    }

    /**
     * Comprueba la preparación para emitir ante SUNAT. Devuelve una lista de
     * verificaciones [ok, titulo, detalle] y un veredicto general.
     */
    public static function probar(FacturacionConfig $c): array
    {
        $checks = [];

        $rucOk = (bool) preg_match('/^\d{11}$/', (string) $c->ruc);
        $checks[] = ['ok' => $rucOk, 'titulo' => 'RUC del emisor',
            'detalle' => $rucOk ? $c->ruc : 'Falta un RUC válido de 11 dígitos.'];

        $credOk = filled($c->sol_usuario) && filled($c->sol_clave);
        $checks[] = ['ok' => $credOk, 'titulo' => 'Credenciales Clave SOL',
            'detalle' => $credOk ? 'Usuario y clave cargados.' : 'Falta usuario y/o clave SOL.'];

        $certOk = $c->certificadoExiste();
        $checks[] = ['ok' => $certOk, 'titulo' => 'Certificado digital (.pem)',
            'detalle' => $certOk ? $c->certificado_ruta : 'No se encontró el certificado en la ruta indicada.'];

        $driverOk = $c->driver !== 'ninguno';
        $checks[] = ['ok' => $driverOk, 'titulo' => 'Driver de emisión',
            'detalle' => $driverOk ? 'Driver: '.$c->driver : 'Driver en "Ninguno": los comprobantes quedan pendientes.'];

        if ($c->driver === 'greenter') {
            $lib = self::greenterDisponible();
            $checks[] = ['ok' => $lib, 'titulo' => 'Librería Greenter',
                'detalle' => $lib ? 'Instalada.' : 'Instálala con: composer require greenter/greenter'];
        }

        $checks[] = ['ok' => true, 'titulo' => 'Entorno SUNAT',
            'detalle' => $c->entorno === 'produccion' ? 'Producción' : 'Beta (homologación / pruebas)'];

        $listo = collect($checks)->every(fn ($x) => $x['ok']) && $driverOk;

        return [
            'checks' => $checks,
            'listo' => $listo,
            'mensaje' => $listo
                ? 'Configuración lista. Puedes emitir un comprobante de prueba desde una venta.'
                : 'Revisa los puntos marcados en rojo antes de emitir ante SUNAT.',
        ];
    }

    /**
     * Descompone un total en sus bases imponibles según el tipo de afectación del
     * IGV (catálogo 07): 10=Gravado (el total incluye IGV), 20=Exonerado y
     * 30=Inafecto (sin IGV, el total es el valor de venta).
     *
     * @return array{gravado: float, exonerado: float, inafecto: float, igv: float}
     */
    public static function calcularImportes(FacturacionConfig $c, float $total, string $afectacion = '10'): array
    {
        if ($afectacion === '20') {
            return ['gravado' => 0.0, 'exonerado' => round($total, 2), 'inafecto' => 0.0, 'igv' => 0.0];
        }
        if ($afectacion === '30') {
            return ['gravado' => 0.0, 'exonerado' => 0.0, 'inafecto' => round($total, 2), 'igv' => 0.0];
        }

        // Gravado: el total incluye el IGV; se separa la base y el impuesto.
        $igvPct = (float) ($c->igv_porcentaje ?: 18);
        $base = round($total / (1 + $igvPct / 100), 2);

        return ['gravado' => $base, 'exonerado' => 0.0, 'inafecto' => 0.0, 'igv' => round($total - $base, 2)];
    }

    /**
     * Prepara un comprobante a partir de datos de una venta/pago. Reserva el
     * siguiente correlativo de la serie según el tipo. Queda en estado
     * "pendiente"; la emisión real la realiza emitir().
     */
    public static function crearComprobante(FacturacionConfig $c, array $data): Comprobante
    {
        $tipo = $data['tipo'] ?? 'boleta';
        $esFactura = $tipo === 'factura';
        $serie = $esFactura ? $c->serie_factura : $c->serie_boleta;
        $correl = ($esFactura ? $c->correlativo_factura : $c->correlativo_boleta) + 1;

        $total = (float) ($data['total'] ?? 0);
        $afect = $data['afectacion'] ?? ($c->afectacion_igv ?: '10');
        $imp = self::calcularImportes($c, $total, $afect);

        $comp = Comprobante::create([
            'empresa_id' => $c->empresa_id,
            'pago_id' => $data['pago_id'] ?? null,
            'paciente_id' => $data['paciente_id'] ?? null,
            'tipo' => $tipo,
            'serie' => $serie,
            'correlativo' => $correl,
            'cliente_tipo_doc' => $data['cliente_tipo_doc'] ?? ($esFactura ? '6' : '1'),
            'cliente_num_doc' => $data['cliente_num_doc'] ?? null,
            'cliente_nombre' => $data['cliente_nombre'] ?? 'Cliente varios',
            'moneda' => $c->moneda ?: 'PEN',
            'afectacion' => $afect,
            'gravado' => $imp['gravado'],
            'exonerado' => $imp['exonerado'],
            'inafecto' => $imp['inafecto'],
            'igv' => $imp['igv'],
            'total' => $total,
            'items' => $data['items'] ?? [],
            'estado' => 'pendiente',
            'fecha_emision' => now()->toDateString(),
        ]);

        // Avanza el correlativo de la serie.
        if ($esFactura) {
            $c->correlativo_factura = $correl;
        } else {
            $c->correlativo_boleta = $correl;
        }
        $c->save();

        return $comp;
    }

    /** Genera un comprobante a partir de un Pago y, si corresponde, lo emite. */
    public static function generarDesdePago(\App\Models\Pago $pago): ?Comprobante
    {
        $c = self::config((int) $pago->empresa_id);
        if (! $c->exists || ! $c->habilitada) {
            return null;
        }
        // Evita duplicar si el pago ya tiene comprobante.
        if (Comprobante::where('pago_id', $pago->id)->exists()) {
            return null;
        }

        $pac = $pago->paciente;
        $esRuc = $pac && $pac->tipo_documento === 'RUC';
        $comp = self::crearComprobante($c, [
            'pago_id' => $pago->id,
            'paciente_id' => $pago->paciente_id,
            'tipo' => $esRuc ? 'factura' : 'boleta',
            'total' => (float) $pago->monto,
            'cliente_tipo_doc' => $esRuc ? '6' : '1',
            'cliente_num_doc' => $pac->documento ?? null,
            'cliente_nombre' => $pac->nombre_completo ?? 'Cliente varios',
            'items' => [[
                'descripcion' => $pago->concepto,
                'cantidad' => 1,
                'total' => (float) $pago->monto,
            ]],
        ]);

        if ($c->emitir_automatico) {
            self::emitir($c, $comp);
        }

        return $comp;
    }

    /**
     * Crea una NOTA DE CRÉDITO que referencia a un comprobante original.
     * Por defecto anula el total; puede indicarse un monto parcial.
     */
    public static function crearNotaCredito(FacturacionConfig $c, Comprobante $orig, array $data): Comprobante
    {
        // La nota usa serie/correlativo propios según el tipo del documento que
        // modifica: boleta -> serie con prefijo "B"; factura -> prefijo "F".
        $esNotaDeBoleta = $orig->tipo === 'boleta';
        if ($esNotaDeBoleta) {
            $serieNota = $c->serie_nota_boleta ?: 'BC01';
            $correl = ($c->correlativo_nota_boleta ?? 0) + 1;
        } else {
            $serieNota = $c->serie_nota ?: 'FC01';
            $correl = ($c->correlativo_nota ?? 0) + 1;
        }

        $total = (float) ($data['total'] ?? $orig->total);
        $afect = $orig->afectacion ?: '10';
        $imp = self::calcularImportes($c, $total, $afect);

        $nota = Comprobante::create([
            'empresa_id' => $c->empresa_id,
            'pago_id' => $orig->pago_id,
            'paciente_id' => $orig->paciente_id,
            'tipo' => 'nota_credito',
            'ref_comprobante_id' => $orig->id,
            'tipo_nota' => $data['tipo_nota'] ?? '01',
            'motivo' => $data['motivo'] ?? (Comprobante::MOTIVOS_NOTA[$data['tipo_nota'] ?? '01'] ?? 'Anulación de la operación'),
            'serie' => $serieNota,
            'correlativo' => $correl,
            'cliente_tipo_doc' => $orig->cliente_tipo_doc,
            'cliente_num_doc' => $orig->cliente_num_doc,
            'cliente_nombre' => $orig->cliente_nombre,
            'moneda' => $orig->moneda,
            'afectacion' => $afect,
            'gravado' => $imp['gravado'],
            'exonerado' => $imp['exonerado'],
            'inafecto' => $imp['inafecto'],
            'igv' => $imp['igv'],
            'total' => $total,
            'items' => $orig->items,
            'estado' => 'pendiente',
            'fecha_emision' => now()->toDateString(),
        ]);

        if ($esNotaDeBoleta) {
            $c->correlativo_nota_boleta = $correl;
        } else {
            $c->correlativo_nota = $correl;
        }
        $c->save();

        return $nota;
    }

    /**
     * Anula un comprobante. En facturas aceptadas envía una COMUNICACIÓN DE BAJA
     * a SUNAT (Greenter Voided); en otros casos lo marca como anulado localmente.
     */
    public static function anular(FacturacionConfig $c, Comprobante $comp, string $motivo = 'Anulación'): array
    {
        // Baja electrónica solo para facturas aceptadas con Greenter listo.
        // El proceso es asíncrono: se guarda el ticket y NO se anula hasta que
        // consultarBaja() confirme la aceptación de SUNAT.
        if ($comp->tipo === 'factura' && $comp->estado === 'aceptado' && $c->listaParaEmitir() && self::greenterDisponible()) {
            if ($comp->baja_pendiente) {
                return ['ok' => false, 'mensaje' => 'Ya hay una comunicación de baja en proceso. Usa "Consultar baja".'];
            }
            try {
                $ticket = self::comunicarBajaGreenter($c, $comp, $motivo);
                $comp->update(['baja_pendiente' => true, 'motivo' => $motivo, 'sunat_ticket' => $ticket,
                    'sunat_respuesta' => 'Comunicación de baja enviada. Ticket '.$ticket.'. Pendiente de confirmación.']);

                return ['ok' => true, 'mensaje' => 'Comunicación de baja enviada (ticket '.$ticket.'). Usa "Consultar baja" para confirmar la anulación.'];
            } catch (\Throwable $e) {
                return ['ok' => false, 'mensaje' => 'No se pudo comunicar la baja: '.$e->getMessage()];
            }
        }

        // Boleta YA reportada en un resumen aceptado: su baja se comunica en un
        // Resumen Diario (catálogo 19, estado "3"), no con comunicación de baja.
        if ($comp->tipo === 'boleta' && $comp->estado === 'aceptado' && $comp->resumen_id) {
            $comp->update(['baja_via_resumen' => true, 'motivo' => $motivo,
                'sunat_respuesta' => 'Baja pendiente: se comunicará en el próximo Resumen Diario.']);

            return ['ok' => true, 'mensaje' => 'Boleta marcada para baja. Genera un Resumen Diario para comunicarla a SUNAT.'];
        }

        $comp->update(['estado' => 'anulado', 'sunat_respuesta' => 'Anulado: '.$motivo]);

        return ['ok' => true, 'mensaje' => 'Comprobante anulado.'];
    }

    /**
     * Consulta el ticket de una comunicación de baja de factura. Si SUNAT la
     * aceptó (código 0), recién ahí marca la factura como anulada.
     */
    public static function consultarBaja(FacturacionConfig $c, Comprobante $comp): array
    {
        if (! $comp->baja_pendiente || ! $comp->sunat_ticket) {
            return ['ok' => false, 'mensaje' => 'Esta factura no tiene una baja en proceso.'];
        }
        if (! $c->listaParaEmitir() || ! self::greenterDisponible()) {
            return ['ok' => false, 'mensaje' => 'La facturación no está lista para consultar a SUNAT.'];
        }

        try {
            $status = self::see($c)->getStatus($comp->sunat_ticket);
            $code = (string) $status->getCode();

            if ($status->isSuccess() && $code === '0') {
                $cdr = $status->getCdrResponse();
                $datos = [
                    'estado' => 'anulado',
                    'baja_pendiente' => false,
                    'sunat_respuesta' => $cdr ? $cdr->getDescription() : 'Baja aceptada por SUNAT.',
                ];

                if ($zip = $status->getCdrZip()) {
                    $dir = storage_path('app/facturacion/'.$comp->empresa_id.'/cdr');
                    if (! is_dir($dir)) { @mkdir($dir, 0775, true); }
                    $name = 'R-BAJA-'.$comp->serie.'-'.$comp->correlativo.'.zip';
                    @file_put_contents($dir.'/'.$name, $zip);
                    $datos['cdr_path'] = 'facturacion/'.$comp->empresa_id.'/cdr/'.$name;
                }

                $comp->update($datos);

                return ['ok' => true, 'mensaje' => 'Baja aceptada por SUNAT. La factura quedó anulada.'];
            }

            if ($code === '98') {
                return ['ok' => true, 'mensaje' => 'SUNAT aún procesa la baja. Intenta de nuevo en unos minutos.'];
            }

            // Rechazada: se libera la baja para poder reintentarla.
            $cdr = $status->getCdrResponse();
            $comp->update(['baja_pendiente' => false,
                'sunat_respuesta' => $cdr ? $cdr->getDescription() : 'Baja rechazada por SUNAT (código '.$code.').']);

            return ['ok' => false, 'mensaje' => 'SUNAT rechazó la baja. La factura sigue vigente; corrige y reintenta.'];
        } catch (\Throwable $e) {
            return ['ok' => false, 'mensaje' => 'No se pudo consultar la baja: '.$e->getMessage()];
        }
    }

    /**
     * Emite el comprobante ante SUNAT según el driver configurado.
     * Con driver 'ninguno' o Greenter no instalado, deja el comprobante
     * PENDIENTE (no inventa una respuesta de SUNAT).
     */
    public static function emitir(FacturacionConfig $c, Comprobante $comp): array
    {
        if (! $c->listaParaEmitir()) {
            return ['ok' => false, 'estado' => 'pendiente', 'mensaje' => 'La facturación no está lista (revisa "Probar conexión").'];
        }
        if ($c->driver !== 'greenter') {
            return ['ok' => false, 'estado' => 'pendiente', 'mensaje' => 'Driver en "Ninguno": el comprobante queda pendiente.'];
        }
        if (! self::greenterDisponible()) {
            return ['ok' => false, 'estado' => 'pendiente', 'mensaje' => 'Instala greenter/greenter para emitir ante SUNAT.'];
        }

        try {
            return self::emitirConGreenter($c, $comp);
        } catch (\Throwable $e) {
            $comp->update(['estado' => 'pendiente', 'sunat_respuesta' => 'Error: '.$e->getMessage()]);

            return ['ok' => false, 'estado' => 'pendiente', 'mensaje' => 'Error al emitir: '.$e->getMessage()];
        }
    }

    /**
     * Emisión real con Greenter (greenter/greenter). Construye el Invoice UBL 2.1,
     * lo firma con el certificado .pem y lo envía al endpoint de SUNAT (beta o
     * producción), guardando el XML firmado y la respuesta (CDR).
     */
    protected static function emitirConGreenter(FacturacionConfig $c, Comprobante $comp): array
    {
        $see = new \Greenter\See();
        $see->setCertificate(file_get_contents($c->certificado_ruta));
        $see->setService($c->entorno === 'produccion'
            ? \Greenter\Ws\Services\SunatEndpoints::FE_PRODUCCION
            : \Greenter\Ws\Services\SunatEndpoints::FE_BETA);
        $see->setClaveSOL($c->ruc, $c->sol_usuario, $c->sol_clave);

        $address = (new \Greenter\Model\Company\Address())
            ->setUbigueo($c->ubigeo)->setDepartamento($c->departamento)
            ->setProvincia($c->provincia)->setDistrito($c->distrito)
            ->setDireccion($c->direccion_fiscal);

        $company = (new \Greenter\Model\Company\Company())
            ->setRuc($c->ruc)->setRazonSocial($c->razon_social)
            ->setNombreComercial($c->nombre_comercial ?: $c->razon_social)
            ->setAddress($address);

        $client = (new \Greenter\Model\Client\Client())
            ->setTipoDoc($comp->cliente_tipo_doc ?: '1')
            ->setNumDoc($comp->cliente_num_doc ?: '00000000')
            ->setRznSocial($comp->cliente_nombre ?: 'Cliente varios');

        // Detalle según el tipo de afectación del IGV (10=gravado, 20=exonerado,
        // 30=inafecto). Se acumulan las bases por categoría para el encabezado.
        $afect = $comp->afectacion ?: '10';
        $igvPct = (float) ($c->igv_porcentaje ?: 18);
        $items = $comp->items ?: [['descripcion' => 'Servicio', 'cantidad' => 1, 'total' => (float) $comp->total]];

        $gravadoTot = 0.0; $exonTot = 0.0; $inafTot = 0.0; $igvTot = 0.0;
        $detalles = [];
        foreach ($items as $it) {
            $tot = (float) ($it['total'] ?? $comp->total);
            $cant = (float) ($it['cantidad'] ?? 1);
            if ($cant <= 0) { $cant = 1; }

            if ($afect === '10') {
                $base = round($tot / (1 + $igvPct / 100), 2);
                $igv = round($tot - $base, 2);
                $pct = $igvPct;
                $gravadoTot += $base; $igvTot += $igv;
            } else {
                $base = round($tot, 2);   // exonerado/inafecto: el total es el valor de venta
                $igv = 0.0;
                $pct = 0.0;
                if ($afect === '20') { $exonTot += $base; } else { $inafTot += $base; }
            }

            $detalles[] = (new \Greenter\Model\Sale\SaleDetail())
                ->setCodProducto('SERV')->setUnidad('NIU')
                ->setCantidad($cant)
                ->setDescripcion($it['descripcion'] ?? 'Servicio')
                ->setMtoBaseIgv($base)->setPorcentajeIgv($pct)->setIgv($igv)
                ->setTipAfeIgv($afect)->setTotalImpuestos($igv)
                ->setMtoValorVenta($base)
                ->setMtoValorUnitario(round($base / $cant, 2))
                ->setMtoPrecioUnitario(round($tot / $cant, 2));
        }

        $gravadoTot = round($gravadoTot, 2);
        $exonTot = round($exonTot, 2);
        $inafTot = round($inafTot, 2);
        $igvTot = round($igvTot, 2);
        $valorVenta = round($gravadoTot + $exonTot + $inafTot, 2);

        $legend = (new \Greenter\Model\Sale\Legend())->setCode('1000')
            ->setValue('SON '.number_format((float) $comp->total, 2).' SOLES');

        if ($comp->tipo === 'nota_credito') {
            $orig = $comp->referencia;
            $doc = (new \Greenter\Model\Sale\Note())
                ->setUblVersion('2.1')
                ->setTipoDoc('07')
                ->setSerie($comp->serie)
                ->setCorrelativo((string) $comp->correlativo)
                ->setFechaEmision(new \DateTime())
                ->setTipDocAfectado($orig && $orig->tipo === 'factura' ? '01' : '03')
                ->setNumDocfectado($orig ? $orig->serie.'-'.$orig->correlativo : '')
                ->setCodMotivo($comp->tipo_nota ?: '01')
                ->setDesMotivo($comp->motivo ?: 'Anulación de la operación')
                ->setTipoMoneda($comp->moneda ?: 'PEN')
                ->setCompany($company)->setClient($client)
                ->setMtoOperGravadas($gravadoTot)
                ->setMtoOperExoneradas($exonTot)
                ->setMtoOperInafectas($inafTot)
                ->setMtoIGV($igvTot)
                ->setTotalImpuestos($igvTot)
                ->setMtoImpVenta((float) $comp->total)
                ->setDetails($detalles)
                ->setLegends([$legend]);
        } else {
            $doc = (new \Greenter\Model\Sale\Invoice())
                ->setUblVersion('2.1')
                ->setTipoOperacion('0101')
                ->setTipoDoc($comp->tipo === 'factura' ? '01' : '03')
                ->setSerie($comp->serie)
                ->setCorrelativo((string) $comp->correlativo)
                ->setFechaEmision(new \DateTime())
                ->setTipoMoneda($comp->moneda ?: 'PEN')
                ->setCompany($company)->setClient($client)
                ->setMtoOperGravadas($gravadoTot)
                ->setMtoOperExoneradas($exonTot)
                ->setMtoOperInafectas($inafTot)
                ->setMtoIGV($igvTot)
                ->setTotalImpuestos($igvTot)
                ->setValorVenta($valorVenta)
                ->setSubTotal((float) $comp->total)
                ->setMtoImpVenta((float) $comp->total)
                ->setDetails($detalles)
                ->setLegends([$legend]);
        }

        $result = $see->send($doc);

        // Guarda el XML firmado y extrae el hash (DigestValue) para el QR.
        $dir = storage_path('app/facturacion/'.$comp->empresa_id);
        if (! is_dir($dir)) { @mkdir($dir, 0775, true); }
        $xmlName = $comp->serie.'-'.$comp->correlativo.'.xml';
        $xmlSigned = $see->getXmlSigned($doc) ?? '';
        @file_put_contents($dir.'/'.$xmlName, $xmlSigned);
        $hash = self::extraerHash($xmlSigned);

        if ($result->isSuccess()) {
            $cdr = $result->getCdrResponse();
            $code = (int) $cdr->getCode();
            $estado = $code === 0 ? 'aceptado' : 'rechazado';

            $datos = [
                'estado' => $estado,
                'sunat_respuesta' => $cdr->getDescription(),
                'xml_path' => 'facturacion/'.$comp->empresa_id.'/'.$xmlName,
                'hash' => $hash,
            ];

            // Guarda el CDR (ZIP) que devuelve SUNAT como constancia de recepción.
            if ($zip = $result->getCdrZip()) {
                $cdrDir = $dir.'/cdr';
                if (! is_dir($cdrDir)) { @mkdir($cdrDir, 0775, true); }
                $cdrName = 'R-'.$comp->serie.'-'.$comp->correlativo.'.zip';
                @file_put_contents($cdrDir.'/'.$cdrName, $zip);
                $datos['cdr_path'] = 'facturacion/'.$comp->empresa_id.'/cdr/'.$cdrName;
            }

            $comp->update($datos);

            return ['ok' => $estado === 'aceptado', 'estado' => $estado, 'mensaje' => $cdr->getDescription()];
        }

        // Aun rechazado, conserva el hash del XML firmado.
        if ($hash) { $comp->hash = $hash; }

        $comp->update(['estado' => 'rechazado', 'sunat_respuesta' => $result->getError()->getCode().': '.$result->getError()->getMessage()]);

        return ['ok' => false, 'estado' => 'rechazado', 'mensaje' => $result->getError()->getMessage()];
    }

    /** Extrae el hash (primer DigestValue) del XML firmado, para el código QR. */
    protected static function extraerHash(?string $xml): ?string
    {
        if (! $xml) {
            return null;
        }
        if (preg_match('/<ds:DigestValue>(.*?)<\/ds:DigestValue>/s', $xml, $m)) {
            return trim($m[1]);
        }
        if (preg_match('/<DigestValue>(.*?)<\/DigestValue>/s', $xml, $m)) {
            return trim($m[1]);
        }

        return null;
    }

    /** Comunicación de baja (Voided) de una factura ante SUNAT. Devuelve el ticket. */
    protected static function comunicarBajaGreenter(FacturacionConfig $c, Comprobante $comp, string $motivo): string
    {
        $see = new \Greenter\See();
        $see->setCertificate(file_get_contents($c->certificado_ruta));
        $see->setService($c->entorno === 'produccion'
            ? \Greenter\Ws\Services\SunatEndpoints::FE_PRODUCCION
            : \Greenter\Ws\Services\SunatEndpoints::FE_BETA);
        $see->setClaveSOL($c->ruc, $c->sol_usuario, $c->sol_clave);

        $company = (new \Greenter\Model\Company\Company())
            ->setRuc($c->ruc)->setRazonSocial($c->razon_social);

        $detail = (new \Greenter\Model\Voided\VoidedDetail())
            ->setTipoDoc('01')
            ->setSerie($comp->serie)
            ->setCorrelativo((string) $comp->correlativo)
            ->setDesMotivoBaja($motivo);

        $voided = (new \Greenter\Model\Voided\Voided())
            ->setCorrelativo('1')
            ->setFecGeneracion(new \DateTime($comp->fecha_emision))
            ->setFecComunicacion(new \DateTime())
            ->setCompany($company)
            ->setDetails([$detail]);

        $result = $see->send($voided);
        if ($result->isSuccess()) {
            return $result->getTicket();
        }

        throw new \RuntimeException($result->getError()->getMessage());
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  RESUMEN DIARIO DE BOLETAS (RC) — reporte de boletas a SUNAT en lote
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Instancia de Greenter\See configurada con el certificado, endpoint y
     * credenciales Clave SOL de la empresa.
     */
    protected static function see(FacturacionConfig $c): \Greenter\See
    {
        $see = new \Greenter\See();
        $see->setCertificate(file_get_contents($c->certificado_ruta));
        $see->setService($c->entorno === 'produccion'
            ? \Greenter\Ws\Services\SunatEndpoints::FE_PRODUCCION
            : \Greenter\Ws\Services\SunatEndpoints::FE_BETA);
        $see->setClaveSOL($c->ruc, $c->sol_usuario, $c->sol_clave);

        return $see;
    }

    /**
     * Boletas (y notas de crédito de boletas) que aún deben reportarse en un
     * resumen, separadas por rol y agrupadas por fecha de emisión.
     *
     * Devuelve una colección de filas [fecha, altas, bajas, documentos, importe]
     * lista para presentar y elegir qué día reportar.
     */
    public static function pendientesResumen(int $empresaId): \Illuminate\Support\Collection
    {
        $altas = self::altasPendientes($empresaId)->get();
        $bajas = self::bajasPendientes($empresaId)->get();

        $fechas = $altas->concat($bajas)
            ->map(fn ($c) => $c->fecha_emision->toDateString())
            ->unique()->sort()->values();

        return $fechas->map(function ($fecha) use ($altas, $bajas) {
            $a = $altas->filter(fn ($c) => $c->fecha_emision->toDateString() === $fecha);
            $b = $bajas->filter(fn ($c) => $c->fecha_emision->toDateString() === $fecha);

            return (object) [
                'fecha' => $fecha,
                'altas' => $a->count(),
                'bajas' => $b->count(),
                'documentos' => $a->count() + $b->count(),
                'importe' => (float) $a->sum('total'),
            ];
        });
    }

    /** Query de boletas/notas pendientes de ALTA (adicionar) sin resumen asignado. */
    protected static function altasPendientes(int $empresaId): \Illuminate\Database\Eloquent\Builder
    {
        return Comprobante::where('empresa_id', $empresaId)
            ->whereIn('tipo', ['boleta', 'nota_credito'])
            ->whereIn('estado', ['pendiente', 'rechazado'])
            ->where('baja_via_resumen', false)
            ->whereNull('resumen_id')
            ->where(function ($q) {
                // boletas directas o notas cuya referencia es una boleta
                $q->where('tipo', 'boleta')
                    ->orWhereHas('referencia', fn ($r) => $r->where('tipo', 'boleta'));
            });
    }

    /** Query de boletas pendientes de BAJA (comunicar anulación) sin resumen de baja. */
    protected static function bajasPendientes(int $empresaId): \Illuminate\Database\Eloquent\Builder
    {
        return Comprobante::where('empresa_id', $empresaId)
            ->where('tipo', 'boleta')
            ->where('baja_via_resumen', true)
            ->whereNull('resumen_baja_id');
    }

    /**
     * Crea la cabecera de un Resumen Diario para una fecha de emisión y le asocia
     * las boletas de esa fecha (altas y bajas pendientes). No envía a SUNAT.
     */
    public static function crearResumen(FacturacionConfig $c, string $fecha): array
    {
        $altas = self::altasPendientes((int) $c->empresa_id)
            ->whereDate('fecha_emision', $fecha)->get();
        $bajas = self::bajasPendientes((int) $c->empresa_id)
            ->whereDate('fecha_emision', $fecha)->get();

        if ($altas->isEmpty() && $bajas->isEmpty()) {
            return ['ok' => false, 'mensaje' => 'No hay boletas pendientes para la fecha '.$fecha.'.'];
        }

        $hoy = now()->toDateString();
        $correl = (int) (Resumen::where('empresa_id', $c->empresa_id)
            ->whereDate('fecha_resumen', $hoy)->max('correlativo') ?? 0) + 1;

        $resumen = Resumen::create([
            'empresa_id' => $c->empresa_id,
            'fecha_generacion' => $fecha,
            'fecha_resumen' => $hoy,
            'correlativo' => $correl,
            'identificador' => 'RC-'.str_replace('-', '', $hoy).'-'.$correl,
            'estado' => 'pendiente',
            'total_documentos' => $altas->count() + $bajas->count(),
            'total_importe' => (float) $altas->sum('total'),
        ]);

        // Enlaza el detalle: alta => resumen_id, baja => resumen_baja_id.
        Comprobante::whereIn('id', $altas->pluck('id'))->update(['resumen_id' => $resumen->id]);
        Comprobante::whereIn('id', $bajas->pluck('id'))->update(['resumen_baja_id' => $resumen->id]);

        return ['ok' => true, 'resumen' => $resumen, 'mensaje' => 'Resumen '.$resumen->identificador.' creado con '.$resumen->total_documentos.' documento(s).'];
    }

    /**
     * Envía el Resumen Diario a SUNAT. El proceso es asíncrono: SUNAT devuelve un
     * TICKET que luego debe consultarse con consultarResumen().
     */
    public static function enviarResumen(FacturacionConfig $c, Resumen $resumen): array
    {
        if (! $c->listaParaEmitir()) {
            return ['ok' => false, 'mensaje' => 'La facturación no está lista (revisa "Probar conexión").'];
        }
        if ($c->driver !== 'greenter' || ! self::greenterDisponible()) {
            return ['ok' => false, 'mensaje' => 'Instala y selecciona el driver Greenter para enviar resúmenes.'];
        }

        try {
            $see = self::see($c);

            $company = (new \Greenter\Model\Company\Company())
                ->setRuc($c->ruc)->setRazonSocial($c->razon_social)
                ->setNombreComercial($c->nombre_comercial ?: $c->razon_social);

            // Detalle = altas (resumen_id) + bajas (resumen_baja_id) de este resumen.
            $comprobantes = Comprobante::with('referencia')
                ->where(function ($q) use ($resumen) {
                    $q->where('resumen_id', $resumen->id)->orWhere('resumen_baja_id', $resumen->id);
                })->get();

            $details = [];
            foreach ($comprobantes as $comp) {
                $esBaja = (int) $comp->resumen_baja_id === (int) $resumen->id;
                $estado = $esBaja ? '3' : '1'; // catálogo 19: 1=adicionar, 3=anular
                $det = (new \Greenter\Model\Summary\SummaryDetail())
                    ->setTipoDoc($comp->tipo === 'nota_credito' ? '07' : '03')
                    ->setSerieNro($comp->serie.'-'.$comp->correlativo)
                    ->setEstado($estado)
                    ->setClienteTipo($comp->cliente_tipo_doc ?: '1')
                    ->setClienteNro($comp->cliente_num_doc ?: '00000000')
                    ->setTotal((float) $comp->total)
                    ->setMtoOperGravadas((float) $comp->gravado)
                    ->setMtoOperExoneradas((float) $comp->exonerado)
                    ->setMtoOperInafectas((float) $comp->inafecto)
                    ->setMtoIGV((float) $comp->igv);

                if ($comp->tipo === 'nota_credito' && $comp->referencia) {
                    $det->setDocReferencia((new \Greenter\Model\Sale\Document())
                        ->setTipoDoc('03')
                        ->setNroDoc($comp->referencia->serie.'-'.$comp->referencia->correlativo));
                }
                $details[] = $det;
            }

            $summary = (new \Greenter\Model\Summary\Summary())
                ->setCorrelativo((string) $resumen->correlativo)
                ->setFecGeneracion(new \DateTime($resumen->fecha_generacion->toDateString()))
                ->setFecResumen(new \DateTime($resumen->fecha_resumen->toDateString()))
                ->setCompany($company)
                ->setDetails($details);

            $result = $see->send($summary);

            // Guarda el XML firmado.
            $dir = storage_path('app/facturacion/'.$resumen->empresa_id.'/resumenes');
            if (! is_dir($dir)) { @mkdir($dir, 0775, true); }
            $xmlName = $resumen->identificador.'.xml';
            @file_put_contents($dir.'/'.$xmlName, $see->getXmlSigned($summary) ?? '');
            $xmlPath = 'facturacion/'.$resumen->empresa_id.'/resumenes/'.$xmlName;

            if ($result->isSuccess()) {
                $resumen->update([
                    'estado' => 'enviado',
                    'sunat_ticket' => $result->getTicket(),
                    'sunat_respuesta' => 'Enviado. Ticket '.$result->getTicket().'. Consulta el estado en SUNAT.',
                    'xml_path' => $xmlPath,
                ]);

                return ['ok' => true, 'mensaje' => 'Resumen enviado. Ticket '.$result->getTicket().'. Usa "Consultar" para obtener la respuesta de SUNAT.'];
            }

            $resumen->update(['estado' => 'rechazado', 'xml_path' => $xmlPath,
                'sunat_respuesta' => $result->getError()->getCode().': '.$result->getError()->getMessage()]);
            self::liberarDetalle($resumen);

            return ['ok' => false, 'mensaje' => 'SUNAT rechazó el envío: '.$result->getError()->getMessage()];
        } catch (\Throwable $e) {
            $resumen->update(['estado' => 'pendiente', 'sunat_respuesta' => 'Error: '.$e->getMessage()]);

            return ['ok' => false, 'mensaje' => 'Error al enviar el resumen: '.$e->getMessage()];
        }
    }

    /**
     * Consulta el estado de un resumen enviado usando su ticket. Si SUNAT lo
     * aceptó (código 0), marca las boletas como aceptadas o anuladas según su rol.
     */
    public static function consultarResumen(FacturacionConfig $c, Resumen $resumen): array
    {
        if ($resumen->estado !== 'enviado' || ! $resumen->sunat_ticket) {
            return ['ok' => false, 'mensaje' => 'El resumen no tiene un ticket pendiente de consulta.'];
        }
        if (! $c->listaParaEmitir() || ! self::greenterDisponible()) {
            return ['ok' => false, 'mensaje' => 'La facturación no está lista para consultar a SUNAT.'];
        }

        try {
            $status = self::see($c)->getStatus($resumen->sunat_ticket);
            $code = (string) $status->getCode();

            if ($status->isSuccess() && $code === '0') {
                $cdr = $status->getCdrResponse();
                $resumen->update(['estado' => 'aceptado',
                    'sunat_respuesta' => $cdr ? $cdr->getDescription() : 'Resumen aceptado por SUNAT.']);

                // Altas -> aceptadas ; Bajas -> anuladas.
                Comprobante::where('resumen_id', $resumen->id)->update(['estado' => 'aceptado']);
                Comprobante::where('resumen_baja_id', $resumen->id)
                    ->update(['estado' => 'anulado', 'baja_via_resumen' => false]);

                return ['ok' => true, 'mensaje' => 'Resumen aceptado por SUNAT.'];
            }

            if ($code === '98') {
                return ['ok' => true, 'mensaje' => 'SUNAT aún está procesando el resumen. Intenta de nuevo en unos minutos.'];
            }

            // Código 99 u otro error de proceso: rechazado.
            $cdr = $status->getCdrResponse();
            $resumen->update(['estado' => 'rechazado',
                'sunat_respuesta' => $cdr ? $cdr->getDescription() : 'Resumen rechazado por SUNAT (código '.$code.').']);
            self::liberarDetalle($resumen);

            return ['ok' => false, 'mensaje' => 'SUNAT rechazó el resumen. Corrige y vuelve a generarlo.'];
        } catch (\Throwable $e) {
            return ['ok' => false, 'mensaje' => 'No se pudo consultar el resumen: '.$e->getMessage()];
        }
    }

    /**
     * Libera el detalle de un resumen rechazado para que pueda reintentarse en un
     * nuevo resumen (quita los enlaces resumen_id / resumen_baja_id).
     */
    protected static function liberarDetalle(Resumen $resumen): void
    {
        Comprobante::where('resumen_id', $resumen->id)->update(['resumen_id' => null]);
        Comprobante::where('resumen_baja_id', $resumen->id)->update(['resumen_baja_id' => null]);
    }
}
