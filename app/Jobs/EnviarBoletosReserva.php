<?php

namespace App\Jobs;

use App\Mail\BoletosReservaMail;
use App\Models\RegistroNotificacion;
use App\Models\Reserva;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * Envía al contacto de la reserva los boletos QR y el comprobante, y deja el
 * resultado en registros_notificacion (RF-38).
 */
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

        $registro = RegistroNotificacion::firstOrCreate([
            'rsv_id' => $reserva->id, 'tip' => 'reserva_confirmada', 'can' => 'email',
        ], [
            'rsv_id' => $reserva->id,
            'usr_id' => $reserva->usr_id,
            'can' => 'email',
            'tip' => 'reserva_confirmada',
            'est' => 'pendiente',
            'cnd' => ['destino' => $reserva->cor_ctc, 'adjuntos' => ['boletos', 'comprobante']],
        ]);

        if ($registro->est === 'enviado') {
            return;
        }

        try {
            Mail::to($reserva->cor_ctc, $reserva->nom_ctc)->send(new BoletosReservaMail($reserva));
            $registro->update(['est' => 'enviado', 'env_en' => now()]);
        } catch (Throwable $e) {
            $registro->update(['est' => 'fallido', 'cnd' => $registro->cnd + ['error' => $e->getMessage()]]);
            throw $e;
        }
    }
}
