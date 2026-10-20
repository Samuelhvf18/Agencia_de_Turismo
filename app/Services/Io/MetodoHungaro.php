<?php

namespace App\Services\Io;

/**
 * Método húngaro (Kuhn–Munkres) para el problema de asignación de costo
 * mínimo, O(n³). Admite matrices rectangulares: se completan con filas o
 * columnas ficticias de costo 0, y esas asignaciones se descartan.
 *
 * min Σ c_ij · x_ij   s.a.   Σ_j x_ij ≤ 1,  Σ_i x_ij ≤ 1,  x_ij ∈ {0, 1}
 */
class MetodoHungaro
{
    /**
     * @param  array<int, array<int, float>>  $costos  matriz filas × columnas (índices 0..n-1)
     * @return array<int, int> fila => columna asignada (solo pares reales)
     */
    public function resolver(array $costos): array
    {
        $filas = count($costos);
        $columnas = $filas ? count($costos[0]) : 0;
        if ($filas === 0 || $columnas === 0) {
            return [];
        }

        $n = max($filas, $columnas);
        // Matriz cuadrada 1-indexada con ceros en las celdas ficticias.
        $a = [];
        for ($i = 1; $i <= $n; $i++) {
            for ($j = 1; $j <= $n; $j++) {
                $a[$i][$j] = ($i <= $filas && $j <= $columnas) ? (float) $costos[$i - 1][$j - 1] : 0.0;
            }
        }

        // Potenciales u (filas), v (columnas); p[j] = fila asignada a la columna j.
        $u = array_fill(0, $n + 1, 0.0);
        $v = array_fill(0, $n + 1, 0.0);
        $p = array_fill(0, $n + 1, 0);
        $camino = array_fill(0, $n + 1, 0);

        for ($i = 1; $i <= $n; $i++) {
            $p[0] = $i;
            $j0 = 0;
            $minv = array_fill(0, $n + 1, INF);
            $usada = array_fill(0, $n + 1, false);

            do {
                $usada[$j0] = true;
                $i0 = $p[$j0];
                $delta = INF;
                $j1 = 0;
                for ($j = 1; $j <= $n; $j++) {
                    if ($usada[$j]) {
                        continue;
                    }
                    $cur = $a[$i0][$j] - $u[$i0] - $v[$j];
                    if ($cur < $minv[$j]) {
                        $minv[$j] = $cur;
                        $camino[$j] = $j0;
                    }
                    if ($minv[$j] < $delta) {
                        $delta = $minv[$j];
                        $j1 = $j;
                    }
                }
                for ($j = 0; $j <= $n; $j++) {
                    if ($usada[$j]) {
                        $u[$p[$j]] += $delta;
                        $v[$j] -= $delta;
                    } else {
                        $minv[$j] -= $delta;
                    }
                }
                $j0 = $j1;
            } while ($p[$j0] !== 0);

            do {
                $j1 = $camino[$j0];
                $p[$j0] = $p[$j1];
                $j0 = $j1;
            } while ($j0 !== 0);
        }

        $asignacion = [];
        for ($j = 1; $j <= $n; $j++) {
            if ($p[$j] >= 1 && $p[$j] <= $filas && $j <= $columnas) {
                $asignacion[$p[$j] - 1] = $j - 1;
            }
        }
        ksort($asignacion);

        return $asignacion;
    }

    /** Costo total de una asignación. */
    public function costo(array $costos, array $asignacion): float
    {
        return array_sum(array_map(fn ($i, $j) => $costos[$i][$j], array_keys($asignacion), $asignacion));
    }
}
