<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Paciente extends Authenticatable
{
    use HasFactory, Notifiable, Auditable;

    protected $fillable = [
        'empresa_id', 'especialidad_id', 'nombres', 'apellidos', 'tipo_documento',
        'documento', 'fecha_nacimiento', 'sexo', 'telefono', 'email', 'direccion',
        'grupo_sanguineo', 'alergias', 'antecedentes', 'activo',
        'password', 'acceso_portal',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'fecha_nacimiento' => 'date',
            'activo' => 'boolean',
            'acceso_portal' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function empresa(): BelongsTo { return $this->belongsTo(Empresa::class); }
    public function especialidad(): BelongsTo { return $this->belongsTo(Especialidad::class); }
    public function citas(): HasMany { return $this->hasMany(Cita::class); }
    public function consultas(): HasMany { return $this->hasMany(Consulta::class); }
    public function pagos(): HasMany { return $this->hasMany(Pago::class); }
    public function adjuntos(): HasMany { return $this->hasMany(Adjunto::class); }
    public function encuestas(): HasMany { return $this->hasMany(Encuesta::class); }
    public function vacunas(): HasMany { return $this->hasMany(Vacuna::class); }
    public function labOrdenes(): HasMany { return $this->hasMany(LabOrden::class); }
    public function hospitalizaciones(): HasMany { return $this->hasMany(Hospitalizacion::class); }
    public function imagenEstudios(): HasMany { return $this->hasMany(ImagenEstudio::class); }
    public function triajes(): HasMany { return $this->hasMany(Triaje::class); }
    public function dispensaciones(): HasMany { return $this->hasMany(Dispensacion::class); }
    public function odontograma(): \Illuminate\Database\Eloquent\Relations\HasOne { return $this->hasOne(Odontograma::class); }
    public function embarazo(): \Illuminate\Database\Eloquent\Relations\HasOne { return $this->hasOne(Embarazo::class); }
    public function evaluacionesCardio(): HasMany { return $this->hasMany(EvaluacionCardio::class); }
    public function dermatograma(): \Illuminate\Database\Eloquent\Relations\HasOne { return $this->hasOne(Dermatograma::class); }
    public function sesionesPsico(): HasMany { return $this->hasMany(SesionPsicologica::class); }
    public function evaluacionesOftalmo(): HasMany { return $this->hasMany(EvaluacionOftalmo::class); }
    public function evaluacionesNutricion(): HasMany { return $this->hasMany(EvaluacionNutricion::class); }
    public function traumatograma(): \Illuminate\Database\Eloquent\Relations\HasOne { return $this->hasOne(Traumatograma::class); }
    public function evaluacionesEspecialidad(): HasMany { return $this->hasMany(EvaluacionEspecialidad::class); }

    public function getNombreCompletoAttribute(): string
    {
        return trim($this->nombres.' '.$this->apellidos);
    }

    public function getEdadAttribute(): ?int
    {
        return $this->fecha_nacimiento?->age;
    }

    public function getEdadMesesAttribute(): ?int
    {
        return $this->fecha_nacimiento ? (int) $this->fecha_nacimiento->diffInMonths(now()) : null;
    }
}
