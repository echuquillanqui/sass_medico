<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vacuna extends Model
{
    protected $table = 'vacunas';

    protected $fillable = [
        'empresa_id', 'paciente_id', 'user_id', 'nombre', 'dosis',
        'fecha_programada', 'fecha_aplicada', 'estado', 'lote', 'observaciones',
    ];

    protected $casts = [
        'fecha_programada' => 'date',
        'fecha_aplicada' => 'date',
    ];

    public function paciente(): BelongsTo { return $this->belongsTo(Paciente::class); }

    // Calendario base del esquema nacional (referencial)
    public const ESQUEMA = [
        'BCG' => 'Recién nacido',
        'Hepatitis B' => 'Recién nacido',
        'Pentavalente (1ra)' => '2 meses',
        'Pentavalente (2da)' => '4 meses',
        'Pentavalente (3ra)' => '6 meses',
        'Polio (1ra)' => '2 meses',
        'Polio (2da)' => '4 meses',
        'Rotavirus (1ra)' => '2 meses',
        'Neumococo (1ra)' => '2 meses',
        'Influenza' => '6 meses',
        'SPR (1ra)' => '12 meses',
        'Varicela' => '12 meses',
        'DPT (refuerzo)' => '18 meses',
    ];
}
