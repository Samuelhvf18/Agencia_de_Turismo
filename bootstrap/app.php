<?php

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
        // El webhook de la pasarela se autentica con firma HMAC, no con token CSRF.
        $middleware->validateCsrfTokens(except: ['webhooks/*']);

        // RNF-01: HTTPS obligatorio en producción y cabeceras de seguridad.
        $middleware->prepend(\App\Http\Middleware\ForzarHttps::class);
        $middleware->trustProxies(at: '*');

        // RNF-14: idioma de la interfaz (español / inglés).
        $middleware->web(append: [\App\Http\Middleware\EstablecerIdioma::class]);

        $middleware->alias([
            'rol' => \App\Http\Middleware\VerificarRol::class,
            'n8n' => \App\Http\Middleware\TokenN8n::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
