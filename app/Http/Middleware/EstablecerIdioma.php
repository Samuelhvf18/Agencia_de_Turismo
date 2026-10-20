<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

/**
 * RNF-14: idioma de la interfaz. Se elige con ?lang=es|en (queda en la
 * sesión); si no, se usa la preferencia del navegador; por defecto español.
 */
class EstablecerIdioma
{
    public const IDIOMAS = ['es', 'en'];

    public function handle(Request $request, Closure $next): Response
    {
        if (in_array($request->query('lang'), self::IDIOMAS, true)) {
            $request->session()->put('idioma', $request->query('lang'));
        }

        $idioma = $request->session()->get('idioma')
            ?? $request->getPreferredLanguage(self::IDIOMAS)
            ?? 'es';

        App::setLocale(in_array($idioma, self::IDIOMAS, true) ? $idioma : 'es');

        return $next($request);
    }
}
