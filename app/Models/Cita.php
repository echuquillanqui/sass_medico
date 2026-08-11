<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cita extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'empresa_id', 'paciente_id', 'medico_id', 'especialidad_id',
        'fecha', 'hora', 'duracion', 'estado', 'motivo', 'notas',
        'es_teleconsulta', 'sala_video', 'estado_sala', 'hora_llegada', 'hora_atencion',
    ];

    protected $casts = ['fecha' => 'date', 'es_teleconsulta' => 'boolean', 'hora_llegada' => 'datetime', 'hora_atencion' => 'datetime'];

    public function empresa(): BelongsTo { return $this->belongsTo(Empresa::class); }
    public function paciente(): BelongsTo { return $this->belongsTo(Paciente::class); }
    public function medico(): BelongsTo { return $this->belongsTo(User::class, 'medico_id'); }
    public function especialidad(): BelongsTo { return $this->belongsTo(Especialidad::class); }
    public function encuesta(): \Illuminate\Database\Eloquent\Relations\HasOne { return $this->hasOne(Encuesta::class); }

    public function getSalaVideoUrlAttribute(): string
    {
        $sala = $this->sala_video ?: ('SuiteSalud-'.$this->empresa_id.'-'.$this->id.'-'.substr(md5('cita'.$this->id), 0, 8));
        return 'https://meet.jit.si/'.$sala;
    }

    public function getWhatsappUrlAttribute(): ?string
    {
        $tel = preg_replace('/[^0-9]/', '', (string) optional($this->paciente)->telefono);
        if (! $tel) return null;
        $msg = 'Hola '.optional($this->paciente)->nombres.', te recordamos tu cita en '
            .optional($this->empresa)->nombre.' el '.$this->fecha->format('d/m/Y').' a las '
            .substr((string) $this->hora, 0, 5).'.';
        return 'https://wa.me/'.$tel.'?text='.rawurlencode($msg);
    }

}
