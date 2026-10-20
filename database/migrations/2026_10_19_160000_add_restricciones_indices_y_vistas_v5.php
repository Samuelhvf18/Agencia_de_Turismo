<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Esquema físico v5 (Documentacion actualizada/03_Base_de_Datos/schema_postgresql_v5.sql):
 * restricciones CHECK de dominio, índices de apoyo y las 7 vistas que usan
 * los modelos de IO II y los dashboards.
 */
return new class extends Migration
{
    /** tabla => [nombre => expresión] */
    private const CHECKS = [
        'users' => [
            'chk_users_tip_doc' => "tip_doc IS NULL OR tip_doc IN ('CI', 'PASAPORTE')",
            'chk_users_ci_dep' => "tip_doc = 'CI' OR ci_dep_id IS NULL",
        ],
        'perfiles_guia' => [
            'chk_prf_gui_ani_exp' => 'ani_exp >= 0',
            'chk_prf_gui_lim_hrs' => 'lim_hrs_sem BETWEEN 1 AND 60',
        ],
        'tours' => [
            'chk_tours_niv_dif' => "niv_dif IN ('facil', 'moderado', 'dificil', 'experto')",
            'chk_tours_duracion_un_dia' => 'dur_hrs BETWEEN 1 AND 14',
            'chk_tours_pre_bas' => 'pre_bas >= 0',
            'chk_tours_cap_max' => 'cap_max > 0',
            'chk_tours_altitudes' => 'alt_min_m IS NULL OR alt_max_m IS NULL OR alt_max_m >= alt_min_m',
        ],
        'puntos_ruta' => [
            'chk_pun_rta_tip' => "tip_pun IN ('inicio', 'checkpoint', 'mirador', 'fin')",
            'chk_pun_rta_lat' => 'lat BETWEEN -90 AND 90',
            'chk_pun_rta_lon' => 'lon BETWEEN -180 AND 180',
        ],
        'vehiculos' => [
            'chk_veh_tip' => "tip IN ('minibus', 'van', 'otro')",
            'chk_veh_cap_asi' => 'cap_asi > 0',
            'chk_veh_est' => "est IN ('activo', 'mantenimiento', 'inactivo')",
        ],
        'tipos_equipo' => [
            'chk_tip_eqp_costos' => '(cos_ord IS NULL OR cos_ord > 0) AND (cos_mnt_anu IS NULL OR cos_mnt_anu > 0)',
        ],
        'items_equipo' => [
            'chk_itm_eqp_cantidades' => 'cnt_tot >= 0 AND cnt_dis BETWEEN 0 AND cnt_tot',
        ],
        'salidas_tour' => [
            'chk_sal_tur_est' => "est IN ('programada', 'en_curso', 'completada', 'cancelada')",
            'chk_sal_tur_cap_max' => 'cap_max > 0',
            'chk_sal_tur_hra_real' => 'hra_fin_rea IS NULL OR hra_ini_rea IS NULL OR hra_fin_rea >= hra_ini_rea',
        ],
        'bitacora_viajes_vehiculo' => [
            'chk_btc_via_valores' => '(dst_km IS NULL OR dst_km >= 0) AND (cos_cmb IS NULL OR cos_cmb >= 0)',
        ],
        'reservas' => [
            'chk_rsv_cnt_ptc' => 'cnt_ptc > 0',
            'chk_rsv_pre_tot' => 'pre_tot >= 0',
            'chk_rsv_est' => "est IN ('pendiente', 'confirmada', 'cancelada', 'completada')",
            'chk_rsv_est_pag' => "est_pag IN ('pendiente', 'parcial', 'pagado', 'reembolsado')",
        ],
        'participantes_reserva' => [
            'chk_ptc_tip_doc' => "tip_doc IS NULL OR tip_doc IN ('CI', 'PASAPORTE')",
            'chk_ptc_ci_dep' => "tip_doc = 'CI' OR ci_dep_id IS NULL",
            'chk_ptc_eda' => 'eda IS NULL OR eda BETWEEN 0 AND 110',
        ],
        'alquileres_equipo_reserva' => [
            'chk_alq_pre' => 'pre >= 0',
        ],
        'boletos' => [
            'chk_bol_est' => "est IN ('emitido', 'validado', 'cancelado')",
        ],
        'comprobantes_pago' => [
            'chk_cmp_pag_montos' => 'sub >= 0 AND imp >= 0 AND mto_tot = sub + imp',
            'chk_cmp_pag_est' => "est IN ('pendiente', 'pagado', 'anulado')",
        ],
        'pagos' => [
            'chk_pag_mtd' => "mtd IN ('tarjeta', 'qr', 'transferencia', 'efectivo')",
            'chk_pag_mto' => 'mto > 0',
            'chk_pag_est' => "est IN ('completado', 'fallido', 'pendiente')",
        ],
        'resenas' => [
            'chk_res_cal' => 'cal BETWEEN 1 AND 5',
        ],
        'conversaciones_chatbot' => [
            'chk_cnv_can' => "can IN ('web', 'whatsapp')",
        ],
        'mensajes_chatbot' => [
            'chk_msj_rem' => "rem IN ('usuario', 'bot')",
        ],
        'bitacora_auditoria' => [
            'chk_btc_aud_acc' => "acc IN ('CREATE', 'UPDATE', 'DELETE', 'READ')",
        ],
        'registros_notificacion' => [
            'chk_reg_not_can' => "can IN ('whatsapp', 'email', 'sms', 'panel')",
            'chk_reg_not_est' => "est IN ('enviado', 'fallido', 'pendiente')",
        ],
        'proyecciones_demanda' => [
            'chk_pry_mes' => 'mes_per BETWEEN 1 AND 12',
            'chk_pry_rsv' => 'rsv_pry >= 0',
        ],
        'cola_parametros_tour' => [
            'chk_cola_par_tasas' => 'lmb >= 0 AND mu > 0',
            'chk_cola_par_c_k' => 'c_srv >= 1 AND k_cap >= c_srv',
            'chk_cola_par_costos' => '(cos_esp IS NULL OR cos_esp >= 0) AND (cos_srv IS NULL OR cos_srv >= 0)',
        ],
        'cola_eventos_llegada' => [
            'chk_cola_evt_cnt' => 'cnt_sol > 0',
            'chk_cola_evt_est' => "est IN ('admitido', 'rechazado', 'cancelado')",
        ],
        'cola_metricas_diarias' => [
            'chk_cola_met_prob' => '(p0 IS NULL OR p0 BETWEEN 0 AND 1) AND (pq IS NULL OR pq BETWEEN 0 AND 1) AND (p_rec IS NULL OR p_rec BETWEEN 0 AND 1)',
            'chk_cola_met_pos' => '(lq IS NULL OR lq >= 0) AND (l_sis IS NULL OR l_sis >= 0) AND (wq_hrs IS NULL OR wq_hrs >= 0) AND (w_hrs IS NULL OR w_hrs >= 0)',
        ],
        'markov_estados_cliente' => [
            'chk_mkv_est_cod' => "cod IN ('prospecto', 'reservado', 'completado', 'fidelizado', 'perdido')",
            'chk_mkv_est_col' => "col_hex IS NULL OR col_hex ~ '^#[0-9A-Fa-f]{6}$'",
        ],
        'markov_transiciones' => [
            'chk_mkv_trn_prb' => 'prb BETWEEN 0 AND 1',
            'chk_mkv_trn_per' => "per IN ('mensual', 'trimestral')",
        ],
        'markov_cliente_estado' => [
            'chk_mkv_cli_cambio' => 'est_ant_id IS NULL OR est_ant_id <> est_id',
        ],
        'markov_cohorts' => [
            'chk_mkv_coh_tot' => 'tot_cli >= 0',
            'chk_mkv_coh_per' => 'EXTRACT(DAY FROM per_cap) = 1',
        ],
        'markov_cohort_distribucion' => [
            'chk_mkv_dis_val' => 'per_rel >= 0 AND cnt >= 0',
        ],
    ];

    /** nombre => definición */
    private const INDICES = [
        'idx_tours_cat_act' => 'tours (cat_id, act)',
        'idx_tours_trz_geo' => 'tours USING GIN (trz_geo)',
        'idx_salidas_fecha' => 'salidas_tour (fec_sal, hra_sal)',
        'idx_salidas_guia' => 'salidas_tour (prf_gui_id, fec_sal)',
        'idx_salidas_vehiculo' => 'salidas_tour (veh_id, fec_sal)',
        'idx_reservas_salida' => 'reservas (sal_tur_id, est)',
        'idx_reservas_usuario' => 'reservas (usr_id)',
        'idx_participantes_reserva' => 'participantes_reserva (rsv_id)',
        'idx_pagos_comprobante' => 'pagos (cmp_pag_id)',
        'idx_bitacora_tabla_registro' => 'bitacora_auditoria (nom_tbl, id_reg)',
        'idx_bitacora_fecha' => 'bitacora_auditoria (cre_en)',
        'idx_notificaciones_reserva' => 'registros_notificacion (rsv_id)',
        'idx_mensajes_conversacion' => 'mensajes_chatbot (cnv_id, cre_en)',
    ];

    private const VISTAS = [
        // Horas acumuladas por guía y semana (asignación de guías, RF-22)
        'vista_carga_horaria_guia' => <<<'SQL'
            SELECT pg.id                                   AS prf_gui_id,
                   u.nom                                   AS guia,
                   pg.ani_exp,
                   pg.lim_hrs_sem,
                   date_trunc('week', st.fec_sal)::date    AS semana,
                   COALESCE(SUM(t.dur_hrs), 0)             AS horas_asignadas,
                   pg.lim_hrs_sem - COALESCE(SUM(t.dur_hrs), 0) AS horas_disponibles
            FROM perfiles_guia pg
            JOIN users u              ON u.id = pg.usr_id
            LEFT JOIN salidas_tour st ON st.prf_gui_id = pg.id AND st.est <> 'cancelada'
            LEFT JOIN tours t         ON t.id = st.tur_id
            WHERE pg.act
            GROUP BY pg.id, u.nom, pg.ani_exp, pg.lim_hrs_sem, date_trunc('week', st.fec_sal)
            SQL,

        // Ocupación de asientos y costo de combustible por salida (asignación de vehículos, RF-23)
        'vista_ocupacion_transporte' => <<<'SQL'
            SELECT st.id                                     AS sal_tur_id,
                   st.fec_sal,
                   v.id                                      AS veh_id,
                   v.pla,
                   v.cap_asi,
                   COALESCE(SUM(r.cnt_ptc) FILTER (WHERE r.est IN ('confirmada', 'completada')), 0) AS pasajeros,
                   ROUND(COALESCE(SUM(r.cnt_ptc) FILTER (WHERE r.est IN ('confirmada', 'completada')), 0)::numeric
                         / NULLIF(v.cap_asi, 0) * 100, 2)    AS porcentaje_ocupacion,
                   MAX(b.dst_km)                             AS dst_km,
                   MAX(b.cos_cmb)                            AS cos_cmb
            FROM salidas_tour st
            JOIN vehiculos v                     ON v.id = st.veh_id
            LEFT JOIN reservas r                 ON r.sal_tur_id = st.id
            LEFT JOIN bitacora_viajes_vehiculo b ON b.sal_tur_id = st.id
            GROUP BY st.id, st.fec_sal, v.id, v.pla, v.cap_asi
            SQL,

        // Rutas más solicitadas por temporada (dashboard, RF-33)
        'vista_demanda_por_tour_mes' => <<<'SQL'
            SELECT t.id AS tur_id, t.nom AS tour,
                   EXTRACT(YEAR  FROM st.fec_sal)::smallint AS anio,
                   EXTRACT(MONTH FROM st.fec_sal)::smallint AS mes,
                   COUNT(r.id)                              AS reservas,
                   COALESCE(SUM(r.cnt_ptc), 0)              AS participantes
            FROM tours t
            JOIN salidas_tour st ON st.tur_id = t.id
            JOIN reservas r      ON r.sal_tur_id = st.id AND r.est <> 'cancelada'
            GROUP BY t.id, t.nom, EXTRACT(YEAR FROM st.fec_sal), EXTRACT(MONTH FROM st.fec_sal)
            SQL,

        // Nacionalidad de los turistas (dashboard, RF-34)
        'vista_turistas_por_nacionalidad' => <<<'SQL'
            SELECT COALESCE(p.nom, 'Sin dato') AS pais,
                   p.cod_iso,
                   COUNT(pr.id)                AS turistas
            FROM participantes_reserva pr
            JOIN reservas r    ON r.id = pr.rsv_id AND r.est <> 'cancelada'
            LEFT JOIN paises p ON p.id = pr.pai_id
            GROUP BY p.nom, p.cod_iso
            SQL,

        // Calificación promedio por tour
        'vista_calificacion_promedio_tour' => <<<'SQL'
            SELECT t.id AS tur_id, t.nom AS tour,
                   ROUND(AVG(re.cal)::numeric, 2) AS calificacion_promedio,
                   COUNT(re.id)                   AS cantidad_resenas
            FROM tours t
            LEFT JOIN resenas re ON re.tur_id = t.id
            GROUP BY t.id, t.nom
            SQL,

        // Demanda anual por tipo de equipo (parámetro D del modelo EOQ, RF-26)
        'vista_demanda_equipo_anual' => <<<'SQL'
            SELECT te.id AS tip_eqp_id, te.nom AS tipo_equipo, te.eqp_cri,
                   te.cos_ord, te.cos_mnt_anu,
                   EXTRACT(YEAR FROM st.fec_sal)::smallint AS anio,
                   COUNT(a.id)                             AS unidades_alquiladas
            FROM tipos_equipo te
            JOIN items_equipo ie             ON ie.tip_eqp_id = te.id
            JOIN alquileres_equipo_reserva a ON a.itm_eqp_id = ie.id
            JOIN participantes_reserva pr    ON pr.id = a.ptc_rsv_id
            JOIN reservas r                  ON r.id = pr.rsv_id AND r.est IN ('confirmada', 'completada')
            JOIN salidas_tour st             ON st.id = r.sal_tur_id
            GROUP BY te.id, te.nom, te.eqp_cri, te.cos_ord, te.cos_mnt_anu, EXTRACT(YEAR FROM st.fec_sal)
            SQL,

        // Llegadas observadas por tour y día (insumo de λ observado, RF-41)
        'vista_llegadas_diarias_tour' => <<<'SQL'
            SELECT e.tur_id,
                   t.nom                                       AS tour,
                   e.fec_evt::date                             AS fec,
                   COUNT(*)                                    AS llegadas,
                   COUNT(*) FILTER (WHERE e.est = 'admitido')  AS admitidas,
                   COUNT(*) FILTER (WHERE e.est = 'rechazado') AS rechazadas,
                   SUM(e.cnt_sol)                              AS cupos_solicitados
            FROM cola_eventos_llegada e
            JOIN tours t ON t.id = e.tur_id
            GROUP BY e.tur_id, t.nom, e.fec_evt::date
            SQL,
    ];

    public function up(): void
    {
        foreach (self::CHECKS as $tabla => $checks) {
            foreach ($checks as $nombre => $expresion) {
                DB::statement("ALTER TABLE {$tabla} ADD CONSTRAINT {$nombre} CHECK ({$expresion})");
            }
        }

        foreach (self::INDICES as $nombre => $definicion) {
            DB::statement("CREATE INDEX IF NOT EXISTS {$nombre} ON {$definicion}");
        }

        foreach (self::VISTAS as $nombre => $sql) {
            DB::statement("CREATE OR REPLACE VIEW {$nombre} AS {$sql}");
        }
    }

    public function down(): void
    {
        foreach (array_reverse(array_keys(self::VISTAS)) as $nombre) {
            DB::statement("DROP VIEW IF EXISTS {$nombre}");
        }

        foreach (array_keys(self::INDICES) as $nombre) {
            DB::statement("DROP INDEX IF EXISTS {$nombre}");
        }

        foreach (self::CHECKS as $tabla => $checks) {
            foreach (array_keys($checks) as $nombre) {
                DB::statement("ALTER TABLE {$tabla} DROP CONSTRAINT IF EXISTS {$nombre}");
            }
        }
    }
};
