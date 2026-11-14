<?php

namespace App\Jobs;

use App\Mail\AvisoMail;
use App\Mail\BoletosReservaMail;
use App\Models\RegistroNotificacion;
use App\Models\Reserva;
use App\Services\Notificaciones\EntregaNotificacion;
use App\Services\Notificaciones\NotificacionService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

/**
 * Despacha un registro de notificación (RF-38, M10):
 * - Con N8N_WEBHOOK_NOTIFICACIONES configurado, envía el evento a n8n, que
 *   arma el mensaje y lo manda por correo/WhatsApp.
 * - Sin n8n, envía un correo sencillo desde Laravel.
 * El resultado (enviado/fallido) queda en el mismo registro.
 */
class NotificacionJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [30, 120];

    public function __construct(public int $registroId) {}

    public function handle(): void
    {
        app(EntregaNotificacion::class)->entregar($this->registroId, function (RegistroNotificacion $registro) {
            $registro->load('reserva.salida.tour');
            if ($registro->tip === 'reserva_confirmada' && ! empty($registro->cnd['adjuntos'])) {
                if (! $registro->reserva || $registro->reserva->est_pag !== Reserva::PAG_PAGADO) {
                    throw new \RuntimeException('La reserva ya no está pagada; revisar antes de enviar boletos.');
                }
                Mail::to($registro->cnd['destino'])->send((new BoletosReservaMail($registro->reserva))->locale($registro->cnd['idioma'] ?? 'es'));

                return;
            }
            $webhook = config('services.n8n.webhook_notificaciones');
            if ($webhook) {
                Http::timeout(10)->withHeaders([
                    'X-Pipocas-Token' => (string) config('services.n8n.token'),
                    'Idempotency-Key' => $registro->clave_evento ?? 'notificacion:'.$registro->id,
                ])->post($webhook, $this->evento($registro))->throw();
            } elseif ($registro->can === 'email' && ! empty($registro->cnd['destino'])) {
                Mail::to($registro->cnd['destino'], $registro->cnd['nombre'] ?? null)->send((new AvisoMail($registro))->locale($registro->cnd['idioma'] ?? 'es'));
            } elseif ($registro->can !== 'panel') {
                throw new \RuntimeException('No hay un canal configurado para entregar la notificación.');
            }
        });
    }

    /** Evento que recibe n8n. */
    private function evento(RegistroNotificacion $registro): array
    {
        $reserva = $registro->reserva;

        return [
            'id' => $registro->id,
            'tipo' => $registro->tip,
            'asunto' => NotificacionService::TIPOS[$registro->tip] ?? $registro->tip,
            'canal' => $registro->can,
            'datos' => $registro->cnd ?? [],
            'reserva' => $reserva ? [
                'cod' => $reserva->cod,
                'contacto' => $reserva->nom_ctc,
                'correo' => $reserva->cor_ctc,
                'telefono' => $reserva->tel_ctc,
                'tour' => $reserva->salida->tour->nom,
                'fecha' => $reserva->salida->fec_sal->toDateString(),
                'hora' => substr($reserva->salida->hra_sal, 0, 5),
                'total' => (float) $reserva->pre_tot,
                'estado' => $reserva->est,
                'url' => route('reservas.consultar', ['cod' => $reserva->cod]),
            ] : null,
        ];
    }
}
