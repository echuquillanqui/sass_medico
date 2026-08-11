<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Adjunto extends Model
{
    protected $table = 'adjuntos';

    protected $fillable = [
        'empresa_id', 'paciente_id', 'consulta_id', 'user_id',
        'nombre', 'archivo', 'tipo', 'tamano', 'categoria',
    ];

    public function paciente(): BelongsTo { return $this->belongsTo(Paciente::class); }
    public function consulta(): BelongsTo { return $this->belongsTo(Consulta::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }

    public function getEsImagenAttribute(): bool
    {
        return str_starts_with((string) $this->tipo, 'image/');
    }

    public function getTamanoLegibleAttribute(): string
    {
        $b = (int) $this->tamano;
        if ($b >= 1048576) return round($b / 1048576, 1).' MB';
        if ($b >= 1024) return round($b / 1024).' KB';
        return $b.' B';
    }
}
