<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
class RoleMiddleware
{
    public function handle(
        Request $request,
        Closure $next,
        string ...$roles
    ): Response {
        $user = $request->user();
        if (!$user) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }
        $rolNombre = $user->rol()->value('nom');
        if (!in_array($rolNombre, $roles, true)) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }
        return $next($request);
    }
}