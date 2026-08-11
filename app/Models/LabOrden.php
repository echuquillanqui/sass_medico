<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LabOrden extends Model
{
    protected $table = 'lab_ordenes';

    protected $fillable = [
        'empresa_id', 'paciente_id', 'medico_id', 'consulta_id',
        'fecha', 'estado', 'observaciones',
    ];

    protected $casts = ['fecha' => 'date'];

    public function empresa(): BelongsTo { return $this->belongsTo(Empresa::class); }
    public function paciente(): BelongsTo { return $this->belongsTo(Paciente::class); }
    public function medico(): BelongsTo { return $this->belongsTo(User::class, 'medico_id'); }
    public function items(): HasMany { return $this->hasMany(LabOrdenItem::class, 'lab_orden_id'); }

    public function getEstadoLabelAttribute(): string
    {
        return [
            'solicitada' => 'Solicitada', 'en_proceso' => 'En proceso',
            'completada' => 'Completada', 'entregada' => 'Entregada',
        ][$this->estado] ?? $this->estado;
    }
}
