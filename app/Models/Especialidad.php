<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Especialidad extends Model
{
    use HasFactory;

    protected $table = 'especialidades';

    protected $fillable = ['nombre', 'slug', 'icono', 'color', 'descripcion', 'activo'];

    protected $casts = ['activo' => 'boolean'];

    public function empresas(): BelongsToMany
    {
        return $this->belongsToMany(Empresa::class, 'empresa_especialidad')
            ->withPivot('activo')->withTimestamps();
    }
}
