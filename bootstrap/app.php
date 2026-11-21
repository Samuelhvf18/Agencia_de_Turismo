<?php

use App\Http\Middleware\ContextoAuditoria;
use App\Http\Middleware\EstablecerIdioma;
use App\Http\Middleware\ForzarHttps;
use App\Http\Middleware\NormalizarCorreos;
use App\Http\Middleware\RecordarPaginaPublica;
use App\Http\Middleware\TokenN8n;
use App\Http\Middleware\VerificarRol;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Webhook autenticado con HMAC.
        $middleware->validateCsrfTokens(except: ['webhooks/*']);

        // HTTPS obligatorio en producción.
        $middleware->prepend(ForzarHttps::class);
        $middleware->trustProxies(at: '*');

        // Idioma de la interfaz.
        $middleware->web(append: [NormalizarCorreos::class, EstablecerIdioma::class, ContextoAuditoria::class, RecordarPaginaPublica::class]);

        $middleware->alias([
            'rol' => VerificarRol::class,
            'n8n' => TokenN8n::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
