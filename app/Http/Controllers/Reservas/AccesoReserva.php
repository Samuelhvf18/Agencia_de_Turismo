<?php

namespace App\Http\Controllers\Reservas;

use App\Models\Reserva;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Quién puede ver una reserva: quien la creó o la buscó con código + correo
 * en esta sesión, su titular con cuenta, y el personal (admin/operaciones).
 */
class AccesoReserva
{
    private const CLAVE = 'reservas.acceso';

    public static function conceder(Request $request, Reserva $reserva): void
    {
        $codigos = $request->session()->get(self::CLAVE, []);
        $codigos[] = $reserva->cod;
        $request->session()->put(self::CLAVE, array_values(array_unique($codigos)));
    }

    public static function permitido(Request $request, Reserva $reserva): bool
    {
        $usuario = $request->user();

        return in_array($reserva->cod, $request->session()->get(self::CLAVE, []), true)
            || ($usuario && $reserva->usr_id === $usuario->id)
            || ($usuario && $usuario->tieneRol([User::ROL_ADMIN, User::ROL_OPERACIONES]));
    }

    public static function exigir(Request $request, Reserva $reserva): void
    {
        abort_unless(self::permitido($request, $reserva), 403, __('Consulta la reserva con su código y el correo del contacto.'));
    }
}
