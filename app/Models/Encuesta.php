<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Encuesta extends Model
{
    protected $table = 'encuestas';

    protected $fillable = [
        'empresa_id', 'paciente_id', 'cita_id', 'puntuacion', 'comentario',
    ];

    public function paciente(): BelongsTo { return $this->belongsTo(Paciente::class); }
    public function cita(): BelongsTo { return $this->belongsTo(Cita::class); }
}
