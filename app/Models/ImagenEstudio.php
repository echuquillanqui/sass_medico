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
        'indicacion', 'hallazgos', 'conclusion', 'archivo', 'archivo_nombre',
    ];

    protected $casts = ['fecha' => 'date'];

    public const MODALIDADES = ['Radiografia', 'Ecografia', 'Tomografia (TAC)', 'Resonancia (RM)', 'Mamografia', 'Densitometria', 'Doppler'];

    public function paciente(): BelongsTo { return $this->belongsTo(Paciente::class); }
    public function medico(): BelongsTo { return $this->belongsTo(User::class, 'medico_id'); }
    public function radiologo(): BelongsTo { return $this->belongsTo(User::class, 'radiologo_id'); }

    public function getEstadoLabelAttribute(): string
    {
        return ['solicitado' => 'Solicitado', 'realizado' => 'Realizado', 'informado' => 'Informado'][$this->estado] ?? $this->estado;
    }

    public function getEsImagenAttribute(): bool
    {
        return $this->archivo && preg_match('/\.(jpg|jpeg|png|webp|gif)$/i', $this->archivo);
    }
}
