<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Donante extends Model
{
    protected $table = 'donantes';

    protected $fillable = [
        'empresa_id', 'nombres', 'apellidos', 'documento', 'grupo',
        'telefono', 'fecha_ultima_donacion', 'activo',
    ];

    protected $casts = ['fecha_ultima_donacion' => 'date', 'activo' => 'boolean'];

    public const GRUPOS = ['O-', 'O+', 'A-', 'A+', 'B-', 'B+', 'AB-', 'AB+'];

    public function empresa(): BelongsTo { return $this->belongsTo(Empresa::class); }
    public function unidades(): HasMany { return $this->hasMany(UnidadSangre::class); }

    public function getNombreCompletoAttribute(): string
    {
        return trim($this->nombres.' '.$this->apellidos);
    }
}
