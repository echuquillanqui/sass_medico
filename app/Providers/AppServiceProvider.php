<?php

namespace App\Providers;

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Compatibilidad con MySQL < 5.7.7 / MariaDB antiguo.
        Schema::defaultStringLength(191);

        // Invitado ya autenticado -> al dashboard (evita el /home por defecto).
        RedirectIfAuthenticated::redirectUsing(fn () => route('dashboard'));

        // No autenticado -> al login correspondiente (portal del paciente o personal).
        Authenticate::redirectUsing(function ($request) {
            return $request->is('portal*') ? route('portal.login') : route('login');
        });

        // Directivas de formato por empresa (moneda y números).
        Blade::directive('money', fn ($expr) => "<?php echo \\App\\Support\\Money::monto($expr); ?>");
        Blade::directive('numero', fn ($expr) => "<?php echo \\App\\Support\\Money::numero($expr); ?>");
    }
}
