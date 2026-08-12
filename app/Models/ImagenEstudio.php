<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImagenEstudio extends Model
{
    protected $table = 'imagen_estudios';

    protected $fillable = [
        'empresa_id', 'paciente_id', 'medico_id', 'radiologo_id',
        'modalidad', 'region', 'fecha', 'estado',
        'tipo_estudio', 'indicacion', 'hallazgos', 'conclusion', 'archivo', 'archivo_nombre',
        'orden_archivo', 'orden_nombre',
    ];

    protected $casts = ['fecha' => 'date'];

    public const MODALIDADES = ['Radiografia', 'Ecografia', 'Tomografia (TAC)', 'Resonancia (RM)', 'Mamografia', 'Densitometria', 'Doppler'];
    public const ESTADOS = ['solicitado' => 'Solicitado', 'realizado' => 'Realizado', 'informado' => 'Informado'];

    public function paciente(): BelongsTo { return $this->belongsTo(Paciente::class); }
    public function medico(): BelongsTo { return $this->belongsTo(User::class, 'medico_id'); }
    public function radiologo(): BelongsTo { return $this->belongsTo(User::class, 'radiologo_id'); }

    public function getEstadoLabelAttribute(): string
    {
        return self::ESTADOS[$this->estado] ?? $this->estado;
    }

    public function getEsImagenAttribute(): bool
    {
        return $this->archivo && preg_match('/\.(jpg|jpeg|png|webp|gif)$/i', $this->archivo);
    }

    public function getTipoEstudioLabelAttribute(): string
    {
        return $this->tipo_estudio ?: trim($this->modalidad.' '.($this->region ?? ''));
    }
}
