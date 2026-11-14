<?php

namespace App\Services\Io;

use App\Models\RegistroNotificacion;
use App\Models\TipoEquipo;
use App\Services\Notificaciones\NotificacionService;
use Illuminate\Support\Facades\DB;

/**
 * Inventario del equipo crítico de alquiler (Módulo 07, RF-25 a RF-27).
 *
 *   Q* = √(2·D·S / H)        cantidad económica de pedido
 *   R  = d·L + SS            punto de reorden, con d = D / 365
 *   SS = z·σ_d·√L            stock de seguridad (z = 1,65 → 95 % de servicio)
 *   CT = (D/Q)·S + (Q/2)·H   costo total anual de inventario
 *
 * D sale de vista_demanda_equipo_anual (unidades alquiladas en reservas
 * confirmadas o completadas en los año calendario actual); S = tipos_equipo.cos_ord
 * y H = tipos_equipo.cos_mnt_anu.
 */
class EoqService
{
    public const Z_SERVICIO = 1.65;

    public const LEAD_TIME_DIAS = 7;

    /** Meses con alquileres necesarios para considerar suficiente el historial. */
    public const MESES_MINIMOS = 3;

    /** Fórmulas puras (también las usan las pruebas). */
    public function calcular(float $demandaAnual, float $costoPedido, float $costoMantener, float $sigmaDiaria = 0.0, int $leadTime = self::LEAD_TIME_DIAS): array
    {
        if ($demandaAnual <= 0 || $costoPedido <= 0 || $costoMantener <= 0) {
            throw new \InvalidArgumentException('D, S y H deben ser mayores que cero.');
        }

        $q = sqrt(2 * $demandaAnual * $costoPedido / $costoMantener);
        $d = $demandaAnual / 365;
        $ss = self::Z_SERVICIO * $sigmaDiaria * sqrt($leadTime);
        $r = $d * $leadTime + $ss;

        return [
            'q' => $q,
            'q_redondeado' => (int) ceil($q),
            'd_diaria' => $d,
            'ss' => $ss,
            'r' => $r,
            'r_redondeado' => (int) ceil($r),
            'pedidos_anuales' => $demandaAnual / $q,
            'ciclo_dias' => 365 * $q / $demandaAnual,
            'costo_total' => ($demandaAnual / $q) * $costoPedido + ($q / 2) * $costoMantener,
        ];
    }

    /** Análisis de cada tipo de equipo con su stock y el aviso de historial insuficiente. */
    public function analizar(): array
    {
        $resultado = [];
        foreach (TipoEquipo::with('items')->orderByDesc('eqp_cri')->orderBy('nom')->get() as $tipo) {
            $historial = $this->demandaMensual($tipo->id);
            $meses = count(array_filter($historial));
            $demanda = $this->demandaAnualVista($tipo->id, (int) now()->year);
            $stock = (int) $tipo->items->sum('cnt_dis');
            $fila = [
                'tipo' => $tipo,
                'stock_disponible' => $stock,
                'stock_total' => (int) $tipo->items->sum('cnt_tot'),
                'demanda_anual' => $demanda,
                'meses_con_datos' => $meses,
                'historial' => $historial,
                'aviso' => null,
                'eoq' => null,
                'reponer' => false,
            ];

            if (! $tipo->cos_ord || ! $tipo->cos_mnt_anu) {
                $fila['aviso'] = 'Faltan los costos S (pedido) y/o H (mantenimiento anual) en el tipo de equipo.';
            } elseif ($meses < self::MESES_MINIMOS || $demanda <= 0) {
                $fila['aviso'] = 'Historial insuficiente: se necesitan alquileres en al menos '.self::MESES_MINIMOS.' meses para estimar D.';
            } else {
                $sigmaDiaria = $this->desviacion(array_map(fn ($m) => $m / 30.4, $historial));
                $fila['eoq'] = $this->calcular($demanda, (float) $tipo->cos_ord, (float) $tipo->cos_mnt_anu, $sigmaDiaria);
                $fila['reponer'] = $stock <= $fila['eoq']['r'];
            }

            $resultado[] = $fila;
        }

        return $resultado;
    }

    /**
     * Revisa el equipo crítico y registra una alerta de reposición (una por
     * tipo y día) cuando el stock disponible cae al punto de reorden (RF-27).
     *
     * @return int alertas nuevas
     */
    public function emitirAlertas(NotificacionService $avisos): int
    {
        $nuevas = 0;
        foreach ($this->analizar() as $fila) {
            if (! $fila['tipo']->eqp_cri || ! $fila['reponer']) {
                continue;
            }
            $yaAvisado = RegistroNotificacion::where('tip', 'alerta_reposicion')
                ->whereDate('env_en', today())
                ->where('cnd->tip_eqp_id', $fila['tipo']->id)
                ->exists()
                || RegistroNotificacion::where('tip', 'alerta_reposicion')->where('est', 'pendiente')->where('cnd->tip_eqp_id', $fila['tipo']->id)->exists();
            if ($yaAvisado) {
                continue;
            }

            $avisos->aPersonal('alerta_reposicion', [
                'tip_eqp_id' => $fila['tipo']->id,
                'equipo' => $fila['tipo']->nom,
                'stock_disponible' => $fila['stock_disponible'],
                'punto_reorden' => $fila['eoq']['r_redondeado'],
                'cantidad_a_pedir' => $fila['eoq']['q_redondeado'],
                'mensaje' => "El stock de {$fila['tipo']->nom} ({$fila['stock_disponible']} u.) llegó al punto de reorden R = {$fila['eoq']['r_redondeado']}. Se sugiere pedir Q* = {$fila['eoq']['q_redondeado']} unidades.",
            ], ['admin', 'operaciones']);
            $nuevas++;
        }

        return $nuevas;
    }

    /** Unidades alquiladas por mes en los año calendario actual: ['2026-01' => n, ...]. */
    public function demandaMensual(int $tipEqpId): array
    {
        $meses = [];
        for ($i = 11; $i >= 0; $i--) {
            $meses[now()->startOfYear()->addMonths(11 - $i)->format('Y-m')] = 0;
        }

        // vista_demanda_equipo_anual agrupa por año; para σ se baja al mes con la misma lógica.
        $filas = DB::table('alquileres_equipo_reserva as a')
            ->join('items_equipo as ie', 'ie.id', '=', 'a.itm_eqp_id')
            ->join('participantes_reserva as pr', 'pr.id', '=', 'a.ptc_rsv_id')
            ->join('reservas as r', 'r.id', '=', 'pr.rsv_id')
            ->join('salidas_tour as st', 'st.id', '=', 'r.sal_tur_id')
            ->where('ie.tip_eqp_id', $tipEqpId)
            ->whereIn('r.est', ['confirmada', 'completada'])
            ->where('st.fec_sal', '>=', now()->startOfYear()->toDateString())
            ->selectRaw("to_char(st.fec_sal, 'YYYY-MM') AS mes, COUNT(a.id) AS n")
            ->groupBy('mes')
            ->pluck('n', 'mes');

        foreach ($filas as $mes => $n) {
            if (array_key_exists($mes, $meses)) {
                $meses[$mes] = (int) $n;
            }
        }

        return $meses;
    }

    /** Demanda anual (D) según vista_demanda_equipo_anual para el año indicado. */
    public function demandaAnualVista(int $tipEqpId, int $anio): int
    {
        return (int) DB::table('vista_demanda_equipo_anual')->where('tip_eqp_id', $tipEqpId)->where('anio', $anio)->value('unidades_alquiladas');
    }

    private function desviacion(array $valores): float
    {
        $n = count($valores);
        if ($n < 2) {
            return 0.0;
        }
        $media = array_sum($valores) / $n;

        return sqrt(array_sum(array_map(fn ($v) => ($v - $media) ** 2, $valores)) / ($n - 1));
    }
}
