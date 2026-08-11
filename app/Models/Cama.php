<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cama extends Model
{
    protected $table = 'camas';

    protected $fillable = ['empresa_id', 'nombre', 'area', 'activo'];

    protected $casts = ['activo' => 'boolean'];

    public function empresa(): BelongsTo { return $this->belongsTo(Empresa::class); }
    public function hospitalizaciones(): HasMany { return $this->hasMany(Hospitalizacion::class); }

    public function hospitalizacionActiva()
    {
        return $this->hospitalizaciones()->where('estado', 'activa')->with('paciente')->first();
    }

    public function getOcupadaAttribute(): bool
    {
        return $this->hospitalizaciones()->where('estado', 'activa')->exists();
    }
}
