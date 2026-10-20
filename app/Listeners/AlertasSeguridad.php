<?php

namespace App\Listeners;

use App\Models\User;
use App\Services\Notificaciones\NotificacionService;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;

/**
 * Alertas de seguridad al administrador (RNF-04): cuenta nueva, tres
 * intentos fallidos de inicio de sesión y cambio de contraseña.
 */
class AlertasSeguridad
{
    public const INTENTOS = 3;

    public const VENTANA_MINUTOS = 15;

    public function __construct(private NotificacionService $avisos) {}

    public function registrada(Registered $evento): void
    {
        $this->alertar('Cuenta nueva', "Se registró la cuenta {$evento->user->cor} ({$evento->user->nom}).", $evento->user);
    }

    public function fallido(Failed $evento): void
    {
        $correo = mb_strtolower((string) ($evento->credentials['login'] ?? $evento->credentials['cor'] ?? $evento->credentials['email'] ?? ''));
        if ($correo === '') {
            return;
        }

        $clave = 'login-fallidos:'.sha1($correo);
        Cache::add($clave, 0, now()->addMinutes(self::VENTANA_MINUTOS));
        $intentos = Cache::increment($clave);

        if ($intentos === self::INTENTOS) {
            $this->alertar('Intentos fallidos de inicio de sesión', self::INTENTOS." intentos fallidos para {$correo} en menos de ".self::VENTANA_MINUTOS.' minutos.', $evento->user);
        }
    }

    public function contrasenaRestablecida(PasswordReset $evento): void
    {
        $this->alertar('Contraseña restablecida', "Se restableció la contraseña de {$evento->user->cor}.", $evento->user);
    }

    /** Lo llama UpdateUserPassword (cambio desde el perfil). */
    public function contrasenaCambiada(User $usuario): void
    {
        $this->alertar('Contraseña cambiada', "{$usuario->cor} cambió su contraseña desde su perfil.", $usuario);
    }

    private function alertar(string $evento, string $mensaje, $usuario = null): void
    {
        $this->avisos->aPersonal('alerta_seguridad', [
            'evento' => $evento,
            'mensaje' => $mensaje,
            'usuario' => $usuario?->cor,
            'ip' => Request::ip(),
            'fecha' => now()->format('d/m/Y H:i'),
        ], [User::ROL_ADMIN]);
    }
}
