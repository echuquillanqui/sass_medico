<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Embarazo extends Model
{
    use Auditable;

    protected $table = 'embarazos';

    protected $fillable = [
        'empresa_id', 'paciente_id', 'user_id', 'fum', 'fpp',
        'gestas', 'partos', 'abortos', 'cesareas', 'grupo_sanguineo',
        'riesgo_alto', 'estado', 'antecedentes',
    ];

    protected function casts(): array
    {
        return [
            'fum' => 'date',
            'fpp' => 'date',
            'riesgo_alto' => 'boolean',
        ];
    }

    public function paciente(): BelongsTo { return $this->belongsTo(Paciente::class); }
    public function medico(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
    public function controles(): HasMany { return $this->hasMany(ControlPrenatal::class)->orderBy('fecha'); }

    /** Semanas de gestación a una fecha (por defecto hoy), calculadas desde la FUM. */
    public function semanasA($fecha = null): ?float
    {
        if (! $this->fum) {
            return null;
        }
        $ref = $fecha ? \Illuminate\Support\Carbon::parse($fecha) : now();
        $dias = $this->fum->diffInDays($ref);

        return round($dias / 7, 1);
    }

    public function getSemanasAttribute(): ?float
    {
        return $this->semanasA();
    }

    /** Fecha probable de parto: FUM + 280 días (regla de Naegele) si no se fijó manualmente. */
    public function getFppCalculadaAttribute(): ?\Illuminate\Support\Carbon
    {
        if ($this->fpp) {
            return $this->fpp;
        }

        return $this->fum ? $this->fum->copy()->addDays(280) : null;
    }
}
