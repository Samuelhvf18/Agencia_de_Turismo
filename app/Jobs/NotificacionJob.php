<?php

namespace App\Jobs;

use App\Mail\AvisoMail;
use App\Models\RegistroNotificacion;
use App\Services\Notificaciones\NotificacionService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Throwable;

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
        $registro = RegistroNotificacion::with('reserva.salida.tour')->find($this->registroId);
        if (! $registro || $registro->est === 'enviado') {
            return;
        }

        try {
            $webhook = config('services.n8n.webhook_notificaciones');
            if ($webhook) {
                Http::timeout(10)
                    ->withHeaders(['X-Pipocas-Token' => (string) config('services.n8n.token')])
                    ->post($webhook, $this->evento($registro))
                    ->throw();
            } elseif ($registro->can === 'email' && ! empty($registro->cnd['destino'])) {
                Mail::to($registro->cnd['destino'], $registro->cnd['nombre'] ?? null)->send(new AvisoMail($registro));
            }

            $registro->update(['est' => 'enviado', 'env_en' => now()]);
        } catch (Throwable $e) {
            $registro->update(['est' => 'fallido', 'cnd' => ($registro->cnd ?? []) + ['error' => mb_substr($e->getMessage(), 0, 300)]]);
            throw $e;
        }
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
