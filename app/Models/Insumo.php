<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Insumo extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'nombre', 'categoria', 'unidad',
        'stock', 'stock_minimo', 'precio', 'codigo', 'activo',
    ];

    protected $casts = [
        'stock' => 'decimal:2',
        'stock_minimo' => 'decimal:2',
        'precio' => 'decimal:2',
        'activo' => 'boolean',
    ];

    public function empresa(): BelongsTo { return $this->belongsTo(Empresa::class); }
    public function movimientos(): HasMany { return $this->hasMany(MovimientoInsumo::class); }

    public function getBajoStockAttribute(): bool
    {
        return $this->stock <= $this->stock_minimo;
    }
}
