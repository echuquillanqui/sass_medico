<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hospitalizacion extends Model
{
    protected $table = 'hospitalizaciones';

    protected $fillable = [
        'empresa_id', 'paciente_id', 'cama_id', 'medico_id', 'especialidad_id',
        'fecha_ingreso', 'fecha_alta', 'estado', 'motivo_ingreso',
        'diagnostico_ingreso', 'resumen_alta',
    ];

    protected $casts = [
        'fecha_ingreso' => 'datetime',
        'fecha_alta' => 'datetime',
    ];

    public function empresa(): BelongsTo { return $this->belongsTo(Empresa::class); }
    public function paciente(): BelongsTo { return $this->belongsTo(Paciente::class); }
    public function cama(): BelongsTo { return $this->belongsTo(Cama::class); }
    public function medico(): BelongsTo { return $this->belongsTo(User::class, 'medico_id'); }
    public function especialidad(): BelongsTo { return $this->belongsTo(Especialidad::class); }
    public function evoluciones(): HasMany { return $this->hasMany(Evolucion::class); }

    public function getDiasEstanciaAttribute(): int
    {
        $fin = $this->fecha_alta ?? now();
        return (int) $this->fecha_ingreso->diffInDays($fin) + 1;
    }
}
