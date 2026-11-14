# Plan de trabajo actualizado — Agencia de Turismo

**Actualización:** 13 de noviembre de 2026, Bolivia.

**Rama:** [WAAAAAA](https://github.com/Samuelhvf18/Agencia_de_Turismo/tree/WAAAAAA).

**Código contrastado:** [33abc90](https://github.com/Samuelhvf18/Agencia_de_Turismo/commit/33abc90a5bebdb1737707011d71c1458887dd7d8), que incorpora [cb12158](https://github.com/Samuelhvf18/Agencia_de_Turismo/commit/cb12158510ae43c3399f1db8fb40561426a78758).

Este es el **plan vigente de lo que falta**. Conserva los identificadores T01–T18 y agrega T19 para el rediseño solicitado después. La [revisión original del 12/11](docs/historico/PLAN-TRABAJO-2026-11-12.md) conserva el diagnóstico, las ocho fuentes documentales y las estimaciones iniciales. Esas estimaciones **no son esfuerzo restante ni horas trabajadas**.

Esta actualización revisa código, documentos del repositorio y evidencias de las ejecuciones anteriores. No vuelve a ejecutar la suite ni a consultar los PDF de Drive; tampoco acredita un despliegue de producción.

## 1. Qué ya existe y no hay que reconstruir

- Georreferenciación de tours: mapa, puntos con coordenadas y altitud, trazado GeoJSON, perfil de elevación y editor administrativo. No incluye seguimiento GPS en tiempo real; este no se incorpora como nuevo requisito.
- Motor de reservas, pagos **simulados**, PDF/QR, check-in, operaciones, reseñas y chatbot con respaldo local.
- Protección de saldo ante intentos simultáneos y callbacks excedentes; corrección de notificaciones por webhook repetido en el flujo probado.
- Reportes financieros exclusivos del administrador; manifiesto del chofer sin documentos ni condiciones médicas.
- Auditoría de escrituras mediante triggers transaccionales, incluidos cambios masivos y pivotes.
- Historial propio de reservas del turista, categorías y perfiles públicos de guía, y proyección de demanda con selector/desglose por tour.
- Correcciones de Markov, ventana común de 30 días para colas y fuente anual de EOQ.
- Panel administrativo unificado y **Planificación inteligente**, con explicaciones sencillas, detalles técnicos desplegables, simulación actual/escenario y revisión de asignaciones.
- Entorno QA documentado, workflow CI y ensayo local de restauración.

Que exista una función no cierra automáticamente su aceptación funcional, operativa o documental.

## 2. Evidencia disponible

| Evidencia | Resultado real | Límite |
|---|---|---|
| [Suite de la preparación de 33abc90](docs/evidencias/suite-33abc90.txt) | **170 aprobadas, 4 omitidas, 0 fallidas; 872 aserciones**, PHP 8.4/PostgreSQL 17 | Las omisiones son tres pruebas de 2FA desactivado y la de registro deshabilitado, porque el registro está habilitado |
| Build frontend | `npm run build` correcto, Vite 6.4.3, 58 módulos | No demuestra rendimiento del navegador ni despliegue |
| [Pagos concurrentes](docs/evidencias/pagos-concurrencia-cb12158.txt) | Dos procesos: un intento aceptado y otro rechazado; callbacks heredados contabilizan **Bs 100 de Bs 100** | No cubre aún carreras de reservas, asignaciones, cancelaciones ni una pasarela real |
| [Revisión de interfaz](docs/evidencias/interfaz-33abc90.txt) | 16 pantallas a 360 px sin desbordamiento del documento; revisión de planificación a 768 px y escritorio a 1440 px; sin errores JS en esos recorridos | Chromium, datos de QA y demostración; no es matriz completa de navegadores ni validación con un administrador real |
| Interacciones del nuevo panel | Comparación de escenarios, filtros, desplegables, menú móvil y apertura/cancelación/confirmación de asignación | La asignación se confirmó en base desechable, no en operación real |
| Restauración local | `pg_dump` custom y `pg_restore --exit-on-error` en otra base PostgreSQL 17; 6 tours, 223 auditorías, 18 migraciones | La muestra no tenía reservas; faltan volumen, archivos adjuntos y recuperación del entorno de entrega |
| CI remoto | [Workflow creado](.github/workflows/ci.yml) y subido | La consulta disponible devolvió estados vacíos y ninguna ejecución de PR; **no demuestra CI verde ni que el workflow de push no se ejecutó** |

Guías: [entorno y pruebas](docs/CI-Y-PRUEBAS.md), [rediseño y uso](docs/REDISENO-ADMIN.md), [matriz de implementación](docs/ESTADO-IMPLEMENTACION.md).

## 3. Estado general por tarea

**Implementado:** código disponible, con el alcance de evidencia indicado. **Parcial:** quedan subtareas concretas. **Pendiente externo:** necesita entorno, datos, credenciales o decisión. No se usa un porcentaje de terminación.

| ID | Estado actual | Lo que sigue |
|---|---|---|
| T01 · Entorno y CI | Implementado local; cierre parcial | Acreditar CI remoto y comparar esquema/migraciones con diccionario y SQL de referencia |
| T02 · Sobrepagos | Corregido y probado en simulación | Completar pago tardío/cancelación y, solo si se exige proveedor real, conciliación/reembolso |
| T03 · Notificaciones idempotentes | Parcial | Recuperación durable entre pago confirmado y notificación; probar fallos/reintentos y límites de SMTP |
| T04 · Permisos y datos sensibles | Correcciones principales implementadas | Revisar todos los accesos HTML/JSON/PDF/API y sus lecturas auditadas |
| T05 · Auditoría | Mecanismo implementado y fallo cerrado probado | Revisar atribución, exclusiones, tablas futuras, privilegios y volumen en entrega |
| T06 · Mis reservas | Implementado y probado localmente | Aceptación visual final; la vinculación automática de invitadas está excluida |
| T07 · URL públicas | Implementado; validación parcial | Completar casos de slugs y verificar que cada acceso público limite los campos |
| T08 · Markov | Reglas corregidas; validación parcial | Reconstrucción histórica, cohortes y matriz persistida con datos de meses completos |
| T09 · Colas | Fuentes y simulador corregidos | Validar supuestos de solicitudes/cupos, trazabilidad histórica de parámetros y ejemplo contractual |
| T10 · EOQ | Fuente y presentación corregidas | Acordar significado económico de demanda y tratamiento de desviación diaria |
| T11 · n8n/SMTP/IA | Pendiente de integración real | Configurar, activar y demostrar servicios, callbacks y ejecución programada |
| T12 · Alcance | Decisiones pendientes | Registrar aceptación de simulación, comprobantes/canal y 2FA/verificación de correo |
| T13 · Offline | Aislamiento mejorado; función incompleta | Shell seguro para reapertura sin red, expiración y pruebas de cambio de cuenta/sincronización |
| T14 · Proyección por tour | Selector y desglose implementados | Validar valores, meses sin ventas y presentación sin historial |
| T15 · Documentación | Repositorio actualizado parcialmente | Matriz completa de trazabilidad, UML, diccionario/informe y aprobación de alcance |
| T16 · Inglés | Traducción principal implementada | Cerrar textos secundarios, errores, placeholders, JS y recorrido público completo |
| T17 · Aceptación | Suite y revisión visual parciales ejecutadas | Carga/p95, mapas 4G/fallos, navegadores, concurrencia restante y volumen |
| T18 · Operación | Restauración pequeña local comprobada | TLS, workers, cron, restauración representativa, retención y disponibilidad real |
| T19 · Panel/IO comprensible | Rediseño implementado y verificado localmente | Validación de usabilidad con administrador, accesibilidad y otros navegadores |

## 4. Trabajo restante, con criterio de cierre

Las casillas marcadas describen trabajo acreditado dentro del alcance indicado; las demás son trabajo pendiente. **P0:** integridad/acceso antes de operación real. **P1:** entrega funcional y defensa. **P2:** operación y seguimiento. Responsables indicados por función, sin asignar personas ni inventar fechas.

### T01. Entorno reproducible, esquema y CI — P1 · integración/QA

- [x] Instalar dependencias bloqueadas, preparar PostgreSQL desechable y ejecutar suite/build.
- [x] Documentar reproducción y crear workflow de GitHub Actions.
- [ ] Obtener una ejecución CI exitosa vinculada al commit que se entregue y resolver cualquier diferencia con QA local.
- [ ] Contrastar tablas, vistas, restricciones y las nuevas migraciones con D7 y el SQL del esquema; registrar diferencias, no solo éxito de `migrate`.

**Cierre:** instalación reproducida desde checkout limpio, CI identificado y contraste de esquema documentado. No usar `migrate:fresh` sobre datos del usuario.

### T02. Pagos y cancelaciones — P0 · backend/pagos

- [x] Un único intento pendiente por reserva, también frente a efectivo; bloqueos reserva → comprobante → pago.
- [x] Revalidar estado/saldo antes de aprobar; no contabilizar excedentes. Probar intentos y callbacks simultáneos en QA.
- [ ] Añadir evidencia específica de callback tras cancelación/expiración y carrera pago–cancelación, no inferirla del nombre de una prueba de sobrepago.
- [ ] Resolver, según T12, conciliación y devolución si un proveedor real ya cobró un pago tardío/excedente. El estado `reembolsado` del prototipo no acredita devolución bancaria.

**Cierre:** total contabilizado coherente y resultado explícito para cada escenario; una devolución real necesita referencia de conciliación. Evidencia: `PagoService`, `ReservasTest`, script QA concurrente.

### T03. Recuperación de notificaciones y reintentos — P0 · backend/integración

- [x] Distinguir callback recién procesado de repetido; no repetir avisos en los casos de webhook probados.
- [x] Usar trabajo único de boletos y reutilizar su registro de notificación; omitir registros ya enviados.
- [ ] Resolver la ventana entre confirmar el pago en BD y registrar/despachar su aviso: caída del proceso o fallo de cola no debe dejar un pago sin notificación recuperable. Evaluar registro transaccional de evento pendiente y posterior envío.
- [ ] Probar reintento del trabajo parcial/total, fallo de worker/cola y envío aceptado por SMTP seguido de caída antes de marcar `enviado`.
- [ ] Registrar política de duplicados/conciliación con el canal externo. No prometer entrega “exactamente una vez” con SMTP sin una garantía adicional del proveedor.

**Cierre:** cada pago genera un evento durable, recuperable y trazable, con reintentos controlados y evidencia del número de envíos. Depende de T11 para comprobar correo real.

### T04. Revisión completa de autorización y privacidad — P0 · backend/seguridad

- [x] Bloquear finanzas/analítica para operaciones y excluir sus importes del dashboard del servidor.
- [x] Filtrar documentos/salud para chofer; probar JSON del manifiesto y auditar lecturas permitidas.
- [ ] Inventariar accesos a participantes, fichas y documentos en HTML, JSON, PDF, APIs/herramientas y almacenamiento; probar cada rol y acceso directo.
- [ ] Verificar que guía/chofer pierdan acceso al cambiar de asignación y que cada lectura sensible autorizada genere auditoría.
- [ ] Incluir dispositivos compartidos y cambio de usuario junto a T13.

**Cierre:** matriz ruta × rol × campos × prueba, sin filtrar datos solo en la vista. Evidencia actual: `CheckinController`, `RolesTest`, `NotificacionesAuditoriaTest`.

### T05. Alcance operativo de auditoría — P0 · datos/seguridad

- [x] Triggers transaccionales para tablas de negocio existentes, incluidos SQL masivo y pivotes; evitar doble auditoría por observer.
- [x] Probar auditoría de cada boleto cancelado y reversión de una escritura cuando falla la bitácora; excluir credenciales conocidas.
- [ ] Revisar casos de atribución HTTP, API, autenticación, tareas CLI y cola; documentar cuándo el actor es el sistema.
- [ ] Acordar las exclusiones técnicas de `CI-Y-PRUEBAS.md` con RNF-06 y verificar que tablas de negocio futuras instalen el trigger.
- [ ] Configurar privilegios que impidan desactivar triggers y política de acceso/retención; medir volumen y costo de auditoría en T17/T18.

**Cierre:** inventario de escrituras y exclusiones aprobado, actor atribuible y auditoría obligatoria en el entorno de entrega. La existencia de triggers no acredita por sí sola ese entorno.

### T06. Historial del turista — P1 · frontend/QA

- [x] Historial autenticado, paginado y limitado a `usr_id`; enlace y redirección del turista.
- [x] Probar separación entre titulares y acceso directo rechazado al estado de otra reserva.
- [ ] Recorrer desde el historial los estados, boletos y comprobante con datos representativos, en móvil y escritorio.

**Cierre:** recorrido de aceptación registrado. **No queda como desarrollo obligatorio** asociar invitadas por correo: se mantiene acceso por código + correo; una futura vinculación requeriría verificación explícita de titularidad.

### T07. Categorías y guías públicos — P1 · catálogo/QA

- [x] Rutas públicas por slug; backfill compatible de slugs de guía; perfil con campos públicos explícitos.
- [x] Prueba de respuesta pública, correo privado ausente y guía inactivo no disponible.
- [ ] Completar pruebas específicas de nombres repetidos/caracteres especiales y correspondencia de cada slug existente.
- [ ] Revisar contenido editorial, enlaces de navegación y ausencia de datos privados en cada página pública.

**Cierre:** URL única/legible y contenido seguro de tours, categorías y guías; evidencia de los casos anteriores. No crear nuevamente las rutas.

### T08. Markov y reconstrucción histórica — P1 · IO/datos

- [x] Excluir canceladas del cómputo de completadas; no convertir impagas en reservado; dar precedencia a fidelizado.
- [x] Considerar alta, pago aprobado y salida completada como actividad; ajustar redondeo para conservar suma de fila.
- [x] Probar cancelación/impago y resultados algebraicos de la matriz inicial.
- [ ] Contrastar reconstrucción de meses completos, dos tours completados más reserva futura, inactividad y conservación de estados absorbentes.
- [ ] Leer la matriz **persistida** tras recalibraciones con datos variados y comprobar tolerancia ≤0,0001 y coherencia de cohortes/proyección, no solo la matriz en memoria.

**Cierre:** resultados por mes comparados con registros reales y ejemplos reproducibles. La vista nueva explica el modelo, pero no sustituye esa validación.

### T09. Demanda y capacidad (colas) — P1 · IO/QA

- [x] Unificar panel/proceso diario: ventana inclusiva de 30 días, μ configurado y cero eventos como λ=0.
- [x] Separar situación actual del escenario; probar cambios solo de demanda y persistencia sin modificaciones.
- [x] Validar relación guías/capacidad y normalizar probabilidades para simulaciones grandes; prueba con capacidad 200.
- [ ] Reproducir explícitamente λ=5, μ=3,2, c=2, K=12 con la tolerancia contractual <0,001. Las pruebas existentes también usan otro ejemplo del informe.
- [ ] Garantizar que un resultado histórico pueda reproducirse tras cambios de configuración: período, fuente y versión de parámetros identificables.
- [ ] Acordar con docente/PO la unidad de K y equivalencia solicitud/pasajero/cupo para grupos; alinear informe y unidades de costo.

**Cierre:** mismo período y parámetros producen los mismos resultados; unidades y supuestos aprobados. No asignar recursos automáticamente desde una simulación.

### T10. Planificación de equipo (EOQ) — P1 · IO/datos/PO

- [x] Usar `vista_demanda_equipo_anual` del año calendario actual y el mismo período para historial mensual.
- [x] Mostrar insuficiencia de datos y explicar que las cantidades son referencias, no órdenes de compra.
- [ ] Aprobar si D representa usos, consumo o reposición por desgaste. Si se quiere recomendar compras, incorporar/validar una tasa de reemplazo para equipo reutilizable.
- [ ] Calcular desviación diaria desde datos diarios o aprobar/documentar la aproximación actual de medias mensuales.
- [ ] Probar límites de año, meses sin alquileres y correspondencia de D/Q/R con base y documento económico final.

**Cierre:** unidad económica y período inequívocos, ejemplos reproducibles y ningún alquiler interpretado automáticamente como una compra. Depende de T12/T15 para aprobación documental.

### T11. Integración real n8n, correo e IA — P1 · integración

- [x] Pruebas con HTTP/Mail simulados, token de callbacks y respaldo local del chatbot.
- [ ] Importar/activar flujos en una instancia autorizada; configurar SMTP, IA y tokens por variables del entorno, sin secretos en GitHub.
- [ ] Comprobar worker y planificador, callbacks autenticados, reintentos y frecuencia diaria/mensual.
- [ ] Recibir correo controlado con PDF/QR y comprobante; guardar ejecución y registro de notificación correlacionados.
- [ ] Probar recomendación, FAQ, consulta y derivación humana en ES/EN con IA real y con n8n caído.

**Cierre:** evidencia de servicios reales y del respaldo local. Si se acepta simulación, identificarla expresamente en entrega; un mock no acredita SMTP/IA/n8n activos. Depende de T03/T12.

### T12. Decisiones de alcance — P1, resolver primero · PO/docente

- [x] Documentar el comportamiento actual: pasarela simulada, comprobante interno pendiente desde el primer intento, correo Laravel con cola y consulta por código + correo.
- [ ] Aprobar simulación o exigir proveedor bancario real; en el segundo caso definir proveedor, acceso de prueba y conciliación.
- [ ] Aprobar momento/canal de comprobante o requerir creación tras pago y envío obligatorio por n8n.
- [ ] Resolver si 2FA y verificación efectiva de correo siguen siendo obligatorios. Actualmente no están acreditados; las pruebas omitidas no son cumplimiento.
- [ ] Registrar decisiones sobre EOQ, colas y alcance de auditoría y alinear los documentos que discrepan.

**Cierre:** decisión escrita por requisito, responsable y evidencia de aprobación. No hay aprobación del PO/docente registrada en esta sesión. Passkeys y GPS en vivo no se agregan como obligaciones.

### T13. Check-in offline completo — P1 · frontend/seguridad/QA

- [x] Retirar caché SW de HTML/JSON privados; separar IndexedDB por sesión/usuario; minimizar datos locales y añadir limpieza al logout/expiración.
- [ ] Implementar shell offline seguro para abrir de nuevo la aplicación desde cero sin red. La página ya abierta y la reapertura no son el mismo escenario.
- [ ] Probar caducidad por salida, cierre/cambio de cuenta, varias pestañas, cierre del navegador y contenido de almacenamiento en dispositivo compartido.
- [ ] Probar cola de escaneos, sincronización automática, duplicados, cancelación durante la desconexión y conflictos visibles.

**Cierre:** manifiesto válido solo para la sesión/salida autorizada y escaneo/sincronización recuperables sin exposición al siguiente usuario. No restaurar la caché global anterior para lograr reapertura.

### T14. Demanda proyectada por tour — P1 · analítica/QA

- [x] Selector y desglose por tour/mes con versión y fecha de generación, además del total.
- [ ] Comparar cada serie filtrada con `proyecciones_demanda`, incluyendo nombres de tour repetidos si los datos lo permiten.
- [ ] Acreditar tratamiento de meses sin ventas y tour sin historial; evitar que un cero proyectado parezca una predicción respaldada por datos suficientes.

**Cierre:** selección inequívoca y valores/modelo/fecha correctos; estados sin historial comprendidos por el usuario. No queda pendiente construir nuevamente el selector.

### T15. Trazabilidad y documentación final — P1 · documentación/QA/PO

- [x] Corregir numeración RF-17/RF-21 del repositorio y documentar decisiones técnicas/limitaciones.
- [x] Actualizar este plan con avances, pendientes y evidencia; conservar revisión histórica.
- [ ] Completar RF/RNF → HU → caso de uso → archivo → prueba → evidencia → estado/aprobación, sin sustituir aceptación por “hay código”.
- [ ] Alinear informe, diccionario y SQL con migraciones nuevas de auditoría y slugs, además del esquema que ya contiene `propuestas_asignacion`.
- [ ] Revisar todas las láminas UML, relaciones y reglas; D4 debe separar horas reales de proyecciones de sprints aún no cerrados.
- [ ] Actualizar documentos originales con las decisiones de T12 y afirmaciones de despliegue/pruebas verificables.

**Cierre:** documentos consistentes, enlaces de evidencia y excepciones aprobadas. Los PDF/Drive no se han modificado mediante esta actualización. UWE solo se agrega si existe consigna explícita.

### T16. Inglés completo en el proceso público — P1 · frontend/QA

- [x] Externalizar textos principales y añadir traducciones; probar conservación de inglés entre pasos del wizard.
- [ ] Revisar placeholders, validaciones personalizadas, JS, mensajes de pago, fechas, estado/cancelación, PDF/correo y chatbot durante el recorrido completo.
- [ ] Probar errores con `lang=en`, mantener idioma al navegar y registrar cada literal residual corregido.

**Cierre:** recorrido público de reserva a consulta en inglés, incluidos errores operativos, con evidencia. No confundir traducción principal con cobertura total.

### T17. Aceptación funcional, mapas y rendimiento — P1/P2 · QA

- [x] Suite automatizada de reservas, pagos, PDF/QR, operaciones, reseñas, IO, roles y chatbot; 170 pruebas aprobadas.
- [x] Carrera de pagos entre procesos y revisión de 16 pantallas administrativas a 360 px; planificación también en tablet/escritorio Chromium.
- [ ] Hacer un recorrido integrado de invitado y usuario: catálogo → reserva → equipo → firma → pago → correo recibido → QR/check-in → cierre → reseña, con servicios reales según T12.
- [ ] Ampliar carreras a cupos/equipo, asignaciones/aprobación de propuestas, un solo juego activo de parámetros y colisión de códigos; comprobar errores gestionados.
- [ ] Probar firma faltante/inválida/archivo no disponible, emisión/reemisión de boletos y validación en campo con casos negativos específicos.
- [ ] **Mapas:** validar coordenadas/trazados reales, creación/movimiento/guardado de puntos, elevación sin datos, GeoJSON inválido y fallo de teselas/CDN. Completar avisos o respaldo cuando falte cobertura.
- [ ] Medir RNF-08: p95 búsquedas <2 s y confirmación <3 s en carga normal definida; RNF-09: mapa completo <4 s con perfil 4G documentado.
- [ ] Definir carga promedio y probar 3× con degradación ≤50 %; medir volumen representativo de JSONB, conversaciones y auditoría.
- [ ] Completar recorrido público y de campo a 360 px y versiones objetivo de Chrome/Firefox/Safari/Edge, incluyendo mapas, chatbot y offline.

**Cierre:** informe de aceptación por requisito, entorno, datos, comandos/recorrido y mediciones. La carrera de pagos y las capturas del panel no acreditan todos los escenarios anteriores.

### T18. Operación, conservación y recuperación — P2 · integración/operación

- [x] Ensayar dump/restauración local en otra base PostgreSQL y comprobar una muestra pequeña.
- [ ] Verificar TLS/cookies, worker/cron, persistencia de volúmenes, secretos de despliegue y endpoints privados en el entorno de entrega.
- [ ] Restaurar una muestra representativa con reservas, participantes, pagos, auditoría y archivos de firmas/boletos; contrastar integridad y disponibilidad de adjuntos.
- [ ] Probar retención de respaldos de 30 días y conservación por estado de comprobantes/reservas, incluidos riesgos de borrado/cascada.
- [ ] Configurar monitoreo y obtener evidencia mensual de disponibilidad ≥99 %; hasta completar el período, marcar **por medir**.
- [ ] Demostrar incorporación de flujo n8n sin redesplegar Laravel, según RNF-18.

**Cierre:** procedimiento operativo ejecutado y evidencia del entorno real. El ensayo pequeño de QA no sustituye recuperación de la entrega.

### T19. Panel administrativo e IO comprensible — P1 · frontend/QA/administrador

- [x] Unificar diseño, navegación, tarjetas, tablas y formularios; entrada `/admin/io` por preguntas prácticas.
- [x] Rediseñar capacidad, asignaciones, equipo, clientes y ajustes; dejar fórmulas en detalles y señalar datos de demostración.
- [x] Comprobar controles, estados vacíos, confirmación de asignación y tamaños de pantalla descritos en la evidencia.
- [ ] Validar con un administrador real tareas como comparar capacidad, interpretar reposición y confirmar una asignación; registrar dudas y corregirlas.
- [ ] Completar revisión de teclado/lector de pantalla, contraste y otros navegadores, incluidos gráficos no disponibles.

**Cierre:** prueba de usabilidad y accesibilidad registrada. La implementación del rediseño está terminada; no queda reconstruir el módulo ni exigir conocimiento de EOQ/Markov para navegarlo.

## 5. Georreferenciación: alcance actual

| Función | Estado |
|---|---|
| Mapa Leaflet/OpenStreetMap en detalle del tour | Implementado; falta aceptación ante red lenta/fallos y coordenadas operativas |
| Puntos con latitud, longitud, altitud y tipo | Implementado; validación de rangos y persistencia cubierta por pruebas de catálogo |
| Editor: agregar/mover puntos y guardar recorrido | Implementado; completar recorrido de navegador y casos extremos |
| Importar GeoJSON o unir puntos | Implementado; ampliar validación de geometrías y errores de contenido |
| Perfil de elevación y distancia acumulada | Implementado; validar ausencia de altitudes y exactitud con trazados de referencia |
| Seguimiento GPS en vivo de guía/vehículo | Fuera del alcance actualmente solicitado; no pendiente obligatorio |

Evidencia: `CatalogoAdminController::guardarRuta`, `resources/views/admin/catalogo/form.blade.php`, `resources/views/tours/show.blade.php` y `CatalogoTest`. Lo pendiente pertenece a **T17**, no a construir otro módulo de mapas.

## 6. Orden recomendado de continuación

| Orden | Trabajo | Dependencia | Resultado para revisar |
|---|---|---|---|
| 1 | T12: fijar alcance; T01: acreditar CI/esquema | PO/docente y entorno | Acta de decisiones + enlace CI + diferencias de esquema |
| 2 | T03, cierre T02, T04/T05 | Código QA; decisiones de pagos/canal | Recuperación de avisos, pruebas de fallos/cancelación y matriz de accesos |
| 3 | T13 y T16 | T04 y datos de prueba | Reapertura offline segura y recorrido público completo en inglés |
| 4 | T08/T09/T10/T14 | Datos de meses completos y aprobación de unidades | Casos de IO contrastados y fuente/versiones trazables |
| 5 | T11, validación T06/T07/T19 y T17 | Credenciales/servicios autorizados | Recorrido real y mediciones; usabilidad del panel |
| 6 | T18 y T15 | Entorno de entrega y resultados previos | Restauración representativa, monitoreo y documentación coherente |

No se vuelve a asignar el total original de **81–131 h-persona** como trabajo pendiente. El esfuerzo residual debe estimarse tras T12, especialmente si se exige banco real, 2FA o cambios del modelo económico.

## 7. Criterio de entrega terminada

- [x] Correcciones de código y rediseño integrados en `WAAAAAA`, con suite y build locales disponibles.
- [x] Registro de trabajo restante con responsables por función, criterios de cierre y límites de evidencia.
- [ ] Cerrar P0 de notificaciones/escenarios de pago/accesos/auditoría con sus pruebas y decisiones.
- [ ] Ejecutar CI sobre la versión final y enlazar su resultado.
- [ ] Obtener aprobación del alcance y estados de cada RF/RNF, incluidas excepciones.
- [ ] Demostrar integraciones reales o declarar y aprobar explícitamente el alcance simulado.
- [ ] Completar offline, idiomas, aceptación funcional, mapas, navegadores y rendimiento comprometidos.
- [ ] Validar modelos IO con unidades y datos acordados; documentación/UML/diccionario alineados.
- [ ] Restaurar datos y archivos representativos del entorno de entrega; evidenciar operación y retención.
- [ ] Acreditar disponibilidad mensual cuando exista el período medido; no anticipar esa evidencia.

**Estado de entrega:** avance funcional con pruebas locales, aún con pendientes de aceptación e integración. Este archivo actualiza la planificación del repositorio; no modifica código de negocio, servicios externos ni documentos de Drive.
