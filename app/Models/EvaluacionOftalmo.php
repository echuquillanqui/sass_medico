<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluacionOftalmo extends Model
{
    use Auditable;

    protected $table = 'evaluaciones_oftalmo';

    protected $fillable = [
        'empresa_id', 'paciente_id', 'user_id', 'fecha',
        'od_av', 'od_esfera', 'od_cilindro', 'od_eje', 'od_pio',
        'os_av', 'os_esfera', 'os_cilindro', 'os_eje', 'os_pio',
        'diagnostico', 'observaciones',
    ];

    protected function casts(): array
    {
        return ['fecha' => 'date'];
    }

    public function paciente(): BelongsTo { return $this->belongsTo(Paciente::class); }
    public function medico(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
}
