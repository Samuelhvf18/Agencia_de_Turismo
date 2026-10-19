<?php

namespace App\Console\Commands;

use App\Services\Io\MarkovService;
use Illuminate\Console\Command;

/** Recalibración mensual de la cadena de Markov del ciclo de vida del cliente. */
class RecalibrarMarkov extends Command
{
    protected $signature = 'io:recalibrar-markov
        {--sincronizar : Deriva primero el estado de cada turista a partir de sus reservas}
        {--meses=6 : Meses de historia usados para estimar la matriz}';

    protected $description = 'Estima la matriz de transición mensual, actualiza las cohortes y muestra las probabilidades de absorción';

    public function handle(MarkovService $markov): int
    {
        if ($this->option('sincronizar')) {
            $this->info('Cambios de estado registrados: ' . $markov->sincronizarDesdeReservas());
        }

        $res = $markov->recalibrar((int) $this->option('meses'));
        $this->info("Transiciones observadas: {$res['transiciones']}");
        $this->table(['De \ A', ...MarkovService::ORDEN], collect($res['P'])->map(
            fn ($fila, $i) => [$i, ...array_map(fn ($p) => number_format($p, 4), array_values($fila))]
        )->values()->all());

        $an = $markov->analizar($res['P']);
        foreach ($an['transitorios'] as $e) {
            $this->line(sprintf('%-11s P(fidelizado) = %.3f · P(perdido) = %.3f · meses hasta absorción = %.2f',
                $e, $an['B'][$e]['fidelizado'], $an['B'][$e]['perdido'], $an['t'][$e]));
        }

        $this->info('Cohortes actualizadas: ' . $markov->construirCohortes());

        return self::SUCCESS;
    }
}
