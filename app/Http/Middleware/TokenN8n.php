<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Token compartido de n8n. */
class TokenN8n
{
    public function handle(Request $request, Closure $next): Response
    {
        $esperado = (string) config('services.n8n.token');
        $recibido = (string) ($request->header('X-Pipocas-Token') ?? $request->bearerToken());

        if ($esperado === '' || ! hash_equals($esperado, $recibido)) {
            return response()->json(['ok' => false, 'error' => 'Token de n8n inválido o no configurado.'], 401);
        }

        return $next($request);
    }
}
