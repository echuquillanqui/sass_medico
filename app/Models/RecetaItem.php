<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecetaItem extends Model
{
    protected $table = 'receta_items';

    protected $fillable = [
        'consulta_id', 'medicamento', 'presentacion',
        'dosis', 'frecuencia', 'duracion', 'indicaciones',
    ];

    public function consulta(): BelongsTo { return $this->belongsTo(Consulta::class); }
}
