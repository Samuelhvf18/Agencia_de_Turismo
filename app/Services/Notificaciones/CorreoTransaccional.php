<?php

namespace App\Services\Notificaciones;

use App\Models\RegistroNotificacion;
use App\Models\Reserva;
use App\Services\Pagos\ComprobanteService;
use App\Services\Reservas\BoletoService;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CorreoTransaccional
{
    public function enviar(string $correo, string $asunto, string $texto, ?string $clave = null, array $adjuntos = []): void
    {
        if ($url = config('services.notificaciones.url')) {
            Http::timeout(15)->withToken((string) config('services.notificaciones.token'))
                ->withHeaders(['Idempotency-Key' => $clave ?? (string) Str::uuid()])
                ->post(rtrim($url, '/').'/api/notificaciones', [
                    'correo' => $correo, 'asunto' => $asunto, 'texto' => $texto, 'adjuntos' => $adjuntos,
                ])->throw();

            return;
        }

        Mail::raw($texto, function ($m) use ($correo, $asunto, $adjuntos) {
            $m->to($correo)->subject($asunto);
            foreach ($adjuntos as $adjunto) {
                $m->attachData(base64_decode($adjunto['contenido'], true), $adjunto['nombre'], ['mime' => 'application/pdf']);
            }
        });
    }

    public function boletos(Reserva $reserva, string $clave, RegistroNotificacion $registro): void
    {
        // Congela PDFs para reintentos idénticos.
        if ($preparado = $registro->cnd['correo_preparado'] ?? null) {
            $payload = json_decode(Crypt::decryptString($preparado), true, flags: JSON_THROW_ON_ERROR);
            $this->enviar($payload['correo'], $payload['asunto'], $payload['texto'], $clave, $payload['adjuntos']);

            return;
        }
        $adjuntos = [];
        $boletos = $reserva->boletos()->where('boletos.est', '<>', 'cancelado')->get();
        if ($boletos->isNotEmpty()) {
            $adjuntos[] = ['nombre' => "boletos-{$reserva->cod}.pdf", 'contenido' => base64_encode(
                app(BoletoService::class)->pdf($reserva, $boletos)->output())];
        }
        if ($reserva->comprobante) {
            $adjuntos[] = ['nombre' => "comprobante-{$reserva->comprobante->cod}.pdf", 'contenido' => base64_encode(
                app(ComprobanteService::class)->pdf($reserva->comprobante)->output())];
        }
        $texto = "Tu reserva {$reserva->cod} está confirmada.\nTour: {$reserva->salida->tour->nom}\nSalida: ".
            $reserva->salida->fec_sal->format('d/m/Y').' '.substr($reserva->salida->hra_sal, 0, 5).
            "\nConsulta con tu código y correo: ".route('reservas.consultar', ['cod' => $reserva->cod]);
        $payload = ['correo' => $reserva->cor_ctc, 'asunto' => "Reserva {$reserva->cod} · Andinist Bolivia", 'texto' => $texto, 'adjuntos' => $adjuntos];
        $registro->update(['cnd' => array_replace($registro->cnd ?? [], [
            'correo_preparado' => Crypt::encryptString(json_encode($payload, JSON_THROW_ON_ERROR)),
        ])]);
        $this->enviar($payload['correo'], $payload['asunto'], $payload['texto'], $clave, $adjuntos);
    }
}
