<?php

namespace App\Services\Pagos;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Pasarela de pago simulada (decisión del equipo: sin proveedor real).
 *
 * Imita a un proveedor externo: el cliente "paga" en la pantalla de la
 * pasarela y esta notifica el resultado a nuestro webhook
 * (POST /webhooks/pasarela) con una firma HMAC-SHA256 del cuerpo en la
 * cabecera X-Pasarela-Firma. El webhook es el único que cambia el estado
 * del pago, igual que con una pasarela real.
 */
class PasarelaSimulada
{
    public const CABECERA_FIRMA = 'X-Pasarela-Firma';

    public static function secreto(): string
    {
        return (string) (config('services.pasarela.secreto') ?: config('app.key'));
    }

    public static function firmar(string $cuerpo): string
    {
        return hash_hmac('sha256', $cuerpo, self::secreto());
    }

    public static function verificar(string $cuerpo, ?string $firma): bool
    {
        return $firma !== null && hash_equals(self::firmar($cuerpo), $firma);
    }

    /**
     * Envía la notificación al webhook. Con PASARELA_WEBHOOK_HTTP=true lo hace
     * por HTTP (servidor con varios workers: Nginx/PHP-FPM). En desarrollo con
     * `php artisan serve` (un solo proceso) una petición HTTP a sí mismo se
     * bloquearía, así que se entrega en el mismo proceso pasando por la misma
     * verificación de firma que usa el controlador del webhook.
     */
    public function notificar(array $evento): void
    {
        $cuerpo = json_encode($evento, JSON_UNESCAPED_UNICODE);
        $firma = self::firmar($cuerpo);

        if (config('services.pasarela.webhook_http')) {
            $respuesta = Http::withHeaders([self::CABECERA_FIRMA => $firma])
                ->withBody($cuerpo, 'application/json')
                ->timeout(10)
                ->post(route('webhooks.pasarela'));

            if (! $respuesta->successful()) {
                Log::warning('El webhook de la pasarela respondió con error', ['status' => $respuesta->status()]);
            }

            return;
        }

        if (! self::verificar($cuerpo, $firma)) {
            return;
        }
        app(PagoService::class)->procesarNotificacion(json_decode($cuerpo, true));
    }
}
