<?php

namespace App\Services\Notificaciones;

use App\Jobs\NotificacionJob;
use App\Models\RegistroNotificacion;
use App\Models\Reserva;
use App\Models\User;

/**
 * Punto único para avisar a clientes y personal (RF-38). Cada aviso queda en
 * registros_notificacion y lo despacha NotificacionJob: al webhook de n8n si
 * está configurado o, si no, por correo directo.
 */
class NotificacionService
{
    /** Tipos de aviso y su asunto por defecto. */
    public const TIPOS = [
        'reserva_confirmada' => 'Tu reserva está confirmada',
        'pago_recibido' => 'Recibimos tu pago',
        'comprobante_emitido' => 'Tu comprobante de pago',
        'recordatorio_salida' => 'Tu tour es mañana',
        'salida_reprogramada' => 'Cambio de fecha u hora de tu tour',
        'salida_cancelada' => 'Tu salida fue cancelada',
        'alerta_reposicion' => 'Alerta de reposición de equipo',
        'alerta_seguridad' => 'Alerta de seguridad',
        'derivacion_chatbot' => 'Un cliente pide hablar con una persona',
    ];

    public function aReserva(Reserva $reserva, string $tipo, array $datos = [], string $canal = 'email'): RegistroNotificacion
    {
        return $this->crear([
            'rsv_id' => $reserva->id,
            'usr_id' => $reserva->usr_id,
            'can' => $canal,
            'tip' => $tipo,
            'cnd' => $datos + ['destino' => $reserva->cor_ctc, 'nombre' => $reserva->nom_ctc, 'reserva' => $reserva->cod],
        ]);
    }

    /** Aviso al personal (admin/operaciones) por panel y correo. */
    public function aPersonal(string $tipo, array $datos, array $roles = [User::ROL_ADMIN, User::ROL_OPERACIONES]): void
    {
        $destinos = User::whereHas('rol', fn ($q) => $q->whereIn('nom', $roles))->where('act', true)->get();
        foreach ($destinos as $u) {
            $this->crear([
                'usr_id' => $u->id,
                'can' => 'email',
                'tip' => $tipo,
                'cnd' => $datos + ['destino' => $u->cor, 'nombre' => $u->nom],
            ]);
        }
    }

    private function crear(array $atributos): RegistroNotificacion
    {
        $registro = RegistroNotificacion::create($atributos + ['est' => 'pendiente']);
        NotificacionJob::dispatch($registro->id)->afterCommit();

        return $registro;
    }
}
