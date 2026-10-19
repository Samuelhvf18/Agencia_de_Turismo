<?php

namespace App\Services\Io;

/**
 * Arma las variables de la vista admin/io/colas a partir de los tours y del tour seleccionado.
 * Lo usan el controlador (datos reales) y la vista previa (datos de maqueta).
 */
class PanelColasBuilder
{
    /**
     * @param  array  $tours      tours con id, nom, lambda, mu, c, k, rho, wq_hrs, p_rec, estado
     * @param  array  $sel        tour seleccionado con id, nom, dur_hrs, lambda, mu, c, k, cos_esp, cos_srv, origen_lambda
     * @param  int|null  $c, $k   escenario a simular (null = sin simulación)
     * @param  array  $historial  métricas diarias: fec, lmb_obs, rho, wq_hrs, p_rec
     */
    public static function armar(ColasService $svc, array $tours, array $sel, ?int $c, ?int $k, array $historial): array
    {
        $m = $svc->metricas($sel['lambda'], $sel['mu'], $sel['c'], $sel['k']);
        $m['estado'] = $svc->estado($m);

        $simK = max(1, min(60, $k ?? $sel['k']));
        $simC = max(1, min($simK, $c ?? $sel['c']));
        $sim = null;
        if ($c !== null || $k !== null) {
            $sim = $svc->metricas($sel['lambda'], $sel['mu'], $simC, $simK);
            $sim['ct'] = $svc->costoTotal($sim, $simC, $sel['cos_esp'], $sel['cos_srv']);
        }

        return [
            'tours' => $tours,
            'sel' => $sel,
            'm' => $m,
            'escenarios' => $svc->escenarios($sel['lambda'], $sel['mu'], $simK, $sel['cos_esp'], $sel['cos_srv'], 6),
            'sim' => $sim,
            'simC' => $simC,
            'simK' => $simK,
            'historial' => $historial,
        ];
    }
}
