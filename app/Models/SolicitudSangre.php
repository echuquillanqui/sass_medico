<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SolicitudSangre extends Model
{
    protected $table = 'solicitudes_sangre';

    protected $fillable = [
        'empresa_id', 'paciente_id', 'medico_id', 'grupo',
        'cantidad', 'fecha', 'estado', 'motivo',
    ];

    protected $casts = ['fecha' => 'date'];

    public function paciente(): BelongsTo { return $this->belongsTo(Paciente::class); }
    public function medico(): BelongsTo { return $this->belongsTo(User::class, 'medico_id'); }
}
