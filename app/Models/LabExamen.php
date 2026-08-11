<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LabExamen extends Model
{
    protected $table = 'lab_examenes';

    protected $fillable = [
        'empresa_id', 'padre_id', 'nombre', 'categoria', 'unidad', 'valor_referencia', 'precio', 'activo',
    ];

    protected $casts = ['precio' => 'decimal:2', 'activo' => 'boolean'];

    public function empresa(): BelongsTo { return $this->belongsTo(Empresa::class); }

    public function padre(): BelongsTo { return $this->belongsTo(self::class, 'padre_id'); }

    public function componentes(): HasMany { return $this->hasMany(self::class, 'padre_id')->orderBy('nombre'); }
}
