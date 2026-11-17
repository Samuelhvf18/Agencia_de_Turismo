# Diccionario físico verificado

Generado del esquema PostgreSQL 17 migrado en QA el 14/11/2026. Describe el repositorio actual; el contraste formal con D7/SQL externo sigue pendiente. No contiene datos ni credenciales. Las migraciones son la fuente ejecutable.


## `alquileres_equipo_reserva`

| Columna | Tipo | Permite nulo | Predeterminado |
|---|---|---|---|
| `id` | bigint | NO | `nextval('alquileres_equipo_reserva_id_seq'::regclass)` |
| `ptc_rsv_id` | bigint | NO | `—` |
| `itm_eqp_id` | bigint | NO | `—` |
| `pre` | numeric | NO | `—` |

## `bitacora_auditoria`

| Columna | Tipo | Permite nulo | Predeterminado |
|---|---|---|---|
| `id` | bigint | NO | `nextval('bitacora_auditoria_id_seq'::regclass)` |
| `usr_id` | bigint | YES | `—` |
| `nom_tbl` | character varying | NO | `—` |
| `id_reg` | bigint | NO | `—` |
| `acc` | character varying | NO | `—` |
| `val_ant` | jsonb | YES | `—` |
| `val_nvo` | jsonb | YES | `—` |
| `dir_ip` | character varying | YES | `—` |
| `age_usr` | text | YES | `—` |
| `cre_en` | timestamp without time zone | NO | `CURRENT_TIMESTAMP` |

## `bitacora_viajes_vehiculo`

| Columna | Tipo | Permite nulo | Predeterminado |
|---|---|---|---|
| `id` | bigint | NO | `nextval('bitacora_viajes_vehiculo_id_seq'::regclass)` |
| `veh_id` | bigint | NO | `—` |
| `sal_tur_id` | bigint | NO | `—` |
| `dst_km` | numeric | YES | `—` |
| `cos_cmb` | numeric | YES | `—` |
| `reg_en` | timestamp without time zone | NO | `CURRENT_TIMESTAMP` |

## `boletos`

| Columna | Tipo | Permite nulo | Predeterminado |
|---|---|---|---|
| `id` | bigint | NO | `nextval('boletos_id_seq'::regclass)` |
| `cod` | character varying | NO | `—` |
| `ptc_rsv_id` | bigint | NO | `—` |
| `est` | character varying | NO | `'emitido'::character varying` |
| `emt_en` | timestamp without time zone | NO | `CURRENT_TIMESTAMP` |
| `vld_en` | timestamp without time zone | YES | `—` |
| `vld_por` | bigint | YES | `—` |

## `cache`

| Columna | Tipo | Permite nulo | Predeterminado |
|---|---|---|---|
| `key` | character varying | NO | `—` |
| `value` | text | NO | `—` |
| `expiration` | integer | NO | `—` |

## `cache_locks`

| Columna | Tipo | Permite nulo | Predeterminado |
|---|---|---|---|
| `key` | character varying | NO | `—` |
| `owner` | character varying | NO | `—` |
| `expiration` | integer | NO | `—` |

## `categorias_tour`

| Columna | Tipo | Permite nulo | Predeterminado |
|---|---|---|---|
| `id` | bigint | NO | `nextval('categorias_tour_id_seq'::regclass)` |
| `nom` | character varying | NO | `—` |
| `rta_amg` | character varying | NO | `—` |
| `des` | text | YES | `—` |
| `ico` | character varying | YES | `—` |

## `cola_eventos_llegada`

| Columna | Tipo | Permite nulo | Predeterminado |
|---|---|---|---|
| `id` | bigint | NO | `nextval('cola_eventos_llegada_id_seq'::regclass)` |
| `tur_id` | bigint | NO | `—` |
| `sal_tur_id` | bigint | YES | `—` |
| `rsv_id` | bigint | YES | `—` |
| `fec_evt` | timestamp without time zone | NO | `CURRENT_TIMESTAMP` |
| `cnt_sol` | smallint | NO | `'1'::smallint` |
| `est` | character varying | NO | `'admitido'::character varying` |
| `cre_en` | timestamp without time zone | NO | `CURRENT_TIMESTAMP` |

## `cola_metricas_diarias`

| Columna | Tipo | Permite nulo | Predeterminado |
|---|---|---|---|
| `id` | bigint | NO | `nextval('cola_metricas_diarias_id_seq'::regclass)` |
| `tur_id` | bigint | NO | `—` |
| `fec` | date | NO | `—` |
| `lmb_obs` | numeric | YES | `—` |
| `mu_obs` | numeric | YES | `—` |
| `rho` | numeric | YES | `—` |
| `p0` | numeric | YES | `—` |
| `lq` | numeric | YES | `—` |
| `l_sis` | numeric | YES | `—` |
| `wq_hrs` | numeric | YES | `—` |
| `w_hrs` | numeric | YES | `—` |
| `pq` | numeric | YES | `—` |
| `p_rec` | numeric | YES | `—` |
| `cre_en` | timestamp without time zone | NO | `CURRENT_TIMESTAMP` |
| `contexto` | jsonb | YES | `—` |

## `cola_parametros_tour`

| Columna | Tipo | Permite nulo | Predeterminado |
|---|---|---|---|
| `id` | bigint | NO | `nextval('cola_parametros_tour_id_seq'::regclass)` |
| `tur_id` | bigint | NO | `—` |
| `lmb` | numeric | NO | `—` |
| `mu` | numeric | NO | `—` |
| `c_srv` | smallint | NO | `'1'::smallint` |
| `k_cap` | smallint | NO | `—` |
| `cos_esp` | numeric | YES | `—` |
| `cos_srv` | numeric | YES | `—` |
| `fec_cal` | date | NO | `CURRENT_DATE` |
| `obs` | text | YES | `—` |
| `act` | boolean | NO | `true` |
| `cre_en` | timestamp without time zone | NO | `CURRENT_TIMESTAMP` |
| `mod_en` | timestamp without time zone | NO | `CURRENT_TIMESTAMP` |

## `comprobantes_pago`

| Columna | Tipo | Permite nulo | Predeterminado |
|---|---|---|---|
| `id` | bigint | NO | `nextval('comprobantes_pago_id_seq'::regclass)` |
| `cod` | character varying | NO | `—` |
| `rsv_id` | bigint | NO | `—` |
| `fec_emi` | date | NO | `CURRENT_DATE` |
| `sub` | numeric | NO | `—` |
| `imp` | numeric | NO | `'0'::numeric` |
| `mto_tot` | numeric | NO | `—` |
| `est` | character varying | NO | `'pendiente'::character varying` |

## `conversaciones_chatbot`

| Columna | Tipo | Permite nulo | Predeterminado |
|---|---|---|---|
| `id` | bigint | NO | `nextval('conversaciones_chatbot_id_seq'::regclass)` |
| `usr_id` | bigint | YES | `—` |
| `id_ses` | character varying | NO | `—` |
| `can` | character varying | NO | `'web'::character varying` |
| `der_hum` | boolean | NO | `false` |
| `ini_en` | timestamp without time zone | NO | `CURRENT_TIMESTAMP` |
| `fin_en` | timestamp without time zone | YES | `—` |

## `departamentos_bolivia`

| Columna | Tipo | Permite nulo | Predeterminado |
|---|---|---|---|
| `id` | bigint | NO | `nextval('departamentos_bolivia_id_seq'::regclass)` |
| `cod` | character | NO | `—` |
| `nom` | character varying | NO | `—` |

## `equipo_por_tour`

| Columna | Tipo | Permite nulo | Predeterminado |
|---|---|---|---|
| `id` | bigint | NO | `nextval('equipo_por_tour_id_seq'::regclass)` |
| `tur_id` | bigint | NO | `—` |
| `tip_eqp_id` | bigint | NO | `—` |
| `pre_alq_esp` | numeric | YES | `—` |
| `act` | boolean | NO | `true` |

## `failed_jobs`

| Columna | Tipo | Permite nulo | Predeterminado |
|---|---|---|---|
| `id` | bigint | NO | `nextval('failed_jobs_id_seq'::regclass)` |
| `uuid` | character varying | NO | `—` |
| `connection` | text | NO | `—` |
| `queue` | text | NO | `—` |
| `payload` | text | NO | `—` |
| `exception` | text | NO | `—` |
| `failed_at` | timestamp without time zone | NO | `CURRENT_TIMESTAMP` |

## `fichas_medicas`

| Columna | Tipo | Permite nulo | Predeterminado |
|---|---|---|---|
| `id` | bigint | NO | `nextval('fichas_medicas_id_seq'::regclass)` |
| `ptc_rsv_id` | bigint | NO | `—` |
| `con_med` | text | YES | `—` |
| `nom_ctc_eme` | character varying | YES | `—` |
| `tel_ctc_eme` | character varying | YES | `—` |
| `rta_fir` | character varying | NO | `—` |
| `fir_en` | timestamp without time zone | NO | `—` |
| `dir_ip` | character varying | YES | `—` |

## `fotos_tour`

| Columna | Tipo | Permite nulo | Predeterminado |
|---|---|---|---|
| `id` | bigint | NO | `nextval('fotos_tour_id_seq'::regclass)` |
| `tur_id` | bigint | NO | `—` |
| `rta_arc` | character varying | NO | `—` |
| `ord_vis` | smallint | NO | `'0'::smallint` |
| `txt_atv` | character varying | YES | `—` |

## `idiomas`

| Columna | Tipo | Permite nulo | Predeterminado |
|---|---|---|---|
| `id` | bigint | NO | `nextval('idiomas_id_seq'::regclass)` |
| `nom` | character varying | NO | `—` |
| `cod_iso` | character | NO | `—` |

## `idiomas_guia`

| Columna | Tipo | Permite nulo | Predeterminado |
|---|---|---|---|
| `id` | bigint | NO | `nextval('idiomas_guia_id_seq'::regclass)` |
| `prf_gui_id` | bigint | NO | `—` |
| `idi_id` | bigint | NO | `—` |

## `items_equipo`

| Columna | Tipo | Permite nulo | Predeterminado |
|---|---|---|---|
| `id` | bigint | NO | `nextval('items_equipo_id_seq'::regclass)` |
| `tip_eqp_id` | bigint | NO | `—` |
| `tal` | character varying | NO | `—` |
| `cnt_tot` | integer | NO | `0` |
| `cnt_dis` | integer | NO | `0` |
| `con` | character varying | NO | `'bueno'::character varying` |

## `job_batches`

| Columna | Tipo | Permite nulo | Predeterminado |
|---|---|---|---|
| `id` | character varying | NO | `—` |
| `name` | character varying | NO | `—` |
| `total_jobs` | integer | NO | `—` |
| `pending_jobs` | integer | NO | `—` |
| `failed_jobs` | integer | NO | `—` |
| `failed_job_ids` | text | NO | `—` |
| `options` | text | YES | `—` |
| `cancelled_at` | integer | YES | `—` |
| `created_at` | integer | NO | `—` |
| `finished_at` | integer | YES | `—` |

## `jobs`

| Columna | Tipo | Permite nulo | Predeterminado |
|---|---|---|---|
| `id` | bigint | NO | `nextval('jobs_id_seq'::regclass)` |
| `queue` | character varying | NO | `—` |
| `payload` | text | NO | `—` |
| `attempts` | smallint | NO | `—` |
| `reserved_at` | integer | YES | `—` |
| `available_at` | integer | NO | `—` |
| `created_at` | integer | NO | `—` |

## `markov_cliente_estado`

| Columna | Tipo | Permite nulo | Predeterminado |
|---|---|---|---|
| `id` | bigint | NO | `nextval('markov_cliente_estado_id_seq'::regclass)` |
| `usr_id` | bigint | NO | `—` |
| `est_id` | bigint | NO | `—` |
| `est_ant_id` | bigint | YES | `—` |
| `fec_cmb` | date | NO | `CURRENT_DATE` |
| `obs` | text | YES | `—` |
| `cre_en` | timestamp without time zone | NO | `CURRENT_TIMESTAMP` |

## `markov_cohort_distribucion`

| Columna | Tipo | Permite nulo | Predeterminado |
|---|---|---|---|
| `id` | bigint | NO | `nextval('markov_cohort_distribucion_id_seq'::regclass)` |
| `coh_id` | bigint | NO | `—` |
| `per_rel` | smallint | NO | `—` |
| `est_id` | bigint | NO | `—` |
| `cnt` | integer | NO | `0` |
| `cre_en` | timestamp without time zone | NO | `CURRENT_TIMESTAMP` |

## `markov_cohorts`

| Columna | Tipo | Permite nulo | Predeterminado |
|---|---|---|---|
| `id` | bigint | NO | `nextval('markov_cohorts_id_seq'::regclass)` |
| `nom` | character varying | NO | `—` |
| `per_cap` | date | NO | `—` |
| `tot_cli` | integer | NO | `0` |
| `obs` | text | YES | `—` |
| `cre_en` | timestamp without time zone | NO | `CURRENT_TIMESTAMP` |
| `mod_en` | timestamp without time zone | NO | `CURRENT_TIMESTAMP` |

## `markov_estados_cliente`

| Columna | Tipo | Permite nulo | Predeterminado |
|---|---|---|---|
| `id` | bigint | NO | `nextval('markov_estados_cliente_id_seq'::regclass)` |
| `cod` | character varying | NO | `—` |
| `nom` | character varying | NO | `—` |
| `des` | text | YES | `—` |
| `es_abs` | boolean | NO | `false` |
| `ord_vis` | smallint | NO | `'0'::smallint` |
| `col_hex` | character varying | YES | `—` |
| `cre_en` | timestamp without time zone | NO | `CURRENT_TIMESTAMP` |

## `markov_transiciones`

| Columna | Tipo | Permite nulo | Predeterminado |
|---|---|---|---|
| `id` | bigint | NO | `nextval('markov_transiciones_id_seq'::regclass)` |
| `est_ori_id` | bigint | NO | `—` |
| `est_des_id` | bigint | NO | `—` |
| `prb` | numeric | NO | `—` |
| `per` | character varying | NO | `'mensual'::character varying` |
| `fec_cal` | date | NO | `CURRENT_DATE` |
| `act` | boolean | NO | `true` |
| `cre_en` | timestamp without time zone | NO | `CURRENT_TIMESTAMP` |

## `mensajes_chatbot`

| Columna | Tipo | Permite nulo | Predeterminado |
|---|---|---|---|
| `id` | bigint | NO | `nextval('mensajes_chatbot_id_seq'::regclass)` |
| `cnv_id` | bigint | NO | `—` |
| `rem` | character varying | NO | `—` |
| `txt_msj` | text | NO | `—` |
| `int_det` | character varying | YES | `—` |
| `cre_en` | timestamp without time zone | NO | `CURRENT_TIMESTAMP` |

## `migrations`

| Columna | Tipo | Permite nulo | Predeterminado |
|---|---|---|---|
| `id` | integer | NO | `nextval('migrations_id_seq'::regclass)` |
| `migration` | character varying | NO | `—` |
| `batch` | integer | NO | `—` |

## `pagos`

| Columna | Tipo | Permite nulo | Predeterminado |
|---|---|---|---|
| `id` | bigint | NO | `nextval('pagos_id_seq'::regclass)` |
| `cmp_pag_id` | bigint | NO | `—` |
| `mtd` | character varying | NO | `—` |
| `mto` | numeric | NO | `—` |
| `ref_trx` | character varying | YES | `—` |
| `pag_en` | timestamp without time zone | NO | `CURRENT_TIMESTAMP` |
| `est` | character varying | NO | `'pendiente'::character varying` |

## `paises`

| Columna | Tipo | Permite nulo | Predeterminado |
|---|---|---|---|
| `id` | bigint | NO | `nextval('paises_id_seq'::regclass)` |
| `nom` | character varying | NO | `—` |
| `cod_iso` | character | NO | `—` |

## `participantes_reserva`

| Columna | Tipo | Permite nulo | Predeterminado |
|---|---|---|---|
| `id` | bigint | NO | `nextval('participantes_reserva_id_seq'::regclass)` |
| `rsv_id` | bigint | NO | `—` |
| `nom_cpl` | character varying | NO | `—` |
| `tip_doc` | character varying | YES | `—` |
| `num_doc` | character varying | YES | `—` |
| `ci_cmp` | character varying | NO | `''::character varying` |
| `ci_dep_id` | bigint | YES | `—` |
| `eda` | smallint | YES | `—` |
| `pai_id` | bigint | YES | `—` |
| `es_ctc_pri` | boolean | NO | `false` |

## `password_reset_tokens`

| Columna | Tipo | Permite nulo | Predeterminado |
|---|---|---|---|
| `email` | character varying | NO | `—` |
| `token` | character varying | NO | `—` |
| `created_at` | timestamp without time zone | YES | `—` |

## `perfiles_chofer`

| Columna | Tipo | Permite nulo | Predeterminado |
|---|---|---|---|
| `id` | bigint | NO | `nextval('perfiles_chofer_id_seq'::regclass)` |
| `usr_id` | bigint | NO | `—` |
| `num_lic` | character varying | NO | `—` |
| `tip_lic` | character varying | NO | `—` |
| `fec_ven_lic` | date | NO | `—` |
| `act` | boolean | NO | `true` |

## `perfiles_guia`

| Columna | Tipo | Permite nulo | Predeterminado |
|---|---|---|---|
| `id` | bigint | NO | `nextval('perfiles_guia_id_seq'::regclass)` |
| `usr_id` | bigint | NO | `—` |
| `rta_amg` | character varying | YES | `—` |
| `ani_exp` | smallint | NO | `'0'::smallint` |
| `lim_hrs_sem` | smallint | NO | `'40'::smallint` |
| `bio` | text | YES | `—` |
| `act` | boolean | NO | `true` |

## `personal_access_tokens`

| Columna | Tipo | Permite nulo | Predeterminado |
|---|---|---|---|
| `id` | bigint | NO | `nextval('personal_access_tokens_id_seq'::regclass)` |
| `tokenable_type` | character varying | NO | `—` |
| `tokenable_id` | bigint | NO | `—` |
| `name` | text | NO | `—` |
| `token` | character varying | NO | `—` |
| `abilities` | text | YES | `—` |
| `last_used_at` | timestamp without time zone | YES | `—` |
| `expires_at` | timestamp without time zone | YES | `—` |
| `created_at` | timestamp without time zone | YES | `—` |
| `updated_at` | timestamp without time zone | YES | `—` |

## `propuestas_asignacion`

| Columna | Tipo | Permite nulo | Predeterminado |
|---|---|---|---|
| `id` | bigint | NO | `nextval('propuestas_asignacion_id_seq'::regclass)` |
| `lote` | uuid | NO | `—` |
| `tip` | character varying | NO | `—` |
| `sal_tur_id` | bigint | NO | `—` |
| `rec_act_id` | bigint | YES | `—` |
| `rec_pro_id` | bigint | YES | `—` |
| `cos` | numeric | YES | `—` |
| `det` | jsonb | YES | `—` |
| `est` | character varying | NO | `'pendiente'::character varying` |
| `rev_por` | bigint | YES | `—` |
| `rev_en` | timestamp without time zone | YES | `—` |
| `gen_en` | timestamp without time zone | NO | `CURRENT_TIMESTAMP` |

## `proyecciones_demanda`

| Columna | Tipo | Permite nulo | Predeterminado |
|---|---|---|---|
| `id` | bigint | NO | `nextval('proyecciones_demanda_id_seq'::regclass)` |
| `tur_id` | bigint | NO | `—` |
| `mes_per` | smallint | NO | `—` |
| `ani_per` | smallint | NO | `—` |
| `rsv_pry` | integer | NO | `—` |
| `ver_mdl` | character varying | YES | `—` |
| `gen_en` | timestamp without time zone | NO | `CURRENT_TIMESTAMP` |

## `puntos_ruta`

| Columna | Tipo | Permite nulo | Predeterminado |
|---|---|---|---|
| `id` | bigint | NO | `nextval('puntos_ruta_id_seq'::regclass)` |
| `tur_id` | bigint | NO | `—` |
| `ord_sec` | smallint | NO | `—` |
| `nom` | character varying | YES | `—` |
| `lat` | numeric | NO | `—` |
| `lon` | numeric | NO | `—` |
| `alt_m` | smallint | YES | `—` |
| `tip_pun` | character varying | NO | `'checkpoint'::character varying` |

## `registros_notificacion`

| Columna | Tipo | Permite nulo | Predeterminado |
|---|---|---|---|
| `id` | bigint | NO | `nextval('registros_notificacion_id_seq'::regclass)` |
| `rsv_id` | bigint | YES | `—` |
| `usr_id` | bigint | YES | `—` |
| `can` | character varying | NO | `—` |
| `tip` | character varying | NO | `—` |
| `est` | character varying | NO | `'pendiente'::character varying` |
| `cnd` | jsonb | YES | `—` |
| `env_en` | timestamp without time zone | YES | `—` |
| `clave_evento` | character varying | YES | `—` |
| `intentos` | smallint | NO | `'0'::smallint` |
| `disponible_en` | timestamp without time zone | YES | `—` |
| `bloqueado_hasta` | timestamp without time zone | YES | `—` |
| `token_entrega` | character varying | YES | `—` |

## `resenas`

| Columna | Tipo | Permite nulo | Predeterminado |
|---|---|---|---|
| `id` | bigint | NO | `nextval('resenas_id_seq'::regclass)` |
| `tur_id` | bigint | NO | `—` |
| `rsv_id` | bigint | YES | `—` |
| `cal` | smallint | NO | `—` |
| `com` | text | YES | `—` |
| `mdt` | jsonb | YES | `—` |
| `cre_en` | timestamp without time zone | NO | `CURRENT_TIMESTAMP` |

## `reservas`

| Columna | Tipo | Permite nulo | Predeterminado |
|---|---|---|---|
| `id` | bigint | NO | `nextval('reservas_id_seq'::regclass)` |
| `cod` | character varying | NO | `—` |
| `sal_tur_id` | bigint | NO | `—` |
| `usr_id` | bigint | YES | `—` |
| `nom_ctc` | character varying | NO | `—` |
| `cor_ctc` | character varying | NO | `—` |
| `tel_ctc` | character varying | NO | `—` |
| `cnt_ptc` | smallint | NO | `—` |
| `est` | character varying | NO | `'pendiente'::character varying` |
| `pre_tot` | numeric | NO | `—` |
| `est_pag` | character varying | NO | `'pendiente'::character varying` |
| `cre_en` | timestamp without time zone | NO | `CURRENT_TIMESTAMP` |
| `mod_en` | timestamp without time zone | NO | `CURRENT_TIMESTAMP` |
| `idioma` | character varying | NO | `'es'::character varying` |

## `roles`

| Columna | Tipo | Permite nulo | Predeterminado |
|---|---|---|---|
| `id` | bigint | NO | `nextval('roles_id_seq'::regclass)` |
| `nom` | character varying | NO | `—` |
| `des` | character varying | YES | `—` |

## `salidas_tour`

| Columna | Tipo | Permite nulo | Predeterminado |
|---|---|---|---|
| `id` | bigint | NO | `nextval('salidas_tour_id_seq'::regclass)` |
| `tur_id` | bigint | NO | `—` |
| `fec_sal` | date | NO | `—` |
| `hra_sal` | time without time zone | NO | `—` |
| `prf_gui_id` | bigint | YES | `—` |
| `prf_cho_id` | bigint | YES | `—` |
| `veh_id` | bigint | YES | `—` |
| `cap_max` | smallint | NO | `—` |
| `est` | character varying | NO | `'programada'::character varying` |
| `hra_ini_rea` | timestamp without time zone | YES | `—` |
| `hra_fin_rea` | timestamp without time zone | YES | `—` |
| `cre_en` | timestamp without time zone | NO | `CURRENT_TIMESTAMP` |
| `mod_en` | timestamp without time zone | NO | `CURRENT_TIMESTAMP` |

## `sessions`

| Columna | Tipo | Permite nulo | Predeterminado |
|---|---|---|---|
| `id` | character varying | NO | `—` |
| `user_id` | bigint | YES | `—` |
| `ip_address` | character varying | YES | `—` |
| `user_agent` | text | YES | `—` |
| `payload` | text | NO | `—` |
| `last_activity` | integer | NO | `—` |

## `tipos_equipo`

| Columna | Tipo | Permite nulo | Predeterminado |
|---|---|---|---|
| `id` | bigint | NO | `nextval('tipos_equipo_id_seq'::regclass)` |
| `nom` | character varying | NO | `—` |
| `des` | text | YES | `—` |
| `pre_alq_bas` | numeric | NO | `'0'::numeric` |
| `cos_ord` | numeric | YES | `—` |
| `cos_mnt_anu` | numeric | YES | `—` |
| `eqp_cri` | boolean | NO | `false` |

## `tours`

| Columna | Tipo | Permite nulo | Predeterminado |
|---|---|---|---|
| `id` | bigint | NO | `nextval('tours_id_seq'::regclass)` |
| `cat_id` | bigint | NO | `—` |
| `cod` | character varying | NO | `—` |
| `rta_amg` | character varying | NO | `—` |
| `nom` | character varying | NO | `—` |
| `des` | text | YES | `—` |
| `niv_dif` | character varying | NO | `—` |
| `alt_min_m` | smallint | YES | `—` |
| `alt_max_m` | smallint | YES | `—` |
| `dur_hrs` | numeric | NO | `—` |
| `req_fis` | text | YES | `—` |
| `pre_bas` | numeric | NO | `—` |
| `cap_max` | smallint | NO | `—` |
| `trz_geo` | jsonb | YES | `—` |
| `act` | boolean | NO | `true` |
| `cre_en` | timestamp without time zone | NO | `CURRENT_TIMESTAMP` |
| `mod_en` | timestamp without time zone | NO | `CURRENT_TIMESTAMP` |
| `eli_en` | timestamp without time zone | YES | `—` |

## `users`

| Columna | Tipo | Permite nulo | Predeterminado |
|---|---|---|---|
| `id` | bigint | NO | `nextval('users_id_seq'::regclass)` |
| `rol_id` | bigint | NO | `—` |
| `pai_id` | bigint | YES | `—` |
| `nom` | character varying | NO | `—` |
| `cor` | character varying | NO | `—` |
| `cor_vrf_en` | timestamp without time zone | YES | `—` |
| `clv` | character varying | NO | `—` |
| `tel` | character varying | YES | `—` |
| `tip_doc` | character varying | YES | `—` |
| `num_doc` | character varying | YES | `—` |
| `ci_cmp` | character varying | NO | `''::character varying` |
| `ci_dep_id` | bigint | YES | `—` |
| `fot_prf` | character varying | YES | `—` |
| `act` | boolean | NO | `true` |
| `remember_token` | character varying | YES | `—` |
| `created_at` | timestamp without time zone | YES | `—` |
| `updated_at` | timestamp without time zone | YES | `—` |
| `eli_en` | timestamp without time zone | YES | `—` |
| `two_factor_secret` | text | YES | `—` |
| `two_factor_recovery_codes` | text | YES | `—` |
| `two_factor_confirmed_at` | timestamp without time zone | YES | `—` |

## `vehiculos`

| Columna | Tipo | Permite nulo | Predeterminado |
|---|---|---|---|
| `id` | bigint | NO | `nextval('vehiculos_id_seq'::regclass)` |
| `pla` | character varying | NO | `—` |
| `mar` | character varying | YES | `—` |
| `mdl` | character varying | YES | `—` |
| `tip` | character varying | NO | `—` |
| `cap_asi` | smallint | NO | `—` |
| `tip_cmb` | character varying | YES | `—` |
| `est` | character varying | NO | `'activo'::character varying` |

## `vista_calificacion_promedio_tour`

| Columna | Tipo | Permite nulo | Predeterminado |
|---|---|---|---|
| `tur_id` | bigint | YES | `—` |
| `tour` | character varying | YES | `—` |
| `calificacion_promedio` | numeric | YES | `—` |
| `cantidad_resenas` | bigint | YES | `—` |

## `vista_carga_horaria_guia`

| Columna | Tipo | Permite nulo | Predeterminado |
|---|---|---|---|
| `prf_gui_id` | bigint | YES | `—` |
| `guia` | character varying | YES | `—` |
| `ani_exp` | smallint | YES | `—` |
| `lim_hrs_sem` | smallint | YES | `—` |
| `semana` | date | YES | `—` |
| `horas_asignadas` | numeric | YES | `—` |
| `horas_disponibles` | numeric | YES | `—` |

## `vista_demanda_equipo_anual`

| Columna | Tipo | Permite nulo | Predeterminado |
|---|---|---|---|
| `tip_eqp_id` | bigint | YES | `—` |
| `tipo_equipo` | character varying | YES | `—` |
| `eqp_cri` | boolean | YES | `—` |
| `cos_ord` | numeric | YES | `—` |
| `cos_mnt_anu` | numeric | YES | `—` |
| `anio` | smallint | YES | `—` |
| `unidades_alquiladas` | bigint | YES | `—` |

## `vista_demanda_por_tour_mes`

| Columna | Tipo | Permite nulo | Predeterminado |
|---|---|---|---|
| `tur_id` | bigint | YES | `—` |
| `tour` | character varying | YES | `—` |
| `anio` | smallint | YES | `—` |
| `mes` | smallint | YES | `—` |
| `reservas` | bigint | YES | `—` |
| `participantes` | bigint | YES | `—` |

## `vista_llegadas_diarias_tour`

| Columna | Tipo | Permite nulo | Predeterminado |
|---|---|---|---|
| `tur_id` | bigint | YES | `—` |
| `tour` | character varying | YES | `—` |
| `fec` | date | YES | `—` |
| `llegadas` | bigint | YES | `—` |
| `admitidas` | bigint | YES | `—` |
| `rechazadas` | bigint | YES | `—` |
| `cupos_solicitados` | bigint | YES | `—` |

## `vista_ocupacion_transporte`

| Columna | Tipo | Permite nulo | Predeterminado |
|---|---|---|---|
| `sal_tur_id` | bigint | YES | `—` |
| `fec_sal` | date | YES | `—` |
| `veh_id` | bigint | YES | `—` |
| `pla` | character varying | YES | `—` |
| `cap_asi` | smallint | YES | `—` |
| `pasajeros` | bigint | YES | `—` |
| `porcentaje_ocupacion` | numeric | YES | `—` |
| `dst_km` | numeric | YES | `—` |
| `cos_cmb` | numeric | YES | `—` |

## `vista_turistas_por_nacionalidad`

| Columna | Tipo | Permite nulo | Predeterminado |
|---|---|---|---|
| `pais` | character varying | YES | `—` |
| `cod_iso` | character | YES | `—` |
| `turistas` | bigint | YES | `—` |

## Restricciones

| Tabla | Regla |
|---|---|
| `alquileres_equipo_reserva` | `FOREIGN KEY (itm_eqp_id) REFERENCES items_equipo(id) ON DELETE RESTRICT` |
| `alquileres_equipo_reserva` | `PRIMARY KEY (id)` |
| `alquileres_equipo_reserva` | `FOREIGN KEY (ptc_rsv_id) REFERENCES participantes_reserva(id) ON DELETE CASCADE` |
| `alquileres_equipo_reserva` | `CHECK ((pre >= (0)::numeric))` |
| `bitacora_auditoria` | `PRIMARY KEY (id)` |
| `bitacora_auditoria` | `FOREIGN KEY (usr_id) REFERENCES users(id) ON DELETE SET NULL` |
| `bitacora_auditoria` | `CHECK (((acc)::text = ANY ((ARRAY['CREATE'::character varying, 'UPDATE'::character varying, 'DELETE'::character varying, 'READ'::character varying])::text[])))` |
| `bitacora_viajes_vehiculo` | `PRIMARY KEY (id)` |
| `bitacora_viajes_vehiculo` | `FOREIGN KEY (sal_tur_id) REFERENCES salidas_tour(id) ON DELETE CASCADE` |
| `bitacora_viajes_vehiculo` | `FOREIGN KEY (veh_id) REFERENCES vehiculos(id) ON DELETE CASCADE` |
| `bitacora_viajes_vehiculo` | `CHECK ((((dst_km IS NULL) OR (dst_km >= (0)::numeric)) AND ((cos_cmb IS NULL) OR (cos_cmb >= (0)::numeric))))` |
| `boletos` | `UNIQUE (cod)` |
| `boletos` | `PRIMARY KEY (id)` |
| `boletos` | `FOREIGN KEY (ptc_rsv_id) REFERENCES participantes_reserva(id) ON DELETE CASCADE` |
| `boletos` | `UNIQUE (ptc_rsv_id)` |
| `boletos` | `FOREIGN KEY (vld_por) REFERENCES users(id) ON DELETE SET NULL` |
| `boletos` | `CHECK (((est)::text = ANY ((ARRAY['emitido'::character varying, 'validado'::character varying, 'cancelado'::character varying])::text[])))` |
| `cache` | `PRIMARY KEY (key)` |
| `cache_locks` | `PRIMARY KEY (key)` |
| `categorias_tour` | `PRIMARY KEY (id)` |
| `categorias_tour` | `UNIQUE (rta_amg)` |
| `cola_eventos_llegada` | `CHECK ((cnt_sol > 0))` |
| `cola_eventos_llegada` | `CHECK (((est)::text = ANY ((ARRAY['admitido'::character varying, 'rechazado'::character varying, 'cancelado'::character varying])::text[])))` |
| `cola_eventos_llegada` | `PRIMARY KEY (id)` |
| `cola_eventos_llegada` | `FOREIGN KEY (rsv_id) REFERENCES reservas(id) ON DELETE SET NULL` |
| `cola_eventos_llegada` | `FOREIGN KEY (sal_tur_id) REFERENCES salidas_tour(id) ON DELETE SET NULL` |
| `cola_eventos_llegada` | `FOREIGN KEY (tur_id) REFERENCES tours(id) ON DELETE CASCADE` |
| `cola_metricas_diarias` | `CHECK ((((lq IS NULL) OR (lq >= (0)::numeric)) AND ((l_sis IS NULL) OR (l_sis >= (0)::numeric)) AND ((wq_hrs IS NULL) OR (wq_hrs >= (0)::numeric)) AND ((w_hrs IS NULL) OR (w_hrs >= (0)::numeric))))` |
| `cola_metricas_diarias` | `CHECK ((((p0 IS NULL) OR ((p0 >= (0)::numeric) AND (p0 <= (1)::numeric))) AND ((pq IS NULL) OR ((pq >= (0)::numeric) AND (pq <= (1)::numeric))) AND ((p_rec IS NULL) OR ((p_rec >= (0)::numeric) AND (p_rec <= (1)::numeric)))))` |
| `cola_metricas_diarias` | `PRIMARY KEY (id)` |
| `cola_metricas_diarias` | `UNIQUE (tur_id, fec)` |
| `cola_metricas_diarias` | `FOREIGN KEY (tur_id) REFERENCES tours(id) ON DELETE CASCADE` |
| `cola_parametros_tour` | `CHECK (((c_srv >= 1) AND (k_cap >= c_srv)))` |
| `cola_parametros_tour` | `CHECK ((((cos_esp IS NULL) OR (cos_esp >= (0)::numeric)) AND ((cos_srv IS NULL) OR (cos_srv >= (0)::numeric))))` |
| `cola_parametros_tour` | `CHECK (((lmb >= (0)::numeric) AND (mu > (0)::numeric)))` |
| `cola_parametros_tour` | `PRIMARY KEY (id)` |
| `cola_parametros_tour` | `FOREIGN KEY (tur_id) REFERENCES tours(id) ON DELETE CASCADE` |
| `comprobantes_pago` | `CHECK (((est)::text = ANY ((ARRAY['pendiente'::character varying, 'pagado'::character varying, 'anulado'::character varying])::text[])))` |
| `comprobantes_pago` | `CHECK (((sub >= (0)::numeric) AND (imp >= (0)::numeric) AND (mto_tot = (sub + imp))))` |
| `comprobantes_pago` | `UNIQUE (cod)` |
| `comprobantes_pago` | `PRIMARY KEY (id)` |
| `comprobantes_pago` | `FOREIGN KEY (rsv_id) REFERENCES reservas(id) ON DELETE RESTRICT` |
| `comprobantes_pago` | `UNIQUE (rsv_id)` |
| `conversaciones_chatbot` | `CHECK (((can)::text = ANY ((ARRAY['web'::character varying, 'whatsapp'::character varying])::text[])))` |
| `conversaciones_chatbot` | `PRIMARY KEY (id)` |
| `conversaciones_chatbot` | `FOREIGN KEY (usr_id) REFERENCES users(id) ON DELETE SET NULL` |
| `departamentos_bolivia` | `UNIQUE (cod)` |
| `departamentos_bolivia` | `PRIMARY KEY (id)` |
| `equipo_por_tour` | `PRIMARY KEY (id)` |
| `equipo_por_tour` | `FOREIGN KEY (tip_eqp_id) REFERENCES tipos_equipo(id) ON DELETE RESTRICT` |
| `equipo_por_tour` | `FOREIGN KEY (tur_id) REFERENCES tours(id) ON DELETE CASCADE` |
| `equipo_por_tour` | `UNIQUE (tur_id, tip_eqp_id)` |
| `failed_jobs` | `PRIMARY KEY (id)` |
| `failed_jobs` | `UNIQUE (uuid)` |
| `fichas_medicas` | `PRIMARY KEY (id)` |
| `fichas_medicas` | `FOREIGN KEY (ptc_rsv_id) REFERENCES participantes_reserva(id) ON DELETE CASCADE` |
| `fichas_medicas` | `UNIQUE (ptc_rsv_id)` |
| `fotos_tour` | `PRIMARY KEY (id)` |
| `fotos_tour` | `FOREIGN KEY (tur_id) REFERENCES tours(id) ON DELETE CASCADE` |
| `idiomas` | `UNIQUE (cod_iso)` |
| `idiomas` | `PRIMARY KEY (id)` |
| `idiomas_guia` | `FOREIGN KEY (idi_id) REFERENCES idiomas(id) ON DELETE RESTRICT` |
| `idiomas_guia` | `PRIMARY KEY (id)` |
| `idiomas_guia` | `FOREIGN KEY (prf_gui_id) REFERENCES perfiles_guia(id) ON DELETE CASCADE` |
| `idiomas_guia` | `UNIQUE (prf_gui_id, idi_id)` |
| `items_equipo` | `CHECK (((cnt_tot >= 0) AND ((cnt_dis >= 0) AND (cnt_dis <= cnt_tot))))` |
| `items_equipo` | `PRIMARY KEY (id)` |
| `items_equipo` | `FOREIGN KEY (tip_eqp_id) REFERENCES tipos_equipo(id) ON DELETE CASCADE` |
| `items_equipo` | `UNIQUE (tip_eqp_id, tal)` |
| `job_batches` | `PRIMARY KEY (id)` |
| `jobs` | `PRIMARY KEY (id)` |
| `markov_cliente_estado` | `CHECK (((est_ant_id IS NULL) OR (est_ant_id <> est_id)))` |
| `markov_cliente_estado` | `FOREIGN KEY (est_ant_id) REFERENCES markov_estados_cliente(id) ON DELETE SET NULL` |
| `markov_cliente_estado` | `FOREIGN KEY (est_id) REFERENCES markov_estados_cliente(id) ON DELETE RESTRICT` |
| `markov_cliente_estado` | `PRIMARY KEY (id)` |
| `markov_cliente_estado` | `FOREIGN KEY (usr_id) REFERENCES users(id) ON DELETE CASCADE` |
| `markov_cohort_distribucion` | `CHECK (((per_rel >= 0) AND (cnt >= 0)))` |
| `markov_cohort_distribucion` | `UNIQUE (coh_id, per_rel, est_id)` |
| `markov_cohort_distribucion` | `FOREIGN KEY (coh_id) REFERENCES markov_cohorts(id) ON DELETE CASCADE` |
| `markov_cohort_distribucion` | `FOREIGN KEY (est_id) REFERENCES markov_estados_cliente(id) ON DELETE RESTRICT` |
| `markov_cohort_distribucion` | `PRIMARY KEY (id)` |
| `markov_cohorts` | `CHECK ((EXTRACT(day FROM per_cap) = (1)::numeric))` |
| `markov_cohorts` | `CHECK ((tot_cli >= 0))` |
| `markov_cohorts` | `UNIQUE (per_cap)` |
| `markov_cohorts` | `PRIMARY KEY (id)` |
| `markov_estados_cliente` | `CHECK (((cod)::text = ANY ((ARRAY['prospecto'::character varying, 'reservado'::character varying, 'completado'::character varying, 'fidelizado'::character varying, 'perdido'::character varying])::text[])))` |
| `markov_estados_cliente` | `CHECK (((col_hex IS NULL) OR ((col_hex)::text ~ '^#[0-9A-Fa-f]{6}$'::text)))` |
| `markov_estados_cliente` | `UNIQUE (cod)` |
| `markov_estados_cliente` | `PRIMARY KEY (id)` |
| `markov_transiciones` | `CHECK (((per)::text = ANY ((ARRAY['mensual'::character varying, 'trimestral'::character varying])::text[])))` |
| `markov_transiciones` | `CHECK (((prb >= (0)::numeric) AND (prb <= (1)::numeric)))` |
| `markov_transiciones` | `UNIQUE (est_ori_id, est_des_id, per, fec_cal)` |
| `markov_transiciones` | `FOREIGN KEY (est_des_id) REFERENCES markov_estados_cliente(id) ON DELETE CASCADE` |
| `markov_transiciones` | `FOREIGN KEY (est_ori_id) REFERENCES markov_estados_cliente(id) ON DELETE CASCADE` |
| `markov_transiciones` | `PRIMARY KEY (id)` |
| `mensajes_chatbot` | `CHECK (((rem)::text = ANY ((ARRAY['usuario'::character varying, 'bot'::character varying])::text[])))` |
| `mensajes_chatbot` | `FOREIGN KEY (cnv_id) REFERENCES conversaciones_chatbot(id) ON DELETE CASCADE` |
| `mensajes_chatbot` | `PRIMARY KEY (id)` |
| `migrations` | `PRIMARY KEY (id)` |
| `pagos` | `CHECK (((est)::text = ANY ((ARRAY['completado'::character varying, 'fallido'::character varying, 'pendiente'::character varying])::text[])))` |
| `pagos` | `CHECK (((mtd)::text = ANY ((ARRAY['tarjeta'::character varying, 'qr'::character varying, 'transferencia'::character varying, 'efectivo'::character varying])::text[])))` |
| `pagos` | `CHECK ((mto > (0)::numeric))` |
| `pagos` | `FOREIGN KEY (cmp_pag_id) REFERENCES comprobantes_pago(id) ON DELETE RESTRICT` |
| `pagos` | `PRIMARY KEY (id)` |
| `paises` | `UNIQUE (cod_iso)` |
| `paises` | `PRIMARY KEY (id)` |
| `participantes_reserva` | `CHECK ((((tip_doc)::text = 'CI'::text) OR (ci_dep_id IS NULL)))` |
| `participantes_reserva` | `CHECK (((eda IS NULL) OR ((eda >= 0) AND (eda <= 110))))` |
| `participantes_reserva` | `CHECK (((tip_doc IS NULL) OR ((tip_doc)::text = ANY ((ARRAY['CI'::character varying, 'PASAPORTE'::character varying])::text[]))))` |
| `participantes_reserva` | `FOREIGN KEY (ci_dep_id) REFERENCES departamentos_bolivia(id)` |
| `participantes_reserva` | `FOREIGN KEY (pai_id) REFERENCES paises(id) ON DELETE SET NULL` |
| `participantes_reserva` | `PRIMARY KEY (id)` |
| `participantes_reserva` | `FOREIGN KEY (rsv_id) REFERENCES reservas(id) ON DELETE CASCADE` |
| `password_reset_tokens` | `PRIMARY KEY (email)` |
| `perfiles_chofer` | `PRIMARY KEY (id)` |
| `perfiles_chofer` | `FOREIGN KEY (usr_id) REFERENCES users(id) ON DELETE CASCADE` |
| `perfiles_chofer` | `UNIQUE (usr_id)` |
| `perfiles_guia` | `CHECK ((ani_exp >= 0))` |
| `perfiles_guia` | `CHECK (((lim_hrs_sem >= 1) AND (lim_hrs_sem <= 60)))` |
| `perfiles_guia` | `PRIMARY KEY (id)` |
| `perfiles_guia` | `UNIQUE (rta_amg)` |
| `perfiles_guia` | `FOREIGN KEY (usr_id) REFERENCES users(id) ON DELETE CASCADE` |
| `perfiles_guia` | `UNIQUE (usr_id)` |
| `personal_access_tokens` | `PRIMARY KEY (id)` |
| `personal_access_tokens` | `UNIQUE (token)` |
| `propuestas_asignacion` | `CHECK (((est)::text = ANY ((ARRAY['pendiente'::character varying, 'aplicada'::character varying, 'ajustada'::character varying, 'rechazada'::character varying])::text[])))` |
| `propuestas_asignacion` | `CHECK (((tip)::text = ANY ((ARRAY['guia'::character varying, 'vehiculo'::character varying])::text[])))` |
| `propuestas_asignacion` | `PRIMARY KEY (id)` |
| `propuestas_asignacion` | `FOREIGN KEY (rev_por) REFERENCES users(id) ON DELETE SET NULL` |
| `propuestas_asignacion` | `FOREIGN KEY (sal_tur_id) REFERENCES salidas_tour(id) ON DELETE CASCADE` |
| `proyecciones_demanda` | `CHECK (((mes_per >= 1) AND (mes_per <= 12)))` |
| `proyecciones_demanda` | `CHECK ((rsv_pry >= 0))` |
| `proyecciones_demanda` | `PRIMARY KEY (id)` |
| `proyecciones_demanda` | `FOREIGN KEY (tur_id) REFERENCES tours(id) ON DELETE CASCADE` |
| `proyecciones_demanda` | `UNIQUE (tur_id, mes_per, ani_per)` |
| `puntos_ruta` | `CHECK (((lat >= ('-90'::integer)::numeric) AND (lat <= (90)::numeric)))` |
| `puntos_ruta` | `CHECK (((lon >= ('-180'::integer)::numeric) AND (lon <= (180)::numeric)))` |
| `puntos_ruta` | `CHECK (((tip_pun)::text = ANY ((ARRAY['inicio'::character varying, 'checkpoint'::character varying, 'mirador'::character varying, 'fin'::character varying])::text[])))` |
| `puntos_ruta` | `PRIMARY KEY (id)` |
| `puntos_ruta` | `FOREIGN KEY (tur_id) REFERENCES tours(id) ON DELETE CASCADE` |
| `puntos_ruta` | `UNIQUE (tur_id, ord_sec)` |
| `registros_notificacion` | `CHECK (((can)::text = ANY ((ARRAY['whatsapp'::character varying, 'email'::character varying, 'sms'::character varying, 'panel'::character varying])::text[])))` |
| `registros_notificacion` | `CHECK (((est)::text = ANY ((ARRAY['enviado'::character varying, 'fallido'::character varying, 'pendiente'::character varying])::text[])))` |
| `registros_notificacion` | `UNIQUE (clave_evento)` |
| `registros_notificacion` | `PRIMARY KEY (id)` |
| `registros_notificacion` | `FOREIGN KEY (rsv_id) REFERENCES reservas(id) ON DELETE SET NULL` |
| `registros_notificacion` | `FOREIGN KEY (usr_id) REFERENCES users(id) ON DELETE SET NULL` |
| `resenas` | `CHECK (((cal >= 1) AND (cal <= 5)))` |
| `resenas` | `PRIMARY KEY (id)` |
| `resenas` | `FOREIGN KEY (rsv_id) REFERENCES reservas(id)` |
| `resenas` | `UNIQUE (rsv_id)` |
| `resenas` | `FOREIGN KEY (tur_id) REFERENCES tours(id) ON DELETE CASCADE` |
| `reservas` | `CHECK ((cnt_ptc > 0))` |
| `reservas` | `CHECK (((est)::text = ANY ((ARRAY['pendiente'::character varying, 'confirmada'::character varying, 'cancelada'::character varying, 'completada'::character varying])::text[])))` |
| `reservas` | `CHECK (((est_pag)::text = ANY ((ARRAY['pendiente'::character varying, 'parcial'::character varying, 'pagado'::character varying, 'reembolsado'::character varying])::text[])))` |
| `reservas` | `CHECK ((pre_tot >= (0)::numeric))` |
| `reservas` | `UNIQUE (cod)` |
| `reservas` | `PRIMARY KEY (id)` |
| `reservas` | `FOREIGN KEY (sal_tur_id) REFERENCES salidas_tour(id) ON DELETE RESTRICT` |
| `reservas` | `FOREIGN KEY (usr_id) REFERENCES users(id) ON DELETE SET NULL` |
| `roles` | `UNIQUE (nom)` |
| `roles` | `PRIMARY KEY (id)` |
| `salidas_tour` | `CHECK ((cap_max > 0))` |
| `salidas_tour` | `CHECK (((est)::text = ANY ((ARRAY['programada'::character varying, 'en_curso'::character varying, 'completada'::character varying, 'cancelada'::character varying])::text[])))` |
| `salidas_tour` | `CHECK (((hra_fin_rea IS NULL) OR (hra_ini_rea IS NULL) OR (hra_fin_rea >= hra_ini_rea)))` |
| `salidas_tour` | `PRIMARY KEY (id)` |
| `salidas_tour` | `FOREIGN KEY (prf_cho_id) REFERENCES perfiles_chofer(id) ON DELETE SET NULL` |
| `salidas_tour` | `FOREIGN KEY (prf_gui_id) REFERENCES perfiles_guia(id) ON DELETE SET NULL` |
| `salidas_tour` | `FOREIGN KEY (tur_id) REFERENCES tours(id) ON DELETE RESTRICT` |
| `salidas_tour` | `FOREIGN KEY (veh_id) REFERENCES vehiculos(id) ON DELETE SET NULL` |
| `sessions` | `PRIMARY KEY (id)` |
| `tipos_equipo` | `CHECK ((((cos_ord IS NULL) OR (cos_ord > (0)::numeric)) AND ((cos_mnt_anu IS NULL) OR (cos_mnt_anu > (0)::numeric))))` |
| `tipos_equipo` | `PRIMARY KEY (id)` |
| `tours` | `CHECK (((alt_min_m IS NULL) OR (alt_max_m IS NULL) OR (alt_max_m >= alt_min_m)))` |
| `tours` | `CHECK ((cap_max > 0))` |
| `tours` | `CHECK (((dur_hrs >= (1)::numeric) AND (dur_hrs <= (14)::numeric)))` |
| `tours` | `CHECK (((niv_dif)::text = ANY ((ARRAY['facil'::character varying, 'moderado'::character varying, 'dificil'::character varying, 'experto'::character varying])::text[])))` |
| `tours` | `CHECK ((pre_bas >= (0)::numeric))` |
| `tours` | `FOREIGN KEY (cat_id) REFERENCES categorias_tour(id) ON DELETE RESTRICT` |
| `tours` | `UNIQUE (cod)` |
| `tours` | `PRIMARY KEY (id)` |
| `tours` | `UNIQUE (rta_amg)` |
| `users` | `CHECK ((((tip_doc)::text = 'CI'::text) OR (ci_dep_id IS NULL)))` |
| `users` | `CHECK (((tip_doc IS NULL) OR ((tip_doc)::text = ANY ((ARRAY['CI'::character varying, 'PASAPORTE'::character varying])::text[]))))` |
| `users` | `FOREIGN KEY (ci_dep_id) REFERENCES departamentos_bolivia(id)` |
| `users` | `UNIQUE (cor)` |
| `users` | `UNIQUE (num_doc, ci_cmp)` |
| `users` | `FOREIGN KEY (pai_id) REFERENCES paises(id) ON DELETE SET NULL` |
| `users` | `PRIMARY KEY (id)` |
| `users` | `FOREIGN KEY (rol_id) REFERENCES roles(id) ON DELETE RESTRICT` |
| `vehiculos` | `CHECK ((cap_asi > 0))` |
| `vehiculos` | `CHECK (((est)::text = ANY ((ARRAY['activo'::character varying, 'mantenimiento'::character varying, 'inactivo'::character varying])::text[])))` |
| `vehiculos` | `CHECK (((tip)::text = ANY ((ARRAY['minibus'::character varying, 'van'::character varying, 'otro'::character varying])::text[])))` |
| `vehiculos` | `PRIMARY KEY (id)` |
| `vehiculos` | `UNIQUE (pla)` |

## Tablas con trigger transaccional de auditoría

`alquileres_equipo_reserva`, `bitacora_viajes_vehiculo`, `boletos`, `categorias_tour`, `cola_eventos_llegada`, `cola_metricas_diarias`, `cola_parametros_tour`, `comprobantes_pago`, `conversaciones_chatbot`, `departamentos_bolivia`, `equipo_por_tour`, `fichas_medicas`, `fotos_tour`, `idiomas`, `idiomas_guia`, `items_equipo`, `markov_cliente_estado`, `markov_cohort_distribucion`, `markov_cohorts`, `markov_estados_cliente`, `markov_transiciones`, `mensajes_chatbot`, `pagos`, `paises`, `participantes_reserva`, `perfiles_chofer`, `perfiles_guia`, `propuestas_asignacion`, `proyecciones_demanda`, `puntos_ruta`, `registros_notificacion`, `resenas`, `reservas`, `roles`, `salidas_tour`, `tipos_equipo`, `tours`, `users`, `vehiculos`.

Las lecturas de participantes/fichas/PDF usan AuditObserver. Se excluyen bitácora, caché, sesiones, colas técnicas y tokens. Un actor nulo representa invitado o sistema; IP/agente contextualizan HTTP. El rol propietario de QA puede cambiar triggers y no acredita los privilegios de producción.
