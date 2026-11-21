<?php

namespace App\Services\Io;

use App\Models\ColaEventoLlegada;
use App\Models\ColaMetricaDiaria;
use App\Models\ColaParametroTour;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/** Modelo de colas de reservas. */
class ColasService
{
    /** Umbral de rechazo para saturación. */
    public const UMBRAL_RECHAZO = 0.05;

    /**
     * Métricas de estado estable.
     *
     * @return array{rho: float, p0: float, pn: float[], lq: float, l: float, lambda_e: float,
     *               wq_hrs: float, w_hrs: float, pq: float, p_rec: float}
     */
    public function metricas(float $lambda, float $mu, int $c, int $k): array
    {
        if (! is_finite($lambda) || ! is_finite($mu) || $lambda < 0 || $mu <= 0 || $c < 1 || $k < $c) {
            throw new \InvalidArgumentException('Parámetros inválidos: se requiere μ > 0, c ≥ 1 y K ≥ c.');
        }

        $rho = $lambda / ($c * $mu);
        // Normalización logarítmica evita desbordamientos.
        $logR = $lambda > 0 ? log($lambda) - log($mu) : -INF;
        $logTerminos = [0.0];
        for ($n = 1; $n <= $k; $n++) {
            $logTerminos[$n] = $logTerminos[$n - 1] + $logR - log(min($n, $c));
        }
        $maxLog = max($logTerminos);
        $terminos = array_map(fn ($t) => exp($t - $maxLog), $logTerminos);
        $suma = array_sum($terminos);
        $pn = array_map(fn ($t) => $t / $suma, $terminos);
        $p0 = $pn[0];

        $lq = 0.0;
        $l = 0.0;
        $pq = 0.0;
        foreach ($pn as $n => $p) {
            $l += $n * $p;
            if ($n > $c) {
                $lq += ($n - $c) * $p;
            }
            if ($n >= $c && $n < $k) {
                $pq += $p;
            }
        }

        $pRec = $pn[$k];
        $lambdaE = $lambda * (1 - $pRec);

        // Ley de Little en horas.
        $wq = $lambdaE > 0 ? $lq / $lambdaE * 24 : 0.0;
        $w = $lambdaE > 0 ? $l / $lambdaE * 24 : 0.0;

        return [
            'rho' => $rho,
            'p0' => $p0,
            'pn' => $pn,
            'lq' => $lq,
            'l' => $l,
            'lambda_e' => $lambdaE,
            'wq_hrs' => $wq,
            'w_hrs' => $w,
            'pq' => $pq,
            'p_rec' => $pRec,
        ];
    }

    /** Costo de espera y ocio. */
    public function costoTotal(array $m, int $c, float $cosEsp, float $cosSrv): float
    {
        $ocupados = $m['l'] - $m['lq'];

        return $cosEsp * $m['lq'] + $cosSrv * max(0.0, $c - $ocupados);
    }

    /** Clasificación de saturación. */
    public function estado(array $m): string
    {
        if ($m['rho'] >= 1 || $m['p_rec'] > self::UMBRAL_RECHAZO) {
            return 'saturado';
        }

        return $m['rho'] >= 0.8 ? 'atencion' : 'estable';
    }

    /**
     * Compara escenarios por costo.
     *
     * @return array<int, array{c: int, rho: float, lq: float, wq_hrs: float, p_rec: float, ct: float, optimo: bool}>
     */
    public function escenarios(float $lambda, float $mu, int $k, float $cosEsp, float $cosSrv, int $cMax = 6): array
    {
        $filas = [];
        for ($c = 1; $c <= min($cMax, $k); $c++) {
            $m = $this->metricas($lambda, $mu, $c, $k);
            $filas[] = [
                'c' => $c,
                'rho' => $m['rho'],
                'lq' => $m['lq'],
                'wq_hrs' => $m['wq_hrs'],
                'p_rec' => $m['p_rec'],
                'ct' => round($this->costoTotal($m, $c, $cosEsp, $cosSrv), 2),
                'optimo' => false,
            ];
        }
        if ($filas) {
            $min = min(array_column($filas, 'ct'));
            foreach ($filas as &$f) {
                if ($f['ct'] == $min) {
                    $f['optimo'] = true;
                    break;
                }
            }
        }

        return $filas;
    }

    /** Servicio estimado según duración. */
    public function muDesdeDuracion(float $durHrs): float
    {
        return $durHrs > 0 ? round(24 / $durHrs, 4) : 1.0;
    }

    /** Llegadas observadas por día. */
    public function lambdaObservado(int $turId, Carbon $desde, Carbon $hasta): ?float
    {
        $llegadas = ColaEventoLlegada::where('tur_id', $turId)
            ->whereIn('est', ['admitido', 'rechazado'])
            ->whereBetween('fec_evt', [$desde->copy()->startOfDay(), $hasta->copy()->endOfDay()])
            ->count();

        $dias = max(1, $desde->copy()->startOfDay()->diffInDays($hasta->copy()->startOfDay()) + 1);

        return $llegadas / $dias;
    }

    /** Servicio observado según duración real. */
    public function muObservado(int $turId, Carbon $desde, Carbon $hasta): ?float
    {
        $horas = DB::table('salidas_tour')
            ->where('tur_id', $turId)
            ->whereNotNull('hra_ini_rea')->whereNotNull('hra_fin_rea')
            ->whereBetween('fec_sal', [$desde->toDateString(), $hasta->toDateString()])
            ->selectRaw('AVG(EXTRACT(EPOCH FROM (hra_fin_rea - hra_ini_rea)) / 3600.0) AS prom')
            ->value('prom');

        return $horas && $horas > 0 ? 24 / (float) $horas : null;
    }

    /** Registra llegadas a la cola. */
    public function registrarLlegada(int $turId, ?int $salTurId, ?int $rsvId, int $cupos, string $estado = 'admitido'): ColaEventoLlegada
    {
        return ColaEventoLlegada::create([
            'tur_id' => $turId,
            'sal_tur_id' => $salTurId,
            'rsv_id' => $rsvId,
            'cnt_sol' => max(1, $cupos),
            'est' => $estado,
            'fec_evt' => now(),
        ]);
    }

    /**
     * Prioriza tasas observadas diariamente.
     *
     * @return int cantidad de tours procesados
     */
    public function generarMetricasDiarias(Carbon $fecha): int
    {
        $procesados = 0;
        foreach (ColaParametroTour::where('act', true)->get() as $par) {
            $lambda = $this->lambdaObservado($par->tur_id, $fecha->copy()->subDays(29), $fecha);
            $mu = (float) $par->mu;
            $m = $this->metricas($lambda, $mu, (int) $par->c_srv, (int) $par->k_cap);

            ColaMetricaDiaria::updateOrCreate(
                ['tur_id' => $par->tur_id, 'fec' => $fecha->toDateString()],
                [
                    'contexto' => [
                        'modelo' => 'MMcK-log-v2', 'unidad_llegada' => 'solicitud',
                        'desde' => $fecha->copy()->subDays(29)->toDateString(), 'hasta' => $fecha->toDateString(),
                        'fuente_lambda' => 'cola_eventos_llegada:admitido+rechazado',
                        'fuente_mu' => 'configurado', 'parametro_id' => $par->id,
                        'lambda' => $lambda, 'mu' => $mu, 'c' => (int) $par->c_srv, 'k' => (int) $par->k_cap,
                        'ce' => (float) $par->cos_esp, 'cs' => (float) $par->cos_srv, 'calculado_en' => now()->toIso8601String(),
                    ],
                    'lmb_obs' => round($lambda, 4),
                    'mu_obs' => round($mu, 4),
                    'rho' => round(min($m['rho'], 99.9999), 4),
                    'p0' => round($m['p0'], 4),
                    'lq' => round($m['lq'], 4),
                    'l_sis' => round($m['l'], 4),
                    'wq_hrs' => round($m['wq_hrs'], 4),
                    'w_hrs' => round($m['w_hrs'], 4),
                    'pq' => round($m['pq'], 4),
                    'p_rec' => round($m['p_rec'], 4),
                ]
            );
            $procesados++;
        }

        return $procesados;
    }
}
