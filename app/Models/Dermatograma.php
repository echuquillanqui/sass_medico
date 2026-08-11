<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dermatograma extends Model
{
    use Auditable;

    protected $table = 'dermatogramas';

    protected $fillable = [
        'empresa_id', 'paciente_id', 'user_id', 'lesiones', 'notas',
    ];

    protected function casts(): array
    {
        return ['lesiones' => 'array'];
    }

    // Tipos de lesión (clave => [etiqueta, color]).
    public const TIPOS = [
        'macula'   => ['Mácula', '#f59e0b'],
        'papula'   => ['Pápula', '#ec4899'],
        'nodulo'   => ['Nódulo', '#8b5cf6'],
        'vesicula' => ['Vesícula', '#3b82f6'],
        'placa'    => ['Placa', '#14b8a6'],
        'ulcera'   => ['Úlcera', '#ef4444'],
        'nevo'     => ['Nevo / lunar', '#78350f'],
        'tumor'    => ['Tumor', '#111827'],
    ];

    public function paciente(): BelongsTo { return $this->belongsTo(Paciente::class); }
    public function medico(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
}
