<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evolucion extends Model
{
    protected $table = 'evoluciones';

    protected $fillable = [
        'hospitalizacion_id', 'user_id', 'fecha', 'nota',
        'presion_arterial', 'frecuencia_cardiaca', 'temperatura', 'saturacion',
    ];

    protected $casts = ['fecha' => 'datetime'];

    public function hospitalizacion(): BelongsTo { return $this->belongsTo(Hospitalizacion::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
