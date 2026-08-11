<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HorarioMedico extends Model
{
    protected $table = 'horarios_medico';

    protected $fillable = [
        'empresa_id', 'user_id', 'dia_semana', 'hora_inicio', 'hora_fin', 'activo',
    ];

    protected $casts = ['activo' => 'boolean'];

    public const DIAS = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];

    public function medico(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }

    public function getDiaNombreAttribute(): string
    {
        return self::DIAS[$this->dia_semana] ?? '';
    }
}
