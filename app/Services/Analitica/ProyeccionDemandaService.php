<?php

namespace App\Services\Analitica;

use App\Models\ProyeccionDemanda;
use App\Models\Tour;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Proyección de demanda por tour y mes (RF-35, objetivo 8).
 *
 * Con 4 o más meses de historial (vista_demanda_por_tour_mes, últimos 12
 * meses) ajusta una regresión lineal por mínimos cuadrados y = a + b·t; con
 * menos usa el promedio móvil de los meses disponibles. Cada fila guarda la
 * versión del modelo en ver_mdl.
 */
class ProyeccionDemandaService
{
    public const MODELO_REGRESION = 'regresion-lineal-v1';

    public const MODELO_PROMEDIO = 'promedio-movil-v1';

    public const MESES_HISTORIAL = 12;

    /** @return int filas generadas */
    public function generar(int $meses = 3, ?Carbon $referencia = null): int
    {
        $referencia = ($referencia ?? now())->copy()->startOfMonth();
        $filas = 0;

        foreach (Tour::where('act', true)->whereNull('eli_en')->pluck('id') as $turId) {
            $serie = $this->serieMensual($turId, $referencia);
            [$valores, $modelo] = $this->proyectar($serie, $meses);

            foreach ($valores as $i => $valor) {
                $mes = $referencia->copy()->addMonths($i);
                ProyeccionDemanda::updateOrCreate(
                    ['tur_id' => $turId, 'mes_per' => (int) $mes->month, 'ani_per' => (int) $mes->year],
                    ['rsv_pry' => max(0, (int) round($valor)), 'ver_mdl' => $modelo, 'gen_en' => now()]
                );
                $filas++;
            }
        }

        return $filas;
    }

    /**
     * @param  array<int, int>  $serie  reservas por mes, del más antiguo al más reciente
     * @return array{0: array<int, float>, 1: string}
     */
    public function proyectar(array $serie, int $meses): array
    {
        // Los meses previos a la primera reserva no son demanda cero: el tour aún no se vendía.
        $serie = array_values($serie);
        while ($serie && $serie[0] === 0) {
            array_shift($serie);
        }
        $n = count($serie);
        $conDatos = count(array_filter($serie, fn ($v) => $v > 0));

        if ($n >= 4 && $conDatos >= 4) {
            // Mínimos cuadrados con t = 1..n.
            $sumT = $n * ($n + 1) / 2;
            $sumT2 = $n * ($n + 1) * (2 * $n + 1) / 6;
            $sumY = array_sum($serie);
            $sumTY = 0;
            foreach ($serie as $i => $y) {
                $sumTY += ($i + 1) * $y;
            }
            $b = ($n * $sumTY - $sumT * $sumY) / ($n * $sumT2 - $sumT ** 2);
            $a = ($sumY - $b * $sumT) / $n;

            return [array_map(fn ($k) => $a + $b * ($n + $k), range(1, $meses)), self::MODELO_REGRESION];
        }

        $ultimos = array_slice(array_filter($serie, fn ($v) => $v > 0), -3);
        $promedio = $ultimos ? (float) array_sum($ultimos) / count($ultimos) : 0.0;

        return [array_fill(0, $meses, $promedio), self::MODELO_PROMEDIO];
    }

    /** Reservas (no canceladas) por mes de salida en los 12 meses previos a la referencia. */
    public function serieMensual(int $turId, Carbon $referencia): array
    {
        $serie = [];
        for ($i = self::MESES_HISTORIAL; $i >= 1; $i--) {
            $m = $referencia->copy()->subMonths($i);
            $serie[$m->format('Y-m')] = 0;
        }

        $filas = DB::table('vista_demanda_por_tour_mes')
            ->where('tur_id', $turId)
            ->whereRaw('make_date(anio, mes, 1) >= ?', [$referencia->copy()->subMonths(self::MESES_HISTORIAL)->toDateString()])
            ->whereRaw('make_date(anio, mes, 1) < ?', [$referencia->toDateString()])
            ->get(['anio', 'mes', 'reservas']);

        foreach ($filas as $f) {
            $clave = sprintf('%04d-%02d', $f->anio, $f->mes);
            if (array_key_exists($clave, $serie)) {
                $serie[$clave] = (int) $f->reservas;
            }
        }

        return $serie;
    }
}
