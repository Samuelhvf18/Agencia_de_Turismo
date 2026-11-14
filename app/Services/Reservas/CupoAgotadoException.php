<?php

namespace App\Services\Reservas;

use RuntimeException;

/** La salida no tiene cupos suficientes: la solicitud se registra como llegada rechazada. */
class CupoAgotadoException extends RuntimeException
{
    public function __construct(public readonly int $disponibles)
    {
        parent::__construct($disponibles > 0
            ? __('Solo quedan :cantidad cupos en esta salida.', ['cantidad' => $disponibles])
            : __('La salida ya no tiene cupos disponibles.'));
    }
}
