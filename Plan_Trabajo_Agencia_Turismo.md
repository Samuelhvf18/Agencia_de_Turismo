# Plan de trabajo: contraste de documentación y código de Agencia de Turismo

> Seguimiento de ejecución: [avance, pruebas y pendientes residuales](docs/AVANCE-PLAN-TRABAJO.md). Esta revisión histórica se conserva; no todos sus criterios de aceptación están cerrados.


Fecha de revisión: 12 de noviembre de 2026 (Bolivia).
Repositorio: [Samuelhvf18/Agencia_de_Turismo](https://github.com/Samuelhvf18/Agencia_de_Turismo/tree/WAAAAAA).
Rama real: **WAAAAAA**. Commit revisado: **2db08026fc752a125177738b58c1d346f6ea12ea**, del 20/10/2026.

## 1. Resultado y alcance

Los diez módulos tienen estructura e implementación en el repositorio. El trabajo pendiente se concentra en corregir reglas de negocio, completar funciones puntuales, alinear documentos y demostrar los requisitos mediante pruebas. No corresponde reconstruir todos los módulos.

Se consultaron en Google Drive los ocho PDF indicados en la captura. Se contrastaron sus requisitos, historias, planificación, modelo de datos y decisiones con rutas, controladores, servicios, modelos, migraciones, configuración, trabajos en cola y algunas pruebas del commit indicado.

Esta es una revisión estática orientada a elaborar el plan. **No se ejecutó la aplicación ni la suite de pruebas**: no hay ejecutables PHP ni Composer disponibles en esta sesión. La presencia de una prueba o de una función no demuestra por sí sola el cumplimiento. Tampoco se inspeccionó un despliegue real ni sus credenciales. Las integraciones figuran como pendientes de verificación, no como ausentes.

El PDF UML tiene gran parte de su contenido como imágenes: su extracción textual identifica los diagramas, pero no todos sus elementos. Se inspeccionó visualmente el diagrama CU-M07b de la copia adjunta (mismo nombre y tamaño que el PDF de Drive). Falta una validación visual completa de todas las láminas; no se declara que todos los diagramas coincidan con el código.

## 2. Fuentes documentales consultadas

| Ref. | Documento actual de Drive | Uso en el contraste |
|---|---|---|
| D1 | [Lista_de_Requerimientos_RF_RNF.pdf](https://drive.google.com/file/d/1ZGELTTU1WQlpW2QCHkGBst4CijeuuBX6/view) | 45 RF, 20 RNF y criterios de aceptación. Referencia principal del alcance. |
| D2 | [Historia_de_Usuario.pdf](https://drive.google.com/file/d/1dBiTIwxngDrZpUIYL86DITjwwWL8Aia6/view) | 52 historias; criterios de valor y alcance por usuario. |
| D3 | [Product_Backlog.pdf](https://drive.google.com/file/d/1SRg19kBhdX7iVLwXv1oYFSzf_zZcrOgw/view) | Prioridades, responsables originales, puntos y sprints. |
| D4 | [Sprint_Backlog.pdf](https://drive.google.com/file/d/1bPNUZ9qmEPpDCMfWhNVSFqi85zDOJtIj/view) | Cronograma, horas estimadas y horas presentadas como reales. |
| D5 | [Informe_Final.pdf](https://drive.google.com/file/d/120HBYWufEd4TdUVzcdYoXo-eAtSeXQQA/view) | Arquitectura, alcance, pruebas y despliegue declarado. |
| D6 | [Informe_IO_Segundo_Avance.pdf](https://drive.google.com/file/d/1dn3Y4lqhEjraIVwLNHIIt4SSCfnXAWpt/view) | Supuestos y resultados esperados de EOQ, colas y Markov. |
| D7 | [diccionario_datos_v5.pdf](https://drive.google.com/file/d/19bXjIqSSz-TpkwpfYnda85yGYQihpl51/view) | Campos, restricciones, datos sensibles y vistas. |
| D8 | [Diagramas_UML_Sistema_Tours.pdf](https://drive.google.com/file/d/1XM7omEfx5eN4zQsdiS-elJOSWAYivG0C/view) | Inventario de diagramas y muestra visual de IO II. |

El diccionario v5 de Drive **sí incorpora propuestas_asignacion**, sección 8.9, y señala su correspondencia con el esquema v6. No debe abrirse una tarea para agregar otra vez esa tabla.

## 3. Cómo interpretar el estado

- **Brecha confirmada:** el código leído difiere del requisito o deja un escenario incorrecto.
- **Decisión pendiente:** hay documentos contradictorios o una simplificación explícita; definir el alcance antes de programar.
- **Por verificar:** existe una implementación, pero faltan ejecución y evidencia de aceptación.
- P0: corregir antes de aceptar reservas/pagos o dar por cerrada seguridad.
- P1: completar para la entrega y defensa.
- P2: evidencia operativa, mantenimiento y cierre documental.

## 4. Matriz de contraste funcional

Esta matriz identifica evidencia y trabajo restante. “Por verificar” no significa que la función falte.

| RF / historias relacionadas | Evidencia localizada | Estado y tarea |
|---|---|---|
| RF-01/02; HU-05/06 | CatalogoController, vistas welcome y tours/show; CatalogoTest | Por verificar filtros combinados sin recarga, mapa, elevación y fallos de teselas. T17. |
| RF-03/04; HU-07/09 | Fortify, User, middleware de roles | Falta historial accesible de reservas del turista descrito en RF-03. T06. Login y registro: verificar. |
| RF-05/06/07; HU-19/20/21 | Wizard, FormRequests, ReservaService con bloqueo | Implementación localizada; probar concurrencia real y correspondencia tipo/talla/equipo al confirmar. T01/T17. |
| RF-08; HU-22 | FirmaDigital, FichaMedicaRequest, ReservaService, BoletoService | Flujo tiene validaciones; verificar que emisión y check-in exijan firma válida, no solo existencia de una ficha. T17. |
| RF-09/10/11; HU-23/25 | Reservas invitadas, generador, BoletoService | Por verificar códigos irrepetibles y PDF/QR/correo real. T01/T11/T17. |
| RF-12; HU-24 | ConsultaReservaController pide código + correo | Diferencia deliberada frente a consulta solo por código del documento. Registrar decisión y ajustar documentación, conservando protección del acceso. T15. |
| RF-13/14; HU-27/28/29 | PagoService, PasarelaSimulada, EnviarBoletosReserva | Brechas de sobrepago, idempotencia y momento/canal de emisión. T02/T03/T11/T12. |
| RF-15/16/19/20/21; HU-10 a 17 | Controladores de personal/flota/inventario, AsignacionService y SalidaService | Base implementada. Validar desactivación con salidas futuras, choques y capacidad bajo concurrencia. T17. |
| RF-17/18; HU-18/26 | CheckinController, SalidaService, IndexedDB y service worker | Revisar privacidad por rol y aislamiento de caché entre usuarios. T04/T13; verificar fechas y sincronización. |
| RF-22/23/24; HU-30/31 | OptimizadorService, MetodoHungaro, propuestas revisables | Implementación localizada, no ausente. Probar propuesta/aplicación concurrente y restricciones semanales. T17. |
| RF-25/26/27; HU-42/43/44 | EoqService, panel y alertas | Fórmula presente; D se calcula con consulta mensual y no mediante el método de la vista anual. Alinear fuente y significado económico. T10. |
| RF-28/29/30/31; HU-32/33/34 | ChatbotService, asistente local y herramientas/n8n | Por verificar IA real, cuatro categorías de FAQ, solo tours activos, consulta y derivación humana. T11/T17. |
| RF-32; HU-35 | ResenaController, pruebas AnaliticaResenasTest | Por verificar reserva completada, propiedad y duplicados. T17. |
| RF-33/34; HU-36/37 | DashboardController consulta vistas y fechas | Por verificar agregados y exclusión de canceladas. Permisos financieros: T04. |
| RF-35; HU-38 | ProyeccionDemandaService y panel de analítica | El panel suma la proyección de todos los tours; falta desglose por tour descrito por HU-38. T14. |
| RF-36 | Generador y restricciones de unicidad | Por verificar colisiones y concurrencia; una restricción debe impedir duplicados sin dejar errores sin gestionar. T17. |
| RF-37 | SlugService y ruta pública de tours | Faltan páginas/rutas públicas de categorías y perfiles de guías. T07. |
| RF-38; HU-39 | NotificacionService, jobs y recordatorios | Hay implementación; probar envío real y corregir repetición de notificaciones. T03/T11. |
| RF-39 | FormRequests, validaciones y CHECK | Por verificar cobertura campo a campo. T17. |
| RF-40; HU-45 | IoGestionController y cola_parametros_tour | Base implementada; comprobar propuesta de μ/K y un solo juego activo bajo concurrencia. T17. |
| RF-41/42/43; HU-46 a 49 | Eventos, ColasService, controlador y simulador | Fórmulas presentes; inconsistencias entre fuentes/ventanas de λ y μ e histórico/panel. T09. |
| RF-44/45; HU-50 a 52 | MarkovService, panel, cohortes y recalibración | Brechas al derivar estados reales de clientes; verificar precisión al persistir matriz. T08. |

Atención: docs/ESTADO-IMPLEMENTACION.md del repositorio intercambia referencias de RF-17 y RF-21 en su descripción de check-in/cancelación. Usar D1 como numeración canónica y reparar esa matriz en T15.

## 5. Brechas y tareas ejecutables

Las estimaciones son **horas-persona nuevas**, orientativas; no son horas ya trabajadas ni compromisos del equipo. Incluyen implementación y prueba específica de cada corrección.

### T01. Preparar entorno reproducible y línea base — P1 — 4 a 8 h

- [ ] Crear una copia de trabajo del commit revisado, instalar versiones compatibles con composer.lock y package-lock.json y preparar PostgreSQL de pruebas.
- [ ] Ejecutar migraciones en una base desechable y verificar restricciones/vistas contra D7 y el SQL v6.
- [ ] Ejecutar la suite y build frontend; guardar comando, versiones, commit, resultado y fallos reales.
- [ ] Configurar CI para repetirlo; el árbol consultado no contiene workflows de GitHub Actions.

Aceptación: otra persona puede reproducir la instalación; informe real de suite y build. No ejecutar migrate:fresh sobre una base con datos del usuario.
Dependencias: ninguna. Responsable sugerido: integración/QA.

### T02. Impedir sobrepagos y ordenar bloqueos de pagos — P0 — 6 a 10 h

Brecha confirmada. iniciar() compara contra pagos completados, permite varios intentos pendientes por el saldo completo y procesarNotificacion() no vuelve a limitar el total aprobado contra el saldo. Una reserva de Bs 100 puede tener dos intentos pendientes de Bs 100, ambos aprobados.

- [ ] Definir si se permite un solo intento activo o varios con monto reservado.
- [ ] Revalidar saldo/estado al recibir cada webhook, con bloqueo coherente de reserva, comprobante y pago.
- [ ] Contemplar pago tardío, cancelación y una política explícita para excedentes/reembolsos.

Aceptación: dos intentos del saldo completo, procesados secuencialmente y en paralelo, nunca contabilizan más de lo debido sin un registro explícito de excedente y resolución.
RF-13; HU-27/29. Evidencia: [app/Services/Pagos/PagoService.php:75](https://github.com/Samuelhvf18/Agencia_de_Turismo/blob/2db08026fc752a125177738b58c1d346f6ea12ea/app/Services/Pagos/PagoService.php#L75).
Dependencia: T01. Responsable: backend/pagos.

### T03. Idempotencia completa de los webhooks — P0 — 3 a 5 h

Brecha confirmada. Si el pago ya fue procesado, la transacción retorna ese pago; después, la rama de notificación vuelve a ejecutarse para un pago completado. La protección de estado no cubre todos los efectos externos.

- [ ] Distinguir “pago procesado ahora” de “evento repetido”.
- [ ] Despachar una sola notificación por evento y hacer que los reintentos del trabajo no dupliquen envíos.
- [ ] Probar webhook repetido tanto para abono parcial como para pago total.

Aceptación: repetir el mismo evento mantiene importes, estados, boletos y cantidad de notificaciones.
RF-13/38. Evidencia: [app/Services/Pagos/PagoService.php:146](https://github.com/Samuelhvf18/Agencia_de_Turismo/blob/2db08026fc752a125177738b58c1d346f6ea12ea/app/Services/Pagos/PagoService.php#L146).
Dependencia: T02. Responsable: backend/pagos.

### T04. Ajustar permisos financieros y datos sensibles — P0 — 5 a 8 h

Brechas confirmadas frente a RNF-03/05. La ruta de finanzas admite operaciones; FinanzasController::index no añade una restricción de administrador. Dashboard también entrega ingresos al rol operaciones. El manifiesto accesible al chofer asignado incluye documento y alerta médica, mientras RNF-05 restringe esos datos a operaciones, guía asignado y administrador.

- [ ] Separar cobro operativo autorizado de reportes financieros exclusivos del administrador.
- [ ] Filtrar campos sensibles en el servidor según rol; no basta ocultarlos en una vista.
- [ ] Auditar lecturas de participantes y fichas en todos los puntos de acceso.
- [ ] Añadir casos negativos para cada rol y cada ruta JSON/PDF, incluyendo acceso directo.

Aceptación: operaciones no obtiene reportes financieros exclusivos; chofer no recibe salud/documentos restringidos; guía solo accede a su salida y cada lectura sensible permitida queda registrada.
Evidencia: [routes/web.php:138](https://github.com/Samuelhvf18/Agencia_de_Turismo/blob/2db08026fc752a125177738b58c1d346f6ea12ea/routes/web.php#L138) y [app/Http/Controllers/CheckinController.php:126](https://github.com/Samuelhvf18/Agencia_de_Turismo/blob/2db08026fc752a125177738b58c1d346f6ea12ea/app/Http/Controllers/CheckinController.php#L126).
Dependencia: T01. Responsable: backend/seguridad.

### T05. Cerrar cobertura de auditoría — P0 — 6 a 10 h

Brecha confirmada. Los observers solo cubren modelos registrados y eventos Eloquent individuales. Hay cambios masivos con where()->update() —por ejemplo cancelación de boletos— y modelos excluidos explícitamente en AppServiceProvider. Además AuditObserver registra un error y deja continuar el cambio si falla la bitácora. Esto difiere del “sin excepción” de RNF-06.

- [ ] Inventariar escrituras ORM, masivas, pivotes, SQL y tareas programadas.
- [ ] Elegir un mecanismo que cubra operaciones masivas y evite auditoría duplicada.
- [ ] Resolver explícitamente qué hacer si falla la auditoría y cómo atribuir cambios del planificador.
- [ ] Mantener credenciales fuera de valores anteriores/nuevos.

Aceptación: cancelar una reserva deja auditoría de cada boleto alterado; las rutas masivas y programadas tienen cobertura y antes/después correctos. Toda excepción de alcance queda acordada y documentada.
Evidencia: [app/Providers/AppServiceProvider.php:21](https://github.com/Samuelhvf18/Agencia_de_Turismo/blob/2db08026fc752a125177738b58c1d346f6ea12ea/app/Providers/AppServiceProvider.php#L21) y [app/Services/Reservas/ReservaService.php:193](https://github.com/Samuelhvf18/Agencia_de_Turismo/blob/2db08026fc752a125177738b58c1d346f6ea12ea/app/Services/Reservas/ReservaService.php#L193).
Dependencia: T01. Responsable: backend/datos.

### T06. Historial de reservas del turista — P1 — 4 a 6 h

Brecha confirmada respecto de la finalidad de RF-03. User tiene relación reservas(), pero las rutas revisadas no exponen “Mis reservas”; rutaInicio() del turista regresa al catálogo.

- [ ] Crear listado autenticado, paginado y limitado al usr_id actual.
- [ ] Añadir enlace tras login y navegación a estado, boletos y comprobante autorizados.
- [ ] Definir por separado la vinculación de reservas invitadas; no asociarlas automáticamente por correo sin verificación.

Aceptación: turista A ve su historial y no puede consultar el de B alterando URL o parámetros.
Evidencia: [app/Models/User.php:142](https://github.com/Samuelhvf18/Agencia_de_Turismo/blob/2db08026fc752a125177738b58c1d346f6ea12ea/app/Models/User.php#L142). Dependencia: T01/T04. Responsable: frontend/backend.

### T07. Completar rutas amigables públicas — P1 — 4 a 6 h

RF-37 incluye tours, categorías y perfiles públicos de guía. La ruta pública por slug está para tours; los modelos leídos de categoría y guía no incorporan una ruta pública equivalente.

- [ ] Agregar slugs únicos y migración compatible con datos existentes donde hagan falta.
- [ ] Crear catálogo por categoría y perfil público de guía con campos expresamente públicos.
- [ ] Probar nombres repetidos, caracteres especiales y recursos inactivos.

Aceptación: categorías y guías tienen URL legible y única; no se exponen documentos, correo privado ni información médica.
Evidencia: routes/web.php, SlugService.php, CategoriaTour.php y PerfilGuia.php.
Dependencia: T01/T04. Responsable: catálogo.

### T08. Corregir estados derivados y persistencia de Markov — P1 — 6 a 10 h

Brechas confirmadas en sincronizarDesdeReservas():
1. Cuenta una reserva cancelada como completada si terminó la salida.
2. Cuenta una reserva pendiente impaga como estado reservado; D6 define ese estado a partir de una reserva pagada.
3. Evalúa existencia de pendientes antes de reconocer dos tours completados, lo que puede demorar el reconocimiento de fidelización en una reconstrucción.

- [ ] Derivar estados de hechos válidos de la reserva, no solo del estado general de la salida.
- [ ] Establecer precedencia coherente y conservar absorbentes según el modelo acordado.
- [ ] Revisar el criterio de última actividad: actualmente usa creación de reserva, no necesariamente pago o cierre.
- [ ] Verificar que redondear probabilidades a cuatro decimales no supere tolerancia 0,0001; ajustar y validar antes de guardar.
- [ ] Probar meses completos y que cohortes/proyección coincidan con los registros reales.

Aceptación: una cancelación nunca añade un tour completado; una reserva sin pago no convierte al prospecto en reservado; matriz persistida tiene filas estocásticas y absorbentes correctos; resultados N/B/proyección contrastados.
RF-44/45; HU-50 a 52. Evidencia: [app/Services/Io/MarkovService.php:285](https://github.com/Samuelhvf18/Agencia_de_Turismo/blob/2db08026fc752a125177738b58c1d346f6ea12ea/app/Services/Io/MarkovService.php#L285).
Dependencia: T01/T02. Responsable: IO II/backend.

### T09. Unificar métricas de colas — P1 — 5 a 8 h

Brechas confirmadas frente a D6: el panel estima λ con los últimos 30 días, mientras generarMetricasDiarias() toma solo el día de cálculo. El panel usa μ configurado y el cálculo diario usa μ observado si lo encuentra. Así, panel e histórico pueden dar respuestas distintas sin explicarlo.

- [ ] Definir y reutilizar la misma ventana y fuente de parámetros o rotular explícitamente métricas distintas.
- [ ] Guardar/mostrar período observado, origen y unidades.
- [ ] Distinguir cero llegadas reales de falta de historial: lambdaObservado() devuelve null con cero registros y puede sustituirlo por demanda configurada.
- [ ] Validar resultados del caso λ=5, μ=3,2, c=2, K=12 y tolerancia menor a 0,001 de RF-42.
- [ ] Documentar con el docente la equivalencia reserva/participante/cupo: el modelo cuenta solicitudes y K se expresa como cupos, y una solicitud puede contener varios participantes. Es una cuestión de supuestos del propio diseño, no solo un fallo de programación.

Aceptación: para la misma fecha, ventana y parámetros, panel, proceso diario e informe reproducen los mismos resultados; simulaciones no cambian parámetros persistentes.
Evidencia: [app/Services/Io/ColasService.php:203](https://github.com/Samuelhvf18/Agencia_de_Turismo/blob/2db08026fc752a125177738b58c1d346f6ea12ea/app/Services/Io/ColasService.php#L203) y [app/Http/Controllers/Admin/AnalisisColasController.php:78](https://github.com/Samuelhvf18/Agencia_de_Turismo/blob/2db08026fc752a125177738b58c1d346f6ea12ea/app/Http/Controllers/Admin/AnalisisColasController.php#L78).
Dependencia: T01. Responsable: IO II.

### T10. Alinear EOQ con la fuente y el significado de D — P1 — 4 a 6 h

RF-26 exige D desde vista_demanda_equipo_anual. analizar() usa la suma de demandaMensual(); existe demandaAnualVista(), pero esa ruta no se usa en el análisis. D6 además mezcla reposición por desgaste con conteo de alquileres. Alquilar una bicicleta muchas veces no equivale por sí mismo a necesitar tantas bicicletas nuevas.

- [ ] Acordar si D mide uso, reemplazos o consumo; documentar esa decisión en D1/D6/D7.
- [ ] Unificar fuente y período del cálculo o actualizar el criterio acordado.
- [ ] Mantener aviso de historial insuficiente; no mostrar una compra recomendada como resultado real cuando no hay datos.
- [ ] Revisar estimación de desviación diaria, que hoy se aproxima desde promedios mensuales.

Aceptación: D tiene una unidad económica explícita, verificable con la base y consistente con Q/R. Los casos del informe se reproducen como ejemplos, separados de datos operativos.
Evidencia: [app/Services/Io/EoqService.php:63](https://github.com/Samuelhvf18/Agencia_de_Turismo/blob/2db08026fc752a125177738b58c1d346f6ea12ea/app/Services/Io/EoqService.php#L63). Dependencia: T01. Responsable: IO II/datos.

### T11. Probar integración real de n8n, correo e IA — P1 — 5 a 8 h

Estado por verificar. El repositorio entrega flujos y configuración; no demuestra que estén importados/activos en una instancia. docs/ESTADO-IMPLEMENTACION.md los enumera como configuración pendiente.

- [ ] Importar y activar notificaciones, chatbot y planificador; configurar token y credenciales en el entorno, sin escribir secretos en archivos del proyecto.
- [ ] Verificar callbacks autenticados, SMTP, ejecución diaria/mensual, worker y reintentos.
- [ ] Probar recomendación, FAQ, consulta y derivación en ES/EN con IA real y con n8n caído.
- [ ] Guardar evidencias de ejecución y de registros_notificacion.
- [ ] En RF-14, resolver la diferencia de canal: EnviarBoletosReserva manda el correo directamente por Laravel y no por n8n.

Aceptación: un pago controlado genera comprobante, QR y correo recibido; la tarea programada corre sin intervención; el respaldo local del chatbot funciona al fallar la IA.
Evidencia: [app/Jobs/EnviarBoletosReserva.php:42](https://github.com/Samuelhvf18/Agencia_de_Turismo/blob/2db08026fc752a125177738b58c1d346f6ea12ea/app/Jobs/EnviarBoletosReserva.php#L42) y docs/n8n/README.md.
Dependencia: T02/T03. Responsable: integración.

### T12. Resolver alcance de pagos, comprobantes y 2FA — P1 — 2 a 4 h para decisión/documentación

Decisiones pendientes, con posible desarrollo adicional:
- RF-13/HU-27 habla de pago en línea; el repositorio declara pasarela simulada. El prototipo no prueba una integración bancaria real.
- RF-14 sitúa creación del comprobante después del pago; el código lo crea pendiente al iniciar el primer intento.
- D5 incluye 2FA y verificación de correo en el alcance; el estado del repositorio indica que 2FA se excluyó y User no implementa el contrato MustVerifyEmail. El middleware verified por sí solo no demuestra verificación efectiva.

- [ ] Registrar qué acepta el docente/PO como entrega: simulación o proveedor real, comprobante pendiente o creación tras pago, 2FA y verificación de correo.
- [ ] Ajustar D5 y criterios o programar lo que siga siendo obligatorio.
- [ ] Si se aprueba proveedor real, estimarlo en una tarea aparte; no está incluido en este esfuerzo.
- [ ] No añadir passkeys como pendiente: no se identificó como obligación de estos ocho documentos.

Aceptación: decisiones trazables y un solo alcance coherente. Un comprobante de simulación no se presenta como facturación fiscal certificada.
Dependencia: ninguna. Responsable: PO/docente y backend.

### T13. Aislar y limpiar datos del check-in offline — P1 — 4 a 7 h

Riesgo identificado en implementación. El service worker cachea GET de /guias/ en una caché global y no contiene separación por usuario ni limpieza al cerrar sesión. Debe comprobarse el comportamiento real junto con IndexedDB.

- [ ] Particionar o purgar caché y manifiestos al logout/cambio de cuenta y al terminar su vigencia.
- [ ] Minimizar documentos y datos médicos en almacenamiento local, conforme a T04.
- [ ] Probar escaneo sin red, reapertura, sincronización automática, duplicados y reserva cancelada durante la desconexión.
- [ ] Confirmar que otro usuario no puede leer el manifiesto previo del dispositivo.

Aceptación: datos offline restringidos al usuario autorizado y validaciones sincronizadas una sola vez, con resolución visible de conflictos.
RNF-05/15. Evidencia: [public/sw-checkin.js:7](https://github.com/Samuelhvf18/Agencia_de_Turismo/blob/2db08026fc752a125177738b58c1d346f6ea12ea/public/sw-checkin.js#L7).
Dependencia: T04. Responsable: frontend/check-in.

### T14. Mostrar proyección de demanda por tour — P1 — 3 a 5 h

El servicio guarda resultados por tur_id, pero DashboardController suma rsv_pry por mes entre todos los tours y muestra una versión agregada del modelo. HU-38 pide demanda esperada por tour.

- [ ] Agregar selector/desglose por tour.
- [ ] Mostrar versión y fecha de generación correspondientes a la serie.
- [ ] Aclarar qué se muestra cuando no hay historial; verificar el tratamiento de meses sin ventas en el promedio móvil.

Aceptación: el valor de cada tour/mes coincide con proyecciones_demanda sin ocultarse dentro de un total.
Evidencia: [app/Http/Controllers/Admin/DashboardController.php:91](https://github.com/Samuelhvf18/Agencia_de_Turismo/blob/2db08026fc752a125177738b58c1d346f6ea12ea/app/Http/Controllers/Admin/DashboardController.php#L91). Dependencia: T01. Responsable: analítica/frontend.

### T15. Reparar trazabilidad y cierre documental — P1 — 4 a 6 h

- [ ] Crear matriz RF/RNF → HU → caso de uso → archivo → prueba → evidencia → estado real.
- [ ] Corregir referencias intercambiadas RF-17/RF-21 en docs/ESTADO-IMPLEMENTACION.md.
- [ ] Alinear D5 con esquema v6 y D7, que ya contempla propuestas_asignacion.
- [ ] Documentar consulta por código + correo, simulación de pagos, envío directo por Laravel y decisiones de T12.
- [ ] El informe afirma despliegue, pruebas y 2FA; conservar afirmaciones de ejecución solo con evidencia verificable.
- [ ] Revisar D4: presenta horas reales de cierre de S9/S10, aunque sus cierres figuran el 18/11 y 25/11, posteriores a esta revisión del 12/11. Distinguir proyección de registro real.
- [ ] Validar todas las láminas UML y sus relaciones contra el alcance final. No convertir casos de uso en navegación UWE automáticamente; si el docente exige esa notación, preparar una tarea separada con la consigna exacta.

Aceptación: todos los estados “terminado” enlazan evidencia; documentos coinciden entre sí y con la versión entregada.
Dependencia: T12 y cierre de correcciones. Responsable: documentación/QA.

### T16. Completar inglés en el proceso público — P1 — 4 a 6 h

Brecha confirmada en la vista de participantes: conserva títulos y etiquetas españolas literales (“¿Quiénes viajan?”, “Datos de contacto”, etc.). El test existente comprueba catálogo y un texto de detalle, no todo el wizard.

- [ ] Revisar catálogo, reserva, pago, estado y mensajes de validación.
- [ ] Externalizar textos y mantener selector desde cualquier página.
- [ ] Verificar chatbot bilingüe y respuestas de error.

Aceptación: recorrido completo con lang=en tiene textos operativos en inglés y conserva el idioma entre pasos.
RNF-14. Evidencia: [resources/views/reservas/participantes.blade.php:71](https://github.com/Samuelhvf18/Agencia_de_Turismo/blob/2db08026fc752a125177738b58c1d346f6ea12ea/resources/views/reservas/participantes.blade.php#L71).
Dependencia: T01. Responsable: frontend.

### T17. Ejecutar aceptación funcional y no funcional — P1/P2 — 8 a 12 h

- [ ] Recorrer catálogo → invitado/usuario → participantes → equipo → firma → pago parcial/total → boletos → check-in → cierre → reseña.
- [ ] Probar cancelación/reprogramación, devolución de equipo, asignación simultánea, aprobación/rechazo de propuestas y alertas.
- [ ] Medir RNF-08: p95 de búsquedas <2 s y confirmación <3 s en carga normal.
- [ ] Medir RNF-09: mapa completo <4 s con perfil 4G definido.
- [ ] Medir RNF-10: 3× carga promedio y degradación dentro del 50%; definir primero la carga promedio.
- [ ] Validar 360 px y versiones objetivo de Chrome, Firefox, Safari y Edge; incluir fallo de mapas y chatbot.
- [ ] Verificar RNF-16/17 con datos suficientes, JSONB, auditoría y conversaciones de volumen representativo.

Aceptación: resultados medidos por requisito, con entorno y datos de prueba; no reemplazarlos por capturas ni afirmaciones de “hay Redis”.
Dependencia: T01 y correcciones relevantes. Responsable: QA con equipo.

### T18. Evidencia de operación y recuperación — P2 — 4 a 6 h

- [ ] Verificar TLS, cookies, trabajadores y programadores en el entorno de entrega.
- [ ] Restaurar un respaldo en otra base y verificar integridad y datos de muestra; no basta probar que pg_dump falla si no existe.
- [ ] Verificar retención de 30 días, persistencia de almacenamiento y conservación de comprobantes por estado.
- [ ] Configurar monitoreo e informe mensual de disponibilidad para RNF-11 (≥99%); no puede acreditarse por leer Docker.
- [ ] Comprobar que agregar un flujo n8n no requiere redesplegar Laravel, como pide RNF-18.

Aceptación: restauración documentada y reproducible, monitoreo activo y evidencia de operación. La disponibilidad mensual queda “por medir” hasta completar el período.
Dependencia: T11/T17. Responsable: integración/operación.

## 6. Cobertura de los RNF y evidencia que falta

| RNF | Situación | Cierre |
|---|---|---|
| 01 HTTPS | Middleware/Docker presentes; certificado real no inspeccionado | T18 |
| 02 autenticación | Middleware presente; confirmar todas las APIs privadas | T04/T17 |
| 03 roles | Finanzas admite operaciones donde el documento reserva reportes al admin | T04 |
| 04 alertas | Listener localizado; probar registro, 3 fallos y cambio/reset de contraseña | T11/T17 |
| 05 sensibles | Chofer obtiene documento/salud; revisar todas las lecturas y caché | T04/T13 |
| 06 auditoría | Cobertura incompleta para cambios masivos y modelos excluidos | T05 |
| 07 unicidad | Restricciones presentes; falta evidencia concurrente | T01/T17 |
| 08/09/10 rendimiento | Infraestructura presente; sin medición en esta revisión | T17 |
| 11 disponibilidad | No demostrable desde código | T18 |
| 12 respaldo | Comando presente; restauración real por demostrar | T18 |
| 13 responsive | Revisar flujos completos desde 360 px | T17 |
| 14 idiomas | Traducción parcial del proceso público | T16 |
| 15 offline | Implementación presente; aislamiento y sincronización por verificar | T13 |
| 16 integridad | Validaciones/CHECK; comprobar correspondencia con diccionario | T01/T17 |
| 17 arquitectura/volumen | JSONB y Redis no demuestran ausencia de degradación | T17 |
| 18 extensibilidad n8n | Flujos presentes; verificar activación sin redespliegue | T11/T18 |
| 19 conservación | Anulación por estado presente; probar que no hay borrado físico/cascada indebida | T01/T18 |
| 20 compatibilidad/fallos | Respaldo chatbot presente; matriz de navegadores y fallo de mapas pendientes | T17 |

## 7. Secuencia de ejecución recomendada

No se fijan fechas de entrega sin conocer la disponibilidad del equipo. Estas fases son nuevas y no sustituyen retrospectivamente los sprints históricos.

| Fase | Tareas | Resultado de salida |
|---|---|---|
| A. Línea base y alcance | T01 + T12 | Entorno reproducible, resultados reales y decisiones de alcance. |
| B. Integridad y acceso | T02, T03, T04, T05 | Pagos y datos protegidos; auditoría cubierta. |
| C. Reglas de IO II | T08, T09, T10 | Datos reales y cálculos coherentes con informes. |
| D. Funciones y experiencia | T06, T07, T13, T14, T16 | Historial, slugs, offline, demanda por tour e inglés completos. |
| E. Integración y aceptación | T11, T17, T18 | Flujos reales, pruebas medibles y recuperación. |
| F. Entrega | T15 y consolidación de evidencias | Documentación alineada y pendientes residuales declarados. |

Estimación total orientativa: **81 a 131 horas-persona**, sin integración de pasarela bancaria real ni cambios de alcance como 2FA. Las tareas se pueden repartir entre backend/pagos, frontend, IO II y QA/integración; respetar sus dependencias.

## 8. Decisiones que conviene resolver primero

1. ¿La entrega acepta pasarela simulada o exige transacciones reales?
2. ¿Se mantiene 2FA/verificación de correo del informe o se elimina esa afirmación del alcance?
3. ¿Los comprobantes deben generarse y enviarse por n8n como RF-14 o se acepta Laravel con cola?
4. ¿Qué unidad exacta representa D en EOQ y qué unidad representa K frente a reservas grupales?
5. ¿Se conserva consulta por código + correo? Se recomienda mantener la protección y actualizar el requisito.

Estas preguntas no bloquean la preparación ni las correcciones evidentes; sí cambian el esfuerzo de sus funciones dependientes.

## 9. Criterio de entrega terminada

- [ ] P0 resueltos y con pruebas de regresión.
- [ ] Cada RF/RNF dispone de estado y evidencia, incluidas excepciones aprobadas.
- [ ] Pruebas y build ejecutados sobre el commit de entrega.
- [ ] Integraciones demostradas con ejecución real o rotuladas como simulación.
- [ ] Ejemplos de IO II reproducibles y datos simulados identificados.
- [ ] Base de datos, diccionario, diagramas e informe alineados.
- [ ] Restauración y flujo completo de usuario demostrados.
- [ ] Registro de pendientes residuales con responsable, impacto y decisión.

Este plan no modifica código, documentos originales ni archivos de Drive. Describe el trabajo necesario a partir de evidencia estática y de los criterios documentales actuales.

