<?php

namespace App\Services\Pagos;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/** Pasarela simulada con webhook firmado. */
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

    /** Despacha por HTTP o localmente. */
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
