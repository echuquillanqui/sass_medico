<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureModuleEnabled
{
    /**
     * Verifica que la empresa del usuario tenga habilitada la especialidad.
     * El slug se toma del parametro de ruta {slug} o de un argumento opcional.
     * Uso: ->middleware('module')  o  ->middleware('module:pediatria')
     */
    public function handle(Request $request, Closure $next, ?string $slug = null): Response
    {
        $slug = $slug ?? $request->route('slug');
        $user = $request->user();

        // El superadmin ve todos los modulos.
        if ($user && $user->isSuperAdmin()) {
            return $next($request);
        }

        $empresa = $user?->empresa;

        if (! $empresa || ! $empresa->tieneEspecialidad($slug)) {
            abort(403, 'Este modulo no esta habilitado para tu empresa.');
        }

        return $next($request);
    }
}
