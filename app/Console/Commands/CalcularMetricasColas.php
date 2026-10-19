<?php

namespace App\Console\Commands;

use App\Services\Io\ColasService;
use Carbon\Carbon;
use Illuminate\Console\Command;

/** Cálculo diario de las métricas M/M/c/K por tour (lo dispara el scheduler o n8n). */
class CalcularMetricasColas extends Command
{
    protected $signature = 'io:metricas-colas {--fecha= : Fecha a calcular (AAAA-MM-DD); por defecto, hoy} {--dias=1 : Cantidad de días hacia atrás a recalcular}';

    protected $description = 'Calcula y guarda en cola_metricas_diarias las métricas M/M/c/K de cada tour';

    public function handle(ColasService $colas): int
    {
        $fin = $this->option('fecha') ? Carbon::parse($this->option('fecha')) : now();
        $dias = max(1, (int) $this->option('dias'));

        for ($d = $dias - 1; $d >= 0; $d--) {
            $fecha = $fin->copy()->subDays($d);
            $n = $colas->generarMetricasDiarias($fecha);
            $this->line("{$fecha->toDateString()}: {$n} tour(s) procesados");
        }

        return self::SUCCESS;
    }
}
