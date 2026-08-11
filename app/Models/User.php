<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'empresa_id', 'especialidad_id', 'name', 'email', 'password',
        'role', 'telefono', 'avatar', 'activo', 'cmp', 'titulo_profesional', 'firma', 'preferencias',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'activo' => 'boolean',
            'preferencias' => 'array',
        ];
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function especialidad(): BelongsTo
    {
        return $this->belongsTo(Especialidad::class);
    }

    public function horarios(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(HorarioMedico::class);
    }

    public function pref(string $key, $default = null)
    {
        return data_get($this->preferencias, $key, $default);
    }

    public function isSuperAdmin(): bool { return $this->role === 'superadmin'; }
    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isMedico(): bool { return $this->role === 'medico'; }
    public function isRecepcion(): bool { return $this->role === 'recepcion'; }

    public function hasRole(string|array $roles): bool
    {
        return in_array($this->role, (array) $roles, true);
    }

    public function initials(): string
    {
        $parts = preg_split('/\s+/', trim($this->name));
        $a = mb_substr($parts[0] ?? '', 0, 1);
        $b = mb_substr($parts[1] ?? '', 0, 1);
        return mb_strtoupper($a.$b) ?: 'U';
    }
}
