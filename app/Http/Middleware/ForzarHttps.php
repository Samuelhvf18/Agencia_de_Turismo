<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** HTTPS y cabeceras de seguridad. */
class ForzarHttps
{
    public function handle(Request $request, Closure $next): Response
    {
        $activo = (bool) config('app.force_https');

        if ($activo && ! $request->secure()) {
            return redirect()->secure($request->getRequestUri(), 301);
        }

        $respuesta = $next($request);

        if ($activo) {
            $respuesta->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }
        $respuesta->headers->set('X-Content-Type-Options', 'nosniff');
        $respuesta->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $respuesta->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        return $respuesta;
    }
}
