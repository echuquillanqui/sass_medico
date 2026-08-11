<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comprobante extends Model
{
    use Auditable;

    protected $table = 'comprobantes';

    protected $fillable = [
        'empresa_id', 'pago_id', 'resumen_id', 'resumen_baja_id', 'paciente_id', 'tipo', 'ref_comprobante_id', 'tipo_nota', 'motivo',
        'serie', 'correlativo', 'cliente_tipo_doc', 'cliente_num_doc', 'cliente_nombre', 'moneda', 'afectacion',
        'gravado', 'exonerado', 'inafecto', 'igv', 'total', 'items', 'estado', 'baja_via_resumen', 'baja_pendiente',
        'sunat_ticket', 'sunat_respuesta', 'hash', 'xml_path', 'cdr_path', 'pdf_path', 'fecha_emision',
    ];

    // Catálogo 09 SUNAT (motivos de nota de crédito) — subconjunto usual.
    public const MOTIVOS_NOTA = [
        '01' => 'Anulación de la operación',
        '02' => 'Anulación por error en el RUC',
        '03' => 'Corrección por error en la descripción',
        '06' => 'Devolución total',
        '07' => 'Devolución por ítem',
        '10' => 'Otros conceptos',
    ];

    protected function casts(): array
    {
        return [
            'items' => 'array',
            'gravado' => 'decimal:2',
            'exonerado' => 'decimal:2',
            'inafecto' => 'decimal:2',
            'igv' => 'decimal:2',
            'total' => 'decimal:2',
            'baja_via_resumen' => 'boolean',
            'baja_pendiente' => 'boolean',
            'fecha_emision' => 'date',
        ];
    }

    public const TIPOS = ['boleta' => 'Boleta', 'factura' => 'Factura', 'nota_credito' => 'Nota de crédito'];

    // Catálogo 07 SUNAT (tipo de afectación del IGV) — subconjunto usual en salud.
    public const AFECTACIONES = [
        '10' => 'Gravado (IGV)',
        '20' => 'Exonerado (sin IGV)',
        '30' => 'Inafecto (sin IGV)',
    ];

    public function empresa(): BelongsTo { return $this->belongsTo(Empresa::class); }
    public function pago(): BelongsTo { return $this->belongsTo(Pago::class); }
    public function resumen(): BelongsTo { return $this->belongsTo(Resumen::class); }
    public function resumenBaja(): BelongsTo { return $this->belongsTo(Resumen::class, 'resumen_baja_id'); }
    public function paciente(): BelongsTo { return $this->belongsTo(Paciente::class); }
    public function referencia(): BelongsTo { return $this->belongsTo(Comprobante::class, 'ref_comprobante_id'); }
    public function notas(): \Illuminate\Database\Eloquent\Relations\HasMany { return $this->hasMany(Comprobante::class, 'ref_comprobante_id'); }

    public function getNumeroAttribute(): string
    {
        return $this->serie.'-'.str_pad((string) $this->correlativo, 8, '0', STR_PAD_LEFT);
    }

    /** Código SUNAT del tipo de documento (catálogo 01): 01=factura, 03=boleta, 07=NC. */
    public function tipoDocSunat(): string
    {
        return ['factura' => '01', 'boleta' => '03', 'nota_credito' => '07'][$this->tipo] ?? '03';
    }

    /**
     * Cadena para el código QR según SUNAT:
     * RUC | tipoDoc | serie | correlativo | IGV | total | fecha | tipoDocCliente | numDocCliente | hash
     */
    public function qrContenido(string $ruc): string
    {
        return implode('|', [
            $ruc,
            $this->tipoDocSunat(),
            $this->serie,
            $this->correlativo,
            number_format((float) $this->igv, 2, '.', ''),
            number_format((float) $this->total, 2, '.', ''),
            optional($this->fecha_emision)->format('Y-m-d'),
            $this->cliente_tipo_doc ?: '0',
            $this->cliente_num_doc ?: '-',
            $this->hash ?: '',
        ]);
    }
}
