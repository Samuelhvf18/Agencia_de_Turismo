<?php

namespace App\Console\Commands;

use App\Models\Reserva;
use App\Services\Reservas\ReservaService;
use Illuminate\Console\Command;

/** Cancela las reservas pendientes sin ningún pago tras el plazo (libera cupos y equipo). */
class ExpirarReservas extends Command
{
    protected $signature = 'reservas:expirar {--horas=24 : Horas que se esperan antes de liberar la reserva}';

    protected $description = 'Cancela las reservas pendientes de pago que superaron el plazo';

    public function handle(ReservaService $reservas): int
    {
        $limite = now()->subHours(max(1, (int) $this->option('horas')));

        $vencidas = Reserva::where('est', Reserva::EST_PENDIENTE)
            ->where('est_pag', Reserva::PAG_PENDIENTE)
            ->where('cre_en', '<', $limite)
            ->get();

        foreach ($vencidas as $reserva) {
            $reservas->cancelar($reserva, 'Plazo de pago vencido');
        }

        $this->info("{$vencidas->count()} reserva(s) vencida(s) cancelada(s).");

        return self::SUCCESS;
    }
}
