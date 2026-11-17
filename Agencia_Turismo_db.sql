--
-- PostgreSQL database dump
--

\restrict 7YbEm8KTJzm2j7AiMxPwv61vsly037fAqHDlopVhEaFF52YVo122yKoG3r73pYs

-- Dumped from database version 18.6
-- Dumped by pg_dump version 18.6

-- Started on 2026-10-17 03:57:57

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 269 (class 1259 OID 21934)
-- Name: alquileres_equipo_reserva; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.alquileres_equipo_reserva (
    id bigint NOT NULL,
    ptc_rsv_id bigint NOT NULL,
    itm_eqp_id bigint NOT NULL,
    pre numeric(8,2) NOT NULL
);


ALTER TABLE public.alquileres_equipo_reserva OWNER TO postgres;

--
-- TOC entry 268 (class 1259 OID 21933)
-- Name: alquileres_equipo_reserva_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.alquileres_equipo_reserva_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.alquileres_equipo_reserva_id_seq OWNER TO postgres;

--
-- TOC entry 5506 (class 0 OID 0)
-- Dependencies: 268
-- Name: alquileres_equipo_reserva_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.alquileres_equipo_reserva_id_seq OWNED BY public.alquileres_equipo_reserva.id;


--
-- TOC entry 285 (class 1259 OID 22111)
-- Name: bitacora_auditoria; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.bitacora_auditoria (
    id bigint NOT NULL,
    usr_id bigint,
    nom_tbl character varying(100) NOT NULL,
    id_reg bigint NOT NULL,
    acc character varying(10) NOT NULL,
    val_ant jsonb,
    val_nvo jsonb,
    dir_ip character varying(45),
    age_usr text,
    cre_en timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.bitacora_auditoria OWNER TO postgres;

--
-- TOC entry 284 (class 1259 OID 22110)
-- Name: bitacora_auditoria_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.bitacora_auditoria_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.bitacora_auditoria_id_seq OWNER TO postgres;

--
-- TOC entry 5507 (class 0 OID 0)
-- Dependencies: 284
-- Name: bitacora_auditoria_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.bitacora_auditoria_id_seq OWNED BY public.bitacora_auditoria.id;


--
-- TOC entry 263 (class 1259 OID 21848)
-- Name: bitacora_viajes_vehiculo; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.bitacora_viajes_vehiculo (
    id bigint NOT NULL,
    veh_id bigint NOT NULL,
    sal_tur_id bigint NOT NULL,
    dst_km numeric(6,2),
    cos_cmb numeric(8,2),
    reg_en timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.bitacora_viajes_vehiculo OWNER TO postgres;

--
-- TOC entry 262 (class 1259 OID 21847)
-- Name: bitacora_viajes_vehiculo_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.bitacora_viajes_vehiculo_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.bitacora_viajes_vehiculo_id_seq OWNER TO postgres;

--
-- TOC entry 5508 (class 0 OID 0)
-- Dependencies: 262
-- Name: bitacora_viajes_vehiculo_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.bitacora_viajes_vehiculo_id_seq OWNED BY public.bitacora_viajes_vehiculo.id;


--
-- TOC entry 271 (class 1259 OID 21955)
-- Name: boletos; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.boletos (
    id bigint NOT NULL,
    cod character varying(64) NOT NULL,
    ptc_rsv_id bigint NOT NULL,
    est character varying(15) DEFAULT 'emitido'::character varying NOT NULL,
    emt_en timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    vld_en timestamp(0) without time zone,
    vld_por bigint
);


ALTER TABLE public.boletos OWNER TO postgres;

--
-- TOC entry 270 (class 1259 OID 21954)
-- Name: boletos_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.boletos_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.boletos_id_seq OWNER TO postgres;

--
-- TOC entry 5509 (class 0 OID 0)
-- Dependencies: 270
-- Name: boletos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.boletos_id_seq OWNED BY public.boletos.id;


--
-- TOC entry 221 (class 1259 OID 21426)
-- Name: cache; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.cache (
    key character varying(255) NOT NULL,
    value text NOT NULL,
    expiration integer NOT NULL
);


ALTER TABLE public.cache OWNER TO postgres;

--
-- TOC entry 222 (class 1259 OID 21436)
-- Name: cache_locks; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.cache_locks (
    key character varying(255) NOT NULL,
    owner character varying(255) NOT NULL,
    expiration integer NOT NULL
);


ALTER TABLE public.cache_locks OWNER TO postgres;

--
-- TOC entry 245 (class 1259 OID 21650)
-- Name: categorias_tour; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.categorias_tour (
    id bigint NOT NULL,
    nom character varying(80) NOT NULL,
    rta_amg character varying(100) NOT NULL,
    des text,
    ico character varying(255)
);


ALTER TABLE public.categorias_tour OWNER TO postgres;

--
-- TOC entry 244 (class 1259 OID 21649)
-- Name: categorias_tour_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.categorias_tour_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.categorias_tour_id_seq OWNER TO postgres;

--
-- TOC entry 5510 (class 0 OID 0)
-- Dependencies: 244
-- Name: categorias_tour_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.categorias_tour_id_seq OWNED BY public.categorias_tour.id;


--
-- TOC entry 275 (class 1259 OID 22003)
-- Name: comprobantes_pago; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.comprobantes_pago (
    id bigint NOT NULL,
    cod character varying(20) NOT NULL,
    rsv_id bigint NOT NULL,
    fec_emi date DEFAULT CURRENT_DATE NOT NULL,
    mto_tot numeric(10,2) NOT NULL,
    est character varying(15) DEFAULT 'emitido'::character varying NOT NULL
);


ALTER TABLE public.comprobantes_pago OWNER TO postgres;

--
-- TOC entry 274 (class 1259 OID 22002)
-- Name: comprobantes_pago_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.comprobantes_pago_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.comprobantes_pago_id_seq OWNER TO postgres;

--
-- TOC entry 5511 (class 0 OID 0)
-- Dependencies: 274
-- Name: comprobantes_pago_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.comprobantes_pago_id_seq OWNED BY public.comprobantes_pago.id;


--
-- TOC entry 281 (class 1259 OID 22073)
-- Name: conversaciones_chatbot; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.conversaciones_chatbot (
    id bigint NOT NULL,
    usr_id bigint,
    id_ses character varying(100) NOT NULL,
    can character varying(20) DEFAULT 'web'::character varying NOT NULL,
    ini_en timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    fin_en timestamp(0) without time zone
);


ALTER TABLE public.conversaciones_chatbot OWNER TO postgres;

--
-- TOC entry 280 (class 1259 OID 22072)
-- Name: conversaciones_chatbot_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.conversaciones_chatbot_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.conversaciones_chatbot_id_seq OWNER TO postgres;

--
-- TOC entry 5512 (class 0 OID 0)
-- Dependencies: 280
-- Name: conversaciones_chatbot_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.conversaciones_chatbot_id_seq OWNED BY public.conversaciones_chatbot.id;


--
-- TOC entry 235 (class 1259 OID 21531)
-- Name: departamentos_bolivia; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.departamentos_bolivia (
    id bigint NOT NULL,
    cod character(2) NOT NULL,
    nom character varying(50) NOT NULL
);


ALTER TABLE public.departamentos_bolivia OWNER TO postgres;

--
-- TOC entry 234 (class 1259 OID 21530)
-- Name: departamentos_bolivia_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.departamentos_bolivia_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.departamentos_bolivia_id_seq OWNER TO postgres;

--
-- TOC entry 5513 (class 0 OID 0)
-- Dependencies: 234
-- Name: departamentos_bolivia_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.departamentos_bolivia_id_seq OWNED BY public.departamentos_bolivia.id;


--
-- TOC entry 259 (class 1259 OID 21786)
-- Name: equipo_por_tour; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.equipo_por_tour (
    id bigint NOT NULL,
    tur_id bigint NOT NULL,
    tip_eqp_id bigint NOT NULL,
    pre_alq_esp numeric(8,2),
    act boolean DEFAULT true NOT NULL
);


ALTER TABLE public.equipo_por_tour OWNER TO postgres;

--
-- TOC entry 258 (class 1259 OID 21785)
-- Name: equipo_por_tour_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.equipo_por_tour_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.equipo_por_tour_id_seq OWNER TO postgres;

--
-- TOC entry 5514 (class 0 OID 0)
-- Dependencies: 258
-- Name: equipo_por_tour_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.equipo_por_tour_id_seq OWNED BY public.equipo_por_tour.id;


--
-- TOC entry 227 (class 1259 OID 21477)
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.failed_jobs (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    connection text NOT NULL,
    queue text NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.failed_jobs OWNER TO postgres;

--
-- TOC entry 226 (class 1259 OID 21476)
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.failed_jobs_id_seq OWNER TO postgres;

--
-- TOC entry 5515 (class 0 OID 0)
-- Dependencies: 226
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- TOC entry 273 (class 1259 OID 21983)
-- Name: fichas_medicas; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.fichas_medicas (
    id bigint NOT NULL,
    ptc_rsv_id bigint NOT NULL,
    con_med text,
    nom_ctc_eme character varying(150),
    tel_ctc_eme character varying(20),
    rta_fir character varying(255) NOT NULL,
    fir_en timestamp(0) without time zone NOT NULL,
    dir_ip character varying(45)
);


ALTER TABLE public.fichas_medicas OWNER TO postgres;

--
-- TOC entry 272 (class 1259 OID 21982)
-- Name: fichas_medicas_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.fichas_medicas_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.fichas_medicas_id_seq OWNER TO postgres;

--
-- TOC entry 5516 (class 0 OID 0)
-- Dependencies: 272
-- Name: fichas_medicas_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.fichas_medicas_id_seq OWNED BY public.fichas_medicas.id;


--
-- TOC entry 249 (class 1259 OID 21697)
-- Name: fotos_tour; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.fotos_tour (
    id bigint NOT NULL,
    tur_id bigint NOT NULL,
    rta_arc character varying(255) NOT NULL,
    ord_vis smallint DEFAULT '0'::smallint NOT NULL,
    txt_atv character varying(150)
);


ALTER TABLE public.fotos_tour OWNER TO postgres;

--
-- TOC entry 248 (class 1259 OID 21696)
-- Name: fotos_tour_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.fotos_tour_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.fotos_tour_id_seq OWNER TO postgres;

--
-- TOC entry 5517 (class 0 OID 0)
-- Dependencies: 248
-- Name: fotos_tour_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.fotos_tour_id_seq OWNED BY public.fotos_tour.id;


--
-- TOC entry 233 (class 1259 OID 21519)
-- Name: idiomas; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.idiomas (
    id bigint NOT NULL,
    nom character varying(50) NOT NULL,
    cod_iso character(2) NOT NULL
);


ALTER TABLE public.idiomas OWNER TO postgres;

--
-- TOC entry 241 (class 1259 OID 21607)
-- Name: idiomas_guia; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.idiomas_guia (
    id bigint NOT NULL,
    prf_gui_id bigint NOT NULL,
    idi_id bigint NOT NULL
);


ALTER TABLE public.idiomas_guia OWNER TO postgres;

--
-- TOC entry 240 (class 1259 OID 21606)
-- Name: idiomas_guia_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.idiomas_guia_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.idiomas_guia_id_seq OWNER TO postgres;

--
-- TOC entry 5518 (class 0 OID 0)
-- Dependencies: 240
-- Name: idiomas_guia_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.idiomas_guia_id_seq OWNED BY public.idiomas_guia.id;


--
-- TOC entry 232 (class 1259 OID 21518)
-- Name: idiomas_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.idiomas_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.idiomas_id_seq OWNER TO postgres;

--
-- TOC entry 5519 (class 0 OID 0)
-- Dependencies: 232
-- Name: idiomas_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.idiomas_id_seq OWNED BY public.idiomas.id;


--
-- TOC entry 257 (class 1259 OID 21763)
-- Name: items_equipo; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.items_equipo (
    id bigint NOT NULL,
    tip_eqp_id bigint NOT NULL,
    tal character varying(20) NOT NULL,
    cnt_tot integer DEFAULT 0 NOT NULL,
    cnt_dis integer DEFAULT 0 NOT NULL,
    con character varying(20) DEFAULT 'bueno'::character varying NOT NULL
);


ALTER TABLE public.items_equipo OWNER TO postgres;

--
-- TOC entry 256 (class 1259 OID 21762)
-- Name: items_equipo_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.items_equipo_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.items_equipo_id_seq OWNER TO postgres;

--
-- TOC entry 5520 (class 0 OID 0)
-- Dependencies: 256
-- Name: items_equipo_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.items_equipo_id_seq OWNED BY public.items_equipo.id;


--
-- TOC entry 225 (class 1259 OID 21462)
-- Name: job_batches; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.job_batches (
    id character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    total_jobs integer NOT NULL,
    pending_jobs integer NOT NULL,
    failed_jobs integer NOT NULL,
    failed_job_ids text NOT NULL,
    options text,
    cancelled_at integer,
    created_at integer NOT NULL,
    finished_at integer
);


ALTER TABLE public.job_batches OWNER TO postgres;

--
-- TOC entry 224 (class 1259 OID 21447)
-- Name: jobs; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.jobs (
    id bigint NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    attempts smallint NOT NULL,
    reserved_at integer,
    available_at integer NOT NULL,
    created_at integer NOT NULL
);


ALTER TABLE public.jobs OWNER TO postgres;

--
-- TOC entry 223 (class 1259 OID 21446)
-- Name: jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.jobs_id_seq OWNER TO postgres;

--
-- TOC entry 5521 (class 0 OID 0)
-- Dependencies: 223
-- Name: jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.jobs_id_seq OWNED BY public.jobs.id;


--
-- TOC entry 283 (class 1259 OID 22091)
-- Name: mensajes_chatbot; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.mensajes_chatbot (
    id bigint NOT NULL,
    cnv_id bigint NOT NULL,
    rem character varying(10) NOT NULL,
    txt_msj text NOT NULL,
    int_det character varying(50),
    cre_en timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.mensajes_chatbot OWNER TO postgres;

--
-- TOC entry 282 (class 1259 OID 22090)
-- Name: mensajes_chatbot_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.mensajes_chatbot_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.mensajes_chatbot_id_seq OWNER TO postgres;

--
-- TOC entry 5522 (class 0 OID 0)
-- Dependencies: 282
-- Name: mensajes_chatbot_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.mensajes_chatbot_id_seq OWNED BY public.mensajes_chatbot.id;


--
-- TOC entry 220 (class 1259 OID 21417)
-- Name: migrations; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


ALTER TABLE public.migrations OWNER TO postgres;

--
-- TOC entry 219 (class 1259 OID 21416)
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.migrations_id_seq OWNER TO postgres;

--
-- TOC entry 5523 (class 0 OID 0)
-- Dependencies: 219
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- TOC entry 277 (class 1259 OID 22027)
-- Name: pagos; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.pagos (
    id bigint NOT NULL,
    cmp_pag_id bigint NOT NULL,
    mtd character varying(15) NOT NULL,
    mto numeric(10,2) NOT NULL,
    ref_trx character varying(100),
    pag_en timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    est character varying(15) DEFAULT 'completado'::character varying NOT NULL
);


ALTER TABLE public.pagos OWNER TO postgres;

--
-- TOC entry 276 (class 1259 OID 22026)
-- Name: pagos_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.pagos_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.pagos_id_seq OWNER TO postgres;

--
-- TOC entry 5524 (class 0 OID 0)
-- Dependencies: 276
-- Name: pagos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.pagos_id_seq OWNED BY public.pagos.id;


--
-- TOC entry 231 (class 1259 OID 21507)
-- Name: paises; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.paises (
    id bigint NOT NULL,
    nom character varying(100) NOT NULL,
    cod_iso character(2) NOT NULL
);


ALTER TABLE public.paises OWNER TO postgres;

--
-- TOC entry 230 (class 1259 OID 21506)
-- Name: paises_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.paises_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.paises_id_seq OWNER TO postgres;

--
-- TOC entry 5525 (class 0 OID 0)
-- Dependencies: 230
-- Name: paises_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.paises_id_seq OWNED BY public.paises.id;


--
-- TOC entry 267 (class 1259 OID 21905)
-- Name: participantes_reserva; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.participantes_reserva (
    id bigint NOT NULL,
    rsv_id bigint NOT NULL,
    nom_cpl character varying(150) NOT NULL,
    tip_doc character varying(20),
    num_doc character varying(15),
    ci_cmp character varying(5) DEFAULT ''::character varying NOT NULL,
    ci_dep_id bigint,
    eda smallint,
    pai_id bigint,
    es_ctc_pri boolean DEFAULT false NOT NULL
);


ALTER TABLE public.participantes_reserva OWNER TO postgres;

--
-- TOC entry 266 (class 1259 OID 21904)
-- Name: participantes_reserva_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.participantes_reserva_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.participantes_reserva_id_seq OWNER TO postgres;

--
-- TOC entry 5526 (class 0 OID 0)
-- Dependencies: 266
-- Name: participantes_reserva_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.participantes_reserva_id_seq OWNED BY public.participantes_reserva.id;


--
-- TOC entry 243 (class 1259 OID 21629)
-- Name: perfiles_chofer; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.perfiles_chofer (
    id bigint NOT NULL,
    usr_id bigint NOT NULL,
    num_lic character varying(30) NOT NULL,
    tip_lic character varying(20) NOT NULL,
    fec_ven_lic date NOT NULL,
    act boolean DEFAULT true NOT NULL
);


ALTER TABLE public.perfiles_chofer OWNER TO postgres;

--
-- TOC entry 242 (class 1259 OID 21628)
-- Name: perfiles_chofer_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.perfiles_chofer_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.perfiles_chofer_id_seq OWNER TO postgres;

--
-- TOC entry 5527 (class 0 OID 0)
-- Dependencies: 242
-- Name: perfiles_chofer_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.perfiles_chofer_id_seq OWNED BY public.perfiles_chofer.id;


--
-- TOC entry 239 (class 1259 OID 21581)
-- Name: perfiles_guia; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.perfiles_guia (
    id bigint NOT NULL,
    usr_id bigint NOT NULL,
    rta_amg character varying(150),
    ani_exp smallint DEFAULT '0'::smallint NOT NULL,
    lim_hrs_sem smallint DEFAULT '40'::smallint NOT NULL,
    bio text,
    act boolean DEFAULT true NOT NULL
);


ALTER TABLE public.perfiles_guia OWNER TO postgres;

--
-- TOC entry 238 (class 1259 OID 21580)
-- Name: perfiles_guia_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.perfiles_guia_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.perfiles_guia_id_seq OWNER TO postgres;

--
-- TOC entry 5528 (class 0 OID 0)
-- Dependencies: 238
-- Name: perfiles_guia_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.perfiles_guia_id_seq OWNED BY public.perfiles_guia.id;


--
-- TOC entry 289 (class 1259 OID 22155)
-- Name: proyecciones_demanda; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.proyecciones_demanda (
    id bigint NOT NULL,
    tur_id bigint NOT NULL,
    mes_per smallint NOT NULL,
    ani_per smallint NOT NULL,
    rsv_pry integer NOT NULL,
    ver_mdl character varying(30),
    gen_en timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.proyecciones_demanda OWNER TO postgres;

--
-- TOC entry 288 (class 1259 OID 22154)
-- Name: proyecciones_demanda_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.proyecciones_demanda_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.proyecciones_demanda_id_seq OWNER TO postgres;

--
-- TOC entry 5529 (class 0 OID 0)
-- Dependencies: 288
-- Name: proyecciones_demanda_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.proyecciones_demanda_id_seq OWNED BY public.proyecciones_demanda.id;


--
-- TOC entry 251 (class 1259 OID 21714)
-- Name: puntos_ruta; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.puntos_ruta (
    id bigint NOT NULL,
    tur_id bigint NOT NULL,
    ord_sec smallint NOT NULL,
    nom character varying(120),
    lat numeric(10,7) NOT NULL,
    lon numeric(10,7) NOT NULL,
    alt_m smallint,
    tip_pun character varying(15) DEFAULT 'checkpoint'::character varying NOT NULL
);


ALTER TABLE public.puntos_ruta OWNER TO postgres;

--
-- TOC entry 250 (class 1259 OID 21713)
-- Name: puntos_ruta_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.puntos_ruta_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.puntos_ruta_id_seq OWNER TO postgres;

--
-- TOC entry 5530 (class 0 OID 0)
-- Dependencies: 250
-- Name: puntos_ruta_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.puntos_ruta_id_seq OWNED BY public.puntos_ruta.id;


--
-- TOC entry 287 (class 1259 OID 22131)
-- Name: registros_notificacion; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.registros_notificacion (
    id bigint NOT NULL,
    rsv_id bigint,
    usr_id bigint,
    can character varying(15) NOT NULL,
    tip character varying(50) NOT NULL,
    est character varying(15) DEFAULT 'pendiente'::character varying NOT NULL,
    cnd jsonb,
    env_en timestamp(0) without time zone
);


ALTER TABLE public.registros_notificacion OWNER TO postgres;

--
-- TOC entry 286 (class 1259 OID 22130)
-- Name: registros_notificacion_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.registros_notificacion_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.registros_notificacion_id_seq OWNER TO postgres;

--
-- TOC entry 5531 (class 0 OID 0)
-- Dependencies: 286
-- Name: registros_notificacion_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.registros_notificacion_id_seq OWNED BY public.registros_notificacion.id;


--
-- TOC entry 279 (class 1259 OID 22047)
-- Name: resenas; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.resenas (
    id bigint NOT NULL,
    tur_id bigint NOT NULL,
    rsv_id bigint,
    cal smallint NOT NULL,
    com text,
    mdt jsonb,
    cre_en timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.resenas OWNER TO postgres;

--
-- TOC entry 278 (class 1259 OID 22046)
-- Name: resenas_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.resenas_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.resenas_id_seq OWNER TO postgres;

--
-- TOC entry 5532 (class 0 OID 0)
-- Dependencies: 278
-- Name: resenas_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.resenas_id_seq OWNED BY public.resenas.id;


--
-- TOC entry 265 (class 1259 OID 21870)
-- Name: reservas; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.reservas (
    id bigint NOT NULL,
    cod character varying(20) NOT NULL,
    sal_tur_id bigint NOT NULL,
    usr_id bigint,
    nom_ctc character varying(150) NOT NULL,
    cor_ctc character varying(150) NOT NULL,
    tel_ctc character varying(20) NOT NULL,
    cnt_ptc smallint NOT NULL,
    est character varying(15) DEFAULT 'pendiente'::character varying NOT NULL,
    pre_tot numeric(10,2) NOT NULL,
    est_pag character varying(15) DEFAULT 'pendiente'::character varying NOT NULL,
    cre_en timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    mod_en timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.reservas OWNER TO postgres;

--
-- TOC entry 264 (class 1259 OID 21869)
-- Name: reservas_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.reservas_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.reservas_id_seq OWNER TO postgres;

--
-- TOC entry 5533 (class 0 OID 0)
-- Dependencies: 264
-- Name: reservas_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.reservas_id_seq OWNED BY public.reservas.id;


--
-- TOC entry 229 (class 1259 OID 21496)
-- Name: roles; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.roles (
    id bigint NOT NULL,
    nom character varying(50) NOT NULL,
    des character varying(255)
);


ALTER TABLE public.roles OWNER TO postgres;

--
-- TOC entry 228 (class 1259 OID 21495)
-- Name: roles_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.roles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.roles_id_seq OWNER TO postgres;

--
-- TOC entry 5534 (class 0 OID 0)
-- Dependencies: 228
-- Name: roles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.roles_id_seq OWNED BY public.roles.id;


--
-- TOC entry 261 (class 1259 OID 21810)
-- Name: salidas_tour; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.salidas_tour (
    id bigint NOT NULL,
    tur_id bigint NOT NULL,
    fec_sal date NOT NULL,
    hra_sal time(0) without time zone NOT NULL,
    prf_gui_id bigint,
    prf_cho_id bigint,
    veh_id bigint,
    cap_max smallint NOT NULL,
    est character varying(15) DEFAULT 'programada'::character varying NOT NULL,
    hra_ini_rea timestamp(0) without time zone,
    hra_fin_rea timestamp(0) without time zone,
    cre_en timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    mod_en timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.salidas_tour OWNER TO postgres;

--
-- TOC entry 260 (class 1259 OID 21809)
-- Name: salidas_tour_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.salidas_tour_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.salidas_tour_id_seq OWNER TO postgres;

--
-- TOC entry 5535 (class 0 OID 0)
-- Dependencies: 260
-- Name: salidas_tour_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.salidas_tour_id_seq OWNED BY public.salidas_tour.id;


--
-- TOC entry 290 (class 1259 OID 22175)
-- Name: sessions; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.sessions (
    id character varying(255) NOT NULL,
    user_id bigint,
    ip_address character varying(45),
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL
);


ALTER TABLE public.sessions OWNER TO postgres;

--
-- TOC entry 255 (class 1259 OID 21748)
-- Name: tipos_equipo; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tipos_equipo (
    id bigint NOT NULL,
    nom character varying(80) NOT NULL,
    des text,
    pre_alq_bas numeric(8,2) DEFAULT '0'::numeric NOT NULL,
    cos_ord numeric(10,2),
    cos_mnt_anu numeric(10,2),
    eqp_cri boolean DEFAULT false NOT NULL
);


ALTER TABLE public.tipos_equipo OWNER TO postgres;

--
-- TOC entry 254 (class 1259 OID 21747)
-- Name: tipos_equipo_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tipos_equipo_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.tipos_equipo_id_seq OWNER TO postgres;

--
-- TOC entry 5536 (class 0 OID 0)
-- Dependencies: 254
-- Name: tipos_equipo_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tipos_equipo_id_seq OWNED BY public.tipos_equipo.id;


--
-- TOC entry 247 (class 1259 OID 21664)
-- Name: tours; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tours (
    id bigint NOT NULL,
    cat_id bigint NOT NULL,
    cod character varying(20) NOT NULL,
    rta_amg character varying(180) NOT NULL,
    nom character varying(150) NOT NULL,
    des text,
    niv_dif character varying(15) NOT NULL,
    alt_min_m smallint,
    alt_max_m smallint,
    dur_hrs numeric(5,2) NOT NULL,
    req_fis text,
    pre_bas numeric(10,2) NOT NULL,
    cap_max smallint NOT NULL,
    trz_geo jsonb,
    act boolean DEFAULT true NOT NULL,
    cre_en timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    mod_en timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    eli_en timestamp(0) without time zone
);


ALTER TABLE public.tours OWNER TO postgres;

--
-- TOC entry 246 (class 1259 OID 21663)
-- Name: tours_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tours_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.tours_id_seq OWNER TO postgres;

--
-- TOC entry 5537 (class 0 OID 0)
-- Dependencies: 246
-- Name: tours_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tours_id_seq OWNED BY public.tours.id;


--
-- TOC entry 237 (class 1259 OID 21543)
-- Name: users; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    rol_id bigint,
    pai_id bigint,
    nom character varying(150) NOT NULL,
    cor character varying(150) NOT NULL,
    cor_vrf_en timestamp(0) without time zone,
    clv character varying(255) NOT NULL,
    rol character varying(30) DEFAULT 'turista'::character varying NOT NULL,
    tel character varying(20),
    tip_doc character varying(20),
    num_doc character varying(15),
    ci_cmp character varying(5) DEFAULT ''::character varying NOT NULL,
    ci_dep_id bigint,
    fot_prf character varying(255),
    tok_rec character varying(100),
    act boolean DEFAULT true NOT NULL,
    remember_token character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    eli_en timestamp(0) without time zone
);


ALTER TABLE public.users OWNER TO postgres;

--
-- TOC entry 236 (class 1259 OID 21542)
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.users_id_seq OWNER TO postgres;

--
-- TOC entry 5538 (class 0 OID 0)
-- Dependencies: 236
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- TOC entry 253 (class 1259 OID 21733)
-- Name: vehiculos; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.vehiculos (
    id bigint NOT NULL,
    pla character varying(15) NOT NULL,
    mar character varying(50),
    mdl character varying(50),
    tip character varying(15) NOT NULL,
    cap_asi smallint NOT NULL,
    tip_cmb character varying(20),
    est character varying(15) DEFAULT 'act'::character varying NOT NULL
);


ALTER TABLE public.vehiculos OWNER TO postgres;

--
-- TOC entry 252 (class 1259 OID 21732)
-- Name: vehiculos_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.vehiculos_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.vehiculos_id_seq OWNER TO postgres;

--
-- TOC entry 5539 (class 0 OID 0)
-- Dependencies: 252
-- Name: vehiculos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.vehiculos_id_seq OWNED BY public.vehiculos.id;


--
-- TOC entry 5090 (class 2604 OID 21937)
-- Name: alquileres_equipo_reserva id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.alquileres_equipo_reserva ALTER COLUMN id SET DEFAULT nextval('public.alquileres_equipo_reserva_id_seq'::regclass);


--
-- TOC entry 5108 (class 2604 OID 22114)
-- Name: bitacora_auditoria id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.bitacora_auditoria ALTER COLUMN id SET DEFAULT nextval('public.bitacora_auditoria_id_seq'::regclass);


--
-- TOC entry 5080 (class 2604 OID 21851)
-- Name: bitacora_viajes_vehiculo id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.bitacora_viajes_vehiculo ALTER COLUMN id SET DEFAULT nextval('public.bitacora_viajes_vehiculo_id_seq'::regclass);


--
-- TOC entry 5091 (class 2604 OID 21958)
-- Name: boletos id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.boletos ALTER COLUMN id SET DEFAULT nextval('public.boletos_id_seq'::regclass);


--
-- TOC entry 5056 (class 2604 OID 21653)
-- Name: categorias_tour id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categorias_tour ALTER COLUMN id SET DEFAULT nextval('public.categorias_tour_id_seq'::regclass);


--
-- TOC entry 5095 (class 2604 OID 22006)
-- Name: comprobantes_pago id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.comprobantes_pago ALTER COLUMN id SET DEFAULT nextval('public.comprobantes_pago_id_seq'::regclass);


--
-- TOC entry 5103 (class 2604 OID 22076)
-- Name: conversaciones_chatbot id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.conversaciones_chatbot ALTER COLUMN id SET DEFAULT nextval('public.conversaciones_chatbot_id_seq'::regclass);


--
-- TOC entry 5044 (class 2604 OID 21534)
-- Name: departamentos_bolivia id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.departamentos_bolivia ALTER COLUMN id SET DEFAULT nextval('public.departamentos_bolivia_id_seq'::regclass);


--
-- TOC entry 5074 (class 2604 OID 21789)
-- Name: equipo_por_tour id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.equipo_por_tour ALTER COLUMN id SET DEFAULT nextval('public.equipo_por_tour_id_seq'::regclass);


--
-- TOC entry 5039 (class 2604 OID 21480)
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- TOC entry 5094 (class 2604 OID 21986)
-- Name: fichas_medicas id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.fichas_medicas ALTER COLUMN id SET DEFAULT nextval('public.fichas_medicas_id_seq'::regclass);


--
-- TOC entry 5061 (class 2604 OID 21700)
-- Name: fotos_tour id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.fotos_tour ALTER COLUMN id SET DEFAULT nextval('public.fotos_tour_id_seq'::regclass);


--
-- TOC entry 5043 (class 2604 OID 21522)
-- Name: idiomas id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.idiomas ALTER COLUMN id SET DEFAULT nextval('public.idiomas_id_seq'::regclass);


--
-- TOC entry 5053 (class 2604 OID 21610)
-- Name: idiomas_guia id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.idiomas_guia ALTER COLUMN id SET DEFAULT nextval('public.idiomas_guia_id_seq'::regclass);


--
-- TOC entry 5070 (class 2604 OID 21766)
-- Name: items_equipo id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.items_equipo ALTER COLUMN id SET DEFAULT nextval('public.items_equipo_id_seq'::regclass);


--
-- TOC entry 5038 (class 2604 OID 21450)
-- Name: jobs id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jobs ALTER COLUMN id SET DEFAULT nextval('public.jobs_id_seq'::regclass);


--
-- TOC entry 5106 (class 2604 OID 22094)
-- Name: mensajes_chatbot id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.mensajes_chatbot ALTER COLUMN id SET DEFAULT nextval('public.mensajes_chatbot_id_seq'::regclass);


--
-- TOC entry 5037 (class 2604 OID 21420)
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- TOC entry 5098 (class 2604 OID 22030)
-- Name: pagos id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pagos ALTER COLUMN id SET DEFAULT nextval('public.pagos_id_seq'::regclass);


--
-- TOC entry 5042 (class 2604 OID 21510)
-- Name: paises id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.paises ALTER COLUMN id SET DEFAULT nextval('public.paises_id_seq'::regclass);


--
-- TOC entry 5087 (class 2604 OID 21908)
-- Name: participantes_reserva id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.participantes_reserva ALTER COLUMN id SET DEFAULT nextval('public.participantes_reserva_id_seq'::regclass);


--
-- TOC entry 5054 (class 2604 OID 21632)
-- Name: perfiles_chofer id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.perfiles_chofer ALTER COLUMN id SET DEFAULT nextval('public.perfiles_chofer_id_seq'::regclass);


--
-- TOC entry 5049 (class 2604 OID 21584)
-- Name: perfiles_guia id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.perfiles_guia ALTER COLUMN id SET DEFAULT nextval('public.perfiles_guia_id_seq'::regclass);


--
-- TOC entry 5112 (class 2604 OID 22158)
-- Name: proyecciones_demanda id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.proyecciones_demanda ALTER COLUMN id SET DEFAULT nextval('public.proyecciones_demanda_id_seq'::regclass);


--
-- TOC entry 5063 (class 2604 OID 21717)
-- Name: puntos_ruta id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.puntos_ruta ALTER COLUMN id SET DEFAULT nextval('public.puntos_ruta_id_seq'::regclass);


--
-- TOC entry 5110 (class 2604 OID 22134)
-- Name: registros_notificacion id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.registros_notificacion ALTER COLUMN id SET DEFAULT nextval('public.registros_notificacion_id_seq'::regclass);


--
-- TOC entry 5101 (class 2604 OID 22050)
-- Name: resenas id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.resenas ALTER COLUMN id SET DEFAULT nextval('public.resenas_id_seq'::regclass);


--
-- TOC entry 5082 (class 2604 OID 21873)
-- Name: reservas id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.reservas ALTER COLUMN id SET DEFAULT nextval('public.reservas_id_seq'::regclass);


--
-- TOC entry 5041 (class 2604 OID 21499)
-- Name: roles id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.roles ALTER COLUMN id SET DEFAULT nextval('public.roles_id_seq'::regclass);


--
-- TOC entry 5076 (class 2604 OID 21813)
-- Name: salidas_tour id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.salidas_tour ALTER COLUMN id SET DEFAULT nextval('public.salidas_tour_id_seq'::regclass);


--
-- TOC entry 5067 (class 2604 OID 21751)
-- Name: tipos_equipo id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tipos_equipo ALTER COLUMN id SET DEFAULT nextval('public.tipos_equipo_id_seq'::regclass);


--
-- TOC entry 5057 (class 2604 OID 21667)
-- Name: tours id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tours ALTER COLUMN id SET DEFAULT nextval('public.tours_id_seq'::regclass);


--
-- TOC entry 5045 (class 2604 OID 21546)
-- Name: users id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- TOC entry 5065 (class 2604 OID 21736)
-- Name: vehiculos id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.vehiculos ALTER COLUMN id SET DEFAULT nextval('public.vehiculos_id_seq'::regclass);


--
-- TOC entry 5479 (class 0 OID 21934)
-- Dependencies: 269
-- Data for Name: alquileres_equipo_reserva; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.alquileres_equipo_reserva (id, ptc_rsv_id, itm_eqp_id, pre) FROM stdin;
1	1	1	20.00
2	2	2	20.00
3	3	3	20.00
4	4	4	20.00
5	5	5	20.00
6	6	6	20.00
7	7	7	20.00
8	8	8	20.00
9	9	9	20.00
10	10	10	20.00
\.


--
-- TOC entry 5495 (class 0 OID 22111)
-- Dependencies: 285
-- Data for Name: bitacora_auditoria; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.bitacora_auditoria (id, usr_id, nom_tbl, id_reg, acc, val_ant, val_nvo, dir_ip, age_usr, cre_en) FROM stdin;
\.


--
-- TOC entry 5473 (class 0 OID 21848)
-- Dependencies: 263
-- Data for Name: bitacora_viajes_vehiculo; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.bitacora_viajes_vehiculo (id, veh_id, sal_tur_id, dst_km, cos_cmb, reg_en) FROM stdin;
1	1	1	29.50	42.50	2026-10-17 03:43:43
2	2	2	34.00	45.00	2026-10-17 03:43:43
3	3	3	38.50	47.50	2026-10-17 03:43:43
4	4	4	43.00	50.00	2026-10-17 03:43:43
5	5	5	47.50	52.50	2026-10-17 03:43:43
6	6	6	52.00	55.00	2026-10-17 03:43:43
7	7	7	56.50	57.50	2026-10-17 03:43:43
8	8	8	61.00	60.00	2026-10-17 03:43:43
9	9	9	65.50	62.50	2026-10-17 03:43:43
10	10	10	70.00	65.00	2026-10-17 03:43:43
11	11	11	74.50	67.50	2026-10-17 03:43:43
12	12	12	79.00	70.00	2026-10-17 03:43:43
13	13	13	83.50	72.50	2026-10-17 03:43:43
14	14	14	88.00	75.00	2026-10-17 03:43:43
15	15	15	92.50	77.50	2026-10-17 03:43:43
16	1	16	97.00	80.00	2026-10-17 03:43:43
17	2	17	101.50	82.50	2026-10-17 03:43:43
18	3	18	106.00	85.00	2026-10-17 03:43:43
19	4	19	110.50	87.50	2026-10-17 03:43:43
20	5	20	115.00	90.00	2026-10-17 03:43:43
21	6	21	119.50	92.50	2026-10-17 03:43:43
22	7	22	124.00	95.00	2026-10-17 03:43:43
23	8	23	128.50	97.50	2026-10-17 03:43:43
24	9	24	133.00	100.00	2026-10-17 03:43:43
25	10	25	137.50	102.50	2026-10-17 03:43:43
26	11	26	142.00	105.00	2026-10-17 03:43:43
27	12	27	146.50	107.50	2026-10-17 03:43:43
28	13	28	151.00	110.00	2026-10-17 03:43:43
29	14	29	155.50	112.50	2026-10-17 03:43:43
30	15	30	160.00	115.00	2026-10-17 03:43:43
\.


--
-- TOC entry 5481 (class 0 OID 21955)
-- Dependencies: 271
-- Data for Name: boletos; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.boletos (id, cod, ptc_rsv_id, est, emt_en, vld_en, vld_por) FROM stdin;
1	BOL-TEST-0001	1	emitido	2026-10-17 03:43:43	\N	\N
2	BOL-TEST-0002	2	emitido	2026-10-17 03:43:43	\N	\N
3	BOL-TEST-0003	3	emitido	2026-10-17 03:43:43	\N	\N
4	BOL-TEST-0004	4	emitido	2026-10-17 03:43:43	\N	\N
5	BOL-TEST-0005	5	emitido	2026-10-17 03:43:43	\N	\N
6	BOL-TEST-0006	6	emitido	2026-10-17 03:43:43	\N	\N
7	BOL-TEST-0007	7	emitido	2026-10-17 03:43:43	\N	\N
8	BOL-TEST-0008	8	emitido	2026-10-17 03:43:43	\N	\N
9	BOL-TEST-0009	9	emitido	2026-10-17 03:43:43	\N	\N
10	BOL-TEST-0010	10	emitido	2026-10-17 03:43:43	\N	\N
11	BOL-TEST-0011	11	emitido	2026-10-17 03:43:43	\N	\N
12	BOL-TEST-0012	12	emitido	2026-10-17 03:43:43	\N	\N
13	BOL-TEST-0013	13	emitido	2026-10-17 03:43:43	\N	\N
14	BOL-TEST-0014	14	emitido	2026-10-17 03:43:43	\N	\N
15	BOL-TEST-0015	15	emitido	2026-10-17 03:43:43	\N	\N
16	BOL-TEST-0016	16	emitido	2026-10-17 03:43:43	\N	\N
17	BOL-TEST-0017	17	emitido	2026-10-17 03:43:43	\N	\N
18	BOL-TEST-0018	18	emitido	2026-10-17 03:43:43	\N	\N
19	BOL-TEST-0019	19	emitido	2026-10-17 03:43:43	\N	\N
20	BOL-TEST-0020	20	emitido	2026-10-17 03:43:43	\N	\N
21	BOL-TEST-0021	21	emitido	2026-10-17 03:43:43	\N	\N
22	BOL-TEST-0022	22	emitido	2026-10-17 03:43:43	\N	\N
23	BOL-TEST-0023	23	emitido	2026-10-17 03:43:43	\N	\N
24	BOL-TEST-0024	24	emitido	2026-10-17 03:43:43	\N	\N
25	BOL-TEST-0025	25	emitido	2026-10-17 03:43:43	\N	\N
26	BOL-TEST-0026	26	emitido	2026-10-17 03:43:43	\N	\N
27	BOL-TEST-0027	27	emitido	2026-10-17 03:43:43	\N	\N
28	BOL-TEST-0028	28	emitido	2026-10-17 03:43:43	\N	\N
29	BOL-TEST-0029	29	emitido	2026-10-17 03:43:43	\N	\N
30	BOL-TEST-0030	30	emitido	2026-10-17 03:43:43	\N	\N
\.


--
-- TOC entry 5431 (class 0 OID 21426)
-- Dependencies: 221
-- Data for Name: cache; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.cache (key, value, expiration) FROM stdin;
\.


--
-- TOC entry 5432 (class 0 OID 21436)
-- Dependencies: 222
-- Data for Name: cache_locks; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.cache_locks (key, owner, expiration) FROM stdin;
\.


--
-- TOC entry 5455 (class 0 OID 21650)
-- Dependencies: 245
-- Data for Name: categorias_tour; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.categorias_tour (id, nom, rta_amg, des, ico) FROM stdin;
1	Aventura	aventura	Categoria de recorridos y experiencias turisticas.	\N
2	Cultura	cultura	Categoria de recorridos y experiencias turisticas.	\N
3	Naturaleza	naturaleza	Categoria de recorridos y experiencias turisticas.	\N
4	Trekking	trekking	Categoria de recorridos y experiencias turisticas.	\N
5	Arqueologia	arqueologia	Categoria de recorridos y experiencias turisticas.	\N
6	Montana	montana	Categoria de recorridos y experiencias turisticas.	\N
7	Lagos	lagos	Categoria de recorridos y experiencias turisticas.	\N
8	Fotografia	fotografia	Categoria de recorridos y experiencias turisticas.	\N
9	Ecoturismo	ecoturismo	Categoria de recorridos y experiencias turisticas.	\N
10	Paisajes Andinos	paisajes-andinos	Categoria de recorridos y experiencias turisticas.	\N
\.


--
-- TOC entry 5485 (class 0 OID 22003)
-- Dependencies: 275
-- Data for Name: comprobantes_pago; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.comprobantes_pago (id, cod, rsv_id, fec_emi, mto_tot, est) FROM stdin;
1	CMP-TEST-0001	1	2026-10-17	115.00	emitido
2	CMP-TEST-0002	2	2026-10-17	130.00	emitido
3	CMP-TEST-0003	3	2026-10-17	145.00	emitido
4	CMP-TEST-0004	4	2026-10-17	160.00	emitido
5	CMP-TEST-0005	5	2026-10-17	175.00	emitido
6	CMP-TEST-0006	6	2026-10-17	190.00	emitido
7	CMP-TEST-0007	7	2026-10-17	205.00	emitido
8	CMP-TEST-0008	8	2026-10-17	220.00	emitido
9	CMP-TEST-0009	9	2026-10-17	235.00	emitido
10	CMP-TEST-0010	10	2026-10-17	250.00	emitido
11	CMP-TEST-0011	11	2026-10-17	265.00	emitido
12	CMP-TEST-0012	12	2026-10-17	280.00	emitido
13	CMP-TEST-0013	13	2026-10-17	295.00	emitido
14	CMP-TEST-0014	14	2026-10-17	310.00	emitido
15	CMP-TEST-0015	15	2026-10-17	325.00	emitido
16	CMP-TEST-0016	16	2026-10-17	340.00	emitido
17	CMP-TEST-0017	17	2026-10-17	355.00	emitido
18	CMP-TEST-0018	18	2026-10-17	370.00	emitido
19	CMP-TEST-0019	19	2026-10-17	385.00	emitido
20	CMP-TEST-0020	20	2026-10-17	400.00	emitido
21	CMP-TEST-0021	21	2026-10-17	415.00	emitido
22	CMP-TEST-0022	22	2026-10-17	430.00	emitido
23	CMP-TEST-0023	23	2026-10-17	445.00	emitido
24	CMP-TEST-0024	24	2026-10-17	460.00	emitido
25	CMP-TEST-0025	25	2026-10-17	475.00	emitido
26	CMP-TEST-0026	26	2026-10-17	490.00	emitido
27	CMP-TEST-0027	27	2026-10-17	505.00	emitido
28	CMP-TEST-0028	28	2026-10-17	520.00	emitido
29	CMP-TEST-0029	29	2026-10-17	535.00	emitido
30	CMP-TEST-0030	30	2026-10-17	550.00	emitido
\.


--
-- TOC entry 5491 (class 0 OID 22073)
-- Dependencies: 281
-- Data for Name: conversaciones_chatbot; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.conversaciones_chatbot (id, usr_id, id_ses, can, ini_en, fin_en) FROM stdin;
\.


--
-- TOC entry 5445 (class 0 OID 21531)
-- Dependencies: 235
-- Data for Name: departamentos_bolivia; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.departamentos_bolivia (id, cod, nom) FROM stdin;
1	LP	La Paz
2	CB	Cochabamba
3	SC	Santa Cruz
4	OR	Oruro
5	PT	Potosí
6	CH	Chuquisaca
7	TJ	Tarija
8	BE	Beni
9	PD	Pando
\.


--
-- TOC entry 5469 (class 0 OID 21786)
-- Dependencies: 259
-- Data for Name: equipo_por_tour; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.equipo_por_tour (id, tur_id, tip_eqp_id, pre_alq_esp, act) FROM stdin;
1	1	1	15.00	t
2	2	2	20.00	t
3	3	3	25.00	t
4	4	4	30.00	t
5	5	5	10.00	t
6	6	6	15.00	t
7	7	7	20.00	t
8	8	8	25.00	t
9	9	9	30.00	t
10	10	10	10.00	t
11	11	1	15.00	t
12	12	2	20.00	t
13	13	3	25.00	t
14	14	4	30.00	t
15	15	5	10.00	t
16	16	6	15.00	t
17	17	7	20.00	t
18	18	8	25.00	t
19	19	9	30.00	t
20	20	10	10.00	t
\.


--
-- TOC entry 5437 (class 0 OID 21477)
-- Dependencies: 227
-- Data for Name: failed_jobs; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.failed_jobs (id, uuid, connection, queue, payload, exception, failed_at) FROM stdin;
\.


--
-- TOC entry 5483 (class 0 OID 21983)
-- Dependencies: 273
-- Data for Name: fichas_medicas; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.fichas_medicas (id, ptc_rsv_id, con_med, nom_ctc_eme, tel_ctc_eme, rta_fir, fir_en, dir_ip) FROM stdin;
1	1	Sin observaciones registradas	Contacto de emergencia de prueba	700000000	firmas/ficha-prueba-0001.png	2026-10-17 03:43:43	\N
2	2	Sin observaciones registradas	Contacto de emergencia de prueba	700000000	firmas/ficha-prueba-0002.png	2026-10-17 03:43:43	\N
3	3	Sin observaciones registradas	Contacto de emergencia de prueba	700000000	firmas/ficha-prueba-0003.png	2026-10-17 03:43:43	\N
4	4	Sin observaciones registradas	Contacto de emergencia de prueba	700000000	firmas/ficha-prueba-0004.png	2026-10-17 03:43:43	\N
5	5	Sin observaciones registradas	Contacto de emergencia de prueba	700000000	firmas/ficha-prueba-0005.png	2026-10-17 03:43:43	\N
6	6	Sin observaciones registradas	Contacto de emergencia de prueba	700000000	firmas/ficha-prueba-0006.png	2026-10-17 03:43:43	\N
7	7	Sin observaciones registradas	Contacto de emergencia de prueba	700000000	firmas/ficha-prueba-0007.png	2026-10-17 03:43:43	\N
8	8	Sin observaciones registradas	Contacto de emergencia de prueba	700000000	firmas/ficha-prueba-0008.png	2026-10-17 03:43:43	\N
9	9	Sin observaciones registradas	Contacto de emergencia de prueba	700000000	firmas/ficha-prueba-0009.png	2026-10-17 03:43:43	\N
10	10	Sin observaciones registradas	Contacto de emergencia de prueba	700000000	firmas/ficha-prueba-0010.png	2026-10-17 03:43:43	\N
11	11	Sin observaciones registradas	Contacto de emergencia de prueba	700000000	firmas/ficha-prueba-0011.png	2026-10-17 03:43:43	\N
12	12	Sin observaciones registradas	Contacto de emergencia de prueba	700000000	firmas/ficha-prueba-0012.png	2026-10-17 03:43:43	\N
13	13	Sin observaciones registradas	Contacto de emergencia de prueba	700000000	firmas/ficha-prueba-0013.png	2026-10-17 03:43:43	\N
14	14	Sin observaciones registradas	Contacto de emergencia de prueba	700000000	firmas/ficha-prueba-0014.png	2026-10-17 03:43:43	\N
15	15	Sin observaciones registradas	Contacto de emergencia de prueba	700000000	firmas/ficha-prueba-0015.png	2026-10-17 03:43:43	\N
16	16	Sin observaciones registradas	Contacto de emergencia de prueba	700000000	firmas/ficha-prueba-0016.png	2026-10-17 03:43:43	\N
17	17	Sin observaciones registradas	Contacto de emergencia de prueba	700000000	firmas/ficha-prueba-0017.png	2026-10-17 03:43:43	\N
18	18	Sin observaciones registradas	Contacto de emergencia de prueba	700000000	firmas/ficha-prueba-0018.png	2026-10-17 03:43:43	\N
19	19	Sin observaciones registradas	Contacto de emergencia de prueba	700000000	firmas/ficha-prueba-0019.png	2026-10-17 03:43:43	\N
20	20	Sin observaciones registradas	Contacto de emergencia de prueba	700000000	firmas/ficha-prueba-0020.png	2026-10-17 03:43:43	\N
21	21	Sin observaciones registradas	Contacto de emergencia de prueba	700000000	firmas/ficha-prueba-0021.png	2026-10-17 03:43:43	\N
22	22	Sin observaciones registradas	Contacto de emergencia de prueba	700000000	firmas/ficha-prueba-0022.png	2026-10-17 03:43:43	\N
23	23	Sin observaciones registradas	Contacto de emergencia de prueba	700000000	firmas/ficha-prueba-0023.png	2026-10-17 03:43:43	\N
24	24	Sin observaciones registradas	Contacto de emergencia de prueba	700000000	firmas/ficha-prueba-0024.png	2026-10-17 03:43:43	\N
25	25	Sin observaciones registradas	Contacto de emergencia de prueba	700000000	firmas/ficha-prueba-0025.png	2026-10-17 03:43:43	\N
26	26	Sin observaciones registradas	Contacto de emergencia de prueba	700000000	firmas/ficha-prueba-0026.png	2026-10-17 03:43:43	\N
27	27	Sin observaciones registradas	Contacto de emergencia de prueba	700000000	firmas/ficha-prueba-0027.png	2026-10-17 03:43:43	\N
28	28	Sin observaciones registradas	Contacto de emergencia de prueba	700000000	firmas/ficha-prueba-0028.png	2026-10-17 03:43:43	\N
29	29	Sin observaciones registradas	Contacto de emergencia de prueba	700000000	firmas/ficha-prueba-0029.png	2026-10-17 03:43:43	\N
30	30	Sin observaciones registradas	Contacto de emergencia de prueba	700000000	firmas/ficha-prueba-0030.png	2026-10-17 03:43:43	\N
\.


--
-- TOC entry 5459 (class 0 OID 21697)
-- Dependencies: 249
-- Data for Name: fotos_tour; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.fotos_tour (id, tur_id, rta_arc, ord_vis, txt_atv) FROM stdin;
1	1	tours/tour-001-foto-1.jpg	1	Imagen de referencia del destino turistico
2	1	tours/tour-001-foto-2.jpg	2	Imagen de referencia del destino turistico
3	2	tours/tour-002-foto-1.jpg	1	Imagen de referencia del destino turistico
4	2	tours/tour-002-foto-2.jpg	2	Imagen de referencia del destino turistico
5	3	tours/tour-003-foto-1.jpg	1	Imagen de referencia del destino turistico
6	3	tours/tour-003-foto-2.jpg	2	Imagen de referencia del destino turistico
7	4	tours/tour-004-foto-1.jpg	1	Imagen de referencia del destino turistico
8	4	tours/tour-004-foto-2.jpg	2	Imagen de referencia del destino turistico
9	5	tours/tour-005-foto-1.jpg	1	Imagen de referencia del destino turistico
10	5	tours/tour-005-foto-2.jpg	2	Imagen de referencia del destino turistico
11	6	tours/tour-006-foto-1.jpg	1	Imagen de referencia del destino turistico
12	6	tours/tour-006-foto-2.jpg	2	Imagen de referencia del destino turistico
13	7	tours/tour-007-foto-1.jpg	1	Imagen de referencia del destino turistico
14	7	tours/tour-007-foto-2.jpg	2	Imagen de referencia del destino turistico
15	8	tours/tour-008-foto-1.jpg	1	Imagen de referencia del destino turistico
16	8	tours/tour-008-foto-2.jpg	2	Imagen de referencia del destino turistico
17	9	tours/tour-009-foto-1.jpg	1	Imagen de referencia del destino turistico
18	9	tours/tour-009-foto-2.jpg	2	Imagen de referencia del destino turistico
19	10	tours/tour-010-foto-1.jpg	1	Imagen de referencia del destino turistico
20	10	tours/tour-010-foto-2.jpg	2	Imagen de referencia del destino turistico
21	11	tours/tour-011-foto-1.jpg	1	Imagen de referencia del destino turistico
22	11	tours/tour-011-foto-2.jpg	2	Imagen de referencia del destino turistico
23	12	tours/tour-012-foto-1.jpg	1	Imagen de referencia del destino turistico
24	12	tours/tour-012-foto-2.jpg	2	Imagen de referencia del destino turistico
25	13	tours/tour-013-foto-1.jpg	1	Imagen de referencia del destino turistico
26	13	tours/tour-013-foto-2.jpg	2	Imagen de referencia del destino turistico
27	14	tours/tour-014-foto-1.jpg	1	Imagen de referencia del destino turistico
28	14	tours/tour-014-foto-2.jpg	2	Imagen de referencia del destino turistico
29	15	tours/tour-015-foto-1.jpg	1	Imagen de referencia del destino turistico
30	15	tours/tour-015-foto-2.jpg	2	Imagen de referencia del destino turistico
31	16	tours/tour-016-foto-1.jpg	1	Imagen de referencia del destino turistico
32	16	tours/tour-016-foto-2.jpg	2	Imagen de referencia del destino turistico
33	17	tours/tour-017-foto-1.jpg	1	Imagen de referencia del destino turistico
34	17	tours/tour-017-foto-2.jpg	2	Imagen de referencia del destino turistico
35	18	tours/tour-018-foto-1.jpg	1	Imagen de referencia del destino turistico
36	18	tours/tour-018-foto-2.jpg	2	Imagen de referencia del destino turistico
37	19	tours/tour-019-foto-1.jpg	1	Imagen de referencia del destino turistico
38	19	tours/tour-019-foto-2.jpg	2	Imagen de referencia del destino turistico
39	20	tours/tour-020-foto-1.jpg	1	Imagen de referencia del destino turistico
40	20	tours/tour-020-foto-2.jpg	2	Imagen de referencia del destino turistico
\.


--
-- TOC entry 5443 (class 0 OID 21519)
-- Dependencies: 233
-- Data for Name: idiomas; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.idiomas (id, nom, cod_iso) FROM stdin;
1	Español	ES
2	Inglés	EN
3	Portugués	PT
\.


--
-- TOC entry 5451 (class 0 OID 21607)
-- Dependencies: 241
-- Data for Name: idiomas_guia; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.idiomas_guia (id, prf_gui_id, idi_id) FROM stdin;
1	1	1
2	1	2
3	1	3
4	2	1
5	2	2
6	2	3
7	3	1
8	3	2
9	3	3
10	4	1
11	4	2
12	4	3
13	5	1
14	5	2
15	5	3
16	6	1
17	6	2
18	6	3
19	7	1
20	7	2
21	7	3
22	8	1
23	8	2
24	8	3
25	9	1
26	9	2
27	9	3
28	10	1
29	10	2
30	10	3
31	11	1
32	11	2
33	11	3
34	12	1
35	12	2
36	12	3
37	13	1
38	13	2
39	13	3
40	14	1
41	14	2
42	14	3
43	15	1
44	15	2
45	15	3
\.


--
-- TOC entry 5467 (class 0 OID 21763)
-- Dependencies: 257
-- Data for Name: items_equipo; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.items_equipo (id, tip_eqp_id, tal, cnt_tot, cnt_dis, con) FROM stdin;
1	1	Unico	10	10	bueno
2	2	Unico	10	10	bueno
3	3	Unico	10	10	bueno
4	4	Unico	10	10	bueno
5	5	Unico	10	10	bueno
6	6	Unico	10	10	bueno
7	7	Unico	10	10	bueno
8	8	Unico	10	10	bueno
9	9	Unico	10	10	bueno
10	10	Unico	10	10	bueno
\.


--
-- TOC entry 5435 (class 0 OID 21462)
-- Dependencies: 225
-- Data for Name: job_batches; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.job_batches (id, name, total_jobs, pending_jobs, failed_jobs, failed_job_ids, options, cancelled_at, created_at, finished_at) FROM stdin;
\.


--
-- TOC entry 5434 (class 0 OID 21447)
-- Dependencies: 224
-- Data for Name: jobs; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.jobs (id, queue, payload, attempts, reserved_at, available_at, created_at) FROM stdin;
\.


--
-- TOC entry 5493 (class 0 OID 22091)
-- Dependencies: 283
-- Data for Name: mensajes_chatbot; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.mensajes_chatbot (id, cnv_id, rem, txt_msj, int_det, cre_en) FROM stdin;
\.


--
-- TOC entry 5430 (class 0 OID 21417)
-- Dependencies: 220
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	0001_01_01_000001_create_cache_table	1
2	0001_01_01_000002_create_jobs_table	1
3	2026_10_12_033611_create_catalogos_generales_table	1
4	2026_10_12_033612_create_usuarios_y_perfiles_table	1
5	2026_10_12_033613_create_tours_y_catalogo_table	1
6	2026_10_12_033614_create_vehiculos_y_flota_table	1
7	2026_10_12_033615_create_equipamiento_y_inventario_table	1
8	2026_10_12_033618_create_operaciones_y_reservas_table	1
9	2026_10_12_033619_create_facturacion_resenas_y_modulos_extras_table	1
10	2026_10_12_040000_create_sessions_table	1
\.


--
-- TOC entry 5487 (class 0 OID 22027)
-- Dependencies: 277
-- Data for Name: pagos; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.pagos (id, cmp_pag_id, mtd, mto, ref_trx, pag_en, est) FROM stdin;
1	1	efectivo	115.00	TRX-TEST-0001	2026-10-17 03:43:43	completado
2	2	efectivo	130.00	TRX-TEST-0002	2026-10-17 03:43:43	completado
3	3	efectivo	145.00	TRX-TEST-0003	2026-10-17 03:43:43	completado
4	4	efectivo	160.00	TRX-TEST-0004	2026-10-17 03:43:43	completado
5	5	efectivo	175.00	TRX-TEST-0005	2026-10-17 03:43:43	completado
6	6	efectivo	190.00	TRX-TEST-0006	2026-10-17 03:43:43	completado
7	7	efectivo	205.00	TRX-TEST-0007	2026-10-17 03:43:43	completado
8	8	efectivo	220.00	TRX-TEST-0008	2026-10-17 03:43:43	completado
9	9	efectivo	235.00	TRX-TEST-0009	2026-10-17 03:43:43	completado
10	10	efectivo	250.00	TRX-TEST-0010	2026-10-17 03:43:43	completado
11	11	efectivo	265.00	TRX-TEST-0011	2026-10-17 03:43:43	completado
12	12	efectivo	280.00	TRX-TEST-0012	2026-10-17 03:43:43	completado
13	13	efectivo	295.00	TRX-TEST-0013	2026-10-17 03:43:43	completado
14	14	efectivo	310.00	TRX-TEST-0014	2026-10-17 03:43:43	completado
15	15	efectivo	325.00	TRX-TEST-0015	2026-10-17 03:43:43	completado
16	16	efectivo	340.00	TRX-TEST-0016	2026-10-17 03:43:43	completado
17	17	efectivo	355.00	TRX-TEST-0017	2026-10-17 03:43:43	completado
18	18	efectivo	370.00	TRX-TEST-0018	2026-10-17 03:43:43	completado
19	19	efectivo	385.00	TRX-TEST-0019	2026-10-17 03:43:43	completado
20	20	efectivo	400.00	TRX-TEST-0020	2026-10-17 03:43:43	completado
21	21	efectivo	415.00	TRX-TEST-0021	2026-10-17 03:43:43	completado
22	22	efectivo	430.00	TRX-TEST-0022	2026-10-17 03:43:43	completado
23	23	efectivo	445.00	TRX-TEST-0023	2026-10-17 03:43:43	completado
24	24	efectivo	460.00	TRX-TEST-0024	2026-10-17 03:43:43	completado
25	25	efectivo	475.00	TRX-TEST-0025	2026-10-17 03:43:43	completado
26	26	efectivo	490.00	TRX-TEST-0026	2026-10-17 03:43:43	completado
27	27	efectivo	505.00	TRX-TEST-0027	2026-10-17 03:43:43	completado
28	28	efectivo	520.00	TRX-TEST-0028	2026-10-17 03:43:43	completado
29	29	efectivo	535.00	TRX-TEST-0029	2026-10-17 03:43:43	completado
30	30	efectivo	550.00	TRX-TEST-0030	2026-10-17 03:43:43	completado
\.


--
-- TOC entry 5441 (class 0 OID 21507)
-- Dependencies: 231
-- Data for Name: paises; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.paises (id, nom, cod_iso) FROM stdin;
\.


--
-- TOC entry 5477 (class 0 OID 21905)
-- Dependencies: 267
-- Data for Name: participantes_reserva; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.participantes_reserva (id, rsv_id, nom_cpl, tip_doc, num_doc, ci_cmp, ci_dep_id, eda, pai_id, es_ctc_pri) FROM stdin;
1	1	Participante de prueba 01	CI	P00001	1A	1	21	\N	t
2	2	Participante de prueba 02	CI	P00002	1A	1	22	\N	t
3	3	Participante de prueba 03	CI	P00003	1A	1	23	\N	t
4	4	Participante de prueba 04	CI	P00004	1A	1	24	\N	t
5	5	Participante de prueba 05	CI	P00005	1A	1	25	\N	t
6	6	Participante de prueba 06	CI	P00006	1A	1	26	\N	t
7	7	Participante de prueba 07	CI	P00007	1A	1	27	\N	t
8	8	Participante de prueba 08	CI	P00008	1A	1	28	\N	t
9	9	Participante de prueba 09	CI	P00009	1A	1	29	\N	t
10	10	Participante de prueba 10	CI	P00010	1A	1	30	\N	t
11	11	Participante de prueba 11	CI	P00011	1A	1	31	\N	t
12	12	Participante de prueba 12	CI	P00012	1A	1	32	\N	t
13	13	Participante de prueba 13	CI	P00013	1A	1	33	\N	t
14	14	Participante de prueba 14	CI	P00014	1A	1	34	\N	t
15	15	Participante de prueba 15	CI	P00015	1A	1	35	\N	t
16	16	Participante de prueba 16	CI	P00016	1A	1	36	\N	t
17	17	Participante de prueba 17	CI	P00017	1A	1	37	\N	t
18	18	Participante de prueba 18	CI	P00018	1A	1	38	\N	t
19	19	Participante de prueba 19	CI	P00019	1A	1	39	\N	t
20	20	Participante de prueba 20	CI	P00020	1A	1	40	\N	t
21	21	Participante de prueba 21	CI	P00021	1A	1	41	\N	t
22	22	Participante de prueba 22	CI	P00022	1A	1	42	\N	t
23	23	Participante de prueba 23	CI	P00023	1A	1	43	\N	t
24	24	Participante de prueba 24	CI	P00024	1A	1	44	\N	t
25	25	Participante de prueba 25	CI	P00025	1A	1	45	\N	t
26	26	Participante de prueba 26	CI	P00026	1A	1	46	\N	t
27	27	Participante de prueba 27	CI	P00027	1A	1	47	\N	t
28	28	Participante de prueba 28	CI	P00028	1A	1	48	\N	t
29	29	Participante de prueba 29	CI	P00029	1A	1	49	\N	t
30	30	Participante de prueba 30	CI	P00030	1A	1	50	\N	t
\.


--
-- TOC entry 5453 (class 0 OID 21629)
-- Dependencies: 243
-- Data for Name: perfiles_chofer; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.perfiles_chofer (id, usr_id, num_lic, tip_lic, fec_ven_lic, act) FROM stdin;
1	17	LIC-000001	C	2027-10-17	t
2	18	LIC-000002	C	2027-10-17	t
3	19	LIC-000003	C	2027-10-17	t
4	20	LIC-000004	C	2027-10-17	t
5	21	LIC-000005	C	2027-10-17	t
6	22	LIC-000006	C	2027-10-17	t
7	23	LIC-000007	C	2027-10-17	t
8	24	LIC-000008	C	2027-10-17	t
9	25	LIC-000009	C	2027-10-17	t
10	26	LIC-000010	C	2027-10-17	t
11	27	LIC-000011	C	2027-10-17	t
12	28	LIC-000012	C	2027-10-17	t
13	29	LIC-000013	C	2027-10-17	t
14	30	LIC-000014	C	2027-10-17	t
15	31	LIC-000015	C	2027-10-17	t
\.


--
-- TOC entry 5449 (class 0 OID 21581)
-- Dependencies: 239
-- Data for Name: perfiles_guia; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.perfiles_guia (id, usr_id, rta_amg, ani_exp, lim_hrs_sem, bio, act) FROM stdin;
1	2	\N	2	40	Guia turistico especializado en recorridos por Bolivia.	t
2	3	\N	3	40	Guia turistico especializado en recorridos por Bolivia.	t
3	4	\N	4	40	Guia turistico especializado en recorridos por Bolivia.	t
4	5	\N	5	40	Guia turistico especializado en recorridos por Bolivia.	t
5	6	\N	6	40	Guia turistico especializado en recorridos por Bolivia.	t
6	7	\N	7	40	Guia turistico especializado en recorridos por Bolivia.	t
7	8	\N	8	40	Guia turistico especializado en recorridos por Bolivia.	t
8	9	\N	9	40	Guia turistico especializado en recorridos por Bolivia.	t
9	10	\N	10	40	Guia turistico especializado en recorridos por Bolivia.	t
10	11	\N	1	40	Guia turistico especializado en recorridos por Bolivia.	t
11	12	\N	2	40	Guia turistico especializado en recorridos por Bolivia.	t
12	13	\N	3	40	Guia turistico especializado en recorridos por Bolivia.	t
13	14	\N	4	40	Guia turistico especializado en recorridos por Bolivia.	t
14	15	\N	5	40	Guia turistico especializado en recorridos por Bolivia.	t
15	16	\N	6	40	Guia turistico especializado en recorridos por Bolivia.	t
\.


--
-- TOC entry 5499 (class 0 OID 22155)
-- Dependencies: 289
-- Data for Name: proyecciones_demanda; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.proyecciones_demanda (id, tur_id, mes_per, ani_per, rsv_pry, ver_mdl, gen_en) FROM stdin;
1	1	1	2026	12	modelo_demo	2026-10-17 03:43:43
2	2	2	2026	14	modelo_demo	2026-10-17 03:43:43
3	3	3	2026	16	modelo_demo	2026-10-17 03:43:43
4	4	4	2026	18	modelo_demo	2026-10-17 03:43:43
5	5	5	2026	20	modelo_demo	2026-10-17 03:43:43
6	6	6	2026	22	modelo_demo	2026-10-17 03:43:43
7	7	7	2026	24	modelo_demo	2026-10-17 03:43:43
8	8	8	2026	26	modelo_demo	2026-10-17 03:43:43
9	9	9	2026	28	modelo_demo	2026-10-17 03:43:43
10	10	10	2026	30	modelo_demo	2026-10-17 03:43:43
11	11	11	2026	32	modelo_demo	2026-10-17 03:43:43
12	12	12	2026	34	modelo_demo	2026-10-17 03:43:43
13	13	1	2027	36	modelo_demo	2026-10-17 03:43:43
14	14	2	2027	38	modelo_demo	2026-10-17 03:43:43
15	15	3	2027	40	modelo_demo	2026-10-17 03:43:43
16	16	4	2027	42	modelo_demo	2026-10-17 03:43:43
17	17	5	2027	44	modelo_demo	2026-10-17 03:43:43
18	18	6	2027	46	modelo_demo	2026-10-17 03:43:43
19	19	7	2027	48	modelo_demo	2026-10-17 03:43:43
20	20	8	2027	50	modelo_demo	2026-10-17 03:43:43
\.


--
-- TOC entry 5461 (class 0 OID 21714)
-- Dependencies: 251
-- Data for Name: puntos_ruta; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.puntos_ruta (id, tur_id, ord_sec, nom, lat, lon, alt_m, tip_pun) FROM stdin;
1	1	1	Inicio del recorrido en Uyuni	-16.5900000	-68.2200000	3520	inicio
2	1	2	Cementerio de trenes	-16.6000000	-68.2300000	3520	parada
3	1	3	Salar de Uyuni	-16.6100000	-68.2400000	3520	destino
4	2	1	Ingreso al Valle de la Luna	-16.6700000	-68.2800000	3540	inicio
5	2	2	Mirador del valle	-16.6800000	-68.2900000	3540	parada
6	2	3	Fin del recorrido	-16.6900000	-68.3000000	3540	destino
7	3	1	Copacabana	-16.7500000	-68.3400000	3560	inicio
8	3	2	Orillas del lago Titicaca	-16.7600000	-68.3500000	3560	parada
9	3	3	Punto panoramico	-16.7700000	-68.3600000	3560	destino
10	4	1	Punto de encuentro	-16.8300000	-68.4000000	3580	inicio
11	4	2	Parada panoramica	-16.8400000	-68.4100000	3580	parada
12	4	3	Destino principal	-16.8500000	-68.4200000	3580	destino
13	5	1	Punto de encuentro	-16.9100000	-68.4600000	3600	inicio
14	5	2	Parada panoramica	-16.9200000	-68.4700000	3600	parada
15	5	3	Destino principal	-16.9300000	-68.4800000	3600	destino
16	6	1	Punto de encuentro	-16.9900000	-68.5200000	3620	inicio
17	6	2	Parada panoramica	-17.0000000	-68.5300000	3620	parada
18	6	3	Destino principal	-17.0100000	-68.5400000	3620	destino
19	7	1	Punto de encuentro	-17.0700000	-68.5800000	3640	inicio
20	7	2	Parada panoramica	-17.0800000	-68.5900000	3640	parada
21	7	3	Destino principal	-17.0900000	-68.6000000	3640	destino
22	8	1	Punto de encuentro	-17.1500000	-68.6400000	3660	inicio
23	8	2	Parada panoramica	-17.1600000	-68.6500000	3660	parada
24	8	3	Destino principal	-17.1700000	-68.6600000	3660	destino
25	9	1	Punto de encuentro	-17.2300000	-68.7000000	3680	inicio
26	9	2	Parada panoramica	-17.2400000	-68.7100000	3680	parada
27	9	3	Destino principal	-17.2500000	-68.7200000	3680	destino
28	10	1	Punto de encuentro	-17.3100000	-68.7600000	3700	inicio
29	10	2	Parada panoramica	-17.3200000	-68.7700000	3700	parada
30	10	3	Destino principal	-17.3300000	-68.7800000	3700	destino
31	11	1	Punto de encuentro	-17.3900000	-68.8200000	3720	inicio
32	11	2	Parada panoramica	-17.4000000	-68.8300000	3720	parada
33	11	3	Destino principal	-17.4100000	-68.8400000	3720	destino
34	12	1	Punto de encuentro	-17.4700000	-68.8800000	3740	inicio
35	12	2	Parada panoramica	-17.4800000	-68.8900000	3740	parada
36	12	3	Destino principal	-17.4900000	-68.9000000	3740	destino
37	13	1	Punto de encuentro	-17.5500000	-68.9400000	3760	inicio
38	13	2	Parada panoramica	-17.5600000	-68.9500000	3760	parada
39	13	3	Destino principal	-17.5700000	-68.9600000	3760	destino
40	14	1	Punto de encuentro	-17.6300000	-69.0000000	3780	inicio
41	14	2	Parada panoramica	-17.6400000	-69.0100000	3780	parada
42	14	3	Destino principal	-17.6500000	-69.0200000	3780	destino
43	15	1	Punto de encuentro	-17.7100000	-69.0600000	3800	inicio
44	15	2	Parada panoramica	-17.7200000	-69.0700000	3800	parada
45	15	3	Destino principal	-17.7300000	-69.0800000	3800	destino
46	16	1	Punto de encuentro	-17.7900000	-69.1200000	3820	inicio
47	16	2	Parada panoramica	-17.8000000	-69.1300000	3820	parada
48	16	3	Destino principal	-17.8100000	-69.1400000	3820	destino
49	17	1	Punto de encuentro	-17.8700000	-69.1800000	3840	inicio
50	17	2	Parada panoramica	-17.8800000	-69.1900000	3840	parada
51	17	3	Destino principal	-17.8900000	-69.2000000	3840	destino
52	18	1	Punto de encuentro	-17.9500000	-69.2400000	3860	inicio
53	18	2	Parada panoramica	-17.9600000	-69.2500000	3860	parada
54	18	3	Destino principal	-17.9700000	-69.2600000	3860	destino
55	19	1	Punto de encuentro	-18.0300000	-69.3000000	3880	inicio
56	19	2	Parada panoramica	-18.0400000	-69.3100000	3880	parada
57	19	3	Destino principal	-18.0500000	-69.3200000	3880	destino
58	20	1	Punto de encuentro	-18.1100000	-69.3600000	3900	inicio
59	20	2	Parada panoramica	-18.1200000	-69.3700000	3900	parada
60	20	3	Destino principal	-18.1300000	-69.3800000	3900	destino
\.


--
-- TOC entry 5497 (class 0 OID 22131)
-- Dependencies: 287
-- Data for Name: registros_notificacion; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.registros_notificacion (id, rsv_id, usr_id, can, tip, est, cnd, env_en) FROM stdin;
1	1	32	email	confirmacion_reserva	pendiente	{"asunto": "Confirmacion de reserva", "codigo": "RSV-TEST-0001"}	\N
2	2	33	email	confirmacion_reserva	pendiente	{"asunto": "Confirmacion de reserva", "codigo": "RSV-TEST-0002"}	\N
3	3	34	email	confirmacion_reserva	pendiente	{"asunto": "Confirmacion de reserva", "codigo": "RSV-TEST-0003"}	\N
4	4	35	email	confirmacion_reserva	pendiente	{"asunto": "Confirmacion de reserva", "codigo": "RSV-TEST-0004"}	\N
5	5	36	email	confirmacion_reserva	pendiente	{"asunto": "Confirmacion de reserva", "codigo": "RSV-TEST-0005"}	\N
6	6	37	email	confirmacion_reserva	pendiente	{"asunto": "Confirmacion de reserva", "codigo": "RSV-TEST-0006"}	\N
7	7	38	email	confirmacion_reserva	pendiente	{"asunto": "Confirmacion de reserva", "codigo": "RSV-TEST-0007"}	\N
8	8	39	email	confirmacion_reserva	pendiente	{"asunto": "Confirmacion de reserva", "codigo": "RSV-TEST-0008"}	\N
9	9	40	email	confirmacion_reserva	pendiente	{"asunto": "Confirmacion de reserva", "codigo": "RSV-TEST-0009"}	\N
10	10	41	email	confirmacion_reserva	pendiente	{"asunto": "Confirmacion de reserva", "codigo": "RSV-TEST-0010"}	\N
11	11	42	email	confirmacion_reserva	pendiente	{"asunto": "Confirmacion de reserva", "codigo": "RSV-TEST-0011"}	\N
12	12	43	email	confirmacion_reserva	pendiente	{"asunto": "Confirmacion de reserva", "codigo": "RSV-TEST-0012"}	\N
13	13	44	email	confirmacion_reserva	pendiente	{"asunto": "Confirmacion de reserva", "codigo": "RSV-TEST-0013"}	\N
14	14	45	email	confirmacion_reserva	pendiente	{"asunto": "Confirmacion de reserva", "codigo": "RSV-TEST-0014"}	\N
15	15	46	email	confirmacion_reserva	pendiente	{"asunto": "Confirmacion de reserva", "codigo": "RSV-TEST-0015"}	\N
16	16	47	email	confirmacion_reserva	pendiente	{"asunto": "Confirmacion de reserva", "codigo": "RSV-TEST-0016"}	\N
17	17	48	email	confirmacion_reserva	pendiente	{"asunto": "Confirmacion de reserva", "codigo": "RSV-TEST-0017"}	\N
18	18	49	email	confirmacion_reserva	pendiente	{"asunto": "Confirmacion de reserva", "codigo": "RSV-TEST-0018"}	\N
19	19	50	email	confirmacion_reserva	pendiente	{"asunto": "Confirmacion de reserva", "codigo": "RSV-TEST-0019"}	\N
20	20	51	email	confirmacion_reserva	pendiente	{"asunto": "Confirmacion de reserva", "codigo": "RSV-TEST-0020"}	\N
21	21	32	email	confirmacion_reserva	pendiente	{"asunto": "Confirmacion de reserva", "codigo": "RSV-TEST-0021"}	\N
22	22	33	email	confirmacion_reserva	pendiente	{"asunto": "Confirmacion de reserva", "codigo": "RSV-TEST-0022"}	\N
23	23	34	email	confirmacion_reserva	pendiente	{"asunto": "Confirmacion de reserva", "codigo": "RSV-TEST-0023"}	\N
24	24	35	email	confirmacion_reserva	pendiente	{"asunto": "Confirmacion de reserva", "codigo": "RSV-TEST-0024"}	\N
25	25	36	email	confirmacion_reserva	pendiente	{"asunto": "Confirmacion de reserva", "codigo": "RSV-TEST-0025"}	\N
26	26	37	email	confirmacion_reserva	pendiente	{"asunto": "Confirmacion de reserva", "codigo": "RSV-TEST-0026"}	\N
27	27	38	email	confirmacion_reserva	pendiente	{"asunto": "Confirmacion de reserva", "codigo": "RSV-TEST-0027"}	\N
28	28	39	email	confirmacion_reserva	pendiente	{"asunto": "Confirmacion de reserva", "codigo": "RSV-TEST-0028"}	\N
29	29	40	email	confirmacion_reserva	pendiente	{"asunto": "Confirmacion de reserva", "codigo": "RSV-TEST-0029"}	\N
30	30	41	email	confirmacion_reserva	pendiente	{"asunto": "Confirmacion de reserva", "codigo": "RSV-TEST-0030"}	\N
\.


--
-- TOC entry 5489 (class 0 OID 22047)
-- Dependencies: 279
-- Data for Name: resenas; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.resenas (id, tur_id, rsv_id, cal, com, mdt, cre_en) FROM stdin;
1	1	1	4	Buena experiencia y guia amable.	\N	2026-10-17 03:43:43
2	2	2	5	Excelente recorrido y buena organizacion.	\N	2026-10-17 03:43:43
3	3	3	3	Buena experiencia y guia amable.	\N	2026-10-17 03:43:43
4	4	4	4	Excelente recorrido y buena organizacion.	\N	2026-10-17 03:43:43
5	5	5	5	Buena experiencia y guia amable.	\N	2026-10-17 03:43:43
6	6	6	3	Excelente recorrido y buena organizacion.	\N	2026-10-17 03:43:43
7	7	7	4	Buena experiencia y guia amable.	\N	2026-10-17 03:43:43
8	8	8	5	Excelente recorrido y buena organizacion.	\N	2026-10-17 03:43:43
9	9	9	3	Buena experiencia y guia amable.	\N	2026-10-17 03:43:43
10	10	10	4	Excelente recorrido y buena organizacion.	\N	2026-10-17 03:43:43
11	11	11	5	Buena experiencia y guia amable.	\N	2026-10-17 03:43:43
12	12	12	3	Excelente recorrido y buena organizacion.	\N	2026-10-17 03:43:43
13	13	13	4	Buena experiencia y guia amable.	\N	2026-10-17 03:43:43
14	14	14	5	Excelente recorrido y buena organizacion.	\N	2026-10-17 03:43:43
15	15	15	3	Buena experiencia y guia amable.	\N	2026-10-17 03:43:43
\.


--
-- TOC entry 5475 (class 0 OID 21870)
-- Dependencies: 265
-- Data for Name: reservas; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.reservas (id, cod, sal_tur_id, usr_id, nom_ctc, cor_ctc, tel_ctc, cnt_ptc, est, pre_tot, est_pag, cre_en, mod_en) FROM stdin;
1	RSV-TEST-0001	1	32	Turista de prueba 01	turista001@prueba.test	700300001	1	confirmada	115.00	completado	2026-10-17 03:43:43	2026-10-17 03:43:43
2	RSV-TEST-0002	2	33	Turista de prueba 02	turista002@prueba.test	700300002	1	confirmada	130.00	completado	2026-10-17 03:43:43	2026-10-17 03:43:43
3	RSV-TEST-0003	3	34	Turista de prueba 03	turista003@prueba.test	700300003	1	confirmada	145.00	completado	2026-10-17 03:43:43	2026-10-17 03:43:43
4	RSV-TEST-0004	4	35	Turista de prueba 04	turista004@prueba.test	700300004	1	confirmada	160.00	completado	2026-10-17 03:43:43	2026-10-17 03:43:43
5	RSV-TEST-0005	5	36	Turista de prueba 05	turista005@prueba.test	700300005	1	confirmada	175.00	completado	2026-10-17 03:43:43	2026-10-17 03:43:43
6	RSV-TEST-0006	6	37	Turista de prueba 06	turista006@prueba.test	700300006	1	confirmada	190.00	completado	2026-10-17 03:43:43	2026-10-17 03:43:43
7	RSV-TEST-0007	7	38	Turista de prueba 07	turista007@prueba.test	700300007	1	confirmada	205.00	completado	2026-10-17 03:43:43	2026-10-17 03:43:43
8	RSV-TEST-0008	8	39	Turista de prueba 08	turista008@prueba.test	700300008	1	confirmada	220.00	completado	2026-10-17 03:43:43	2026-10-17 03:43:43
9	RSV-TEST-0009	9	40	Turista de prueba 09	turista009@prueba.test	700300009	1	confirmada	235.00	completado	2026-10-17 03:43:43	2026-10-17 03:43:43
10	RSV-TEST-0010	10	41	Turista de prueba 10	turista010@prueba.test	700300010	1	confirmada	250.00	completado	2026-10-17 03:43:43	2026-10-17 03:43:43
11	RSV-TEST-0011	11	42	Turista de prueba 11	turista011@prueba.test	700300011	1	confirmada	265.00	completado	2026-10-17 03:43:43	2026-10-17 03:43:43
12	RSV-TEST-0012	12	43	Turista de prueba 12	turista012@prueba.test	700300012	1	confirmada	280.00	completado	2026-10-17 03:43:43	2026-10-17 03:43:43
13	RSV-TEST-0013	13	44	Turista de prueba 13	turista013@prueba.test	700300013	1	confirmada	295.00	completado	2026-10-17 03:43:43	2026-10-17 03:43:43
14	RSV-TEST-0014	14	45	Turista de prueba 14	turista014@prueba.test	700300014	1	confirmada	310.00	completado	2026-10-17 03:43:43	2026-10-17 03:43:43
15	RSV-TEST-0015	15	46	Turista de prueba 15	turista015@prueba.test	700300015	1	confirmada	325.00	completado	2026-10-17 03:43:43	2026-10-17 03:43:43
16	RSV-TEST-0016	16	47	Turista de prueba 16	turista016@prueba.test	700300016	1	confirmada	340.00	completado	2026-10-17 03:43:43	2026-10-17 03:43:43
17	RSV-TEST-0017	17	48	Turista de prueba 17	turista017@prueba.test	700300017	1	confirmada	355.00	completado	2026-10-17 03:43:43	2026-10-17 03:43:43
18	RSV-TEST-0018	18	49	Turista de prueba 18	turista018@prueba.test	700300018	1	confirmada	370.00	completado	2026-10-17 03:43:43	2026-10-17 03:43:43
19	RSV-TEST-0019	19	50	Turista de prueba 19	turista019@prueba.test	700300019	1	confirmada	385.00	completado	2026-10-17 03:43:43	2026-10-17 03:43:43
20	RSV-TEST-0020	20	51	Turista de prueba 20	turista020@prueba.test	700300020	1	confirmada	400.00	completado	2026-10-17 03:43:43	2026-10-17 03:43:43
21	RSV-TEST-0021	21	32	Turista de prueba 21	turista001@prueba.test	700300001	1	confirmada	415.00	completado	2026-10-17 03:43:43	2026-10-17 03:43:43
22	RSV-TEST-0022	22	33	Turista de prueba 22	turista002@prueba.test	700300002	1	confirmada	430.00	completado	2026-10-17 03:43:43	2026-10-17 03:43:43
23	RSV-TEST-0023	23	34	Turista de prueba 23	turista003@prueba.test	700300003	1	confirmada	445.00	completado	2026-10-17 03:43:43	2026-10-17 03:43:43
24	RSV-TEST-0024	24	35	Turista de prueba 24	turista004@prueba.test	700300004	1	confirmada	460.00	completado	2026-10-17 03:43:43	2026-10-17 03:43:43
25	RSV-TEST-0025	25	36	Turista de prueba 25	turista005@prueba.test	700300005	1	confirmada	475.00	completado	2026-10-17 03:43:43	2026-10-17 03:43:43
26	RSV-TEST-0026	26	37	Turista de prueba 26	turista006@prueba.test	700300006	1	confirmada	490.00	completado	2026-10-17 03:43:43	2026-10-17 03:43:43
27	RSV-TEST-0027	27	38	Turista de prueba 27	turista007@prueba.test	700300007	1	confirmada	505.00	completado	2026-10-17 03:43:43	2026-10-17 03:43:43
28	RSV-TEST-0028	28	39	Turista de prueba 28	turista008@prueba.test	700300008	1	confirmada	520.00	completado	2026-10-17 03:43:43	2026-10-17 03:43:43
29	RSV-TEST-0029	29	40	Turista de prueba 29	turista009@prueba.test	700300009	1	confirmada	535.00	completado	2026-10-17 03:43:43	2026-10-17 03:43:43
30	RSV-TEST-0030	30	41	Turista de prueba 30	turista010@prueba.test	700300010	1	confirmada	550.00	completado	2026-10-17 03:43:43	2026-10-17 03:43:43
\.


--
-- TOC entry 5439 (class 0 OID 21496)
-- Dependencies: 229
-- Data for Name: roles; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.roles (id, nom, des) FROM stdin;
1	admin	Administrador general del sistema
2	operaciones	Personal de logistica y salidas
3	guia	Guia turistico autorizado
4	chofer	Chofer de la flota de vehiculos
5	turista	Turista nacional o extranjero
\.


--
-- TOC entry 5471 (class 0 OID 21810)
-- Dependencies: 261
-- Data for Name: salidas_tour; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.salidas_tour (id, tur_id, fec_sal, hra_sal, prf_gui_id, prf_cho_id, veh_id, cap_max, est, hra_ini_rea, hra_fin_rea, cre_en, mod_en) FROM stdin;
1	1	2026-10-18	08:00:00	1	1	1	7	programada	\N	\N	2026-10-17 03:43:43	2026-10-17 03:43:43
2	2	2026-10-19	08:00:00	2	2	2	7	programada	\N	\N	2026-10-17 03:43:43	2026-10-17 03:43:43
3	3	2026-10-20	08:00:00	3	3	3	7	programada	\N	\N	2026-10-17 03:43:43	2026-10-17 03:43:43
4	4	2026-10-21	08:00:00	4	4	4	7	programada	\N	\N	2026-10-17 03:43:43	2026-10-17 03:43:43
5	5	2026-10-22	08:00:00	5	5	5	7	programada	\N	\N	2026-10-17 03:43:43	2026-10-17 03:43:43
6	6	2026-10-23	08:00:00	6	6	6	7	programada	\N	\N	2026-10-17 03:43:43	2026-10-17 03:43:43
7	7	2026-10-24	08:00:00	7	7	7	7	programada	\N	\N	2026-10-17 03:43:43	2026-10-17 03:43:43
8	8	2026-10-25	08:00:00	8	8	8	7	programada	\N	\N	2026-10-17 03:43:43	2026-10-17 03:43:43
9	9	2026-10-26	08:00:00	9	9	9	7	programada	\N	\N	2026-10-17 03:43:43	2026-10-17 03:43:43
10	10	2026-10-27	08:00:00	10	10	10	7	programada	\N	\N	2026-10-17 03:43:43	2026-10-17 03:43:43
11	11	2026-10-28	08:00:00	11	11	11	7	programada	\N	\N	2026-10-17 03:43:43	2026-10-17 03:43:43
12	12	2026-10-29	08:00:00	12	12	12	7	programada	\N	\N	2026-10-17 03:43:43	2026-10-17 03:43:43
13	13	2026-10-30	08:00:00	13	13	13	7	programada	\N	\N	2026-10-17 03:43:43	2026-10-17 03:43:43
14	14	2026-10-31	08:00:00	14	14	14	7	programada	\N	\N	2026-10-17 03:43:43	2026-10-17 03:43:43
15	15	2026-11-01	08:00:00	15	15	15	7	programada	\N	\N	2026-10-17 03:43:43	2026-10-17 03:43:43
16	16	2026-11-02	08:00:00	1	1	1	7	programada	\N	\N	2026-10-17 03:43:43	2026-10-17 03:43:43
17	17	2026-11-03	08:00:00	2	2	2	7	programada	\N	\N	2026-10-17 03:43:43	2026-10-17 03:43:43
18	18	2026-11-04	08:00:00	3	3	3	7	programada	\N	\N	2026-10-17 03:43:43	2026-10-17 03:43:43
19	19	2026-11-05	08:00:00	4	4	4	7	programada	\N	\N	2026-10-17 03:43:43	2026-10-17 03:43:43
20	20	2026-11-06	08:00:00	5	5	5	7	programada	\N	\N	2026-10-17 03:43:43	2026-10-17 03:43:43
21	1	2026-11-07	08:00:00	6	6	6	7	programada	\N	\N	2026-10-17 03:43:43	2026-10-17 03:43:43
22	2	2026-11-08	08:00:00	7	7	7	7	programada	\N	\N	2026-10-17 03:43:43	2026-10-17 03:43:43
23	3	2026-11-09	08:00:00	8	8	8	7	programada	\N	\N	2026-10-17 03:43:43	2026-10-17 03:43:43
24	4	2026-11-10	08:00:00	9	9	9	7	programada	\N	\N	2026-10-17 03:43:43	2026-10-17 03:43:43
25	5	2026-11-11	08:00:00	10	10	10	7	programada	\N	\N	2026-10-17 03:43:43	2026-10-17 03:43:43
26	6	2026-11-12	08:00:00	11	11	11	7	programada	\N	\N	2026-10-17 03:43:43	2026-10-17 03:43:43
27	7	2026-11-13	08:00:00	12	12	12	7	programada	\N	\N	2026-10-17 03:43:43	2026-10-17 03:43:43
28	8	2026-11-14	08:00:00	13	13	13	7	programada	\N	\N	2026-10-17 03:43:43	2026-10-17 03:43:43
29	9	2026-11-15	08:00:00	14	14	14	7	programada	\N	\N	2026-10-17 03:43:43	2026-10-17 03:43:43
30	10	2026-11-16	08:00:00	15	15	15	7	programada	\N	\N	2026-10-17 03:43:43	2026-10-17 03:43:43
\.


--
-- TOC entry 5500 (class 0 OID 22175)
-- Dependencies: 290
-- Data for Name: sessions; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.sessions (id, user_id, ip_address, user_agent, payload, last_activity) FROM stdin;
\.


--
-- TOC entry 5465 (class 0 OID 21748)
-- Dependencies: 255
-- Data for Name: tipos_equipo; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.tipos_equipo (id, nom, des, pre_alq_bas, cos_ord, cos_mnt_anu, eqp_cri) FROM stdin;
1	Mochila de excursion	Mochila para recorridos	20.00	\N	\N	f
2	Baston de trekking	Baston para caminatas	10.00	\N	\N	f
3	Casco de seguridad	Proteccion para actividades	15.00	\N	\N	t
4	Linterna	Linterna para recorridos	8.00	\N	\N	f
5	Botiquin	Equipo basico de primeros auxilios	12.00	\N	\N	t
6	Binoculares	Observacion de paisajes	15.00	\N	\N	f
7	Poncho impermeable	Proteccion contra lluvia	10.00	\N	\N	f
8	Bastones de apoyo	Apoyo para caminatas	10.00	\N	\N	f
9	Chaleco reflectivo	Seguridad en traslados	5.00	\N	\N	t
10	Bolsa de dormir	Equipo para excursiones largas	25.00	\N	\N	f
\.


--
-- TOC entry 5457 (class 0 OID 21664)
-- Dependencies: 247
-- Data for Name: tours; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.tours (id, cat_id, cod, rta_amg, nom, des, niv_dif, alt_min_m, alt_max_m, dur_hrs, req_fis, pre_bas, cap_max, trz_geo, act, cre_en, mod_en, eli_en) FROM stdin;
1	1	TUR001	salar-de-uyuni	Salar de Uyuni	Recorrido turistico guiado por destinos de Bolivia.	baja	\N	\N	4.00	\N	115.00	12	\N	t	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
2	2	TUR002	valle-de-la-luna	Valle de la Luna	Recorrido turistico guiado por destinos de Bolivia.	media	\N	\N	5.00	\N	130.00	12	\N	t	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
3	3	TUR003	lago-titicaca-copacabana	Lago Titicaca y Copacabana	Recorrido turistico guiado por destinos de Bolivia.	alta	\N	\N	6.00	\N	145.00	12	\N	t	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
4	4	TUR004	tiwanaku-arqueologico	Complejo arqueologico de Tiwanaku	Recorrido turistico guiado por destinos de Bolivia.	baja	\N	\N	7.00	\N	160.00	12	\N	t	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
5	5	TUR005	chacaltaya-paisajes-andinos	Chacaltaya y paisajes andinos	Recorrido turistico guiado por destinos de Bolivia.	media	\N	\N	8.00	\N	175.00	12	\N	t	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
6	6	TUR006	centro-historico-la-paz	Centro historico de La Paz	Recorrido turistico guiado por destinos de Bolivia.	alta	\N	\N	3.00	\N	190.00	12	\N	t	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
7	7	TUR007	ruta-cultural-sucre	Ruta cultural de Sucre	Recorrido turistico guiado por destinos de Bolivia.	baja	\N	\N	4.00	\N	205.00	12	\N	t	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
8	8	TUR008	misiones-chiquitos	Misiones de Chiquitos	Recorrido turistico guiado por destinos de Bolivia.	media	\N	\N	5.00	\N	220.00	12	\N	t	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
9	9	TUR009	parque-nacional-sajama	Parque Nacional Sajama	Recorrido turistico guiado por destinos de Bolivia.	alta	\N	\N	6.00	\N	235.00	12	\N	t	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
10	10	TUR010	coroico-yungas	Coroico y los Yungas	Recorrido turistico guiado por destinos de Bolivia.	baja	\N	\N	7.00	\N	250.00	12	\N	t	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
11	1	TUR011	ruta-gastronomica-tradicional	Ruta gastronomica tradicional	Recorrido turistico guiado por destinos de Bolivia.	media	\N	\N	8.00	\N	265.00	12	\N	t	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
12	2	TUR012	trekking-cordillera-real	Trekking en la Cordillera Real	Recorrido turistico guiado por destinos de Bolivia.	alta	\N	\N	3.00	\N	280.00	12	\N	t	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
13	3	TUR013	isla-del-sol	Isla del Sol	Recorrido turistico guiado por destinos de Bolivia.	baja	\N	\N	4.00	\N	295.00	12	\N	t	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
14	4	TUR014	potosi-historico	Potosi historico	Recorrido turistico guiado por destinos de Bolivia.	media	\N	\N	5.00	\N	310.00	12	\N	t	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
15	5	TUR015	cristo-concordia-cochabamba	Cristo de la Concordia y Cochabamba	Recorrido turistico guiado por destinos de Bolivia.	alta	\N	\N	6.00	\N	325.00	12	\N	t	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
16	6	TUR016	laguna-colorada	Laguna Colorada	Recorrido turistico guiado por destinos de Bolivia.	baja	\N	\N	7.00	\N	340.00	12	\N	t	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
17	7	TUR017	reserva-eduardo-avaroa	Reserva Eduardo Avaroa	Recorrido turistico guiado por destinos de Bolivia.	media	\N	\N	8.00	\N	355.00	12	\N	t	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
18	8	TUR018	cascadas-yungas	Ruta de cascadas de los Yungas	Recorrido turistico guiado por destinos de Bolivia.	alta	\N	\N	3.00	\N	370.00	12	\N	t	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
19	9	TUR019	sorata-montanas	Sorata y sus montanas	Recorrido turistico guiado por destinos de Bolivia.	baja	\N	\N	4.00	\N	385.00	12	\N	t	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
20	10	TUR020	fotografia-paisajes-bolivia	Fotografia de paisajes bolivianos	Recorrido turistico guiado por destinos de Bolivia.	media	\N	\N	5.00	\N	400.00	12	\N	t	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
\.


--
-- TOC entry 5447 (class 0 OID 21543)
-- Dependencies: 237
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.users (id, rol_id, pai_id, nom, cor, cor_vrf_en, clv, rol, tel, tip_doc, num_doc, ci_cmp, ci_dep_id, fot_prf, tok_rec, act, remember_token, created_at, updated_at, eli_en) FROM stdin;
1	1	\N	Administrador Pipocas	admin@gmail.com	\N	$2y$12$un/UIg8CUXrvV1EyOsX.rOpCpHKMH1T2WzQIDgsqXzN2mbR2pndCy	admin	76845008	CI	1260084	1A	1	\N	\N	t	\N	2026-10-17 07:26:21	2026-10-17 07:26:21	\N
2	3	\N	Guia de prueba 01	guia001@prueba.test	\N	$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.	guia	700100001	CI	G00001	1A	1	\N	\N	t	\N	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
3	3	\N	Guia de prueba 02	guia002@prueba.test	\N	$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.	guia	700100002	CI	G00002	1A	1	\N	\N	t	\N	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
4	3	\N	Guia de prueba 03	guia003@prueba.test	\N	$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.	guia	700100003	CI	G00003	1A	1	\N	\N	t	\N	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
5	3	\N	Guia de prueba 04	guia004@prueba.test	\N	$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.	guia	700100004	CI	G00004	1A	1	\N	\N	t	\N	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
6	3	\N	Guia de prueba 05	guia005@prueba.test	\N	$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.	guia	700100005	CI	G00005	1A	1	\N	\N	t	\N	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
7	3	\N	Guia de prueba 06	guia006@prueba.test	\N	$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.	guia	700100006	CI	G00006	1A	1	\N	\N	t	\N	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
8	3	\N	Guia de prueba 07	guia007@prueba.test	\N	$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.	guia	700100007	CI	G00007	1A	1	\N	\N	t	\N	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
9	3	\N	Guia de prueba 08	guia008@prueba.test	\N	$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.	guia	700100008	CI	G00008	1A	1	\N	\N	t	\N	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
10	3	\N	Guia de prueba 09	guia009@prueba.test	\N	$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.	guia	700100009	CI	G00009	1A	1	\N	\N	t	\N	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
11	3	\N	Guia de prueba 10	guia010@prueba.test	\N	$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.	guia	700100010	CI	G00010	1A	1	\N	\N	t	\N	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
12	3	\N	Guia de prueba 11	guia011@prueba.test	\N	$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.	guia	700100011	CI	G00011	1A	1	\N	\N	t	\N	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
13	3	\N	Guia de prueba 12	guia012@prueba.test	\N	$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.	guia	700100012	CI	G00012	1A	1	\N	\N	t	\N	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
14	3	\N	Guia de prueba 13	guia013@prueba.test	\N	$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.	guia	700100013	CI	G00013	1A	1	\N	\N	t	\N	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
15	3	\N	Guia de prueba 14	guia014@prueba.test	\N	$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.	guia	700100014	CI	G00014	1A	1	\N	\N	t	\N	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
16	3	\N	Guia de prueba 15	guia015@prueba.test	\N	$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.	guia	700100015	CI	G00015	1A	1	\N	\N	t	\N	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
17	4	\N	Chofer de prueba 01	chofer001@prueba.test	\N	$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.	chofer	700200001	CI	C00001	1A	1	\N	\N	t	\N	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
18	4	\N	Chofer de prueba 02	chofer002@prueba.test	\N	$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.	chofer	700200002	CI	C00002	1A	1	\N	\N	t	\N	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
19	4	\N	Chofer de prueba 03	chofer003@prueba.test	\N	$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.	chofer	700200003	CI	C00003	1A	1	\N	\N	t	\N	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
20	4	\N	Chofer de prueba 04	chofer004@prueba.test	\N	$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.	chofer	700200004	CI	C00004	1A	1	\N	\N	t	\N	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
21	4	\N	Chofer de prueba 05	chofer005@prueba.test	\N	$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.	chofer	700200005	CI	C00005	1A	1	\N	\N	t	\N	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
22	4	\N	Chofer de prueba 06	chofer006@prueba.test	\N	$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.	chofer	700200006	CI	C00006	1A	1	\N	\N	t	\N	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
23	4	\N	Chofer de prueba 07	chofer007@prueba.test	\N	$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.	chofer	700200007	CI	C00007	1A	1	\N	\N	t	\N	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
24	4	\N	Chofer de prueba 08	chofer008@prueba.test	\N	$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.	chofer	700200008	CI	C00008	1A	1	\N	\N	t	\N	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
25	4	\N	Chofer de prueba 09	chofer009@prueba.test	\N	$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.	chofer	700200009	CI	C00009	1A	1	\N	\N	t	\N	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
26	4	\N	Chofer de prueba 10	chofer010@prueba.test	\N	$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.	chofer	700200010	CI	C00010	1A	1	\N	\N	t	\N	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
27	4	\N	Chofer de prueba 11	chofer011@prueba.test	\N	$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.	chofer	700200011	CI	C00011	1A	1	\N	\N	t	\N	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
28	4	\N	Chofer de prueba 12	chofer012@prueba.test	\N	$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.	chofer	700200012	CI	C00012	1A	1	\N	\N	t	\N	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
29	4	\N	Chofer de prueba 13	chofer013@prueba.test	\N	$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.	chofer	700200013	CI	C00013	1A	1	\N	\N	t	\N	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
30	4	\N	Chofer de prueba 14	chofer014@prueba.test	\N	$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.	chofer	700200014	CI	C00014	1A	1	\N	\N	t	\N	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
31	4	\N	Chofer de prueba 15	chofer015@prueba.test	\N	$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.	chofer	700200015	CI	C00015	1A	1	\N	\N	t	\N	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
32	5	\N	Turista de prueba 01	turista001@prueba.test	\N	$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.	turista	700300001	CI	T00001	1A	1	\N	\N	t	\N	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
33	5	\N	Turista de prueba 02	turista002@prueba.test	\N	$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.	turista	700300002	CI	T00002	1A	1	\N	\N	t	\N	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
34	5	\N	Turista de prueba 03	turista003@prueba.test	\N	$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.	turista	700300003	CI	T00003	1A	1	\N	\N	t	\N	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
35	5	\N	Turista de prueba 04	turista004@prueba.test	\N	$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.	turista	700300004	CI	T00004	1A	1	\N	\N	t	\N	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
36	5	\N	Turista de prueba 05	turista005@prueba.test	\N	$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.	turista	700300005	CI	T00005	1A	1	\N	\N	t	\N	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
37	5	\N	Turista de prueba 06	turista006@prueba.test	\N	$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.	turista	700300006	CI	T00006	1A	1	\N	\N	t	\N	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
38	5	\N	Turista de prueba 07	turista007@prueba.test	\N	$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.	turista	700300007	CI	T00007	1A	1	\N	\N	t	\N	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
39	5	\N	Turista de prueba 08	turista008@prueba.test	\N	$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.	turista	700300008	CI	T00008	1A	1	\N	\N	t	\N	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
40	5	\N	Turista de prueba 09	turista009@prueba.test	\N	$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.	turista	700300009	CI	T00009	1A	1	\N	\N	t	\N	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
41	5	\N	Turista de prueba 10	turista010@prueba.test	\N	$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.	turista	700300010	CI	T00010	1A	1	\N	\N	t	\N	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
42	5	\N	Turista de prueba 11	turista011@prueba.test	\N	$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.	turista	700300011	CI	T00011	1A	1	\N	\N	t	\N	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
43	5	\N	Turista de prueba 12	turista012@prueba.test	\N	$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.	turista	700300012	CI	T00012	1A	1	\N	\N	t	\N	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
44	5	\N	Turista de prueba 13	turista013@prueba.test	\N	$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.	turista	700300013	CI	T00013	1A	1	\N	\N	t	\N	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
45	5	\N	Turista de prueba 14	turista014@prueba.test	\N	$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.	turista	700300014	CI	T00014	1A	1	\N	\N	t	\N	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
46	5	\N	Turista de prueba 15	turista015@prueba.test	\N	$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.	turista	700300015	CI	T00015	1A	1	\N	\N	t	\N	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
47	5	\N	Turista de prueba 16	turista016@prueba.test	\N	$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.	turista	700300016	CI	T00016	1A	1	\N	\N	t	\N	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
48	5	\N	Turista de prueba 17	turista017@prueba.test	\N	$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.	turista	700300017	CI	T00017	1A	1	\N	\N	t	\N	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
49	5	\N	Turista de prueba 18	turista018@prueba.test	\N	$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.	turista	700300018	CI	T00018	1A	1	\N	\N	t	\N	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
50	5	\N	Turista de prueba 19	turista019@prueba.test	\N	$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.	turista	700300019	CI	T00019	1A	1	\N	\N	t	\N	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
51	5	\N	Turista de prueba 20	turista020@prueba.test	\N	$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.	turista	700300020	CI	T00020	1A	1	\N	\N	t	\N	2026-10-17 03:43:43	2026-10-17 03:43:43	\N
\.


--
-- TOC entry 5463 (class 0 OID 21733)
-- Dependencies: 253
-- Data for Name: vehiculos; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.vehiculos (id, pla, mar, mdl, tip, cap_asi, tip_cmb, est) FROM stdin;
1	BOL-0001	Nissan	Urvan	vagoneta	7	diesel	act
2	BOL-0002	Toyota	Hiace	vagoneta	7	diesel	act
3	BOL-0003	Nissan	Urvan	minibus	15	diesel	act
4	BOL-0004	Toyota	Hiace	vagoneta	7	diesel	act
5	BOL-0005	Nissan	Urvan	vagoneta	7	diesel	act
6	BOL-0006	Toyota	Hiace	minibus	15	diesel	act
7	BOL-0007	Nissan	Urvan	vagoneta	7	diesel	act
8	BOL-0008	Toyota	Hiace	vagoneta	7	diesel	act
9	BOL-0009	Nissan	Urvan	minibus	15	diesel	act
10	BOL-0010	Toyota	Hiace	vagoneta	7	diesel	act
11	BOL-0011	Nissan	Urvan	vagoneta	7	diesel	act
12	BOL-0012	Toyota	Hiace	minibus	15	diesel	act
13	BOL-0013	Nissan	Urvan	vagoneta	7	diesel	act
14	BOL-0014	Toyota	Hiace	vagoneta	7	diesel	act
15	BOL-0015	Nissan	Urvan	minibus	15	diesel	act
\.


--
-- TOC entry 5540 (class 0 OID 0)
-- Dependencies: 268
-- Name: alquileres_equipo_reserva_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.alquileres_equipo_reserva_id_seq', 10, true);


--
-- TOC entry 5541 (class 0 OID 0)
-- Dependencies: 284
-- Name: bitacora_auditoria_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.bitacora_auditoria_id_seq', 1, false);


--
-- TOC entry 5542 (class 0 OID 0)
-- Dependencies: 262
-- Name: bitacora_viajes_vehiculo_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.bitacora_viajes_vehiculo_id_seq', 30, true);


--
-- TOC entry 5543 (class 0 OID 0)
-- Dependencies: 270
-- Name: boletos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.boletos_id_seq', 270, true);


--
-- TOC entry 5544 (class 0 OID 0)
-- Dependencies: 244
-- Name: categorias_tour_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.categorias_tour_id_seq', 90, true);


--
-- TOC entry 5545 (class 0 OID 0)
-- Dependencies: 274
-- Name: comprobantes_pago_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.comprobantes_pago_id_seq', 270, true);


--
-- TOC entry 5546 (class 0 OID 0)
-- Dependencies: 280
-- Name: conversaciones_chatbot_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.conversaciones_chatbot_id_seq', 1, false);


--
-- TOC entry 5547 (class 0 OID 0)
-- Dependencies: 234
-- Name: departamentos_bolivia_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.departamentos_bolivia_id_seq', 9, true);


--
-- TOC entry 5548 (class 0 OID 0)
-- Dependencies: 258
-- Name: equipo_por_tour_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.equipo_por_tour_id_seq', 180, true);


--
-- TOC entry 5549 (class 0 OID 0)
-- Dependencies: 226
-- Name: failed_jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.failed_jobs_id_seq', 1, false);


--
-- TOC entry 5550 (class 0 OID 0)
-- Dependencies: 272
-- Name: fichas_medicas_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.fichas_medicas_id_seq', 270, true);


--
-- TOC entry 5551 (class 0 OID 0)
-- Dependencies: 248
-- Name: fotos_tour_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.fotos_tour_id_seq', 40, true);


--
-- TOC entry 5552 (class 0 OID 0)
-- Dependencies: 240
-- Name: idiomas_guia_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.idiomas_guia_id_seq', 45, true);


--
-- TOC entry 5553 (class 0 OID 0)
-- Dependencies: 232
-- Name: idiomas_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.idiomas_id_seq', 3, true);


--
-- TOC entry 5554 (class 0 OID 0)
-- Dependencies: 256
-- Name: items_equipo_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.items_equipo_id_seq', 10, true);


--
-- TOC entry 5555 (class 0 OID 0)
-- Dependencies: 223
-- Name: jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.jobs_id_seq', 1, false);


--
-- TOC entry 5556 (class 0 OID 0)
-- Dependencies: 282
-- Name: mensajes_chatbot_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.mensajes_chatbot_id_seq', 1, false);


--
-- TOC entry 5557 (class 0 OID 0)
-- Dependencies: 219
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.migrations_id_seq', 10, true);


--
-- TOC entry 5558 (class 0 OID 0)
-- Dependencies: 276
-- Name: pagos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.pagos_id_seq', 30, true);


--
-- TOC entry 5559 (class 0 OID 0)
-- Dependencies: 230
-- Name: paises_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.paises_id_seq', 1, false);


--
-- TOC entry 5560 (class 0 OID 0)
-- Dependencies: 266
-- Name: participantes_reserva_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.participantes_reserva_id_seq', 30, true);


--
-- TOC entry 5561 (class 0 OID 0)
-- Dependencies: 242
-- Name: perfiles_chofer_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.perfiles_chofer_id_seq', 135, true);


--
-- TOC entry 5562 (class 0 OID 0)
-- Dependencies: 238
-- Name: perfiles_guia_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.perfiles_guia_id_seq', 135, true);


--
-- TOC entry 5563 (class 0 OID 0)
-- Dependencies: 288
-- Name: proyecciones_demanda_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.proyecciones_demanda_id_seq', 180, true);


--
-- TOC entry 5564 (class 0 OID 0)
-- Dependencies: 250
-- Name: puntos_ruta_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.puntos_ruta_id_seq', 60, true);


--
-- TOC entry 5565 (class 0 OID 0)
-- Dependencies: 286
-- Name: registros_notificacion_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.registros_notificacion_id_seq', 30, true);


--
-- TOC entry 5566 (class 0 OID 0)
-- Dependencies: 278
-- Name: resenas_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.resenas_id_seq', 15, true);


--
-- TOC entry 5567 (class 0 OID 0)
-- Dependencies: 264
-- Name: reservas_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.reservas_id_seq', 270, true);


--
-- TOC entry 5568 (class 0 OID 0)
-- Dependencies: 228
-- Name: roles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.roles_id_seq', 5, true);


--
-- TOC entry 5569 (class 0 OID 0)
-- Dependencies: 260
-- Name: salidas_tour_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.salidas_tour_id_seq', 30, true);


--
-- TOC entry 5570 (class 0 OID 0)
-- Dependencies: 254
-- Name: tipos_equipo_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tipos_equipo_id_seq', 10, true);


--
-- TOC entry 5571 (class 0 OID 0)
-- Dependencies: 246
-- Name: tours_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tours_id_seq', 180, true);


--
-- TOC entry 5572 (class 0 OID 0)
-- Dependencies: 236
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.users_id_seq', 451, true);


--
-- TOC entry 5573 (class 0 OID 0)
-- Dependencies: 252
-- Name: vehiculos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.vehiculos_id_seq', 135, true);


--
-- TOC entry 5204 (class 2606 OID 21943)
-- Name: alquileres_equipo_reserva alquileres_equipo_reserva_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.alquileres_equipo_reserva
    ADD CONSTRAINT alquileres_equipo_reserva_pkey PRIMARY KEY (id);


--
-- TOC entry 5232 (class 2606 OID 22124)
-- Name: bitacora_auditoria bitacora_auditoria_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.bitacora_auditoria
    ADD CONSTRAINT bitacora_auditoria_pkey PRIMARY KEY (id);


--
-- TOC entry 5196 (class 2606 OID 21858)
-- Name: bitacora_viajes_vehiculo bitacora_viajes_vehiculo_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.bitacora_viajes_vehiculo
    ADD CONSTRAINT bitacora_viajes_vehiculo_pkey PRIMARY KEY (id);


--
-- TOC entry 5206 (class 2606 OID 21979)
-- Name: boletos boletos_cod_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.boletos
    ADD CONSTRAINT boletos_cod_unique UNIQUE (cod);


--
-- TOC entry 5208 (class 2606 OID 21967)
-- Name: boletos boletos_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.boletos
    ADD CONSTRAINT boletos_pkey PRIMARY KEY (id);


--
-- TOC entry 5210 (class 2606 OID 21981)
-- Name: boletos boletos_ptc_rsv_id_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.boletos
    ADD CONSTRAINT boletos_ptc_rsv_id_unique UNIQUE (ptc_rsv_id);


--
-- TOC entry 5119 (class 2606 OID 21445)
-- Name: cache_locks cache_locks_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cache_locks
    ADD CONSTRAINT cache_locks_pkey PRIMARY KEY (key);


--
-- TOC entry 5117 (class 2606 OID 21435)
-- Name: cache cache_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cache
    ADD CONSTRAINT cache_pkey PRIMARY KEY (key);


--
-- TOC entry 5166 (class 2606 OID 21660)
-- Name: categorias_tour categorias_tour_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categorias_tour
    ADD CONSTRAINT categorias_tour_pkey PRIMARY KEY (id);


--
-- TOC entry 5168 (class 2606 OID 21662)
-- Name: categorias_tour categorias_tour_rta_amg_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categorias_tour
    ADD CONSTRAINT categorias_tour_rta_amg_unique UNIQUE (rta_amg);


--
-- TOC entry 5216 (class 2606 OID 22023)
-- Name: comprobantes_pago comprobantes_pago_cod_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.comprobantes_pago
    ADD CONSTRAINT comprobantes_pago_cod_unique UNIQUE (cod);


--
-- TOC entry 5218 (class 2606 OID 22016)
-- Name: comprobantes_pago comprobantes_pago_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.comprobantes_pago
    ADD CONSTRAINT comprobantes_pago_pkey PRIMARY KEY (id);


--
-- TOC entry 5220 (class 2606 OID 22025)
-- Name: comprobantes_pago comprobantes_pago_rsv_id_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.comprobantes_pago
    ADD CONSTRAINT comprobantes_pago_rsv_id_unique UNIQUE (rsv_id);


--
-- TOC entry 5228 (class 2606 OID 22084)
-- Name: conversaciones_chatbot conversaciones_chatbot_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.conversaciones_chatbot
    ADD CONSTRAINT conversaciones_chatbot_pkey PRIMARY KEY (id);


--
-- TOC entry 5142 (class 2606 OID 21541)
-- Name: departamentos_bolivia departamentos_bolivia_cod_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.departamentos_bolivia
    ADD CONSTRAINT departamentos_bolivia_cod_unique UNIQUE (cod);


--
-- TOC entry 5144 (class 2606 OID 21539)
-- Name: departamentos_bolivia departamentos_bolivia_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.departamentos_bolivia
    ADD CONSTRAINT departamentos_bolivia_pkey PRIMARY KEY (id);


--
-- TOC entry 5190 (class 2606 OID 21796)
-- Name: equipo_por_tour equipo_por_tour_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.equipo_por_tour
    ADD CONSTRAINT equipo_por_tour_pkey PRIMARY KEY (id);


--
-- TOC entry 5192 (class 2606 OID 21808)
-- Name: equipo_por_tour equipo_por_tour_tur_id_tip_eqp_id_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.equipo_por_tour
    ADD CONSTRAINT equipo_por_tour_tur_id_tip_eqp_id_unique UNIQUE (tur_id, tip_eqp_id);


--
-- TOC entry 5126 (class 2606 OID 21492)
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- TOC entry 5128 (class 2606 OID 21494)
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- TOC entry 5212 (class 2606 OID 21994)
-- Name: fichas_medicas fichas_medicas_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.fichas_medicas
    ADD CONSTRAINT fichas_medicas_pkey PRIMARY KEY (id);


--
-- TOC entry 5214 (class 2606 OID 22001)
-- Name: fichas_medicas fichas_medicas_ptc_rsv_id_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.fichas_medicas
    ADD CONSTRAINT fichas_medicas_ptc_rsv_id_unique UNIQUE (ptc_rsv_id);


--
-- TOC entry 5176 (class 2606 OID 21707)
-- Name: fotos_tour fotos_tour_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.fotos_tour
    ADD CONSTRAINT fotos_tour_pkey PRIMARY KEY (id);


--
-- TOC entry 5138 (class 2606 OID 21529)
-- Name: idiomas idiomas_cod_iso_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.idiomas
    ADD CONSTRAINT idiomas_cod_iso_unique UNIQUE (cod_iso);


--
-- TOC entry 5158 (class 2606 OID 21615)
-- Name: idiomas_guia idiomas_guia_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.idiomas_guia
    ADD CONSTRAINT idiomas_guia_pkey PRIMARY KEY (id);


--
-- TOC entry 5160 (class 2606 OID 21627)
-- Name: idiomas_guia idiomas_guia_prf_gui_id_idi_id_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.idiomas_guia
    ADD CONSTRAINT idiomas_guia_prf_gui_id_idi_id_unique UNIQUE (prf_gui_id, idi_id);


--
-- TOC entry 5140 (class 2606 OID 21527)
-- Name: idiomas idiomas_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.idiomas
    ADD CONSTRAINT idiomas_pkey PRIMARY KEY (id);


--
-- TOC entry 5186 (class 2606 OID 21777)
-- Name: items_equipo items_equipo_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.items_equipo
    ADD CONSTRAINT items_equipo_pkey PRIMARY KEY (id);


--
-- TOC entry 5188 (class 2606 OID 21784)
-- Name: items_equipo items_equipo_tip_eqp_id_tal_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.items_equipo
    ADD CONSTRAINT items_equipo_tip_eqp_id_tal_unique UNIQUE (tip_eqp_id, tal);


--
-- TOC entry 5124 (class 2606 OID 21475)
-- Name: job_batches job_batches_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.job_batches
    ADD CONSTRAINT job_batches_pkey PRIMARY KEY (id);


--
-- TOC entry 5121 (class 2606 OID 21460)
-- Name: jobs jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY (id);


--
-- TOC entry 5230 (class 2606 OID 22104)
-- Name: mensajes_chatbot mensajes_chatbot_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.mensajes_chatbot
    ADD CONSTRAINT mensajes_chatbot_pkey PRIMARY KEY (id);


--
-- TOC entry 5115 (class 2606 OID 21425)
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- TOC entry 5222 (class 2606 OID 22040)
-- Name: pagos pagos_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pagos
    ADD CONSTRAINT pagos_pkey PRIMARY KEY (id);


--
-- TOC entry 5134 (class 2606 OID 21517)
-- Name: paises paises_cod_iso_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.paises
    ADD CONSTRAINT paises_cod_iso_unique UNIQUE (cod_iso);


--
-- TOC entry 5136 (class 2606 OID 21515)
-- Name: paises paises_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.paises
    ADD CONSTRAINT paises_pkey PRIMARY KEY (id);


--
-- TOC entry 5202 (class 2606 OID 21917)
-- Name: participantes_reserva participantes_reserva_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.participantes_reserva
    ADD CONSTRAINT participantes_reserva_pkey PRIMARY KEY (id);


--
-- TOC entry 5162 (class 2606 OID 21641)
-- Name: perfiles_chofer perfiles_chofer_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.perfiles_chofer
    ADD CONSTRAINT perfiles_chofer_pkey PRIMARY KEY (id);


--
-- TOC entry 5164 (class 2606 OID 21648)
-- Name: perfiles_chofer perfiles_chofer_usr_id_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.perfiles_chofer
    ADD CONSTRAINT perfiles_chofer_usr_id_unique UNIQUE (usr_id);


--
-- TOC entry 5152 (class 2606 OID 21596)
-- Name: perfiles_guia perfiles_guia_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.perfiles_guia
    ADD CONSTRAINT perfiles_guia_pkey PRIMARY KEY (id);


--
-- TOC entry 5154 (class 2606 OID 21605)
-- Name: perfiles_guia perfiles_guia_rta_amg_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.perfiles_guia
    ADD CONSTRAINT perfiles_guia_rta_amg_unique UNIQUE (rta_amg);


--
-- TOC entry 5156 (class 2606 OID 21603)
-- Name: perfiles_guia perfiles_guia_usr_id_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.perfiles_guia
    ADD CONSTRAINT perfiles_guia_usr_id_unique UNIQUE (usr_id);


--
-- TOC entry 5236 (class 2606 OID 22167)
-- Name: proyecciones_demanda proyecciones_demanda_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.proyecciones_demanda
    ADD CONSTRAINT proyecciones_demanda_pkey PRIMARY KEY (id);


--
-- TOC entry 5238 (class 2606 OID 22174)
-- Name: proyecciones_demanda proyecciones_demanda_tur_id_mes_per_ani_per_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.proyecciones_demanda
    ADD CONSTRAINT proyecciones_demanda_tur_id_mes_per_ani_per_unique UNIQUE (tur_id, mes_per, ani_per);


--
-- TOC entry 5178 (class 2606 OID 21726)
-- Name: puntos_ruta puntos_ruta_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.puntos_ruta
    ADD CONSTRAINT puntos_ruta_pkey PRIMARY KEY (id);


--
-- TOC entry 5234 (class 2606 OID 22143)
-- Name: registros_notificacion registros_notificacion_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.registros_notificacion
    ADD CONSTRAINT registros_notificacion_pkey PRIMARY KEY (id);


--
-- TOC entry 5224 (class 2606 OID 22059)
-- Name: resenas resenas_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.resenas
    ADD CONSTRAINT resenas_pkey PRIMARY KEY (id);


--
-- TOC entry 5226 (class 2606 OID 22071)
-- Name: resenas resenas_rsv_id_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.resenas
    ADD CONSTRAINT resenas_rsv_id_unique UNIQUE (rsv_id);


--
-- TOC entry 5198 (class 2606 OID 21903)
-- Name: reservas reservas_cod_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.reservas
    ADD CONSTRAINT reservas_cod_unique UNIQUE (cod);


--
-- TOC entry 5200 (class 2606 OID 21891)
-- Name: reservas reservas_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.reservas
    ADD CONSTRAINT reservas_pkey PRIMARY KEY (id);


--
-- TOC entry 5130 (class 2606 OID 21505)
-- Name: roles roles_nom_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_nom_unique UNIQUE (nom);


--
-- TOC entry 5132 (class 2606 OID 21503)
-- Name: roles roles_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (id);


--
-- TOC entry 5194 (class 2606 OID 21826)
-- Name: salidas_tour salidas_tour_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.salidas_tour
    ADD CONSTRAINT salidas_tour_pkey PRIMARY KEY (id);


--
-- TOC entry 5241 (class 2606 OID 22184)
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- TOC entry 5184 (class 2606 OID 21761)
-- Name: tipos_equipo tipos_equipo_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tipos_equipo
    ADD CONSTRAINT tipos_equipo_pkey PRIMARY KEY (id);


--
-- TOC entry 5170 (class 2606 OID 21693)
-- Name: tours tours_cod_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tours
    ADD CONSTRAINT tours_cod_unique UNIQUE (cod);


--
-- TOC entry 5172 (class 2606 OID 21686)
-- Name: tours tours_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tours
    ADD CONSTRAINT tours_pkey PRIMARY KEY (id);


--
-- TOC entry 5174 (class 2606 OID 21695)
-- Name: tours tours_rta_amg_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tours
    ADD CONSTRAINT tours_rta_amg_unique UNIQUE (rta_amg);


--
-- TOC entry 5146 (class 2606 OID 21579)
-- Name: users users_cor_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_cor_unique UNIQUE (cor);


--
-- TOC entry 5148 (class 2606 OID 21577)
-- Name: users users_num_doc_ci_cmp_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_num_doc_ci_cmp_unique UNIQUE (num_doc, ci_cmp);


--
-- TOC entry 5150 (class 2606 OID 21560)
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- TOC entry 5180 (class 2606 OID 21744)
-- Name: vehiculos vehiculos_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.vehiculos
    ADD CONSTRAINT vehiculos_pkey PRIMARY KEY (id);


--
-- TOC entry 5182 (class 2606 OID 21746)
-- Name: vehiculos vehiculos_pla_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.vehiculos
    ADD CONSTRAINT vehiculos_pla_unique UNIQUE (pla);


--
-- TOC entry 5122 (class 1259 OID 21461)
-- Name: jobs_queue_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX jobs_queue_index ON public.jobs USING btree (queue);


--
-- TOC entry 5239 (class 1259 OID 22186)
-- Name: sessions_last_activity_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sessions_last_activity_index ON public.sessions USING btree (last_activity);


--
-- TOC entry 5242 (class 1259 OID 22185)
-- Name: sessions_user_id_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sessions_user_id_index ON public.sessions USING btree (user_id);


--
-- TOC entry 5267 (class 2606 OID 21949)
-- Name: alquileres_equipo_reserva alquileres_equipo_reserva_itm_eqp_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.alquileres_equipo_reserva
    ADD CONSTRAINT alquileres_equipo_reserva_itm_eqp_id_foreign FOREIGN KEY (itm_eqp_id) REFERENCES public.items_equipo(id) ON DELETE RESTRICT;


--
-- TOC entry 5268 (class 2606 OID 21944)
-- Name: alquileres_equipo_reserva alquileres_equipo_reserva_ptc_rsv_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.alquileres_equipo_reserva
    ADD CONSTRAINT alquileres_equipo_reserva_ptc_rsv_id_foreign FOREIGN KEY (ptc_rsv_id) REFERENCES public.participantes_reserva(id) ON DELETE CASCADE;


--
-- TOC entry 5278 (class 2606 OID 22125)
-- Name: bitacora_auditoria bitacora_auditoria_usr_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.bitacora_auditoria
    ADD CONSTRAINT bitacora_auditoria_usr_id_foreign FOREIGN KEY (usr_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5260 (class 2606 OID 21864)
-- Name: bitacora_viajes_vehiculo bitacora_viajes_vehiculo_sal_tur_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.bitacora_viajes_vehiculo
    ADD CONSTRAINT bitacora_viajes_vehiculo_sal_tur_id_foreign FOREIGN KEY (sal_tur_id) REFERENCES public.salidas_tour(id) ON DELETE CASCADE;


--
-- TOC entry 5261 (class 2606 OID 21859)
-- Name: bitacora_viajes_vehiculo bitacora_viajes_vehiculo_veh_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.bitacora_viajes_vehiculo
    ADD CONSTRAINT bitacora_viajes_vehiculo_veh_id_foreign FOREIGN KEY (veh_id) REFERENCES public.vehiculos(id) ON DELETE CASCADE;


--
-- TOC entry 5269 (class 2606 OID 21968)
-- Name: boletos boletos_ptc_rsv_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.boletos
    ADD CONSTRAINT boletos_ptc_rsv_id_foreign FOREIGN KEY (ptc_rsv_id) REFERENCES public.participantes_reserva(id) ON DELETE CASCADE;


--
-- TOC entry 5270 (class 2606 OID 21973)
-- Name: boletos boletos_vld_por_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.boletos
    ADD CONSTRAINT boletos_vld_por_foreign FOREIGN KEY (vld_por) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5272 (class 2606 OID 22017)
-- Name: comprobantes_pago comprobantes_pago_rsv_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.comprobantes_pago
    ADD CONSTRAINT comprobantes_pago_rsv_id_foreign FOREIGN KEY (rsv_id) REFERENCES public.reservas(id) ON DELETE RESTRICT;


--
-- TOC entry 5276 (class 2606 OID 22085)
-- Name: conversaciones_chatbot conversaciones_chatbot_usr_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.conversaciones_chatbot
    ADD CONSTRAINT conversaciones_chatbot_usr_id_foreign FOREIGN KEY (usr_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5254 (class 2606 OID 21802)
-- Name: equipo_por_tour equipo_por_tour_tip_eqp_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.equipo_por_tour
    ADD CONSTRAINT equipo_por_tour_tip_eqp_id_foreign FOREIGN KEY (tip_eqp_id) REFERENCES public.tipos_equipo(id) ON DELETE RESTRICT;


--
-- TOC entry 5255 (class 2606 OID 21797)
-- Name: equipo_por_tour equipo_por_tour_tur_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.equipo_por_tour
    ADD CONSTRAINT equipo_por_tour_tur_id_foreign FOREIGN KEY (tur_id) REFERENCES public.tours(id) ON DELETE CASCADE;


--
-- TOC entry 5271 (class 2606 OID 21995)
-- Name: fichas_medicas fichas_medicas_ptc_rsv_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.fichas_medicas
    ADD CONSTRAINT fichas_medicas_ptc_rsv_id_foreign FOREIGN KEY (ptc_rsv_id) REFERENCES public.participantes_reserva(id) ON DELETE CASCADE;


--
-- TOC entry 5251 (class 2606 OID 21708)
-- Name: fotos_tour fotos_tour_tur_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.fotos_tour
    ADD CONSTRAINT fotos_tour_tur_id_foreign FOREIGN KEY (tur_id) REFERENCES public.tours(id) ON DELETE CASCADE;


--
-- TOC entry 5247 (class 2606 OID 21621)
-- Name: idiomas_guia idiomas_guia_idi_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.idiomas_guia
    ADD CONSTRAINT idiomas_guia_idi_id_foreign FOREIGN KEY (idi_id) REFERENCES public.idiomas(id) ON DELETE RESTRICT;


--
-- TOC entry 5248 (class 2606 OID 21616)
-- Name: idiomas_guia idiomas_guia_prf_gui_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.idiomas_guia
    ADD CONSTRAINT idiomas_guia_prf_gui_id_foreign FOREIGN KEY (prf_gui_id) REFERENCES public.perfiles_guia(id) ON DELETE CASCADE;


--
-- TOC entry 5253 (class 2606 OID 21778)
-- Name: items_equipo items_equipo_tip_eqp_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.items_equipo
    ADD CONSTRAINT items_equipo_tip_eqp_id_foreign FOREIGN KEY (tip_eqp_id) REFERENCES public.tipos_equipo(id) ON DELETE CASCADE;


--
-- TOC entry 5277 (class 2606 OID 22105)
-- Name: mensajes_chatbot mensajes_chatbot_cnv_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.mensajes_chatbot
    ADD CONSTRAINT mensajes_chatbot_cnv_id_foreign FOREIGN KEY (cnv_id) REFERENCES public.conversaciones_chatbot(id) ON DELETE CASCADE;


--
-- TOC entry 5273 (class 2606 OID 22041)
-- Name: pagos pagos_cmp_pag_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pagos
    ADD CONSTRAINT pagos_cmp_pag_id_foreign FOREIGN KEY (cmp_pag_id) REFERENCES public.comprobantes_pago(id) ON DELETE CASCADE;


--
-- TOC entry 5264 (class 2606 OID 21923)
-- Name: participantes_reserva participantes_reserva_ci_dep_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.participantes_reserva
    ADD CONSTRAINT participantes_reserva_ci_dep_id_foreign FOREIGN KEY (ci_dep_id) REFERENCES public.departamentos_bolivia(id);


--
-- TOC entry 5265 (class 2606 OID 21928)
-- Name: participantes_reserva participantes_reserva_pai_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.participantes_reserva
    ADD CONSTRAINT participantes_reserva_pai_id_foreign FOREIGN KEY (pai_id) REFERENCES public.paises(id) ON DELETE SET NULL;


--
-- TOC entry 5266 (class 2606 OID 21918)
-- Name: participantes_reserva participantes_reserva_rsv_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.participantes_reserva
    ADD CONSTRAINT participantes_reserva_rsv_id_foreign FOREIGN KEY (rsv_id) REFERENCES public.reservas(id) ON DELETE CASCADE;


--
-- TOC entry 5249 (class 2606 OID 21642)
-- Name: perfiles_chofer perfiles_chofer_usr_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.perfiles_chofer
    ADD CONSTRAINT perfiles_chofer_usr_id_foreign FOREIGN KEY (usr_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- TOC entry 5246 (class 2606 OID 21597)
-- Name: perfiles_guia perfiles_guia_usr_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.perfiles_guia
    ADD CONSTRAINT perfiles_guia_usr_id_foreign FOREIGN KEY (usr_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- TOC entry 5281 (class 2606 OID 22168)
-- Name: proyecciones_demanda proyecciones_demanda_tur_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.proyecciones_demanda
    ADD CONSTRAINT proyecciones_demanda_tur_id_foreign FOREIGN KEY (tur_id) REFERENCES public.tours(id) ON DELETE CASCADE;


--
-- TOC entry 5252 (class 2606 OID 21727)
-- Name: puntos_ruta puntos_ruta_tur_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.puntos_ruta
    ADD CONSTRAINT puntos_ruta_tur_id_foreign FOREIGN KEY (tur_id) REFERENCES public.tours(id) ON DELETE CASCADE;


--
-- TOC entry 5279 (class 2606 OID 22144)
-- Name: registros_notificacion registros_notificacion_rsv_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.registros_notificacion
    ADD CONSTRAINT registros_notificacion_rsv_id_foreign FOREIGN KEY (rsv_id) REFERENCES public.reservas(id) ON DELETE SET NULL;


--
-- TOC entry 5280 (class 2606 OID 22149)
-- Name: registros_notificacion registros_notificacion_usr_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.registros_notificacion
    ADD CONSTRAINT registros_notificacion_usr_id_foreign FOREIGN KEY (usr_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5274 (class 2606 OID 22065)
-- Name: resenas resenas_rsv_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.resenas
    ADD CONSTRAINT resenas_rsv_id_foreign FOREIGN KEY (rsv_id) REFERENCES public.reservas(id);


--
-- TOC entry 5275 (class 2606 OID 22060)
-- Name: resenas resenas_tur_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.resenas
    ADD CONSTRAINT resenas_tur_id_foreign FOREIGN KEY (tur_id) REFERENCES public.tours(id) ON DELETE CASCADE;


--
-- TOC entry 5262 (class 2606 OID 21892)
-- Name: reservas reservas_sal_tur_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.reservas
    ADD CONSTRAINT reservas_sal_tur_id_foreign FOREIGN KEY (sal_tur_id) REFERENCES public.salidas_tour(id) ON DELETE RESTRICT;


--
-- TOC entry 5263 (class 2606 OID 21897)
-- Name: reservas reservas_usr_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.reservas
    ADD CONSTRAINT reservas_usr_id_foreign FOREIGN KEY (usr_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- TOC entry 5256 (class 2606 OID 21837)
-- Name: salidas_tour salidas_tour_prf_cho_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.salidas_tour
    ADD CONSTRAINT salidas_tour_prf_cho_id_foreign FOREIGN KEY (prf_cho_id) REFERENCES public.perfiles_chofer(id) ON DELETE SET NULL;


--
-- TOC entry 5257 (class 2606 OID 21832)
-- Name: salidas_tour salidas_tour_prf_gui_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.salidas_tour
    ADD CONSTRAINT salidas_tour_prf_gui_id_foreign FOREIGN KEY (prf_gui_id) REFERENCES public.perfiles_guia(id) ON DELETE SET NULL;


--
-- TOC entry 5258 (class 2606 OID 21827)
-- Name: salidas_tour salidas_tour_tur_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.salidas_tour
    ADD CONSTRAINT salidas_tour_tur_id_foreign FOREIGN KEY (tur_id) REFERENCES public.tours(id) ON DELETE RESTRICT;


--
-- TOC entry 5259 (class 2606 OID 21842)
-- Name: salidas_tour salidas_tour_veh_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.salidas_tour
    ADD CONSTRAINT salidas_tour_veh_id_foreign FOREIGN KEY (veh_id) REFERENCES public.vehiculos(id) ON DELETE SET NULL;


--
-- TOC entry 5250 (class 2606 OID 21687)
-- Name: tours tours_cat_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tours
    ADD CONSTRAINT tours_cat_id_foreign FOREIGN KEY (cat_id) REFERENCES public.categorias_tour(id) ON DELETE RESTRICT;


--
-- TOC entry 5243 (class 2606 OID 21571)
-- Name: users users_ci_dep_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_ci_dep_id_foreign FOREIGN KEY (ci_dep_id) REFERENCES public.departamentos_bolivia(id);


--
-- TOC entry 5244 (class 2606 OID 21566)
-- Name: users users_pai_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pai_id_foreign FOREIGN KEY (pai_id) REFERENCES public.paises(id) ON DELETE SET NULL;


--
-- TOC entry 5245 (class 2606 OID 21561)
-- Name: users users_rol_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_rol_id_foreign FOREIGN KEY (rol_id) REFERENCES public.roles(id) ON DELETE RESTRICT;


-- Completed on 2026-10-17 03:57:57

--
-- PostgreSQL database dump complete
--

\unrestrict 7YbEm8KTJzm2j7AiMxPwv61vsly037fAqHDlopVhEaFF52YVo122yKoG3r73pYs

