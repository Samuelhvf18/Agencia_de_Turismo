<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/** Recuerda destinos públicos locales. */
class RecordarPaginaPublica
{
    private const PAGINAS = ['home', 'categorias.show', 'guias.perfil', 'tours.show',
        'reservas.salida', 'reservas.participantes', 'reservas.equipo', 'reservas.ficha',
        'reservas.resumen', 'reservas.confirmada', 'reservas.estado', 'reservas.consultar',
        'pagos.create', 'chatbot', 'legacy.chatbot.get'];

    public function handle(Request $request, Closure $next)
    {
        if (! $request->user() && $request->isMethod('GET') && ! $request->expectsJson()) {
            if (in_array($request->route()?->getName(), ['login', 'legacy.login', 'register', 'legacy.register', 'google.redirect'], true)
                && ! $request->session()->has('url.intended') && $request->session()->has('auth.pagina_anterior')) {
                $request->session()->put('url.intended', $request->session()->get('auth.pagina_anterior'));
            }
        }
        $response = $next($request);
        if (! $request->user() && $request->isMethod('GET') && $response->getStatusCode() === 200
            && ! $request->expectsJson() && in_array($request->route()?->getName(), self::PAGINAS, true)) {
            // Usa solo rutas locales.
            $request->session()->put('auth.pagina_anterior', '/'.ltrim($request->getRequestUri(), '/'));
        }

        return $response;
    }
}
