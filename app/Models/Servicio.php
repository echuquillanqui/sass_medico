<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Servicio extends Model
{
    protected $table = 'servicios';

    protected $fillable = [
        'empresa_id', 'especialidad_id', 'nombre', 'precio', 'activo',
    ];

    protected $casts = ['precio' => 'decimal:2', 'activo' => 'boolean'];

    public function especialidad(): BelongsTo { return $this->belongsTo(Especialidad::class); }
}
