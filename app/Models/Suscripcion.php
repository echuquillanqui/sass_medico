<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Suscripcion extends Model
{
    use Auditable;

    protected $table = 'suscripciones';

    protected $fillable = [
        'empresa_id', 'plan_id', 'user_id', 'ticket', 'plan_nombre', 'plan_precio',
        'ciclo', 'duracion', 'unidad', 'descuento', 'tipo_descuento',
        'subtotal', 'total', 'fecha_inicio', 'fecha_fin', 'metodo', 'nota',
    ];

    protected function casts(): array
    {
        return [
            'plan_precio' => 'decimal:2',
            'descuento' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'total' => 'decimal:2',
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
        ];
    }

    public function empresa(): BelongsTo { return $this->belongsTo(Empresa::class); }
    public function plan(): BelongsTo { return $this->belongsTo(Plan::class); }
    public function usuario(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
}
