# Estado de implementación por requisito

Rama `WAAAAAA`, código de referencia `33abc90`, actualización documental del 13/11/2026 (Bolivia). La tabla describe implementación localizada; no acredita aceptación operativa. Consultar primero el [plan vigente](../Plan_Trabajo_Agencia_Turismo.md). Ver también [avance y pendientes](AVANCE-PLAN-TRABAJO.md) y [verificación reproducible](CI-Y-PRUEBAS.md) para evidencia y límites.

## Requerimientos funcionales

| RF | Requisito | Implementación | Pruebas |
|---|---|---|---|
| RF-01 | Filtros del catálogo | Categoría + dificultad + altitud sin recargar (`welcome.blade.php`) | CatalogoTest |
| RF-02 | Mapa y perfil de elevación | Leaflet con `trz_geo`/`puntos_ruta` y Chart.js (`tours/show`) | CatalogoTest |
| RF-03, RF-04 | Registro, login, roles e historial propio | Fortify sobre `users`, middleware y `/mis-reservas` por titular | RolesTest, RegistrationTest, ReservasTest |
| RF-05 a RF-07 | Salida, participantes, equipo | `ReservaController`, `BorradorReserva`, FormRequests CI/pasaporte | ReservasTest |
| RF-08 | Ficha médica con firma | Canvas → `FirmaDigital`; obligatoria en alto riesgo | ReservasTest |
| RF-09, RF-36 | Cupo y código único | `ReservaService::confirmar` con `lockForUpdate`; `CodeGeneratorService` | ReservasTest |
| RF-10, RF-11 | Boletos QR y PDF | `BoletoService` (QR SVG, dompdf), correo `EnviarBoletosReserva` | ReservasTest |
| RF-12 | Consulta por código | `ConsultaReservaController` (código + correo) | ReservasTest |
| RF-13, RF-14 | Pagos y comprobante | `PagoService`, `PasarelaSimulada` (webhook HMAC), `ComprobanteService` | ReservasTest |
| RF-15, RF-19, RF-20 | Personal, flota, inventario | `PersonalController`, `FlotaController`, `InventarioController` | OperacionesTest |
| RF-16, RF-21 | Programación, asignación y cancelación | `SalidaService`, `AsignacionService` | OperacionesTest |
| RF-17, RF-18 | Check-in QR y manifiesto | `CheckinController` + html5-qrcode + IndexedDB | OperacionesTest |
| RF-22 | Asignación de guías | `OptimizadorService` + `MetodoHungaro` | OptimizacionTest, IoGestionTest |
| RF-23 | Asignación de vehículos | `OptimizadorService::proponerVehiculos` | IoGestionTest |
| RF-24 | Revisión de propuestas | Tabla `propuestas_asignacion`, panel de propuestas | IoGestionTest |
| RF-25 a RF-27 | EOQ y reposición | `EoqService`, `io:alertas-eoq` | OptimizacionTest, IoGestionTest |
| RF-28 a RF-31 | Chatbot | `ChatbotService`, `AsistenteLocal`, flujo n8n `chatbot.json` | ChatbotTest |
| RF-32 | Reseñas verificadas | `ResenaController` (solo reservas completadas) | AnaliticaResenasTest |
| RF-33 a RF-35 | Dashboards y proyección | `DashboardController`, `ProyeccionDemandaService`, selector/desglose por tour | AnaliticaResenasTest |
| RF-37 | Rutas amigables públicas | Tours, categorías y perfiles de guía; `SlugService` y backfill de slugs | CatalogoTest |
| RF-38 | Notificaciones | `NotificacionService`, `NotificacionJob`, flujo n8n | NotificacionesAuditoriaTest |
| RF-39 | Validación de formato | FormRequests (reservas, tours, vehículos, salidas) + CHECK | NoFuncionalesTest |
| RF-40 | Parámetros de colas | Formulario con μ>0, c≥1, K≥c, un juego activo | IoGestionTest |
| RF-41 a RF-43 | Colas M/M/c/K | `ColasService`, ventana común de 30 días y comparación actual/escenario sin cambios persistentes | ColasServiceTest, ReservasTest, IoGestionTest |
| RF-44, RF-45 | Cadena de Markov | `MarkovService` conectado a registro, pago y fin de tour | MarkovServiceTest, ReservasTest |

## Requerimientos no funcionales

| RNF | Implementación |
|---|---|
| RNF-01 HTTPS | Middleware/configuración de Nginx presentes; certificado y despliegue real pendientes |
| RNF-02/03 Roles | Middleware `rol:` en todas las rutas del panel y del check-in |
| RNF-04 Alertas de seguridad | `AlertasSeguridad`: cuenta nueva, 3 intentos fallidos, cambio de contraseña |
| RNF-05 Lecturas sensibles | `AuditObserver::lectura` en participantes y fichas médicas |
| RNF-06 Auditoría | Triggers PostgreSQL transaccionales; READ mediante AuditObserver; panel con antes/después |
| RNF-07 Integridad | CHECK, UNIQUE y FK del esquema; transacciones con bloqueo |
| RNF-08 a RNF-10 Rendimiento | Infraestructura prevista; mediciones de p95/4G/3× pendientes |
| RNF-12 Respaldo | Comando con retención configurada; restauración local pequeña comprobada; recuperación/retención operativas pendientes |
| RNF-13 Responsive | 16 pantallas administrativas revisadas a 360 px en Chromium; completar flujos públicos/campo y otros navegadores |
| RNF-14 Idiomas | ES/EN principal con selector y sesión; textos secundarios y recorrido integral pendientes |
| RNF-15 Sin conexión | IndexedDB separado por sesión y caché privada retirada; reapertura desde cero sin red y aceptación integral pendientes |
| RNF-20 Fallo controlado | Chatbot con respaldo local si la IA no responde |

## Administración y planificación

El [rediseño implementado](REDISENO-ADMIN.md) unifica administración e IO. La entrada `/admin/io` organiza las herramientas como demanda/capacidad, asignaciones, equipo, retención y ajustes. La suite local más reciente tiene **170 aprobadas, 4 omitidas y 0 fallidas**; ver [evidencia](evidencias/suite-33abc90.txt). Esto no sustituye aceptación externa ni certifica CI remoto.

## Cambios respecto al diseño original

- **Tabla nueva `propuestas_asignacion`** (RF-24): el esquema pasa de 40 a 41 tablas. Ver `Documentacion actualizada/03_Base_de_Datos/schema_postgresql_v6.sql` y `diccionario_datos_v5.docx`.
- La tabla de usuarios es `users` (decisión del equipo). El registro pide nombre y apellidos y los guarda juntos en `nom`. El inicio de sesión es por correo.
- Sin 2FA ni passkeys: `users` no tiene esas columnas en el diccionario.
- La pasarela de pago es una simulación propia con webhook firmado (decisión del equipo).
- El planificador de IO II es n8n (`docs/n8n/planificador-io.json`); el cron de Laravel queda para la expiración de reservas, los recordatorios y el respaldo.

## Configuración pendiente para producción y defensa

| Qué | Dónde | Sin configurar… |
|---|---|---|
| n8n: importar flujos, credencial SMTP y API key de Anthropic | `docs/n8n/*.json` | Notificaciones por correo directo; chatbot con el asistente local |
| `N8N_TOKEN`, `N8N_WEBHOOK_NOTIFICACIONES`, `N8N_WEBHOOK_CHATBOT` | `.env` | Igual que arriba; el planificador no corre (tareas manuales o cron) |
| SMTP real (`MAIL_*`) | `.env` | Los correos no salen |
| `PASARELA_SECRETO`, `PASARELA_WEBHOOK_HTTP=true` | `.env` (producción) | Usa `APP_KEY` como secreto |
| `PIPOCAS_ADMIN_EMAIL` / `PIPOCAS_ADMIN_PASSWORD` | `.env` (producción) | El seeder falla en producción |
| Certificados TLS | `docker/certs/` | Nginx no arranca en 443 |
| Worker de colas y planificador | `queue:work`, `schedule:work` | Correos y avisos quedan en cola |
