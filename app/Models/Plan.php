<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    use Auditable;

    protected $table = 'planes';

    protected $fillable = [
        'nombre', 'slug', 'precio', 'ciclo', 'descripcion',
        'limite_especialidades', 'limite_usuarios', 'destacado', 'activo', 'orden',
    ];

    protected function casts(): array
    {
        return [
            'precio' => 'decimal:2',
            'destacado' => 'boolean',
            'activo' => 'boolean',
        ];
    }

    public function empresas(): HasMany { return $this->hasMany(Empresa::class); }
    public function suscripciones(): HasMany { return $this->hasMany(Suscripcion::class); }

    public function getEtiquetaAttribute(): string
    {
        return $this->nombre.' — '.($this->moneda ?? 'S/').' '.number_format((float) $this->precio, 2).' / '.$this->ciclo;
    }
}
