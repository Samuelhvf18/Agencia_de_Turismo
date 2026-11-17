<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\RegisterResponse;

/**
 * Respuesta de Fortify tras iniciar sesión o registrarse: envía a cada rol a
 * su pantalla de inicio (admin/operaciones → panel, guía/chofer → check-in,
 * turista → catálogo), respetando la URL que se intentaba visitar.
 */
class RedirigirSegunRol implements LoginResponse, RegisterResponse
{
    public function toResponse($request)
    {
        if ($request->wantsJson()) {
            return new JsonResponse(['two_factor' => false], 200);
        }

        if (! $request->user()->hasVerifiedEmail()) {
            // La verificación consume el destino después, no durante el registro.
            return redirect()->route('verification.notice');
        }

        return redirect()->intended($request->user()->rutaInicio());
    }
}
