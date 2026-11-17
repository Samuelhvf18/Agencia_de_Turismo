# Diagramas de paquetes por sprint

**Sistema Web de Control Logístico, Asignación de Recursos y Venta de Tours Georreferenciados para Agencias de Viajes y Turismo**

Este paquete contiene un diagrama de paquetes UML por cada sprint del proyecto. El paquete raíz se llama `Sprint N / Módulo` y sus paquetes hijos representan las historias de usuario (HU) comprometidas en el Sprint Backlog.

## Contenido

- `SP01...SP10_*.png`: 10 diagramas en imagen PNG de alta resolución, sin marca de agua.
- [`Diagramas_Paquetes_Sprints.pdf`](Diagramas_Paquetes_Sprints.pdf): versión consolidada, una página por sprint.
- `../StarUML/Diagramas_Paquetes_Sprints.mdj`: proyecto editable de StarUML con los 10 diagramas nativos `UMLPackageDiagram`.

## Cómo leer los diagramas

- **Paquete raíz (pestaña superior izquierda):** `Sprint N / Módulo`. Contiene todo el backlog del sprint.
- **Paquetes blancos:** historias de usuario (HU) asignadas al sprint. En el cuerpo se indica la prioridad y los puntos de historia.
- **Paquetes grises:** paquetes externos de los que depende el sprint (otros módulos del sistema o la base de datos PostgreSQL).
- **Flechas discontinuas:** dependencia UML. La flecha apunta del paquete que depende hacia el paquete del que depende. Por ejemplo, `HU-07 → HU-09` significa que HU-07 depende de HU-09.
- **Nota amarilla:** fechas del sprint, fases del Sprint Backlog, cantidad de HU, puntos y horas estimadas/reales.

## Resumen por sprint

| Sprint | Módulo | Fechas | HU | Puntos | Paquetes externos | Imagen |
|---|---|---|---:|---:|---|---|
| 1 | Cuentas y Seguridad | 20/08/2026 – 27/08/2026 | 4 (HU-07, HU-08, HU-09, HU-41) | 6 | Base de Datos PostgreSQL | [SP01_Cuentas_y_Seguridad.png](SP01_Cuentas_y_Seguridad.png) |
| 2 | Catálogo de Tours | 27/08/2026 – 08/09/2026 | 6 (HU-01, HU-02, HU-03, HU-04, HU-05, HU-06) | 14 | Base de Datos PostgreSQL | [SP02_Catalogo_de_Tours.png](SP02_Catalogo_de_Tours.png) |
| 3 | Personal y Recursos | 08/09/2026 – 17/09/2026 | 5 (HU-10, HU-11, HU-12, HU-13, HU-14) | 8 | Base de Datos PostgreSQL | [SP03_Personal_y_Recursos.png](SP03_Personal_y_Recursos.png) |
| 4 | Operaciones y Salidas | 17/09/2026 – 28/09/2026 | 5 (HU-15, HU-16, HU-17, HU-18, HU-26) | 10 | M03 · Personal y Recursos, Base de Datos PostgreSQL | [SP04_Operaciones_y_Salidas.png](SP04_Operaciones_y_Salidas.png) |
| 5 | Motor de Reservas | 28/09/2026 – 09/10/2026 | 7 (HU-19, HU-20, HU-21, HU-22, HU-23, HU-24, HU-25) | 14 | M04 · Operaciones y Salidas, Base de Datos PostgreSQL | [SP05_Motor_de_Reservas.png](SP05_Motor_de_Reservas.png) |
| 6 | Facturación y Pagos | 09/10/2026 – 19/10/2026 | 3 (HU-27, HU-28, HU-29) | 7 | M05 · Motor de Reservas, Base de Datos PostgreSQL | [SP06_Facturacion_y_Pagos.png](SP06_Facturacion_y_Pagos.png) |
| 7 | Investigación Operativa II | 19/10/2026 – 02/11/2026 | 13 (HU-30, HU-31, HU-42, HU-43, HU-44, HU-45, HU-46, HU-47, HU-48, HU-49, HU-50, HU-51, HU-52) | 36 | M04 · Operaciones y Salidas, M05 · Motor de Reservas, Base de Datos PostgreSQL | [SP07_Investigacion_Operativa_II.png](SP07_Investigacion_Operativa_II.png) |
| 8 | Chatbot Turístico | 02/11/2026 – 11/11/2026 | 3 (HU-32, HU-33, HU-34) | 7 | M02 · Catálogo de Tours, M05 · Motor de Reservas, Base de Datos PostgreSQL | [SP08_Chatbot_Turistico.png](SP08_Chatbot_Turistico.png) |
| 9 | Reseñas y Dashboards | 11/11/2026 – 18/11/2026 | 4 (HU-35, HU-36, HU-37, HU-38) | 6 | M05 · Motor de Reservas, Base de Datos PostgreSQL | [SP09_Resenas_y_Dashboards.png](SP09_Resenas_y_Dashboards.png) |
| 10 | Notificaciones y Auditoría | 18/11/2026 – 25/11/2026 | 2 (HU-39, HU-40) | 5 | M05 · Motor de Reservas, M06 · Facturación y Pagos, Base de Datos PostgreSQL | [SP10_Notificaciones_y_Auditoria.png](SP10_Notificaciones_y_Auditoria.png) |
| **Total** | **10 sprints** |  | **52** | **113** |  |  |

## Detalle del backlog por sprint

### Sprint 1 / Cuentas y Seguridad

**Fechas:** 20/08/2026 – 27/08/2026 · **Puntos:** 6 · **Horas:** 72 estimadas / 70 reales

- `HU-07` — Registro de cuenta de usuario · Muy Alta, 1 pt
- `HU-08` — Asignación de roles a los usuarios · Muy Alta, 1 pt
- `HU-09` — Inicio de sesión con control de roles · Muy Alta, 2 pts
- `HU-41` — Alertas de seguridad al administrador · Muy Alta, 2 pts

**Paquetes externos:** Base de Datos PostgreSQL.

**Imagen:** `SP01_Cuentas_y_Seguridad.png`

### Sprint 2 / Catálogo de Tours

**Fechas:** 27/08/2026 – 08/09/2026 · **Puntos:** 14 · **Horas:** 116 estimadas / 124 reales

- `HU-01` — Gestión de categorías de tour · Muy Alta, 2 pts
- `HU-02` — Gestión del catálogo de tours · Muy Alta, 3 pts
- `HU-03` — Gestión de galería de fotos del tour · Alta, 1 pt
- `HU-04` — Registro del trazado y puntos de interés · Alta, 3 pts
- `HU-05` — Exploración y filtrado del catálogo · Muy Alta, 2 pts
- `HU-06` — Visualización del detalle de un tour · Alta, 3 pts

**Paquetes externos:** Base de Datos PostgreSQL.

**Imagen:** `SP02_Catalogo_de_Tours.png`

### Sprint 3 / Personal y Recursos

**Fechas:** 08/09/2026 – 17/09/2026 · **Puntos:** 8 · **Horas:** 86 estimadas / 84 reales

- `HU-10` — Registro de personal (guías y choferes) · Alta, 2 pts
- `HU-11` — Gestión de tipos de equipo de alquiler · Media, 1 pt
- `HU-12` — Gestión del inventario de equipo · Alta, 2 pts
- `HU-13` — Configuración de equipo por tour · Media, 1 pt
- `HU-14` — Gestión de la flota de vehículos · Alta, 2 pts

**Paquetes externos:** Base de Datos PostgreSQL.

**Imagen:** `SP03_Personal_y_Recursos.png`

### Sprint 4 / Operaciones y Salidas

**Fechas:** 17/09/2026 – 28/09/2026 · **Puntos:** 10 · **Horas:** 100 estimadas / 104 reales

- `HU-15` — Programación de salidas de un tour · Muy Alta, 2 pts
- `HU-16` — Asignación de guía, chofer y vehículo a una salida · Muy Alta, 3 pts
- `HU-17` — Cancelación o reprogramación de una salida · Media, 2 pts
- `HU-18` — Registro de inicio y fin real del recorrido · Alta, 1 pt
- `HU-26` — Validación de tickets mediante código QR · Muy Alta, 2 pts

**Paquetes externos:** M03 · Personal y Recursos, Base de Datos PostgreSQL.

**Imagen:** `SP04_Operaciones_y_Salidas.png`

### Sprint 5 / Motor de Reservas

**Fechas:** 28/09/2026 – 09/10/2026 · **Puntos:** 14 · **Horas:** 132 estimadas / 140 reales

- `HU-19` — Selección de fecha y participantes para reservar · Muy Alta, 2 pts
- `HU-20` — Registro de datos de cada participante · Muy Alta, 2 pts
- `HU-21` — Alquiler de equipo adicional por participante · Alta, 2 pts
- `HU-22` — Firma digital de ficha médica · Muy Alta, 3 pts
- `HU-23` — Reserva sin registro previo · Alta, 1 pt
- `HU-24` — Verificación del estado de una reserva · Alta, 1 pt
- `HU-25` — Generación de ticket digital con código QR · Muy Alta, 3 pts

**Paquetes externos:** M04 · Operaciones y Salidas, Base de Datos PostgreSQL.

**Imagen:** `SP05_Motor_de_Reservas.png`

### Sprint 6 / Facturación y Pagos

**Fechas:** 09/10/2026 – 19/10/2026 · **Puntos:** 7 · **Horas:** 90 estimadas / 88 reales

- `HU-27` — Pago en línea de la reserva · Muy Alta, 4 pts
- `HU-28` — Emisión automática de comprobante (factura) · Alta, 2 pts
- `HU-29` — Registro y consulta de pagos · Media, 1 pt

**Paquetes externos:** M05 · Motor de Reservas, Base de Datos PostgreSQL.

**Imagen:** `SP06_Facturacion_y_Pagos.png`

### Sprint 7 / Investigación Operativa II

**Fechas:** 19/10/2026 – 02/11/2026 · **Puntos:** 36 · **Horas:** 192 estimadas / 210 reales

- `HU-30` — Propuesta de asignación óptima de guías · Media, 4 pts
- `HU-31` — Propuesta de asignación óptima de vehículos · Media, 4 pts
- `HU-42` — Configuración de parámetros de costo del inventario · Media, 1 pt
- `HU-43` — Cálculo de la cantidad económica de pedido (EOQ) · Media, 4 pts
- `HU-44` — Alerta automática de reposición de inventario · Media, 2 pts
- `HU-45` — Configuración de parámetros del modelo de colas · Media, 2 pts
- `HU-46` — Registro automático de eventos de llegada de reservas · Alta, 2 pts
- `HU-47` — Cálculo de métricas del modelo de colas M/M/C/K · Alta, 4 pts
- `HU-48` — Panel de análisis de colas de reservas · Media, 3 pts
- `HU-49` — Simulación de escenarios de guías y cupos · Media, 3 pts
- `HU-50` — Registro del estado del ciclo de vida del cliente · Media, 2 pts
- `HU-51` — Estimación de la matriz de transición de Markov · Media, 3 pts
- `HU-52` — Panel del ciclo de vida del cliente y cohortes · Baja, 2 pts

**Paquetes externos:** M04 · Operaciones y Salidas, M05 · Motor de Reservas, Base de Datos PostgreSQL.

**Imagen:** `SP07_Investigacion_Operativa_II.png`

### Sprint 8 / Chatbot Turístico

**Fechas:** 02/11/2026 – 11/11/2026 · **Puntos:** 7 · **Horas:** 86 estimadas / 90 reales

- `HU-32` — Recomendación de tours mediante chatbot · Media, 3 pts
- `HU-33` — Resolución de preguntas frecuentes mediante chatbot · Media, 2 pts
- `HU-34` — Consulta de reserva mediante chatbot · Baja, 2 pts

**Paquetes externos:** M02 · Catálogo de Tours, M05 · Motor de Reservas, Base de Datos PostgreSQL.

**Imagen:** `SP08_Chatbot_Turistico.png`

### Sprint 9 / Reseñas y Dashboards

**Fechas:** 11/11/2026 – 18/11/2026 · **Puntos:** 6 · **Horas:** 66 estimadas / 64 reales

- `HU-35` — Registro de reseña y calificación · Media, 1 pt
- `HU-36` — Dashboard de rutas más solicitadas por temporada · Baja, 2 pts
- `HU-37` — Dashboard de nacionalidad de los turistas · Baja, 1 pt
- `HU-38` — Dashboard de proyección de demanda · Baja, 2 pts

**Paquetes externos:** M05 · Motor de Reservas, Base de Datos PostgreSQL.

**Imagen:** `SP09_Resenas_y_Dashboards.png`

### Sprint 10 / Notificaciones y Auditoría

**Fechas:** 18/11/2026 – 25/11/2026 · **Puntos:** 5 · **Horas:** 66 estimadas / 70 reales

- `HU-39` — Envío automático de notificaciones · Alta, 2 pts
- `HU-40` — Registro de bitácora de auditoría · Muy Alta, 3 pts

**Paquetes externos:** M05 · Motor de Reservas, M06 · Facturación y Pagos, Base de Datos PostgreSQL.

**Imagen:** `SP10_Notificaciones_y_Auditoria.png`

## Nota sobre la fuente y la renderización

El archivo `.mdj` es la fuente editable en StarUML: contiene los 10 diagramas, los paquetes raíz, los 52 paquetes de HU, los 20 paquetes externos, las dependencias y las notas. Las imágenes PNG se generaron con un render propio a partir del mismo modelo y del backlog, porque la instalación de StarUML disponible no está registrada y sus exportaciones directas incluyen la marca `UNREGISTERED`. En las imágenes, los paquetes externos se ubican debajo del paquete raíz para facilitar la lectura; el proyecto de StarUML conserva la disposición original editable.

En la vista nativa de StarUML, la pestaña del paquete es un adorno de ancho fijo y el nombre se dibuja en el cuerpo; StarUML no ofrece una opción para moverlo dentro de la pestaña. Por eso el `.mdj` conserva esa representación nativa, mientras que las PNG se renderizaron con la notación clásica solicitada: el nombre dentro de la pestaña superior izquierda.

_Documento generado el 28/10/2026._