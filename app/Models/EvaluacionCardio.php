<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluacionCardio extends Model
{
    use Auditable;

    protected $table = 'evaluaciones_cardio';

    protected $fillable = [
        'empresa_id', 'paciente_id', 'user_id', 'fecha',
        'pa_sistolica', 'pa_diastolica', 'fc',
        'colesterol_total', 'hdl', 'ldl', 'trigliceridos', 'glucosa',
        'fumador', 'diabetes', 'ecg_ritmo', 'ecg_hallazgos',
        'riesgo_pct', 'riesgo_nivel', 'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'fumador' => 'boolean',
            'diabetes' => 'boolean',
        ];
    }

    public function paciente(): BelongsTo { return $this->belongsTo(Paciente::class); }
    public function medico(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }

    /**
     * Estimación simplificada de riesgo cardiovascular a 10 años (inspirada en Framingham).
     * Devuelve ['pct' => float, 'nivel' => string]. Es orientativa, no sustituye el juicio clínico.
     */
    public static function estimarRiesgo(array $d, ?int $edad, ?string $sexo): array
    {
        $p = 0;
        $edad = $edad ?? 45;
        $p += max(0, ($edad - 40)) * 0.5;                 // edad
        if (($sexo ?? 'M') === 'M') { $p += 3; }          // sexo masculino
        $pas = (int) ($d['pa_sistolica'] ?? 120);
        if ($pas >= 160) { $p += 8; } elseif ($pas >= 140) { $p += 5; } elseif ($pas >= 130) { $p += 2; }
        $ct = (int) ($d['colesterol_total'] ?? 180);
        if ($ct >= 280) { $p += 8; } elseif ($ct >= 240) { $p += 5; } elseif ($ct >= 200) { $p += 2; }
        $hdl = (int) ($d['hdl'] ?? 50);
        if ($hdl < 40) { $p += 3; } elseif ($hdl >= 60) { $p -= 2; }
        if (! empty($d['fumador'])) { $p += 6; }
        if (! empty($d['diabetes'])) { $p += 6; }

        $pct = max(1, min(60, round($p, 1)));
        $nivel = $pct < 10 ? 'bajo' : ($pct < 20 ? 'moderado' : ($pct < 30 ? 'alto' : 'muy alto'));

        return ['pct' => $pct, 'nivel' => $nivel];
    }
}
