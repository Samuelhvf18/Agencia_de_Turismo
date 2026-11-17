-- =====================================================================
-- Sistema Web de Control Logístico, Asignación de Recursos y Venta de
-- Tours Georreferenciados para Agencias de Viajes y Turismo
-- Esquema físico PostgreSQL — versión 5
-- ---------------------------------------------------------------------
-- Cambios respecto a v4 (19/10/2026):
--  * Secciones 10 y 11 nuevas: Módulo 07 – Investigación Operativa II.
--    Teoría de colas M/M/c/K sobre el motor de reservas (3 tablas) y
--    cadenas de Markov para el ciclo de vida del cliente (5 tablas).
--    Equivalen a las migraciones de Laravel
--    2026_10_19_153000_create_colas_io_table.php y
--    2026_10_19_153001_create_markov_cliente_table.php; aquí se agregan
--    además las restricciones CHECK de dominio.
--  * 1 vista nueva (vista_llegadas_diarias_tour) y 7 índices de apoyo.
--  * Datos semilla: 5 estados Markov y la matriz de transición inicial.
-- ---------------------------------------------------------------------
-- Historial v4:
-- Cambios respecto a db.sql (v3):
--  * users: se eliminan columnas redundantes `rol` (duplicaba rol_id) y
--    `tok_rec` (duplicaba remember_token); rol_id pasa a NOT NULL.
--  * vehiculos.est: el valor por defecto 'act' no coincidía con el
--    dominio documentado; ahora es 'activo'.
--  * comprobantes_pago: se agregan subtotal (sub) e impuestos (imp),
--    alineado con el diccionario de datos.
--  * conversaciones_chatbot: se agrega der_hum (derivación a humano,
--    RF Ficha 31).
--  * Se agregan TODAS las restricciones CHECK de dominio descritas en el
--    diseño (duración 1–14 h, estados, calificación 1–5, etc.).
--  * Se agregan índices de apoyo y las 6 vistas para IO II y dashboards.
-- Convención: nombres de tabla completos; atributos abreviados según el
-- glosario del Diccionario de Datos v3.
-- =====================================================================

-- ==========================================
-- 1. TABLAS CATÁLOGO / BASE
-- ==========================================

CREATE TABLE roles (
    id  BIGSERIAL PRIMARY KEY,
    nom VARCHAR(50)  UNIQUE NOT NULL,
    des VARCHAR(255) NULL
);

CREATE TABLE paises (
    id      BIGSERIAL PRIMARY KEY,
    nom     VARCHAR(100) NOT NULL,
    cod_iso CHAR(2) UNIQUE NOT NULL
);

CREATE TABLE idiomas (
    id      BIGSERIAL PRIMARY KEY,
    nom     VARCHAR(50) NOT NULL,
    cod_iso CHAR(2) UNIQUE NOT NULL
);

CREATE TABLE departamentos_bolivia (
    id  BIGSERIAL PRIMARY KEY,
    cod CHAR(2) UNIQUE NOT NULL,
    nom VARCHAR(50) NOT NULL
);

-- ==========================================
-- 2. USUARIOS Y PERFILES
-- ==========================================

CREATE TABLE users (
    id             BIGSERIAL PRIMARY KEY,
    rol_id         BIGINT NOT NULL REFERENCES roles(id) ON DELETE RESTRICT,
    pai_id         BIGINT NULL REFERENCES paises(id) ON DELETE SET NULL,
    nom            VARCHAR(150) NOT NULL,
    cor            VARCHAR(150) UNIQUE NOT NULL,
    cor_vrf_en     TIMESTAMP NULL,
    clv            VARCHAR(255) NOT NULL,
    tel            VARCHAR(20) NULL,
    tip_doc        VARCHAR(20) NULL,
    num_doc        VARCHAR(15) NULL,
    ci_cmp         VARCHAR(5) NOT NULL DEFAULT '',
    ci_dep_id      BIGINT NULL REFERENCES departamentos_bolivia(id),
    fot_prf        VARCHAR(255) NULL,
    act            BOOLEAN NOT NULL DEFAULT TRUE,
    remember_token VARCHAR(100) NULL,
    created_at     TIMESTAMP NULL,
    updated_at     TIMESTAMP NULL,
    eli_en         TIMESTAMP NULL,
    CONSTRAINT users_num_doc_ci_cmp_unique UNIQUE (num_doc, ci_cmp),
    CONSTRAINT chk_users_tip_doc CHECK (tip_doc IS NULL OR tip_doc IN ('CI', 'PASAPORTE')),
    CONSTRAINT chk_users_ci_dep  CHECK (tip_doc = 'CI' OR ci_dep_id IS NULL)
);

CREATE TABLE perfiles_guia (
    id          BIGSERIAL PRIMARY KEY,
    usr_id      BIGINT UNIQUE NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    rta_amg     VARCHAR(150) UNIQUE NULL,
    ani_exp     SMALLINT NOT NULL DEFAULT 0,
    lim_hrs_sem SMALLINT NOT NULL DEFAULT 40,
    bio         TEXT NULL,
    act         BOOLEAN NOT NULL DEFAULT TRUE,
    CONSTRAINT chk_prf_gui_ani_exp CHECK (ani_exp >= 0),
    CONSTRAINT chk_prf_gui_lim_hrs CHECK (lim_hrs_sem BETWEEN 1 AND 60)
);

CREATE TABLE idiomas_guia (
    id         BIGSERIAL PRIMARY KEY,
    prf_gui_id BIGINT NOT NULL REFERENCES perfiles_guia(id) ON DELETE CASCADE,
    idi_id     BIGINT NOT NULL REFERENCES idiomas(id) ON DELETE RESTRICT,
    CONSTRAINT idiomas_guia_prf_gui_id_idi_id_unique UNIQUE (prf_gui_id, idi_id)
);

CREATE TABLE perfiles_chofer (
    id          BIGSERIAL PRIMARY KEY,
    usr_id      BIGINT UNIQUE NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    num_lic     VARCHAR(30) NOT NULL,
    tip_lic     VARCHAR(20) NOT NULL,
    fec_ven_lic DATE NOT NULL,
    act         BOOLEAN NOT NULL DEFAULT TRUE
);

-- ==========================================
-- 3. TOURS Y RUTAS
-- ==========================================

CREATE TABLE categorias_tour (
    id      BIGSERIAL PRIMARY KEY,
    nom     VARCHAR(80) NOT NULL,
    rta_amg VARCHAR(100) UNIQUE NOT NULL,
    des     TEXT NULL,
    ico     VARCHAR(255) NULL
);

CREATE TABLE tours (
    id        BIGSERIAL PRIMARY KEY,
    cat_id    BIGINT NOT NULL REFERENCES categorias_tour(id) ON DELETE RESTRICT,
    cod       VARCHAR(20) UNIQUE NOT NULL,
    rta_amg   VARCHAR(180) UNIQUE NOT NULL,
    nom       VARCHAR(150) NOT NULL,
    des       TEXT NULL,
    niv_dif   VARCHAR(15) NOT NULL,
    alt_min_m SMALLINT NULL,
    alt_max_m SMALLINT NULL,
    dur_hrs   NUMERIC(5, 2) NOT NULL,
    req_fis   TEXT NULL,
    pre_bas   NUMERIC(10, 2) NOT NULL,
    cap_max   SMALLINT NOT NULL,
    trz_geo   JSONB NULL,
    act       BOOLEAN NOT NULL DEFAULT TRUE,
    cre_en    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    mod_en    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    eli_en    TIMESTAMP NULL,
    CONSTRAINT chk_tours_niv_dif          CHECK (niv_dif IN ('facil', 'moderado', 'dificil', 'experto')),
    CONSTRAINT chk_tours_duracion_un_dia  CHECK (dur_hrs BETWEEN 1 AND 14),
    CONSTRAINT chk_tours_pre_bas          CHECK (pre_bas >= 0),
    CONSTRAINT chk_tours_cap_max          CHECK (cap_max > 0),
    CONSTRAINT chk_tours_altitudes        CHECK (alt_min_m IS NULL OR alt_max_m IS NULL OR alt_max_m >= alt_min_m)
);

CREATE TABLE fotos_tour (
    id      BIGSERIAL PRIMARY KEY,
    tur_id  BIGINT NOT NULL REFERENCES tours(id) ON DELETE CASCADE,
    rta_arc VARCHAR(255) NOT NULL,
    ord_vis SMALLINT NOT NULL DEFAULT 0,
    txt_atv VARCHAR(150) NULL
);

CREATE TABLE puntos_ruta (
    id      BIGSERIAL PRIMARY KEY,
    tur_id  BIGINT NOT NULL REFERENCES tours(id) ON DELETE CASCADE,
    ord_sec SMALLINT NOT NULL,
    nom     VARCHAR(120) NULL,
    lat     NUMERIC(10, 7) NOT NULL,
    lon     NUMERIC(10, 7) NOT NULL,
    alt_m   SMALLINT NULL,
    tip_pun VARCHAR(15) NOT NULL DEFAULT 'checkpoint',
    CONSTRAINT puntos_ruta_tur_id_ord_sec_unique UNIQUE (tur_id, ord_sec),
    CONSTRAINT chk_pun_rta_tip CHECK (tip_pun IN ('inicio', 'checkpoint', 'mirador', 'fin')),
    CONSTRAINT chk_pun_rta_lat CHECK (lat BETWEEN -90 AND 90),
    CONSTRAINT chk_pun_rta_lon CHECK (lon BETWEEN -180 AND 180)
);

-- ==========================================
-- 4. VEHÍCULOS Y EQUIPAMIENTO
-- ==========================================

CREATE TABLE vehiculos (
    id      BIGSERIAL PRIMARY KEY,
    pla     VARCHAR(15) UNIQUE NOT NULL,
    mar     VARCHAR(50) NULL,
    mdl     VARCHAR(50) NULL,
    tip     VARCHAR(15) NOT NULL,
    cap_asi SMALLINT NOT NULL,
    tip_cmb VARCHAR(20) NULL,
    est     VARCHAR(15) NOT NULL DEFAULT 'activo',
    CONSTRAINT chk_veh_tip     CHECK (tip IN ('minibus', 'van', 'otro')),
    CONSTRAINT chk_veh_cap_asi CHECK (cap_asi > 0),
    CONSTRAINT chk_veh_est     CHECK (est IN ('activo', 'mantenimiento', 'inactivo'))
);

CREATE TABLE tipos_equipo (
    id          BIGSERIAL PRIMARY KEY,
    nom         VARCHAR(80) NOT NULL,
    des         TEXT NULL,
    pre_alq_bas NUMERIC(8, 2) NOT NULL DEFAULT 0,
    cos_ord     NUMERIC(10, 2) NULL,
    cos_mnt_anu NUMERIC(10, 2) NULL,
    eqp_cri     BOOLEAN NOT NULL DEFAULT FALSE,
    CONSTRAINT chk_tip_eqp_costos CHECK ((cos_ord IS NULL OR cos_ord > 0) AND (cos_mnt_anu IS NULL OR cos_mnt_anu > 0))
);

CREATE TABLE items_equipo (
    id         BIGSERIAL PRIMARY KEY,
    tip_eqp_id BIGINT NOT NULL REFERENCES tipos_equipo(id) ON DELETE CASCADE,
    tal        VARCHAR(20) NOT NULL,
    cnt_tot    INTEGER NOT NULL DEFAULT 0,
    cnt_dis    INTEGER NOT NULL DEFAULT 0,
    con        VARCHAR(20) NOT NULL DEFAULT 'bueno',
    CONSTRAINT items_equipo_tip_eqp_id_tal_unique UNIQUE (tip_eqp_id, tal),
    CONSTRAINT chk_itm_eqp_cantidades CHECK (cnt_tot >= 0 AND cnt_dis BETWEEN 0 AND cnt_tot)
);

CREATE TABLE equipo_por_tour (
    id          BIGSERIAL PRIMARY KEY,
    tur_id      BIGINT NOT NULL REFERENCES tours(id) ON DELETE CASCADE,
    tip_eqp_id  BIGINT NOT NULL REFERENCES tipos_equipo(id) ON DELETE RESTRICT,
    pre_alq_esp NUMERIC(8, 2) NULL,
    act         BOOLEAN NOT NULL DEFAULT TRUE,
    CONSTRAINT equipo_por_tour_tur_id_tip_eqp_id_unique UNIQUE (tur_id, tip_eqp_id)
);

-- ==========================================
-- 5. SALIDAS, RESERVAS Y PARTICIPANTES
-- ==========================================

CREATE TABLE salidas_tour (
    id          BIGSERIAL PRIMARY KEY,
    tur_id      BIGINT NOT NULL REFERENCES tours(id) ON DELETE RESTRICT,
    fec_sal     DATE NOT NULL,
    hra_sal     TIME NOT NULL,
    prf_gui_id  BIGINT NULL REFERENCES perfiles_guia(id) ON DELETE SET NULL,
    prf_cho_id  BIGINT NULL REFERENCES perfiles_chofer(id) ON DELETE SET NULL,
    veh_id      BIGINT NULL REFERENCES vehiculos(id) ON DELETE SET NULL,
    cap_max     SMALLINT NOT NULL,
    est         VARCHAR(15) NOT NULL DEFAULT 'programada',
    hra_ini_rea TIMESTAMP NULL,
    hra_fin_rea TIMESTAMP NULL,
    cre_en      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    mod_en      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_sal_tur_est      CHECK (est IN ('programada', 'en_curso', 'completada', 'cancelada')),
    CONSTRAINT chk_sal_tur_cap_max  CHECK (cap_max > 0),
    CONSTRAINT chk_sal_tur_hra_real CHECK (hra_fin_rea IS NULL OR hra_ini_rea IS NULL OR hra_fin_rea >= hra_ini_rea)
);

CREATE TABLE bitacora_viajes_vehiculo (
    id         BIGSERIAL PRIMARY KEY,
    veh_id     BIGINT NOT NULL REFERENCES vehiculos(id) ON DELETE CASCADE,
    sal_tur_id BIGINT NOT NULL REFERENCES salidas_tour(id) ON DELETE CASCADE,
    dst_km     NUMERIC(6, 2) NULL,
    cos_cmb    NUMERIC(8, 2) NULL,
    reg_en     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_btc_via_valores CHECK ((dst_km IS NULL OR dst_km >= 0) AND (cos_cmb IS NULL OR cos_cmb >= 0))
);

CREATE TABLE reservas (
    id         BIGSERIAL PRIMARY KEY,
    cod        VARCHAR(20) UNIQUE NOT NULL,
    sal_tur_id BIGINT NOT NULL REFERENCES salidas_tour(id) ON DELETE RESTRICT,
    usr_id     BIGINT NULL REFERENCES users(id) ON DELETE SET NULL,
    nom_ctc    VARCHAR(150) NOT NULL,
    cor_ctc    VARCHAR(150) NOT NULL,
    tel_ctc    VARCHAR(20) NOT NULL,
    cnt_ptc    SMALLINT NOT NULL,
    est        VARCHAR(15) NOT NULL DEFAULT 'pendiente',
    pre_tot    NUMERIC(10, 2) NOT NULL,
    est_pag    VARCHAR(15) NOT NULL DEFAULT 'pendiente',
    cre_en     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    mod_en     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_rsv_cnt_ptc CHECK (cnt_ptc > 0),
    CONSTRAINT chk_rsv_pre_tot CHECK (pre_tot >= 0),
    CONSTRAINT chk_rsv_est     CHECK (est IN ('pendiente', 'confirmada', 'cancelada', 'completada')),
    CONSTRAINT chk_rsv_est_pag CHECK (est_pag IN ('pendiente', 'parcial', 'pagado', 'reembolsado'))
);

CREATE TABLE participantes_reserva (
    id         BIGSERIAL PRIMARY KEY,
    rsv_id     BIGINT NOT NULL REFERENCES reservas(id) ON DELETE CASCADE,
    nom_cpl    VARCHAR(150) NOT NULL,
    tip_doc    VARCHAR(20) NULL,
    num_doc    VARCHAR(15) NULL,
    ci_cmp     VARCHAR(5) NOT NULL DEFAULT '',
    ci_dep_id  BIGINT NULL REFERENCES departamentos_bolivia(id),
    eda        SMALLINT NULL,
    pai_id     BIGINT NULL REFERENCES paises(id) ON DELETE SET NULL,
    es_ctc_pri BOOLEAN NOT NULL DEFAULT FALSE,
    CONSTRAINT chk_ptc_tip_doc CHECK (tip_doc IS NULL OR tip_doc IN ('CI', 'PASAPORTE')),
    CONSTRAINT chk_ptc_ci_dep  CHECK (tip_doc = 'CI' OR ci_dep_id IS NULL),
    CONSTRAINT chk_ptc_eda     CHECK (eda IS NULL OR eda BETWEEN 0 AND 110)
);

CREATE TABLE alquileres_equipo_reserva (
    id         BIGSERIAL PRIMARY KEY,
    ptc_rsv_id BIGINT NOT NULL REFERENCES participantes_reserva(id) ON DELETE CASCADE,
    itm_eqp_id BIGINT NOT NULL REFERENCES items_equipo(id) ON DELETE RESTRICT,
    pre        NUMERIC(8, 2) NOT NULL,
    CONSTRAINT chk_alq_pre CHECK (pre >= 0)
);

CREATE TABLE boletos (
    id         BIGSERIAL PRIMARY KEY,
    cod        VARCHAR(64) UNIQUE NOT NULL,
    ptc_rsv_id BIGINT UNIQUE NOT NULL REFERENCES participantes_reserva(id) ON DELETE CASCADE,
    est        VARCHAR(15) NOT NULL DEFAULT 'emitido',
    emt_en     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    vld_en     TIMESTAMP NULL,
    vld_por    BIGINT NULL REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT chk_bol_est CHECK (est IN ('emitido', 'validado', 'cancelado'))
);

CREATE TABLE fichas_medicas (
    id          BIGSERIAL PRIMARY KEY,
    ptc_rsv_id  BIGINT UNIQUE NOT NULL REFERENCES participantes_reserva(id) ON DELETE CASCADE,
    con_med     TEXT NULL,
    nom_ctc_eme VARCHAR(150) NULL,
    tel_ctc_eme VARCHAR(20) NULL,
    rta_fir     VARCHAR(255) NOT NULL,
    fir_en      TIMESTAMP NOT NULL,
    dir_ip      VARCHAR(45) NULL
);

-- ==========================================
-- 6. PAGOS, RESEÑAS, CHATBOT, AUDITORÍA
-- ==========================================

CREATE TABLE comprobantes_pago (
    id      BIGSERIAL PRIMARY KEY,
    cod     VARCHAR(20) UNIQUE NOT NULL,
    rsv_id  BIGINT UNIQUE NOT NULL REFERENCES reservas(id) ON DELETE RESTRICT,
    fec_emi DATE NOT NULL DEFAULT CURRENT_DATE,
    sub     NUMERIC(10, 2) NOT NULL,
    imp     NUMERIC(10, 2) NOT NULL DEFAULT 0,
    mto_tot NUMERIC(10, 2) NOT NULL,
    est     VARCHAR(15) NOT NULL DEFAULT 'pendiente',
    CONSTRAINT chk_cmp_pag_montos CHECK (sub >= 0 AND imp >= 0 AND mto_tot = sub + imp),
    CONSTRAINT chk_cmp_pag_est    CHECK (est IN ('pendiente', 'pagado', 'anulado'))
);

CREATE TABLE pagos (
    id         BIGSERIAL PRIMARY KEY,
    cmp_pag_id BIGINT NOT NULL REFERENCES comprobantes_pago(id) ON DELETE RESTRICT,
    mtd        VARCHAR(15) NOT NULL,
    mto        NUMERIC(10, 2) NOT NULL,
    ref_trx    VARCHAR(100) NULL,
    pag_en     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    est        VARCHAR(15) NOT NULL DEFAULT 'pendiente',
    CONSTRAINT chk_pag_mtd CHECK (mtd IN ('tarjeta', 'qr', 'transferencia', 'efectivo')),
    CONSTRAINT chk_pag_mto CHECK (mto > 0),
    CONSTRAINT chk_pag_est CHECK (est IN ('completado', 'fallido', 'pendiente'))
);

CREATE TABLE resenas (
    id     BIGSERIAL PRIMARY KEY,
    tur_id BIGINT NOT NULL REFERENCES tours(id) ON DELETE CASCADE,
    rsv_id BIGINT UNIQUE NULL REFERENCES reservas(id) ON DELETE SET NULL,
    cal    SMALLINT NOT NULL,
    com    TEXT NULL,
    mdt    JSONB NULL,
    cre_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_res_cal CHECK (cal BETWEEN 1 AND 5)
);

CREATE TABLE conversaciones_chatbot (
    id      BIGSERIAL PRIMARY KEY,
    usr_id  BIGINT NULL REFERENCES users(id) ON DELETE SET NULL,
    id_ses  VARCHAR(100) NOT NULL,
    can     VARCHAR(20) NOT NULL DEFAULT 'web',
    der_hum BOOLEAN NOT NULL DEFAULT FALSE,
    ini_en  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fin_en  TIMESTAMP NULL,
    CONSTRAINT chk_cnv_can CHECK (can IN ('web', 'whatsapp'))
);

CREATE TABLE mensajes_chatbot (
    id      BIGSERIAL PRIMARY KEY,
    cnv_id  BIGINT NOT NULL REFERENCES conversaciones_chatbot(id) ON DELETE CASCADE,
    rem     VARCHAR(10) NOT NULL,
    txt_msj TEXT NOT NULL,
    int_det VARCHAR(50) NULL,
    cre_en  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_msj_rem CHECK (rem IN ('usuario', 'bot'))
);

CREATE TABLE bitacora_auditoria (
    id      BIGSERIAL PRIMARY KEY,
    usr_id  BIGINT NULL REFERENCES users(id) ON DELETE SET NULL,
    nom_tbl VARCHAR(100) NOT NULL,
    id_reg  BIGINT NOT NULL,
    acc     VARCHAR(10) NOT NULL,
    val_ant JSONB NULL,
    val_nvo JSONB NULL,
    dir_ip  VARCHAR(45) NULL,
    age_usr TEXT NULL,
    cre_en  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_btc_aud_acc CHECK (acc IN ('CREATE', 'UPDATE', 'DELETE', 'READ'))
);

CREATE TABLE registros_notificacion (
    id     BIGSERIAL PRIMARY KEY,
    rsv_id BIGINT NULL REFERENCES reservas(id) ON DELETE SET NULL,
    usr_id BIGINT NULL REFERENCES users(id) ON DELETE SET NULL,
    can    VARCHAR(15) NOT NULL,
    tip    VARCHAR(50) NOT NULL,
    est    VARCHAR(15) NOT NULL DEFAULT 'pendiente',
    cnd    JSONB NULL,
    env_en TIMESTAMP NULL,
    CONSTRAINT chk_reg_not_can CHECK (can IN ('whatsapp', 'email', 'sms', 'panel')),
    CONSTRAINT chk_reg_not_est CHECK (est IN ('enviado', 'fallido', 'pendiente'))
);

CREATE TABLE proyecciones_demanda (
    id      BIGSERIAL PRIMARY KEY,
    tur_id  BIGINT NOT NULL REFERENCES tours(id) ON DELETE CASCADE,
    mes_per SMALLINT NOT NULL,
    ani_per SMALLINT NOT NULL,
    rsv_pry INTEGER NOT NULL,
    ver_mdl VARCHAR(30) NULL,
    gen_en  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT proyecciones_demanda_tur_id_mes_per_ani_per_unique UNIQUE (tur_id, mes_per, ani_per),
    CONSTRAINT chk_pry_mes CHECK (mes_per BETWEEN 1 AND 12),
    CONSTRAINT chk_pry_rsv CHECK (rsv_pry >= 0)
);

-- Tabla de framework (Laravel)
CREATE TABLE sessions (
    id            VARCHAR(255) PRIMARY KEY,
    user_id       BIGINT NULL,
    ip_address    VARCHAR(45) NULL,
    user_agent    TEXT NULL,
    payload       TEXT NOT NULL,
    last_activity INTEGER NOT NULL
);

-- ==========================================
-- 7. ÍNDICES DE APOYO
-- ==========================================
CREATE INDEX sessions_user_id_index           ON sessions(user_id);
CREATE INDEX sessions_last_activity_index     ON sessions(last_activity);
CREATE INDEX idx_tours_cat_act                ON tours(cat_id, act);
CREATE INDEX idx_tours_trz_geo                ON tours USING GIN (trz_geo);
CREATE INDEX idx_puntos_ruta_tur              ON puntos_ruta(tur_id, ord_sec);
CREATE INDEX idx_salidas_fecha                ON salidas_tour(fec_sal, hra_sal);
CREATE INDEX idx_salidas_guia                 ON salidas_tour(prf_gui_id, fec_sal);
CREATE INDEX idx_salidas_vehiculo             ON salidas_tour(veh_id, fec_sal);
CREATE INDEX idx_reservas_salida              ON reservas(sal_tur_id, est);
CREATE INDEX idx_reservas_usuario             ON reservas(usr_id);
CREATE INDEX idx_participantes_reserva        ON participantes_reserva(rsv_id);
CREATE INDEX idx_pagos_comprobante            ON pagos(cmp_pag_id);
CREATE INDEX idx_bitacora_tabla_registro      ON bitacora_auditoria(nom_tbl, id_reg);
CREATE INDEX idx_bitacora_fecha               ON bitacora_auditoria(cre_en);
CREATE INDEX idx_notificaciones_reserva       ON registros_notificacion(rsv_id);
CREATE INDEX idx_mensajes_conversacion        ON mensajes_chatbot(cnv_id, cre_en);

-- ==========================================
-- 8. VISTAS (IO II y Dashboards)
-- ==========================================

-- Horas acumuladas por guía y semana (modelo de asignación de guías)
CREATE VIEW vista_carga_horaria_guia AS
SELECT pg.id                                   AS prf_gui_id,
       u.nom                                   AS guia,
       pg.ani_exp,
       pg.lim_hrs_sem,
       date_trunc('week', st.fec_sal)::date    AS semana,
       COALESCE(SUM(t.dur_hrs), 0)             AS horas_asignadas,
       pg.lim_hrs_sem - COALESCE(SUM(t.dur_hrs), 0) AS horas_disponibles
FROM perfiles_guia pg
JOIN users u            ON u.id = pg.usr_id
LEFT JOIN salidas_tour st ON st.prf_gui_id = pg.id AND st.est <> 'cancelada'
LEFT JOIN tours t       ON t.id = st.tur_id
WHERE pg.act
GROUP BY pg.id, u.nom, pg.ani_exp, pg.lim_hrs_sem, date_trunc('week', st.fec_sal);

-- Ocupación de asientos y costo de combustible por salida (modelo de transporte)
CREATE VIEW vista_ocupacion_transporte AS
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
JOIN vehiculos v               ON v.id = st.veh_id
LEFT JOIN reservas r           ON r.sal_tur_id = st.id
LEFT JOIN bitacora_viajes_vehiculo b ON b.sal_tur_id = st.id
GROUP BY st.id, st.fec_sal, v.id, v.pla, v.cap_asi;

-- Rutas más solicitadas por temporada (dashboard)
CREATE VIEW vista_demanda_por_tour_mes AS
SELECT t.id AS tur_id, t.nom AS tour,
       EXTRACT(YEAR  FROM st.fec_sal)::smallint AS anio,
       EXTRACT(MONTH FROM st.fec_sal)::smallint AS mes,
       COUNT(r.id)                              AS reservas,
       COALESCE(SUM(r.cnt_ptc), 0)              AS participantes
FROM tours t
JOIN salidas_tour st ON st.tur_id = t.id
JOIN reservas r      ON r.sal_tur_id = st.id AND r.est <> 'cancelada'
GROUP BY t.id, t.nom, EXTRACT(YEAR FROM st.fec_sal), EXTRACT(MONTH FROM st.fec_sal);

-- Nacionalidad de los turistas (dashboard)
CREATE VIEW vista_turistas_por_nacionalidad AS
SELECT COALESCE(p.nom, 'Sin dato') AS pais,
       p.cod_iso,
       COUNT(pr.id)                AS turistas
FROM participantes_reserva pr
JOIN reservas r     ON r.id = pr.rsv_id AND r.est <> 'cancelada'
LEFT JOIN paises p  ON p.id = pr.pai_id
GROUP BY p.nom, p.cod_iso;

-- Calificación promedio por tour
CREATE VIEW vista_calificacion_promedio_tour AS
SELECT t.id AS tur_id, t.nom AS tour,
       ROUND(AVG(re.cal)::numeric, 2) AS calificacion_promedio,
       COUNT(re.id)                   AS cantidad_resenas
FROM tours t
LEFT JOIN resenas re ON re.tur_id = t.id
GROUP BY t.id, t.nom;

-- Demanda anual por tipo de equipo (parámetro D del modelo EOQ)
CREATE VIEW vista_demanda_equipo_anual AS
SELECT te.id AS tip_eqp_id, te.nom AS tipo_equipo, te.eqp_cri,
       te.cos_ord, te.cos_mnt_anu,
       EXTRACT(YEAR FROM st.fec_sal)::smallint AS anio,
       COUNT(a.id)                             AS unidades_alquiladas
FROM tipos_equipo te
JOIN items_equipo ie               ON ie.tip_eqp_id = te.id
JOIN alquileres_equipo_reserva a   ON a.itm_eqp_id = ie.id
JOIN participantes_reserva pr      ON pr.id = a.ptc_rsv_id
JOIN reservas r                    ON r.id = pr.rsv_id AND r.est IN ('confirmada', 'completada')
JOIN salidas_tour st               ON st.id = r.sal_tur_id
GROUP BY te.id, te.nom, te.eqp_cri, te.cos_ord, te.cos_mnt_anu, EXTRACT(YEAR FROM st.fec_sal);

-- ==========================================
-- 9. DATOS SEMILLA DE CATÁLOGOS
-- ==========================================
INSERT INTO roles (nom, des) VALUES
 ('turista', 'Cliente que reserva tours'),
 ('guia', 'Guía de turismo asignado a salidas'),
 ('chofer', 'Conductor de la flota'),
 ('operaciones', 'Personal de operaciones de la agencia'),
 ('admin', 'Administrador del sistema');

INSERT INTO departamentos_bolivia (cod, nom) VALUES
 ('LP','La Paz'),('CB','Cochabamba'),('SC','Santa Cruz'),('OR','Oruro'),('PT','Potosí'),
 ('CH','Chuquisaca'),('TJ','Tarija'),('BE','Beni'),('PD','Pando');

INSERT INTO categorias_tour (nom, rta_amg, des) VALUES
 ('Biking',    'biking',    'Descensos en bicicleta de montaña'),
 ('Hiking',    'hiking',    'Caminatas de baja y media dificultad'),
 ('Trekking',  'trekking',  'Caminatas de alta montaña'),
 ('Climbing',  'climbing',  'Ascensos y escalada técnica'),
 ('City Tour', 'city-tour', 'Recorridos urbanos y culturales');


-- ==========================================
-- 10. MÓDULO 07 – IO II: TEORÍA DE COLAS (M/M/c/K)
--     c = guías, K = cupos por salida, λ = reservas/día,
--     μ = 24 / dur_hrs (tours atendidos por guía y día)
-- ==========================================

CREATE TABLE cola_parametros_tour (
    id      BIGSERIAL PRIMARY KEY,
    tur_id  BIGINT NOT NULL REFERENCES tours(id) ON DELETE CASCADE,
    lmb     NUMERIC(8, 4) NOT NULL,
    mu      NUMERIC(8, 4) NOT NULL,
    c_srv   SMALLINT NOT NULL DEFAULT 1,
    k_cap   SMALLINT NOT NULL,
    cos_esp NUMERIC(8, 2) NULL,
    cos_srv NUMERIC(8, 2) NULL,
    fec_cal DATE NOT NULL DEFAULT CURRENT_DATE,
    obs     TEXT NULL,
    act     BOOLEAN NOT NULL DEFAULT TRUE,
    cre_en  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    mod_en  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_cola_par_tasas  CHECK (lmb >= 0 AND mu > 0),
    CONSTRAINT chk_cola_par_c_k    CHECK (c_srv >= 1 AND k_cap >= c_srv),
    CONSTRAINT chk_cola_par_costos CHECK ((cos_esp IS NULL OR cos_esp >= 0) AND (cos_srv IS NULL OR cos_srv >= 0))
);
COMMENT ON TABLE cola_parametros_tour IS 'Parámetros del modelo M/M/c/K por tour (configurables por el administrador)';

CREATE TABLE cola_eventos_llegada (
    id         BIGSERIAL PRIMARY KEY,
    tur_id     BIGINT NOT NULL REFERENCES tours(id) ON DELETE CASCADE,
    sal_tur_id BIGINT NULL REFERENCES salidas_tour(id) ON DELETE SET NULL,
    rsv_id     BIGINT NULL REFERENCES reservas(id) ON DELETE SET NULL,
    fec_evt    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    cnt_sol    SMALLINT NOT NULL DEFAULT 1,
    est        VARCHAR(15) NOT NULL DEFAULT 'admitido',
    cre_en     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_cola_evt_cnt CHECK (cnt_sol > 0),
    CONSTRAINT chk_cola_evt_est CHECK (est IN ('admitido', 'rechazado', 'cancelado'))
);
COMMENT ON TABLE cola_eventos_llegada IS 'Log de llegadas (solicitudes de reserva) para estimar λ observado';

CREATE TABLE cola_metricas_diarias (
    id      BIGSERIAL PRIMARY KEY,
    tur_id  BIGINT NOT NULL REFERENCES tours(id) ON DELETE CASCADE,
    fec     DATE NOT NULL,
    lmb_obs NUMERIC(8, 4) NULL,
    mu_obs  NUMERIC(8, 4) NULL,
    rho     NUMERIC(6, 4) NULL,
    p0      NUMERIC(6, 4) NULL,
    lq      NUMERIC(8, 4) NULL,
    l_sis   NUMERIC(8, 4) NULL,
    wq_hrs  NUMERIC(8, 4) NULL,
    w_hrs   NUMERIC(8, 4) NULL,
    pq      NUMERIC(6, 4) NULL,
    p_rec   NUMERIC(6, 4) NULL,
    cre_en  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT uq_cola_met_tur_fec UNIQUE (tur_id, fec),
    CONSTRAINT chk_cola_met_prob CHECK ((p0 IS NULL OR p0 BETWEEN 0 AND 1) AND (pq IS NULL OR pq BETWEEN 0 AND 1)
                                        AND (p_rec IS NULL OR p_rec BETWEEN 0 AND 1)),
    CONSTRAINT chk_cola_met_pos  CHECK ((lq IS NULL OR lq >= 0) AND (l_sis IS NULL OR l_sis >= 0)
                                        AND (wq_hrs IS NULL OR wq_hrs >= 0) AND (w_hrs IS NULL OR w_hrs >= 0))
);
COMMENT ON TABLE cola_metricas_diarias IS 'Snapshot diario de ρ, P0, Lq, L, Wq, W, Pq y P(rechazo) por tour';

-- ==========================================
-- 11. MÓDULO 07 – IO II: CADENAS DE MARKOV (ciclo de vida del cliente)
--     prospecto → reservado → completado → fidelizado / perdido
-- ==========================================

CREATE TABLE markov_estados_cliente (
    id      BIGSERIAL PRIMARY KEY,
    cod     VARCHAR(20) UNIQUE NOT NULL,
    nom     VARCHAR(60) NOT NULL,
    des     TEXT NULL,
    es_abs  BOOLEAN NOT NULL DEFAULT FALSE,
    ord_vis SMALLINT NOT NULL DEFAULT 0,
    col_hex VARCHAR(7) NULL,
    cre_en  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_mkv_est_cod CHECK (cod IN ('prospecto', 'reservado', 'completado', 'fidelizado', 'perdido')),
    CONSTRAINT chk_mkv_est_col CHECK (col_hex IS NULL OR col_hex ~ '^#[0-9A-Fa-f]{6}$')
);

CREATE TABLE markov_transiciones (
    id         BIGSERIAL PRIMARY KEY,
    est_ori_id BIGINT NOT NULL REFERENCES markov_estados_cliente(id) ON DELETE CASCADE,
    est_des_id BIGINT NOT NULL REFERENCES markov_estados_cliente(id) ON DELETE CASCADE,
    prb        NUMERIC(6, 4) NOT NULL,
    per        VARCHAR(15) NOT NULL DEFAULT 'mensual',
    fec_cal    DATE NOT NULL DEFAULT CURRENT_DATE,
    act        BOOLEAN NOT NULL DEFAULT TRUE,
    cre_en     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT markov_trans_unica UNIQUE (est_ori_id, est_des_id, per, fec_cal),
    CONSTRAINT chk_mkv_trn_prb CHECK (prb BETWEEN 0 AND 1),
    CONSTRAINT chk_mkv_trn_per CHECK (per IN ('mensual', 'trimestral'))
);
COMMENT ON TABLE markov_transiciones IS 'Matriz de transición P[i,j]; cada fila (origen, per, fec_cal) debe sumar 1 (se valida en MarkovService)';

CREATE TABLE markov_cliente_estado (
    id         BIGSERIAL PRIMARY KEY,
    usr_id     BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    est_id     BIGINT NOT NULL REFERENCES markov_estados_cliente(id) ON DELETE RESTRICT,
    est_ant_id BIGINT NULL REFERENCES markov_estados_cliente(id) ON DELETE SET NULL,
    fec_cmb    DATE NOT NULL DEFAULT CURRENT_DATE,
    obs        TEXT NULL,
    cre_en     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_mkv_cli_cambio CHECK (est_ant_id IS NULL OR est_ant_id <> est_id)
);

CREATE TABLE markov_cohorts (
    id      BIGSERIAL PRIMARY KEY,
    nom     VARCHAR(80) NOT NULL,
    per_cap DATE UNIQUE NOT NULL,
    tot_cli INTEGER NOT NULL DEFAULT 0,
    obs     TEXT NULL,
    cre_en  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    mod_en  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_mkv_coh_tot CHECK (tot_cli >= 0),
    CONSTRAINT chk_mkv_coh_per CHECK (EXTRACT(DAY FROM per_cap) = 1)
);

CREATE TABLE markov_cohort_distribucion (
    id      BIGSERIAL PRIMARY KEY,
    coh_id  BIGINT NOT NULL REFERENCES markov_cohorts(id) ON DELETE CASCADE,
    per_rel SMALLINT NOT NULL,
    est_id  BIGINT NOT NULL REFERENCES markov_estados_cliente(id) ON DELETE RESTRICT,
    cnt     INTEGER NOT NULL DEFAULT 0,
    cre_en  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT markov_coh_dist_unica UNIQUE (coh_id, per_rel, est_id),
    CONSTRAINT chk_mkv_dis_val CHECK (per_rel >= 0 AND cnt >= 0)
);

-- Índices de apoyo (IO II)
CREATE INDEX idx_cola_par_tur_act ON cola_parametros_tour(tur_id, act);
CREATE INDEX idx_cola_evt_tur_fec ON cola_eventos_llegada(tur_id, fec_evt);
CREATE INDEX idx_cola_evt_est     ON cola_eventos_llegada(est);
CREATE INDEX idx_cola_met_fec     ON cola_metricas_diarias(fec);
CREATE INDEX idx_mkv_trn_per_act  ON markov_transiciones(per, act);
CREATE INDEX idx_mkv_cli_usr_fec  ON markov_cliente_estado(usr_id, fec_cmb);
CREATE INDEX idx_mkv_cli_est      ON markov_cliente_estado(est_id);

-- Vista: llegadas observadas por tour y día (insumo de λ observado)
CREATE VIEW vista_llegadas_diarias_tour AS
SELECT e.tur_id,
       t.nom                                      AS tour,
       e.fec_evt::date                            AS fec,
       COUNT(*)                                   AS llegadas,
       COUNT(*) FILTER (WHERE e.est = 'admitido')  AS admitidas,
       COUNT(*) FILTER (WHERE e.est = 'rechazado') AS rechazadas,
       SUM(e.cnt_sol)                             AS cupos_solicitados
FROM cola_eventos_llegada e
JOIN tours t ON t.id = e.tur_id
GROUP BY e.tur_id, t.nom, e.fec_evt::date;

-- Datos semilla: estados de la cadena y matriz P inicial (mensual)
INSERT INTO markov_estados_cliente (cod, nom, des, es_abs, ord_vis, col_hex) VALUES
 ('prospecto',  'Prospecto',          'Visitante registrado que aún no reserva.',               FALSE, 1, '#6B6F72'),
 ('reservado',  'Reservado',          'Cliente con al menos una reserva confirmada pendiente.', FALSE, 2, '#F4C95D'),
 ('completado', 'Tour completado',    'Cliente que al menos una vez vivió el tour.',            FALSE, 3, '#1F5F8B'),
 ('fidelizado', 'Cliente fidelizado', 'Cliente recurrente (≥ 2 tours). Estado absorbente.',     TRUE,  4, '#C96F4A'),
 ('perdido',    'Cliente perdido',    'Sin actividad en ≥ 12 meses. Estado absorbente.',        TRUE,  5, '#174870');

INSERT INTO markov_transiciones (est_ori_id, est_des_id, prb, per)
SELECT o.id, d.id, v.prb, 'mensual'
FROM (VALUES
  ('prospecto',  'prospecto',  0.60), ('prospecto',  'reservado',  0.25), ('prospecto',  'perdido',    0.15),
  ('reservado',  'reservado',  0.10), ('reservado',  'completado', 0.80), ('reservado',  'perdido',    0.10),
  ('completado', 'completado', 0.25), ('completado', 'reservado',  0.20), ('completado', 'fidelizado', 0.40),
  ('completado', 'perdido',    0.15),
  ('fidelizado', 'fidelizado', 1.00),
  ('perdido',    'perdido',    1.00)
) AS v(ori, des, prb)
JOIN markov_estados_cliente o ON o.cod = v.ori
JOIN markov_estados_cliente d ON d.cod = v.des;
