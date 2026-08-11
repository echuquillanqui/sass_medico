<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Triaje extends Model
{
    protected $table = 'triajes';

    protected $fillable = [
        'empresa_id', 'paciente_id', 'user_id', 'medico_id', 'nivel', 'motivo',
        'presion_arterial', 'frecuencia_cardiaca', 'frecuencia_respiratoria',
        'temperatura', 'saturacion', 'dolor', 'estado', 'hora_llegada', 'hora_atencion', 'observaciones',
    ];

    protected $casts = [
        'hora_llegada' => 'datetime',
        'hora_atencion' => 'datetime',
    ];

    // Clasificación Manchester
    public const NIVELES = [
        1 => ['label' => 'Resucitación', 'color' => '#ef4444', 'nombre' => 'Rojo', 'espera' => 'Inmediato'],
        2 => ['label' => 'Emergencia', 'color' => '#f97316', 'nombre' => 'Naranja', 'espera' => '10 min'],
        3 => ['label' => 'Urgente', 'color' => '#eab308', 'nombre' => 'Amarillo', 'espera' => '60 min'],
        4 => ['label' => 'Estándar', 'color' => '#22c55e', 'nombre' => 'Verde', 'espera' => '120 min'],
        5 => ['label' => 'No urgente', 'color' => '#3b82f6', 'nombre' => 'Azul', 'espera' => '240 min'],
    ];

    public function paciente(): BelongsTo { return $this->belongsTo(Paciente::class); }
    public function medico(): BelongsTo { return $this->belongsTo(User::class, 'medico_id'); }
    public function enfermero(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }

    public function getNivelInfoAttribute(): array
    {
        return self::NIVELES[$this->nivel] ?? self::NIVELES[5];
    }
}
