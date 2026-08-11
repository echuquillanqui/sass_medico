<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Odontograma extends Model
{
    use Auditable;

    protected $fillable = [
        'empresa_id', 'paciente_id', 'user_id', 'dientes', 'plan', 'notas',
    ];

    protected function casts(): array
    {
        return [
            'dientes' => 'array',
            'plan' => 'array',
        ];
    }

    // Estados posibles de cada pieza dental (clave => [etiqueta, color]).
    public const ESTADOS = [
        'sano'      => ['Sano', '#ffffff'],
        'caries'    => ['Caries', '#ef4444'],
        'obturado'  => ['Obturado', '#3b82f6'],
        'corona'    => ['Corona', '#f59e0b'],
        'endodoncia'=> ['Endodoncia', '#8b5cf6'],
        'extraccion'=> ['Extracción indicada', '#f97316'],
        'ausente'   => ['Ausente', '#111827'],
        'implante'  => ['Implante', '#10b981'],
    ];

    public function empresa(): BelongsTo { return $this->belongsTo(Empresa::class); }
    public function paciente(): BelongsTo { return $this->belongsTo(Paciente::class); }
    public function medico(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
}
