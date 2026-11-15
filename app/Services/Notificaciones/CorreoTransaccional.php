<?php

namespace App\Services\Notificaciones;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CorreoTransaccional
{
    public function enviar(string $correo, string $asunto, string $texto, ?string $clave = null): void
    {
        if ($url = config('services.notificaciones.url')) {
            Http::timeout(15)->withToken((string) config('services.notificaciones.token'))
                ->withHeaders(['Idempotency-Key' => $clave ?? (string) Str::uuid()])
                ->post(rtrim($url, '/').'/api/notificaciones', [
                    'correo' => $correo, 'asunto' => $asunto, 'texto' => $texto,
                ])->throw();

            return;
        }

        Mail::raw($texto, fn ($m) => $m->to($correo)->subject($asunto));
    }
}
