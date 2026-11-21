<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\RegisterResponse;

/** Redirección según rol y destino. */
class RedirigirSegunRol implements LoginResponse, RegisterResponse
{
    public function toResponse($request)
    {
        if ($request->wantsJson()) {
            return new JsonResponse(['two_factor' => false], 200);
        }

        if (! $request->user()->hasVerifiedEmail()) {
            // Conserva destino hasta verificar.
            return redirect()->route('verification.notice');
        }

        return redirect()->intended($request->user()->rutaInicio());
    }
}
