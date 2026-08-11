<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluacionEspecialidad extends Model
{
    use Auditable;

    protected $table = 'evaluaciones_especialidad';

    protected $fillable = [
        'empresa_id', 'paciente_id', 'user_id', 'especialidad_slug', 'fecha', 'datos', 'notas',
    ];

    protected function casts(): array
    {
        return ['fecha' => 'date', 'datos' => 'array'];
    }

    public function paciente(): BelongsTo { return $this->belongsTo(Paciente::class); }
    public function medico(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }

    /** Devuelve un dato por clave. */
    public function dato(string $key, $default = null)
    {
        return $this->datos[$key] ?? $default;
    }
}
