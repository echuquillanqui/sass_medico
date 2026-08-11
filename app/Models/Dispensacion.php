<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dispensacion extends Model
{
    protected $table = 'dispensaciones';

    protected $fillable = [
        'empresa_id', 'paciente_id', 'consulta_id', 'user_id',
        'fecha', 'total', 'observaciones',
    ];

    protected $casts = ['fecha' => 'date', 'total' => 'decimal:2'];

    public function empresa(): BelongsTo { return $this->belongsTo(Empresa::class); }
    public function paciente(): BelongsTo { return $this->belongsTo(Paciente::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function items(): HasMany { return $this->hasMany(DispensacionItem::class); }
}
