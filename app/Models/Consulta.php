<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Consulta extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'paciente_id', 'medico_id', 'especialidad_id', 'cita_id',
        'fecha', 'motivo', 'diagnostico', 'tratamiento', 'peso', 'talla',
        'presion_arterial', 'frecuencia_cardiaca', 'temperatura',
        'datos_especialidad', 'observaciones',
    ];

    protected $casts = [
        'fecha' => 'date',
        'datos_especialidad' => 'array',
    ];

    public function paciente(): BelongsTo { return $this->belongsTo(Paciente::class); }
    public function medico(): BelongsTo { return $this->belongsTo(User::class, 'medico_id'); }
    public function especialidad(): BelongsTo { return $this->belongsTo(Especialidad::class); }
    public function recetaItems(): \Illuminate\Database\Eloquent\Relations\HasMany { return $this->hasMany(RecetaItem::class); }
    public function adjuntos(): \Illuminate\Database\Eloquent\Relations\HasMany { return $this->hasMany(Adjunto::class); }

    public function getImcAttribute(): ?float
    {
        if ($this->peso && $this->talla) {
            $m = $this->talla / 100;
            return round($this->peso / ($m * $m), 1);
        }
        return null;
    }
}
