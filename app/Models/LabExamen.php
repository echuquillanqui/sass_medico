<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LabExamen extends Model
{
    protected $table = 'lab_examenes';

    protected $fillable = [
        'empresa_id', 'nombre', 'categoria', 'unidad', 'valor_referencia', 'precio', 'activo',
    ];

    protected $casts = ['precio' => 'decimal:2', 'activo' => 'boolean'];

    public function empresa(): BelongsTo { return $this->belongsTo(Empresa::class); }
}
