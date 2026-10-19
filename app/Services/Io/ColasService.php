<?php

namespace App\Services\Io;

use App\Models\ColaEventoLlegada;
use App\Models\ColaMetricaDiaria;
use App\Models\ColaParametroTour;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Módulo 07 — Teoría de colas M/M/c/K aplicada al motor de reservas.
 *
 *  λ = tasa de llegada de solicitudes de reserva (reservas/día)
 *  μ = tasa de servicio de un guía (tours/día) = 24 / dur_hrs
 *  c = servidores (guías disponibles) · K = capacidad del sistema (cupos por salida)
 */
class ColasService
{
    /** Umbral de probabilidad de rechazo a partir del cual el tour se considera saturado. */
    public const UMBRAL_RECHAZO = 0.05;

    /**
     * Métricas de estado estable del modelo M/M/c/K.
     *
     * Pn = (r^n / n!)·P0 para n ≤ c;  Pn = r^n / (c!·c^(n−c))·P0 para c < n ≤ K,  con r = λ/μ.
     * Los términos se calculan de forma recursiva (t_n = t_{n−1}·r / min(n, c)) para evitar factoriales grandes.
     *
     * @return array{rho: float, p0: float, pn: float[], lq: float, l: float, lambda_e: float,
     *               wq_hrs: float, w_hrs: float, pq: float, p_rec: float}
     */
    public function metricas(float $lambda, float $mu, int $c, int $k): array
    {
        if ($mu <= 0 || $c < 1 || $k < $c) {
            throw new \InvalidArgumentException('Parámetros inválidos: se requiere μ > 0, c ≥ 1 y K ≥ c.');
        }

        $r = $lambda / $mu;
        $rho = $lambda / ($c * $mu);

        $terminos = [1.0];
        for ($n = 1; $n <= $k; $n++) {
            $terminos[$n] = $terminos[$n - 1] * $r / min($n, $c);
        }
        $p0 = 1 / array_sum($terminos);
        $pn = array_map(fn ($t) => $t * $p0, $terminos);

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

        // Ley de Little con la tasa efectiva; λ en reservas/día → tiempos en horas.
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

    /**
     * Costo total esperado por hora: CT = Ce·Lq + Cs·(c − (L − Lq)).
     * L − Lq es el número promedio de guías ocupados; c − (L − Lq), el de guías ociosos.
     */
    public function costoTotal(array $m, int $c, float $cosEsp, float $cosSrv): float
    {
        $ocupados = $m['l'] - $m['lq'];

        return $cosEsp * $m['lq'] + $cosSrv * max(0.0, $c - $ocupados);
    }

    /** Clasificación para el panel: saturado (ρ ≥ 1 o P(rechazo) > 5 %), atención (ρ ≥ 0,8) o estable. */
    public function estado(array $m): string
    {
        if ($m['rho'] >= 1 || $m['p_rec'] > self::UMBRAL_RECHAZO) {
            return 'saturado';
        }

        return $m['rho'] >= 0.8 ? 'atencion' : 'estable';
    }

    /**
     * Escenarios c = 1..cMax con su costo total; marca el de menor costo.
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

    /** μ inicial a partir de la duración del tour: un guía atiende 24/dur_hrs tours por día. */
    public function muDesdeDuracion(float $durHrs): float
    {
        return $durHrs > 0 ? round(24 / $durHrs, 4) : 1.0;
    }

    /**
     * λ observado: llegadas (admitidas + rechazadas) por día en el intervalo, a partir de cola_eventos_llegada.
     * Devuelve null si no hay eventos registrados.
     */
    public function lambdaObservado(int $turId, Carbon $desde, Carbon $hasta): ?float
    {
        $llegadas = ColaEventoLlegada::where('tur_id', $turId)
            ->whereIn('est', ['admitido', 'rechazado'])
            ->whereBetween('fec_evt', [$desde->copy()->startOfDay(), $hasta->copy()->endOfDay()])
            ->count();

        if ($llegadas === 0) {
            return null;
        }
        $dias = max(1, $desde->copy()->startOfDay()->diffInDays($hasta->copy()->startOfDay()) + 1);

        return $llegadas / $dias;
    }

    /**
     * μ observado: 24 / duración real promedio de las salidas completadas del tour (hra_ini_rea → hra_fin_rea).
     * Devuelve null si no hay salidas con hora real de inicio y fin.
     */
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

    /**
     * Registra una llegada al sistema de colas. Debe llamarse desde el motor de reservas
     * al confirmar (admitido), rechazar por falta de cupo (rechazado) o cancelar una solicitud.
     */
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
     * Cálculo diario (lo ejecuta el planificador / n8n): guarda una fila por tour activo en cola_metricas_diarias.
     * Usa λ y μ observados cuando existen datos y, si no, los parámetros configurados.
     *
     * @return int cantidad de tours procesados
     */
    public function generarMetricasDiarias(Carbon $fecha): int
    {
        $procesados = 0;
        foreach (ColaParametroTour::where('act', true)->get() as $par) {
            $lambda = $this->lambdaObservado($par->tur_id, $fecha, $fecha) ?? (float) $par->lmb;
            $mu = $this->muObservado($par->tur_id, $fecha->copy()->subDays(30), $fecha) ?? (float) $par->mu;
            $m = $this->metricas($lambda, $mu, (int) $par->c_srv, (int) $par->k_cap);

            ColaMetricaDiaria::updateOrCreate(
                ['tur_id' => $par->tur_id, 'fec' => $fecha->toDateString()],
                [
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
