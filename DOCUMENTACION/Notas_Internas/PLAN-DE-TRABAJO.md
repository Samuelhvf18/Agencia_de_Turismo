# Plan de trabajo: documentación actualizada vs. código implementado

Fecha del análisis: 19/10/2026. Lo revisé en el repositorio `Agencia_de_Turismo/Agencia_de_Turismo`, rama `WAAAAAA` (commit `da6f8f9`). No modifiqué código.

---

## 1. Qué define la documentación actualizada

Archivos leídos: `README.md`, `Informe_Final.docx/.pdf`, `01_Requerimientos/Lista_de_Requerimientos_RF_RNF`, `02_Scrum/` (Historias de usuario, Product Backlog y Sprint Backlog), `03_Base_de_Datos/` (diccionario v4 y `schema_postgresql_v5.sql`) y `04_Diagramas/`.

- **Objetivos:** 9 objetivos específicos. El nuevo es el **objetivo 5**: modelar el motor de reservas como una cola M/M/c/K y agregar una cadena de Markov para el ciclo de vida del cliente.
- **Requerimientos:** **45 RF** (RF-01 a RF-45) y **20 RNF** (seguridad, auditoría, rendimiento, disponibilidad, usabilidad, integridad, arquitectura, cumplimiento legal y compatibilidad).
- **Scrum:** **52 historias y 113 puntos** en **10 sprints**, uno por módulo:
  - S1 Cuentas
  - S2 Catálogo
  - S3 Personal y recursos
  - S4 Operaciones
  - S5 Reservas
  - S6 Pagos
  - S7 IO II (192 h estimadas, del 19/10 al 02/11)
  - S8 Chatbot
  - S9 Reseñas y dashboards
  - S10 Notificaciones y auditoría (hasta el 25/11)
- **Base de datos:** el esquema v5 tiene **40 tablas y 7 vistas**: `vista_carga_horaria_guia`, `vista_ocupacion_transporte`, `vista_demanda_por_tour_mes`, `vista_turistas_por_nacionalidad`, `vista_calificacion_promedio_tour`, `vista_demanda_equipo_anual` y `vista_llegadas_diarias_tour`. Incluye restricciones CHECK y JSONB (`trz_geo`, auditoría y notificaciones).
- **Arquitectura según el informe:**
  - Laravel 12 MVC con capa de servicios, Livewire 3, Alpine y Tailwind.
  - PostgreSQL 16 con JSONB, Redis para caché y colas, y n8n mediante webhooks.
  - `AuditoriaObserver` para la bitácora, `NotificacionJob` para avisos, FormRequests y middleware de roles.
  - `OptimizadorService` con método húngaro para guías y asignación de vehículos, más EOQ.
  - `ColasService` y `MarkovService`.
  - Pruebas con Pest.
- **IO II, según el informe:**
  - Asignación de guías: binaria, método húngaro, con restricciones de horas e idioma.
  - Asignación de vehículos: minimiza el costo de combustible.
  - EOQ: Q* = √(2DS/H) y R = d·L + SS.
  - Colas M/M/c/K: ρ, P0, Lq, L, Wq, W, Pq, P(rechazo) y CT.
  - Markov absorbente: N = (I−Q)⁻¹, B = N·R y proyección v₀·Pⁿ.
  - Las propuestas **nunca se aplican solas** (RF-24).

> ⚠️ **Discrepancia importante:** el informe y el Sprint Backlog dan como **terminados** los 10 sprints, las 52 HU, el despliegue en servidor y las pruebas Pest. **El código real está muy lejos de eso.** Antes de la defensa hay que implementar lo que falta o ajustar el informe al estado real.

---

## 2. Estado real del código (verificado, no supuesto)

### 2.1 Hallazgos transversales críticos

| # | Hallazgo | Impacto |
|---|---|---|
| T1 | **La autenticación no funciona con el esquema actual.** La migración `2026_10_12_033612` crea `users` con columnas propias (`nom`, `cor`, `clv`, `rol_id`), pero `App\Models\User` sigue siendo el de Jetstream (`name`, `email`, `password`). `migrate:status` muestra **pendientes** `0001_create_users_table`, `add_two_factor_columns`, `create_passkeys` y `create_personal_access_tokens`. Sanctum no tiene su tabla. | Login, registro y todo lo protegido por `auth:sanctum` (incluidos los paneles IO reales) quedan inutilizables. |
| T2 | **Migraciones duplicadas.** `2026_10_03_154753_create_tours_system_tables` vuelve a crear `categorias_tour`, `tours`, `vehiculos`, `salidas_tour`, `reservas` y `bitacora_auditoria`. `0001_…users` y `2026_10_12_040000_sessions` chocan con otras. Un `migrate:fresh` falla. | No se puede levantar la BD desde cero (ni en otra PC ni en el servidor). |
| T3 | **No hay ninguna vista SQL** (de las 7 del esquema v5) ni CHECKs del esquema en las migraciones. `vista_llegadas_diarias_tour` tampoco existe. | Fallan RF-22, 23, 26, 33, 34 y 41, que leen de esas vistas. |
| T4 | **No hay control de roles.** No existe middleware, policies ni Gates, y no hay modelo `Role` en WAAAAAA. `/admin/dashboard`, `/chatbot` y `/guias/checkin` son **públicas**. | Incumple RNF-02, RNF-03 y RNF-05. |
| T5 | **El trabajo de login con roles está en otra rama.** `origin/Gabriel-Rama` (`2e5b7c6`) tiene `Role`, un `User` adaptado (`clv` y `cor`), `RolesSeeder`, `AdminUserSeeder` y vistas de login y registro. Usa `$table = 'usuarios'`, pero la migración crea `users`, así que **hay que conciliarlo**. | Conviene integrarlo en lugar de rehacerlo. |
| T6 | **El dashboard admin de Elmer está en otra rama.** `origin/elmer-rama` (`aadfba8`) tiene `layouts/admin`, `panels/analitica` y rutas `/admin/*`. No está en WAAAAAA. | Hay que fusionarlo para no duplicar trabajo. |
| T7 | **`AuditObserver` existe pero no está registrado** (`AppServiceProvider::boot()` está vacío). Además silencia todos los errores. | No se escribe nada en `bitacora_auditoria` (objetivo 7 y RNF-06). |
| T8 | **Faltan casi todos los modelos Eloquent.** Solo hay `Tour`, `CategoriaTour`, `FotoTour`, `User` y los 8 de IO II. Faltan `SalidaTour`, `Reserva`, `ParticipanteReserva`, `Boleto`, `FichaMedica`, `Pago`, `ComprobantePago`, `Vehiculo`, `TipoEquipo`, `ItemEquipo`, `PerfilGuia`, `PerfilChofer`, `Resena`, `ConversacionChatbot`, etc. | Ningún flujo de negocio fuera del catálogo e IO tiene backend. |
| T9 | **No se pueden correr las pruebas.** `vendor/` no tiene Pest ni PHPUnit (`php artisan test` falla con `SebastianBergmann\Environment\Console not found`). Solo hay `ColasServiceTest` y `MarkovServiceTest` más los tests por defecto de Jetstream. | Lo que dice el informe sobre las pruebas no está respaldado. Hay que ejecutar `composer install` con dependencias de desarrollo. |
| T10 | **`CodeGeneratorService` no garantiza unicidad.** No verifica contra la BD y `TUR-` solo tiene 90 combinaciones posibles. | RF-10, RF-36 y RNF-07. |
| T11 | Hay **cambios preparados sin commit** en WAAAAAA: rediseño de las 8 vistas de reservas y de `stepper`, y se eliminaron `hero` e `incluye` (13 archivos). | Hay que confirmarlos con su autor antes de tocar esas vistas. |
| T12 | **La rama `investigacion-operativa-2` no existe**, ni local ni en `origin`. El trabajo de IO II está en WAAAAAA (`da6f8f9`) y `Samuel-Rama` (`92210fa`) ya está contenido en WAAAAAA. | Hay que crear la rama si se quiere separar el trabajo de IO. |

### 2.2 Qué hay implementado de verdad

| Área | Implementado | Tipo |
|---|---|---|
| Catálogo público | `CatalogoController@index` lista tours activos con foto y filtra por **categoría** en el cliente. `@show` muestra el detalle por slug (`/tours/{rta_amg}`), con galería y **mapa Leaflet** con polilínea y puntos de `puntos_ruta`. `CatalogoLandingSeeder`. | **Real** (con BD) |
| Motor de reservas | 8 vistas (`salida`, `participantes`, `equipo`, `ficha`, `resumen`, `confirmada`, `consultar` y `estado`) con layout y stepper, rediseñadas. | **Solo maqueta** (`/reservas/preview/*` con datos fijos y sin controlador) |
| IO II: colas y Markov | Migraciones de las 8 tablas; `ColasService` (fórmulas M/M/c/K, CT, escenarios, λ y μ observados, `registrarLlegada`, `generarMetricasDiarias`); `MarkovService` (normalizar, inversa, N, B, proyección, estimación, `registrarCambio`, `sincronizarDesdeReservas`, `recalibrar` y cohortes); comandos `io:metricas-colas` (diario) e `io:recalibrar-markov` (mensual) programados en `routes/console.php`; paneles `/admin/io/colas` y `/admin/io/ciclo-vida` con simulación de c, K y λ; `IoColasMarkovSeeder`; 2 tests unitarios. | **Real**, pero **no está conectado al motor de reservas**: `registrarLlegada()` no se llama en ningún lugar, y `registrarCambio()` solo se llama desde la sincronización por lotes. No hay pantalla para configurar parámetros (RF-40). |
| Admin (dashboard y paneles) | `admin/dashboard` y `panels/{tours, reservas, finanzas, inventario, transporte, auditoria, operaciones, personal}`. | **HTML estático** (cero variables PHP). La mayoría de los paneles no tiene ruta. |
| Check-in del guía | `guias/checkin`. | **Maqueta estática** |
| Chatbot | `chatbot/index`. | **Maqueta estática** (sin n8n ni IA) |
| Auth | Jetstream y Fortify instalados (login, registro, 2FA y perfil). | **Roto** (ver T1) |
| Auditoría | `AuditObserver`. | **Sin registrar** (T7) |

---

## 3. Comparación por módulo y objetivo

Leyenda: ✅ hecho · 🟡 parcial o maqueta · ❌ no existe.

### M01 Cuentas y seguridad (objetivo 9) · RF-03, RF-04, RNF-01 a RNF-04 · HU 7, 8, 9, 41 (6 pts)
- ✅ Jetstream y Fortify están instalados.
- 🟡 Login y registro con roles: están en `Gabriel-Rama`, sin integrar.
- ❌ Falta todo esto:
  - Coherencia entre migraciones, `User` y Fortify (T1).
  - Middleware `rol:` y redirección por rol (turista, guía, chofer, operaciones y administrador).
  - Policies.
  - Alertas de seguridad al administrador: nueva cuenta, 3 intentos fallidos y cambio de contraseña (RNF-04).
  - Forzar HTTPS (RNF-01).

### M02 Catálogo (objetivo 1) · RF-01, RF-02, RF-37 · HU 1 a 6 (14 pts)
- ✅ Hecho: listado público, detalle por slug, galería, mapa Leaflet con trazado y puntos de interés.
- 🟡 Parcial:
  - El filtro es solo por categoría. Faltan **dificultad y rango de altitud** combinados (RF-01).
  - El trazado usa los puntos de `puntos_ruta`, no el GeoJSON `trz_geo`.
- ❌ Falta:
  - **Perfil de elevación** (RF-02, objetivo 1), por ejemplo con Chart.js usando `alt_m`.
  - **CRUD admin** de categorías, tours, galería (subir, ordenar, eliminar) y editor de trazado y puntos de interés (HU 1 a 4).
  - Generación automática del slug con sufijo único (RF-37).
  - i18n ES/EN (RNF-14).

### M03 Personal y recursos (objetivo 3) · RF-15, RF-19, RF-20 · HU 10 a 14 (8 pts)
- 🟡 Las tablas existen y hay paneles HTML estáticos (personal, transporte, inventario).
- ❌ Falta:
  - Modelos y CRUD de guías y choferes, con su regla: no desactivar si tienen salidas futuras.
  - CRUD de tipos de equipo.
  - Inventario por talla, con descuento y devolución automáticos.
  - Flota con sus estados.
  - Configuración de equipo por tour.

### M04 Operaciones y salidas (objetivo 3) · RF-16 a RF-18, RF-21 · HU 15 a 18, 26 (10 pts)
- 🟡 Las tablas existen y la vista de check-in es una maqueta.
- ❌ Falta:
  - Programar salidas.
  - Asignar guía, chofer y vehículo con validación de choque de horario, capacidad y vehículo activo.
  - Cancelar o reprogramar con notificación.
  - **Check-in por QR** (cámara, por ejemplo `html5-qrcode`) que cambie el boleto a `validado`.
  - Registrar el inicio y fin real del recorrido.
  - Modo **sin conexión** con sincronización (RNF-15).

### M05 Motor de reservas (objetivo 2) · RF-05 a RF-12, RF-36 · HU 19 a 25 (14 pts)
- 🟡 Las 8 vistas están diseñadas, pero solo como preview.
- ❌ Falta todo el backend:
  - `ReservaController` y FormRequests.
  - Sesión o borrador del wizard.
  - **Control transaccional de cupo** (`lockForUpdate`).
  - Participantes con regla CI/pasaporte (complemento y departamento).
  - Alquiler de equipo con validación de talla.
  - Ficha médica con **firma digital** (canvas) como bloqueo del boleto en tours de alto riesgo.
  - Reserva de invitado (`usr_id` nulo).
  - Código `RES-XXXXXX` único con verificación en BD y UNIQUE.
  - **Boletos con QR** por participante, en PDF y por correo.
  - Consulta por código.
  - **Conexión con IO:** `ColasService::registrarLlegada()` (admitida, rechazada o cancelada) y `MarkovService::registrarCambio()` (prospecto → reservado → completado).

### M06 Facturación y pagos (objetivo 2 y 6) · RF-13, RF-14, RNF-19 · HU 27 a 29 (7 pts)
- 🟡 Las tablas existen y el panel de finanzas es HTML estático.
- ❌ Falta:
  - Pasarela: tarjeta, QR o transferencia. Se puede usar un modo de prueba o un simulador con webhook.
  - Registrar cada intento en `pagos`.
  - Pagos parciales.
  - Comprobante automático en `comprobantes_pago`, en PDF.
  - Envío por n8n.
  - Anulación solo por estado, sin borrado físico.
  - Historial de pagos.

### M07 Investigación Operativa II (objetivos 3, 4 y 5) · RF-22 a RF-27, RF-40 a RF-45 · HU 30, 31, 42 a 52 (34 pts)
- ✅ Hecho:
  - Colas M/M/c/K: servicio, métricas diarias, panel y simulación de escenarios con CT (RF-42, RF-43).
  - Markov: servicio, recalibración mensual, panel con P, N, B, proyección y cohortes (RF-44, RF-45).
  - Migraciones, seeder y comandos programados.
- 🟡 Parcial:
  - RF-40: los parámetros se cargan por seeder, pero **no hay formulario**. Falta validar μ>0, c≥1 y K≥c y garantizar un solo juego activo por tour.
  - RF-41: falta el registro automático de llegadas y la vista `vista_llegadas_diarias_tour`.
  - RF-44: falta llamar `registrarCambio()` en cada evento real.
  - Los paneles usan datos de demostración si no hay parámetros.
- ❌ Falta:
  - **RF-22:** asignación óptima de guías con **método húngaro** sobre `vista_carga_horaria_guia` (idioma, horas y experiencia).
  - **RF-23:** asignación de vehículos sobre `vista_ocupacion_transporte` (combustible y ocupación).
  - **RF-24:** pantalla para revisar, ajustar, confirmar o rechazar propuestas sin que se apliquen solas.
  - **RF-25 a RF-27:** costos de inventario, **EOQ** y punto de reorden desde `vista_demanda_equipo_anual` (con aviso si no hay historial), y **alerta de reposición** registrada en `registros_notificacion`.
  - Que el planificador de n8n dispare los cálculos, o que se use el `schedule:run` de Laravel y se documente.

### M08 Chatbot (objetivo 6) · RF-28 a RF-31 · HU 32 a 34 (7 pts)
- 🟡 La vista es una maqueta.
- ❌ Falta:
  - Widget conectado a un **webhook de n8n**.
  - Flujo n8n con LLM y herramientas: recomendar tours activos por tiempo, condición y presupuesto; preguntas frecuentes; consulta de reserva en solo lectura.
  - Persistencia en `conversaciones_chatbot` y `mensajes_chatbot`.
  - Derivación a humano (`der_hum` y aviso).
  - Respuesta en el idioma del usuario.
  - Fallo controlado si la API no responde (RNF-20).

### M09 Reseñas y dashboards (objetivo 8) · RF-32 a RF-35 · HU 35 a 38 (6 pts)
- 🟡 El dashboard y el panel de analítica de `elmer-rama` son estáticos.
- ❌ Falta:
  - Reseñas solo para reservas `completada` (verificadas).
  - Gráficos reales de demanda por tour y mes con filtro de fechas, de nacionalidad (sin canceladas) y de **proyección de demanda** desde `proyecciones_demanda`, indicando la versión del modelo.
  - Generar esas proyecciones: por ejemplo, promedio móvil o regresión con un comando.

### M10 Notificaciones y auditoría (objetivos 6 y 7) · RF-38, RNF-05, RNF-06, RNF-18 · HU 39, 40 (5 pts)
- 🟡 `AuditObserver` está escrito y el panel de auditoría es HTML estático.
- ❌ Falta:
  - Registrar el observer en **todos** los modelos de negocio, o usar triggers de PostgreSQL. Hoy no se puede porque faltan los modelos.
  - Auditar las **lecturas** de datos sensibles: participantes y fichas (RNF-05).
  - Conectar el panel de auditoría con filtros.
  - `NotificacionJob` hacia webhooks de n8n: reserva confirmada, pago, comprobante y recordatorio de salida.
  - Registrar todo en `registros_notificacion`.

### Transversales (objetivo 9 y RNF)
- ❌ Falta:
  - Validación de formato de nombres, montos y fechas (RF-39, RNF-16) mediante FormRequests y CHECKs.
  - Responsive verificado a 360 px (RNF-13).
  - Rendimiento con índices y caché, más pruebas de carga (RNF-08 a RNF-10).
  - Respaldo diario de 30 días con `pg_dump` y cron (RNF-12).
  - Despliegue con Nginx, PHP-FPM, Redis y n8n en Docker.
  - Suite de pruebas Pest funcionando.

---

## 4. Plan de trabajo propuesto (orden y esfuerzo)

Las estimaciones están en **días-persona** de un desarrollador del equipo. Con 4 personas en paralelo, el calendario se acorta más o menos a la cuarta parte, salvo en la fase 0, que es secuencial.

### Fase 0: Base técnica (bloqueante) · ~4 d · ALTA prioridad
1. **Sanear las migraciones** (T2 y T3): borrar o fusionar `2026_10_03_154753` y `0001_users`, dejar un único `users` alineado con el diccionario v4 y crear las 7 vistas SQL y los CHECK del esquema v5 en una migración `DB::statement`. Comprobarlo con `migrate:fresh --seed`. **1,5 d**
2. **Arreglar la autenticación** (T1 y T5): integrar `Gabriel-Rama` y conciliar el nombre de la tabla (`users`), `User` (`clv` y `cor`), Fortify, `Role` y el seeder de roles y admin. Las tablas de Sanctum y 2FA se migran o se quitan. **1 d**
3. **Roles** (T4): middleware `rol:admin,operaciones,…`, redirección por rol tras iniciar sesión, proteger `/admin/*` y `/guias/*` y quitar las rutas públicas de las maquetas. **0,5 d**
4. **Modelos Eloquent** de todas las tablas de negocio, con sus relaciones, y **registrar `AuditObserver`** (T7 y T8). **1 d**
5. Instalar las dependencias de desarrollo (`composer install`) para que `php artisan test` corra, y fusionar `elmer-rama` (layout admin) (T6 y T9). **0,5 d**

### Fase 1: Flujo de venta de punta a punta · ~12 d · ALTA (objetivo 2, el núcleo del sistema)
1. **Motor de reservas real** (M05): controlador y wizard sobre las vistas ya diseñadas, cupo transaccional, participantes, equipo con talla, invitado y código único (arreglar `CodeGeneratorService` con verificación y UNIQUE). **5 d**
2. Ficha médica con **firma digital** y bloqueo del boleto en tours de alto riesgo. **1,5 d**
3. **Boletos QR** por participante, PDF y consulta por código. **1,5 d**
4. **Conectar IO** en los eventos de la reserva: `registrarLlegada()` (admitida, rechazada por cupo o cancelada) y `registrarCambio()` (reservado, completado). Agregar un test de integración. **0,5 d**
5. **Pagos** (M06): pasarela en modo prueba o simulador con webhook, `pagos`, comprobante PDF automático, anulación por estado e historial. **3,5 d**

### Fase 2: Operación y recursos · ~9 d · ALTA/MEDIA (objetivo 3)
1. CRUD de **personal** (guías y choferes con idiomas y licencia), **flota** con estados, **tipos de equipo**, **inventario por talla** (descuento y devolución automáticos) y equipo por tour (M03). **3,5 d**
2. **Programación de salidas** y **asignación manual** con validaciones de choque de horario, capacidad y vehículo activo, más cancelación o reprogramación (M04). **3 d**
3. **Check-in QR** desde el celular (cámara), estado `validado`, inicio y fin real del recorrido y modo sin conexión con IndexedDB y sincronización. **2,5 d**

### Fase 3: Completar IO II · ~7 d · ALTA para la materia (objetivos 3, 4 y 5)
1. **RF-40:** formulario de parámetros de colas con validaciones y un solo juego activo; quitar el uso de datos demo cuando ya haya datos reales. **0,5 d**
2. **RF-22:** asignación de guías con **método húngaro** (servicio `OptimizadorService` y tests). **2 d**
3. **RF-23:** asignación de vehículos (combustible y ocupación). **1,5 d**
4. **RF-24:** panel de propuestas para revisar, ajustar, confirmar o rechazar. **1 d**
5. **RF-25 a RF-27: EOQ** con costos, Q*, punto de reorden, aviso de historial insuficiente y alerta de reposición. **2 d**

### Fase 4: Catálogo completo y administración del contenido · ~5 d · MEDIA (objetivo 1)
1. Filtros combinados por categoría, dificultad y altitud sin recargar (RF-01). **0,5 d**
2. **Perfil de elevación** en el detalle; usar `trz_geo` en GeoJSON si existe. **1 d**
3. CRUD admin de categorías, tours, galería y editor de trazado y puntos de interés, con slug único automático (RF-37). **3,5 d**

### Fase 5: Notificaciones, auditoría y n8n · ~5 d · MEDIA (objetivos 6 y 7)
1. `NotificacionJob` hacia los webhooks de n8n (confirmación, pago, comprobante y recordatorio) y `registros_notificacion`. Exportar los flujos n8n al repositorio. **2 d**
2. Panel de **auditoría** real con filtros, registro de lecturas sensibles (RNF-05) y alertas de seguridad (RNF-04). **2 d**
3. Alertas de EOQ y cancelación de salidas por el mismo canal. **1 d**

### Fase 6: Chatbot con IA (n8n) · ~5 d · MEDIA (objetivo 6)
Widget, webhook, flujo LLM con herramientas (recomendación, preguntas frecuentes y consulta de reserva), persistencia, derivación a humano, ES/EN y manejo de fallos. **5 d**

### Fase 7: Dashboards y reseñas · ~4 d · BAJA/MEDIA (objetivo 8)
Reseñas verificadas (1 d), dashboards conectados a las vistas SQL con Chart.js (1,5 d) y proyección de demanda con un comando y su versión de modelo (1,5 d).

### Fase 8: No funcionales, pruebas y cierre · ~6 d · MEDIA
- Tests Pest de reservas, pagos, asignación, IO y roles.
- FormRequests con formato (RF-39).
- i18n ES/EN (RNF-14).
- Responsive a 360 px.
- HTTPS.
- Respaldo `pg_dump`.
- Despliegue.
- Ajustar el **informe** al estado final real.

### Resumen de esfuerzo

| Fase | Contenido | Prioridad | Esfuerzo (d-p) |
|---|---|---|---|
| 0 | Base técnica: migraciones, auth, roles, modelos y auditoría | **Bloqueante** | 4 |
| 1 | Reservas, QR, pagos y conexión con IO | Alta | 12 |
| 2 | Personal, recursos, salidas y check-in | Alta/Media | 9 |
| 3 | IO II: húngaro, vehículos, propuestas y EOQ | Alta (materia) | 7 |
| 4 | Catálogo: filtros, elevación y CRUD | Media | 5 |
| 5 | Notificaciones, auditoría y n8n | Media | 5 |
| 6 | Chatbot IA | Media | 5 |
| 7 | Dashboards y reseñas | Baja/Media | 4 |
| 8 | RNF, pruebas, despliegue e informe | Media | 6 |
| | **Total** | | **≈ 57 días-persona** (≈ 3 a 4 semanas con 4 desarrolladores) |

**Paralelización sugerida, después de la fase 0:**
- Dev A: fase 1 (reservas y pagos).
- Dev B: fases 2 y 4.
- Dev C: fase 3 (IO II, en la rama `investigacion-operativa-2`, que hay que crear).
- Dev D: fases 5, 6 y 7.
- Todos: fase 8.

La conexión entre IO y reservas (fase 1.4) depende de los modelos de la fase 0 y de que exista el controlador de reservas.

---

## 5. Decisiones que el equipo debe tomar
1. **Informe vs. realidad:** decidir si se implementa todo antes de la defensa o si se ajusta el informe, que hoy da todo por terminado.
R. Quiero que implementemos todo lo del informe.
2. **Nombre de la tabla de usuarios:** `users` (migración) o `usuarios` (rama de Gabriel). El diccionario v4 es la referencia.
R. que se quede como users
3. **Pasarela de pago:** proveedor real en modo sandbox o un simulador propio.
R. Hagamos una simulacion propia 
4. **Planificador de IO:** n8n (como dice el informe) o `schedule:run` de Laravel (lo que hay hoy).
R. Hazlo con n8n como dice el informe
5. **Commits preparados de WAAAAAA** (rediseño de reservas): confirmar con su autor y hacer el commit antes de empezar la fase 1.
Si haz el commit 

---

## 6. Estado final (20/10/2026)

Todas las fases del plan (0 a 8) están implementadas en la rama `WAAAAAA` del repositorio `Agencia_de_Turismo` (16 commits, sin push). `php artisan test` ejecuta 160 pruebas: 156 pasan y 4 se omiten (son de 2FA, que se desactivó porque `users` no tiene esas columnas).

| Hallazgo | Estado |
|---|---|
| T1–T3 Migraciones, auth y vistas SQL | ✅ `migrate:fresh --seed` funciona; 7 vistas y 58 CHECK |
| T4–T6 Roles y ramas de Gabriel y Elmer | ✅ Middleware `rol:`; ramas integradas |
| T7–T8 Auditoría y modelos | ✅ 27 modelos auditados |
| T9 Pruebas | ✅ Pest sobre PostgreSQL (`agencia_turismo_test`) |
| T10 Códigos únicos | ✅ Verificación en BD y alfabeto sin ambigüedades |
| T11 Rediseño de reservas | ✅ Confirmado (commit 96b94f0) |

Fases: 0 base técnica · 1 reservas, QR, pagos e IO · 2 personal, flota, salidas y check-in · 3 IO II (húngaro, vehículos, propuestas, EOQ, n8n) · 4 catálogo · 5 notificaciones y auditoría · 6 chatbot · 7 reseñas y dashboards · 8 RNF, despliegue y documentación.

**Cambio en la base de datos:** se agregó la tabla `propuestas_asignacion` (RF-24); el esquema pasa de **40 a 41 tablas**. Ver `03_Base_de_Datos/schema_postgresql_v6.sql` y `03_Base_de_Datos/diccionario_datos_v5.docx`.

El detalle por requisito está en `DOCUMENTACION/Tecnica/ESTADO-IMPLEMENTACION.md`, y los ajustes pendientes del informe en `AJUSTES-AL-INFORME.md`.
