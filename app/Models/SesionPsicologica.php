<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SesionPsicologica extends Model
{
    use Auditable;

    protected $table = 'sesiones_psicologicas';

    protected $fillable = [
        'empresa_id', 'paciente_id', 'user_id', 'fecha', 'numero', 'motivo',
        'enfoque', 'desarrollo', 'tareas', 'estado_animo', 'progreso', 'proxima_cita',
    ];

    protected function casts(): array
    {
        return ['fecha' => 'date', 'proxima_cita' => 'date'];
    }

    public function paciente(): BelongsTo { return $this->belongsTo(Paciente::class); }
    public function medico(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
}
