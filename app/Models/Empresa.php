<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Empresa extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre', 'slug', 'ruc', 'email', 'telefono', 'direccion', 'logo',
        'color_primario', 'moneda', 'horario_inicio', 'horario_fin',
        'dias_atencion', 'sitio_web', 'plan', 'activo',
        'separador_decimal', 'separador_miles', 'decimales', 'moneda_posicion',
        'plan_id', 'vence_suscripcion',
    ];

    protected $casts = ['activo' => 'boolean', 'decimales' => 'integer', 'vence_suscripcion' => 'date'];

    public function planRef(): \Illuminate\Database\Eloquent\Relations\BelongsTo { return $this->belongsTo(Plan::class, 'plan_id'); }
    public function facturacionConfig(): \Illuminate\Database\Eloquent\Relations\HasOne { return $this->hasOne(FacturacionConfig::class); }
    public function comprobantes(): HasMany { return $this->hasMany(Comprobante::class); }
    public function suscripciones(): HasMany { return $this->hasMany(Suscripcion::class)->latest('fecha_fin')->latest('id'); }

    public function suscripcionActual(): ?Suscripcion
    {
        return $this->suscripciones()->first();
    }

    /** Días restantes de la suscripción (negativo si venció). */
    public function getDiasRestantesAttribute(): ?int
    {
        return $this->vence_suscripcion ? (int) now()->startOfDay()->diffInDays($this->vence_suscripcion, false) : null;
    }

    /** Estado: sin_plan | vigente | por_vencer | vencida */
    public function getEstadoSuscripcionAttribute(): string
    {
        if (! $this->vence_suscripcion) {
            return 'sin_plan';
        }
        $d = $this->dias_restantes;
        if ($d < 0) {
            return 'vencida';
        }

        return $d <= 7 ? 'por_vencer' : 'vigente';
    }

    /** Formatea un número según la configuración de la empresa (separadores y decimales). */
    public function formatoNumero($valor, ?int $decimales = null): string
    {
        $dec = $decimales ?? (int) ($this->decimales ?? 2);

        return number_format((float) $valor, $dec, $this->separador_decimal ?: '.', $this->separador_miles ?: ',');
    }

    /** Formatea un monto con el símbolo de moneda en la posición configurada. */
    public function formatoMonto($valor, ?int $decimales = null): string
    {
        $mon = $this->moneda ?: 'S/';
        $num = $this->formatoNumero($valor, $decimales);

        return ($this->moneda_posicion === 'despues') ? "{$num} {$mon}" : "{$mon} {$num}";
    }

    public function especialidades(): BelongsToMany
    {
        return $this->belongsToMany(Especialidad::class, 'empresa_especialidad')
            ->withPivot('activo')->withTimestamps();
    }

    public function especialidadesActivas()
    {
        return $this->especialidades()->wherePivot('activo', true)->where('especialidades.activo', true);
    }

    public function usuarios(): HasMany { return $this->hasMany(User::class); }
    public function pacientes(): HasMany { return $this->hasMany(Paciente::class); }
    public function citas(): HasMany { return $this->hasMany(Cita::class); }
    public function pagos(): HasMany { return $this->hasMany(Pago::class); }

    public function tieneEspecialidad(string $slug): bool
    {
        return $this->especialidadesActivas()->where('slug', $slug)->exists();
    }
}
