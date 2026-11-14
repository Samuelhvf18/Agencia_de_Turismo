<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Maneja el acceso por rol (soporta tanto campo 'rol', 'rol_id' o Spatie hasRole).
     */
    public function handle(
        Request $request,
        Closure $next,
        string ...$roles
    ): Response {
        $user = $request->user();

        if (!$user) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        // 1. Verificar si el usuario tiene relación con Rol o columna 'rol'
        $rolNombre = $user->rol ?? null;
        if (is_object($rolNombre) && isset($rolNombre->nom)) {
            $rolNombre = $rolNombre->nom;
        } elseif (!$rolNombre && method_exists($user, 'rol') && $user->rol()->exists()) {
            $rolNombre = $user->rol()->value('nom');
        }

        // 2. Verificar coincidencia directa por nombre de rol
        if ($rolNombre && in_array($rolNombre, $roles, true)) {
            return $next($request);
        }

        // 3. Verificar mediante Spatie Permission (hasRole o hasAnyRole)
        if (method_exists($user, 'hasAnyRole')) {
            try {
                if ($user->hasAnyRole($roles)) {
                    return $next($request);
                }
            } catch (\Throwable $e) {
                // Silencioso si no hay tabla de roles de spatie
            }
        }

        abort(403, 'No tienes permiso para acceder a esta sección.');
    }
}
