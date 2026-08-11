<?php

namespace App\Models\Concerns;

use App\Models\Auditoria;

trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(fn ($m) => $m->registrarAuditoria('creó'));
        static::updated(fn ($m) => $m->registrarAuditoria('actualizó'));
        static::deleted(fn ($m) => $m->registrarAuditoria('eliminó'));
    }

    public function registrarAuditoria(string $accion): void
    {
        $user = auth()->user();
        if (! $user) {
            return; // no auditar seeders / consola
        }

        $modelo = class_basename($this);

        // Etiqueta amigable del registro
        $etiqueta = $this->nombre_completo
            ?? $this->nombre
            ?? $this->concepto
            ?? ('#'.$this->getKey());

        Auditoria::create([
            'empresa_id' => $this->empresa_id ?? $user->empresa_id,
            'user_id' => $user->id,
            'user_nombre' => $user->name,
            'accion' => $accion,
            'modelo' => $modelo,
            'modelo_id' => $this->getKey(),
            'descripcion' => trim($modelo.': '.$etiqueta),
            'ip' => request()->ip(),
        ]);
    }
}
