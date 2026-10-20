<?php

namespace App\Console\Commands;

use App\Services\Io\OptimizadorService;
use Illuminate\Console\Command;

/** Corre el modelo de asignación (guías o vehículos) y deja las propuestas pendientes de revisión. */
class GenerarPropuestas extends Command
{
    protected $signature = 'io:propuestas {--tipo=guia : guia | vehiculo} {--dias=14 : Horizonte en días desde hoy} {--todas : Reoptimizar también las salidas ya asignadas}';

    protected $description = 'Genera propuestas de asignación de guías (método húngaro) o vehículos (combustible) — RF-22/23';

    public function handle(OptimizadorService $optimizador): int
    {
        $tipo = $this->option('tipo');
        if (! in_array($tipo, ['guia', 'vehiculo'], true)) {
            $this->error('El tipo debe ser guia o vehiculo.');

            return self::INVALID;
        }

        $desde = today();
        $hasta = today()->addDays(max(1, (int) $this->option('dias')));
        $soloSinAsignar = ! $this->option('todas');

        $r = $tipo === 'guia'
            ? $optimizador->proponerGuias($desde, $hasta, $soloSinAsignar)
            : $optimizador->proponerVehiculos($desde, $hasta, $soloSinAsignar);

        $this->info("Lote {$r['lote']}: {$r['propuestas']} propuesta(s), {$r['sin_solucion']} salida(s) sin recurso factible.");

        return self::SUCCESS;
    }
}
