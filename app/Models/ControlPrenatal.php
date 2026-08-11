<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ControlPrenatal extends Model
{
    protected $table = 'controles_prenatales';

    protected $fillable = [
        'embarazo_id', 'user_id', 'fecha', 'semanas', 'peso',
        'presion_arterial', 'altura_uterina', 'fcf', 'presentacion',
        'movimientos_fetales', 'edema', 'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'movimientos_fetales' => 'boolean',
            'edema' => 'boolean',
        ];
    }

    public function embarazo(): BelongsTo { return $this->belongsTo(Embarazo::class); }
    public function medico(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
}
