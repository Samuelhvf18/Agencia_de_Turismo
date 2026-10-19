<?php

namespace App\Services\Io;

/**
 * Datos de maqueta para las vistas previas de los paneles de IO II
 * (rutas /admin/io/preview/...). No consulta la base de datos: usa tours de ejemplo
 * con los parámetros iniciales del seeder y aplica las mismas fórmulas que los controladores.
 */
class IoDemoData
{
    public const TOURS = [
        ['id' => 1, 'nom' => 'Descenso Camino de la Muerte', 'dur_hrs' => 7.5, 'lambda' => 5.0, 'c' => 2, 'k' => 12],
        ['id' => 2, 'nom' => 'Glaciar Huayna Potosí', 'dur_hrs' => 8.0, 'lambda' => 2.0, 'c' => 1, 'k' => 6],
        ['id' => 3, 'nom' => 'Valle de la Luna & Teleféricos', 'dur_hrs' => 4.0, 'lambda' => 6.0, 'c' => 1, 'k' => 20],
        ['id' => 4, 'nom' => 'Tiwanaku & Puma Punku', 'dur_hrs' => 6.0, 'lambda' => 6.0, 'c' => 2, 'k' => 20],
        ['id' => 5, 'nom' => 'Camino Prehispánico El Choro', 'dur_hrs' => 7.0, 'lambda' => 2.0, 'c' => 1, 'k' => 12],
        ['id' => 6, 'nom' => 'Laguna Chiar Khota & Condoriri', 'dur_hrs' => 6.5, 'lambda' => 3.0, 'c' => 1, 'k' => 10],
    ];

    public function __construct(private ColasService $colas, private MarkovService $markov) {}

    public function colas(?int $tourId, ?int $c, ?int $k, ?float $lambda): array
    {
        $tours = [];
        foreach (self::TOURS as $t) {
            $mu = $this->colas->muDesdeDuracion($t['dur_hrs']);
            $m = $this->colas->metricas($t['lambda'], $mu, $t['c'], $t['k']);
            $tours[] = $t + ['mu' => $mu, 'rho' => $m['rho'], 'lq' => $m['lq'], 'wq_hrs' => $m['wq_hrs'], 'p_rec' => $m['p_rec'], 'estado' => $this->colas->estado($m)];
        }
        $base = collect($tours)->firstWhere('id', $tourId) ?? $tours[0];
        $sel = $base + ['cos_esp' => 15.0, 'cos_srv' => 25.0, 'origen_lambda' => 'parámetro configurado'];
        if ($lambda !== null) {
            $sel['lambda'] = $lambda;
            $sel['origen_lambda'] = 'valor simulado';
        }

        // Historial de 14 días con variación determinista alrededor de λ
        $historial = [];
        for ($d = 13; $d >= 0; $d--) {
            $l = max(0.5, $sel['lambda'] * (1 + 0.25 * sin($d * 0.9)));
            $mh = $this->colas->metricas($l, $sel['mu'], $sel['c'], $sel['k']);
            $historial[] = ['fec' => now()->subDays($d)->format('d/m'), 'lmb_obs' => round($l, 2), 'rho' => round($mh['rho'], 4), 'wq_hrs' => round($mh['wq_hrs'], 2), 'p_rec' => round($mh['p_rec'], 4)];
        }

        return PanelColasBuilder::armar($this->colas, $tours, $sel, $c, $k, $historial) + ['demo' => true,
            'avisoDemo' => 'Vista previa con tours y parámetros de ejemplo (λ, c y K del seeder); los cálculos usan las mismas fórmulas M/M/c/K del panel real.'];
    }

    public function cicloVida(?string $inicio): array
    {
        $conteos = ['prospecto' => 64, 'reservado' => 21, 'completado' => 18, 'fidelizado' => 12, 'perdido' => 25];
        $cohortes = [];
        foreach ([['Cohorte julio 2026', 40], ['Cohorte agosto 2026', 52], ['Cohorte septiembre 2026', 47], ['Cohorte octubre 2026', 31]] as $i => [$nom, $tot]) {
            $p = $this->markov->proyectar($this->markov->normalizar(MarkovService::MATRIZ_INICIAL), 'prospecto', 3 - $i);
            $filas = [];
            foreach ($p['meses'] as $n) {
                $filas[$n] = [
                    'activos' => $p['series']['prospecto'][$n] + $p['series']['reservado'][$n] + $p['series']['completado'][$n],
                    'fidelizados' => $p['series']['fidelizado'][$n],
                    'perdidos' => $p['series']['perdido'][$n],
                ];
            }
            $cohortes[] = ['nom' => $nom, 'per_cap' => null, 'tot' => $tot, 'filas' => $filas];
        }

        return PanelMarkovBuilder::armar($this->markov, $this->markov->normalizar(MarkovService::MATRIZ_INICIAL), now()->toDateString(), $conteos, $inicio, array_reverse($cohortes)) + [
            'demo' => true,
            'avisoDemo' => 'Vista previa con la matriz inicial del seeder y conteos de ejemplo; las probabilidades de absorción y la proyección se calculan con las fórmulas reales.',
        ];
    }
}
