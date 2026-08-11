<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Resumen Diario de Boletas (RC). Cabecera del lote enviado a SUNAT; el detalle
 * son los comprobantes relacionados por comprobantes.resumen_id.
 */
class Resumen extends Model
{
    use Auditable;

    protected $table = 'resumenes';

    protected $fillable = [
        'empresa_id', 'fecha_generacion', 'fecha_resumen', 'correlativo', 'identificador',
        'estado', 'sunat_ticket', 'sunat_respuesta', 'xml_path', 'total_documentos', 'total_importe',
    ];

    protected function casts(): array
    {
        return [
            'fecha_generacion' => 'date',
            'fecha_resumen' => 'date',
            'total_importe' => 'decimal:2',
        ];
    }

    public const ESTADOS = [
        'pendiente' => 'Pendiente',
        'enviado' => 'Enviado (esperando SUNAT)',
        'aceptado' => 'Aceptado',
        'rechazado' => 'Rechazado',
    ];

    public function empresa(): BelongsTo { return $this->belongsTo(Empresa::class); }

    public function comprobantes(): HasMany { return $this->hasMany(Comprobante::class, 'resumen_id'); }
}
