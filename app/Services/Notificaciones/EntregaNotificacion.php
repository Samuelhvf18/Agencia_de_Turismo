<?php

namespace App\Services\Notificaciones;

use App\Models\RegistroNotificacion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

/** Durable delivery lease, shared by queued jobs and recovery. SMTP is at-least-once. */
class EntregaNotificacion
{
    public const MAX_INTENTOS = 10;

    public function entregar(int $id, callable $enviar): void
    {
        $registro = DB::transaction(function () use ($id) {
            $r = RegistroNotificacion::lockForUpdate()->find($id);
            if (! $r || $r->est === 'enviado' || $r->intentos >= self::MAX_INTENTOS
                || $r->bloqueado_hasta?->isFuture() || $r->disponible_en?->isFuture()) {
                return null;
            }
            $r->update(['intentos' => $r->intentos + 1, 'token_entrega' => Str::random(48), 'bloqueado_hasta' => now()->addMinutes(5)]);

            return $r;
        });
        if (! $registro) {
            return;
        }
        try {
            $enviar($registro);
            RegistroNotificacion::whereKey($id)->where('token_entrega', $registro->token_entrega)
                ->update(['est' => 'enviado', 'env_en' => now(), 'bloqueado_hasta' => null, 'token_entrega' => null]);
        } catch (Throwable $e) {
            RegistroNotificacion::whereKey($id)->where('token_entrega', $registro->token_entrega)->update([
                'est' => 'fallido', 'bloqueado_hasta' => null, 'token_entrega' => null,
                'disponible_en' => now()->addSeconds(min(3600, 30 * (2 ** min(7, $registro->intentos - 1)))),
                'cnd' => json_encode(array_replace($registro->cnd ?? [], ['error' => mb_substr($e->getMessage(), 0, 300)])),
            ]);
            throw $e;
        }
    }
}
