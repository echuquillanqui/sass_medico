<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DispensacionItem extends Model
{
    protected $table = 'dispensacion_items';

    protected $fillable = [
        'dispensacion_id', 'insumo_id', 'nombre', 'cantidad', 'precio', 'indicaciones',
    ];

    protected $casts = ['cantidad' => 'decimal:2', 'precio' => 'decimal:2'];

    public function dispensacion(): BelongsTo { return $this->belongsTo(Dispensacion::class); }
    public function insumo(): BelongsTo { return $this->belongsTo(Insumo::class); }
}
