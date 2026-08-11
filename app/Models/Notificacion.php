<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notificacion extends Model
{
    protected $table = 'notificaciones';

    protected $fillable = [
        'empresa_id', 'user_id', 'tipo', 'icono', 'titulo', 'mensaje', 'url', 'leido',
    ];

    protected $casts = ['leido' => 'boolean'];

    public function empresa(): BelongsTo { return $this->belongsTo(Empresa::class); }

    /** Crea una notificación para toda la empresa (o un usuario concreto). */
    public static function crear(int $empresaId, string $titulo, array $opts = []): self
    {
        return static::create([
            'empresa_id' => $empresaId,
            'user_id' => $opts['user_id'] ?? null,
            'tipo' => $opts['tipo'] ?? 'info',
            'icono' => $opts['icono'] ?? 'fa-bell',
            'titulo' => $titulo,
            'mensaje' => $opts['mensaje'] ?? null,
            'url' => $opts['url'] ?? null,
        ]);
    }
}
