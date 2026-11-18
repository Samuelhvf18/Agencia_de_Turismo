<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class NormalizarCorreos
{
    public function handle(Request $request, Closure $next)
    {
        if (! $request->isMethod('GET')) {
            $datos = $request->all();
            foreach (['cor', 'email:rfc', 'correo', 'cor_ctc', 'login'] as $campo) {
                if (isset($datos[$campo]) && is_string($datos[$campo])) {
                    $request->merge([$campo => mb_strtolower(trim($datos[$campo]))]);
                }
            }
        }

        return $next($request);
    }
}
