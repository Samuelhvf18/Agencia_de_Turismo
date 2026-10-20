<?php

namespace App\Console\Commands;

use App\Models\RegistroNotificacion;
use App\Models\Reserva;
use App\Services\Notificaciones\NotificacionService;
use Illuminate\Console\Command;

/** Recordatorio del día anterior a la salida para cada reserva confirmada (una sola vez). */
class RecordatoriosSalida extends Command
{
    protected $signature = 'reservas:recordatorios {--dias=1 : Días de anticipación}';

    protected $description = 'Envía el recordatorio de salida a las reservas confirmadas';

    public function handle(NotificacionService $avisos): int
    {
        $fecha = today()->addDays(max(0, (int) $this->option('dias')))->toDateString();

        $reservas = Reserva::with('salida.tour', 'salida.guia.usuario')
            ->where('est', Reserva::EST_CONFIRMADA)
            ->whereHas('salida', fn ($q) => $q->where('fec_sal', $fecha)->where('est', 'programada'))
            ->whereNotIn('id', RegistroNotificacion::where('tip', 'recordatorio_salida')->whereNotNull('rsv_id')->select('rsv_id'))
            ->get();

        foreach ($reservas as $r) {
            $avisos->aReserva($r, 'recordatorio_salida', [
                'mensaje' => "Te esperamos el {$r->salida->fec_sal->format('d/m/Y')} a las ".substr($r->salida->hra_sal, 0, 5)
                    ." para {$r->salida->tour->nom}. Llega 15 minutos antes con tu ticket QR, ropa abrigada y agua.",
                'guia' => $r->salida->guia?->usuario?->nom,
            ], 'email');
        }

        $this->info("{$reservas->count()} recordatorio(s) programado(s) para salidas del {$fecha}.");

        return self::SUCCESS;
    }
}
