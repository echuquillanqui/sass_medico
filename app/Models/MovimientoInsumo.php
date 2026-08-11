<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovimientoInsumo extends Model
{
    use HasFactory;

    protected $table = 'movimientos_insumo';

    protected $fillable = [
        'empresa_id', 'insumo_id', 'user_id', 'tipo', 'cantidad', 'motivo', 'fecha',
    ];

    protected $casts = ['fecha' => 'date', 'cantidad' => 'decimal:2'];

    public function insumo(): BelongsTo { return $this->belongsTo(Insumo::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
