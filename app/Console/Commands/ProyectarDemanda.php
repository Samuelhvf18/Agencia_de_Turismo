<?php

namespace App\Console\Commands;

use App\Services\Analitica\ProyeccionDemandaService;
use Illuminate\Console\Command;

/** Genera proyecciones_demanda para los próximos meses (RF-35); lo dispara n8n cada mes. */
class ProyectarDemanda extends Command
{
    protected $signature = 'io:proyectar-demanda {--meses=3 : Meses a proyectar desde el actual}';

    protected $description = 'Proyecta la demanda de reservas por tour (regresión lineal o promedio móvil)';

    public function handle(ProyeccionDemandaService $servicio): int
    {
        $filas = $servicio->generar(max(1, min(12, (int) $this->option('meses'))));
        $this->info("{$filas} proyección(es) guardadas en proyecciones_demanda.");

        return self::SUCCESS;
    }
}
