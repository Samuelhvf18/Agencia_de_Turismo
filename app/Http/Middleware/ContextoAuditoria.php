<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContextoAuditoria
{
    public function handle(Request $request, Closure $next)
    {
        DB::select("SELECT set_config('app.usuario', ?, false), set_config('app.ip', ?, false), set_config('app.agente', ?, false)", [
            (string) ($request->user()?->id ?? ''), (string) $request->ip(), (string) $request->userAgent(),
        ]);
        try {
            $response = $next($request);
            if ($request->user() || $request->routeIs('reservas.*', 'pagos.*', 'pasarela.*', 'boletos.*')) {
                $response->headers->set('Cache-Control', 'no-store, private');
            }

            return $response;
        } finally {
            DB::select("SELECT set_config('app.usuario', '', false), set_config('app.ip', '', false), set_config('app.agente', '', false)");
        }
    }
}
