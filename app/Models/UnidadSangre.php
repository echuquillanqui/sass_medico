<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnidadSangre extends Model
{
    protected $table = 'unidades_sangre';

    protected $fillable = [
        'empresa_id', 'donante_id', 'codigo', 'grupo', 'volumen',
        'fecha_extraccion', 'fecha_vencimiento', 'estado',
    ];

    protected $casts = ['fecha_extraccion' => 'date', 'fecha_vencimiento' => 'date'];

    public function empresa(): BelongsTo { return $this->belongsTo(Empresa::class); }
    public function donante(): BelongsTo { return $this->belongsTo(Donante::class); }

    public function getVencidaAttribute(): bool
    {
        return $this->fecha_vencimiento && $this->fecha_vencimiento->isPast();
    }
}
