<?php

namespace App\Services\Notificaciones;

use App\Jobs\NotificacionJob;
use App\Models\RegistroNotificacion;
use App\Models\Reserva;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/** Registro y despacho de avisos. */
class NotificacionService
{
    /** Tipos y asuntos de avisos. */
    public const TIPOS = [
        'reserva_creada' => 'Recibimos tu reserva',
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

    public function aReserva(Reserva $reserva, string $tipo, array $datos = [], string $canal = 'email', ?string $clave = null): RegistroNotificacion
    {
        return $this->crear([
            'clave_evento' => $clave,
            'rsv_id' => $reserva->id,
            'usr_id' => $reserva->usr_id,
            'can' => $canal,
            'tip' => $tipo,
            'cnd' => $datos + ['destino' => $reserva->cor_ctc, 'nombre' => $reserva->nom_ctc, 'reserva' => $reserva->cod, 'idioma' => $reserva->idioma ?? 'es'],
        ]);
    }

    /** Avisos por panel y correo. */
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

    public function confirmacionReserva(Reserva $reserva): RegistroNotificacion
    {
        return RegistroNotificacion::firstOrCreate(['clave_evento' => 'reserva-confirmada:'.$reserva->id], [
            'rsv_id' => $reserva->id, 'usr_id' => $reserva->usr_id, 'can' => 'email',
            'tip' => 'reserva_confirmada', 'est' => 'pendiente',
            'cnd' => ['destino' => $reserva->cor_ctc, 'nombre' => $reserva->nom_ctc, 'idioma' => $reserva->idioma ?? 'es', 'adjuntos' => ['boletos', 'comprobante']],
        ]);
    }

    private function crear(array $atributos): RegistroNotificacion
    {
        $registro = ! empty($atributos['clave_evento'])
            ? RegistroNotificacion::firstOrCreate(['clave_evento' => $atributos['clave_evento']], $atributos + ['est' => 'pendiente'])
            : RegistroNotificacion::create($atributos + ['est' => 'pendiente']);
        // Evento persistente en la transacción.
        // Recuperación independiente de la cola.
        DB::afterCommit(function () use ($registro) {
            try {
                NotificacionJob::dispatch($registro->id);
            } catch (\Throwable $e) {
                report($e);
            }
        });

        return $registro;
    }
}
