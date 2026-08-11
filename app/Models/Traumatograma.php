<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Traumatograma extends Model
{
    use Auditable;

    protected $table = 'traumatogramas';

    protected $fillable = [
        'empresa_id', 'paciente_id', 'user_id', 'lesiones', 'notas',
    ];

    protected function casts(): array
    {
        return ['lesiones' => 'array'];
    }

    // Tipos de lesión musculoesquelética (clave => [etiqueta, color]).
    public const TIPOS = [
        'fractura'    => ['Fractura', '#ef4444'],
        'esguince'    => ['Esguince', '#f59e0b'],
        'luxacion'    => ['Luxación', '#8b5cf6'],
        'contusion'   => ['Contusión', '#3b82f6'],
        'desgarro'    => ['Desgarro muscular', '#ec4899'],
        'tendinitis'  => ['Tendinitis', '#14b8a6'],
        'artrosis'    => ['Artrosis', '#78350f'],
        'protesis'    => ['Prótesis / osteosíntesis', '#111827'],
    ];

    public function paciente(): BelongsTo { return $this->belongsTo(Paciente::class); }
    public function medico(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
}
