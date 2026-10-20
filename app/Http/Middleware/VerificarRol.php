<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Control de acceso basado en roles (RNF-02, RNF-03).
 *
 * Uso en rutas: ->middleware('rol:admin,operaciones')
 */
class VerificarRol
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $usuario = $request->user();

        if (! $usuario) {
            return redirect()->guest(route('login'));
        }

        abort_unless($usuario->act && $usuario->tieneRol($roles), 403, 'No tienes permiso para acceder a esta sección.');

        return $next($request);
    }
}
