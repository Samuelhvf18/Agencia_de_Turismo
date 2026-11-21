<?php

namespace App\Services\Io;

/** Variables del panel de colas. */
class PanelColasBuilder
{
    /**
     * @param  array  $tours  Métricas por tour.
     * @param  array  $sel  Tour seleccionado con sus parámetros.
     * @param  int|null  $c,  $k   Escenario opcional de simulación.
     * @param  array  $historial  Historial de métricas diarias.
     */
    public static function armar(ColasService $svc, array $tours, array $sel, ?int $c, ?int $k, array $historial): array
    {
        $m = $svc->metricas($sel['lambda'], $sel['mu'], $sel['c'], $sel['k']);
        $m['estado'] = $svc->estado($m);

        $simK = max(1, min(200, $k ?? $sel['k']));
        $simC = max(1, min(60, $simK, $c ?? $sel['c']));
        $simLambda = $sel['lambda_simulada'] ?? $sel['lambda'];
        $sim = null;
        if ($c !== null || $k !== null || isset($sel['lambda_simulada'])) {
            $sim = $svc->metricas($simLambda, $sel['mu'], $simC, $simK);
            $sim['ct'] = $svc->costoTotal($sim, $simC, $sel['cos_esp'], $sel['cos_srv']);
        }

        return [
            'tours' => $tours,
            'sel' => $sel,
            'm' => $m,
            'costoActual' => $svc->costoTotal($m, $sel['c'], $sel['cos_esp'], $sel['cos_srv']),
            'simLambda' => $simLambda,
            'escenarios' => $svc->escenarios($simLambda, $sel['mu'], $simK, $sel['cos_esp'], $sel['cos_srv'], 6),
            'sim' => $sim,
            'simC' => $simC,
            'simK' => $simK,
            'historial' => $historial,
        ];
    }
}
