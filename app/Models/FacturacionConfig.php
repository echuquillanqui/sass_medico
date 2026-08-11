<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacturacionConfig extends Model
{
    use Auditable;

    protected $table = 'facturacion_configs';

    protected $fillable = [
        'empresa_id', 'habilitada', 'emitir_automatico', 'driver', 'entorno',
        'ruc', 'razon_social', 'nombre_comercial', 'direccion_fiscal',
        'ubigeo', 'departamento', 'provincia', 'distrito',
        'sol_usuario', 'sol_clave', 'certificado_ruta',
        'serie_boleta', 'serie_factura', 'serie_nota', 'serie_nota_boleta',
        'correlativo_boleta', 'correlativo_factura', 'correlativo_nota', 'correlativo_nota_boleta',
        'igv_porcentaje', 'afectacion_igv', 'moneda',
    ];

    protected function casts(): array
    {
        return [
            'habilitada' => 'boolean',
            'emitir_automatico' => 'boolean',
            'sol_clave' => 'encrypted',   // se guarda cifrada en la BD
            'igv_porcentaje' => 'decimal:2',
        ];
    }

    public function empresa(): BelongsTo { return $this->belongsTo(Empresa::class); }

    /** ¿El certificado .pem existe en la ruta indicada? */
    public function certificadoExiste(): bool
    {
        return $this->certificado_ruta && is_file($this->certificado_ruta);
    }

    /** ¿Está lista para intentar emitir? */
    public function listaParaEmitir(): bool
    {
        return $this->habilitada
            && $this->driver !== 'ninguno'
            && $this->ruc
            && $this->sol_usuario
            && $this->certificadoExiste();
    }
}
