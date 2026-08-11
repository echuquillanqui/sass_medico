<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Auditoria extends Model
{
    protected $table = 'auditorias';

    protected $fillable = [
        'empresa_id', 'user_id', 'user_nombre', 'accion',
        'modelo', 'modelo_id', 'descripcion', 'ip',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
