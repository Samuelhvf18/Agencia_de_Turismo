<?php

namespace App\Providers;

use App\Listeners\AlertasSeguridad;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Las escrituras se auditan con triggers PostgreSQL, incluidas operaciones masivas.
        // RNF-04: alertas de seguridad al administrador.
        Event::listen(Registered::class, [AlertasSeguridad::class, 'registrada']);
        Event::listen(Failed::class, [AlertasSeguridad::class, 'fallido']);
        Event::listen(PasswordReset::class, [AlertasSeguridad::class, 'contrasenaRestablecida']);
    }
}
