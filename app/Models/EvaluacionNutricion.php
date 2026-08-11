<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluacionNutricion extends Model
{
    use Auditable;

    protected $table = 'evaluaciones_nutricion';

    protected $fillable = [
        'empresa_id', 'paciente_id', 'user_id', 'fecha',
        'peso', 'talla', 'imc', 'grasa', 'cintura', 'cadera', 'musculo',
        'objetivo_kcal', 'peso_objetivo', 'plan', 'observaciones',
    ];

    protected function casts(): array
    {
        return ['fecha' => 'date'];
    }

    /** Clasificación del IMC (OMS). */
    public function getClasificacionImcAttribute(): string
    {
        $i = (float) $this->imc;
        if ($i <= 0) { return '—'; }
        if ($i < 18.5) { return 'Bajo peso'; }
        if ($i < 25) { return 'Normal'; }
        if ($i < 30) { return 'Sobrepeso'; }
        if ($i < 35) { return 'Obesidad I'; }
        if ($i < 40) { return 'Obesidad II'; }

        return 'Obesidad III';
    }

    /** Índice cintura-cadera. */
    public function getIccAttribute(): ?float
    {
        return ($this->cintura && $this->cadera) ? round($this->cintura / $this->cadera, 2) : null;
    }

    public function paciente(): BelongsTo { return $this->belongsTo(Paciente::class); }
    public function medico(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
}
