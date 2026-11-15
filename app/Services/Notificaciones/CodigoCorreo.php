<?php

namespace App\Services\Notificaciones;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class CodigoCorreo
{
    public function enviar(string $correo, string $proposito): void
    {
        $codigo = (string) random_int(100000, 999999);
        DB::transaction(function () use ($correo, $proposito, $codigo) {
            $anterior = DB::table('codigos_correo')->where('correo', $correo)->where('proposito', $proposito)->lockForUpdate()->first();
            if ($anterior && now()->diffInSeconds($anterior->creado_en, absolute: true) < 60) {
                throw ValidationException::withMessages(['codigo' => 'Espera un minuto antes de solicitar otro código.']);
            }
            DB::table('codigos_correo')->updateOrInsert(['correo' => $correo, 'proposito' => $proposito], [
                'hash' => Hash::make($codigo), 'vence_en' => now()->addMinutes(10), 'intentos' => 0, 'creado_en' => now(),
            ]);
        });

        $accion = $proposito === 'verificar' ? 'verificar tu cuenta' : 'restablecer tu contraseña';
        try {
            app(CorreoTransaccional::class)->enviar($correo, 'Pipocas · Código de seguridad',
                "Tu código para {$accion} es: {$codigo}\n\nVence en 10 minutos y solo se puede usar una vez. Ingresa el código en la página donde lo solicitaste. No lo compartas. Si no lo solicitaste, ignora este correo.");
        } catch (\Throwable $e) {
            DB::table('codigos_correo')->where('correo', $correo)->where('proposito', $proposito)->delete();
            report($e);
            throw ValidationException::withMessages(['codigo' => 'No se pudo enviar el correo. Inténtalo más tarde.']);
        }
    }

    /** Se consume dentro de la transacción del cambio protegido. Los fallos no revierten el contador. */
    public function comprobar(string $correo, string $proposito, string $codigo, callable $accion): bool
    {
        return DB::transaction(function () use ($correo, $proposito, $codigo, $accion) {
            $q = DB::table('codigos_correo')->where('correo', $correo)->where('proposito', $proposito);
            $fila = (clone $q)->lockForUpdate()->first();
            if (! $fila || now()->greaterThanOrEqualTo($fila->vence_en) || $fila->intentos >= 5) {
                return false;
            }
            $q->increment('intentos');
            if (! Hash::check($codigo, $fila->hash)) {
                return false;
            }
            $accion();
            $q->delete();

            return true;
        });
    }
}
