<?php

namespace App\Console\Commands;

use App\Services\Io\EoqService;
use App\Services\Notificaciones\NotificacionService;
use Illuminate\Console\Command;

/** Alertas de reposición EOQ. */
class AlertasEoq extends Command
{
    protected $signature = 'io:alertas-eoq';

    protected $description = 'Emite alertas de reposición del equipo crítico según el modelo EOQ';

    public function handle(EoqService $eoq, NotificacionService $avisos): int
    {
        $n = $eoq->emitirAlertas($avisos);
        $this->info("{$n} alerta(s) de reposición registradas.");

        return self::SUCCESS;
    }
}
