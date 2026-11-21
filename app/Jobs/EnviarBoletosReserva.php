<?php

namespace App\Jobs;

use App\Models\Reserva;
use App\Services\Notificaciones\NotificacionService;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/** Envío de boletos y comprobante. */
class EnviarBoletosReserva implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(public int $reservaId) {}

    public function uniqueId(): string
    {
        return (string) $this->reservaId;
    }

    public function handle(): void
    {
        $reserva = Reserva::with('salida.tour', 'comprobante')->find($this->reservaId);
        if (! $reserva) {
            return;
        }

        $registro = app(NotificacionService::class)->confirmacionReserva($reserva);
        (new NotificacionJob($registro->id))->handle();
    }
}
