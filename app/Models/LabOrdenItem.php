<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LabOrdenItem extends Model
{
    protected $table = 'lab_orden_items';

    protected $fillable = [
        'lab_orden_id', 'lab_examen_id', 'nombre', 'unidad',
        'valor_referencia', 'resultado', 'fuera_rango', 'notas',
    ];

    protected $casts = ['fuera_rango' => 'boolean'];

    public function orden(): BelongsTo { return $this->belongsTo(LabOrden::class, 'lab_orden_id'); }
}
