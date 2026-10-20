# Estado de implementación por requisito

Rama `WAAAAAA`, 20/10/2026. Todo lo listado está implementado y cubierto por pruebas automáticas (`php artisan test`), salvo lo indicado en *Configuración pendiente*.

## Requerimientos funcionales

| RF | Requisito | Implementación | Pruebas |
|---|---|---|---|
| RF-01 | Filtros del catálogo | Categoría + dificultad + altitud sin recargar (`welcome.blade.php`) | CatalogoTest |
| RF-02 | Mapa y perfil de elevación | Leaflet con `trz_geo`/`puntos_ruta` y Chart.js (`tours/show`) | CatalogoTest |
| RF-03, RF-04 | Registro, login y roles | Fortify sobre `users`, `VerificarRol`, `RedirigirSegunRol` | RolesTest, RegistrationTest |
| RF-05 a RF-07 | Salida, participantes, equipo | `ReservaController`, `BorradorReserva`, FormRequests CI/pasaporte | ReservasTest |
| RF-08 | Ficha médica con firma | Canvas → `FirmaDigital`; obligatoria en alto riesgo | ReservasTest |
| RF-09, RF-36 | Cupo y código único | `ReservaService::confirmar` con `lockForUpdate`; `CodeGeneratorService` | ReservasTest |
| RF-10, RF-11 | Boletos QR y PDF | `BoletoService` (QR SVG, dompdf), correo `EnviarBoletosReserva` | ReservasTest |
| RF-12 | Consulta por código | `ConsultaReservaController` (código + correo) | ReservasTest |
| RF-13, RF-14 | Pagos y comprobante | `PagoService`, `PasarelaSimulada` (webhook HMAC), `ComprobanteService` | ReservasTest |
| RF-15, RF-19, RF-20 | Personal, flota, inventario | `PersonalController`, `FlotaController`, `InventarioController` | OperacionesTest |
| RF-16 a RF-18 | Programación y asignación | `SalidaService`, `AsignacionService` | OperacionesTest |
| RF-21 | Check-in QR | `CheckinController` + html5-qrcode + IndexedDB | OperacionesTest |
| RF-22 | Asignación de guías | `OptimizadorService` + `MetodoHungaro` | OptimizacionTest, IoGestionTest |
| RF-23 | Asignación de vehículos | `OptimizadorService::proponerVehiculos` | IoGestionTest |
| RF-24 | Revisión de propuestas | Tabla `propuestas_asignacion`, panel de propuestas | IoGestionTest |
| RF-25 a RF-27 | EOQ y reposición | `EoqService`, `io:alertas-eoq` | OptimizacionTest, IoGestionTest |
| RF-28 a RF-31 | Chatbot | `ChatbotService`, `AsistenteLocal`, flujo n8n `chatbot.json` | ChatbotTest |
| RF-32 | Reseñas verificadas | `ResenaController` (solo reservas completadas) | AnaliticaResenasTest |
| RF-33 a RF-35 | Dashboards y proyección | `DashboardController`, `ProyeccionDemandaService` | AnaliticaResenasTest |
| RF-37 | Ruta amigable única | `SlugService` | CatalogoTest |
| RF-38 | Notificaciones | `NotificacionService`, `NotificacionJob`, flujo n8n | NotificacionesAuditoriaTest |
| RF-39 | Validación de formato | FormRequests (reservas, tours, vehículos, salidas) + CHECK | NoFuncionalesTest |
| RF-40 | Parámetros de colas | Formulario con μ>0, c≥1, K≥c, un juego activo | IoGestionTest |
| RF-41 a RF-43 | Colas M/M/c/K | `ColasService` conectado a confirmar/rechazar/cancelar | ColasServiceTest, ReservasTest |
| RF-44, RF-45 | Cadena de Markov | `MarkovService` conectado a registro, pago y fin de tour | MarkovServiceTest, ReservasTest |

## Requerimientos no funcionales

| RNF | Implementación |
|---|---|
| RNF-01 HTTPS | `ForzarHttps` (301 + HSTS en producción), Nginx con TLS |
| RNF-02/03 Roles | Middleware `rol:` en todas las rutas del panel y del check-in |
| RNF-04 Alertas de seguridad | `AlertasSeguridad`: cuenta nueva, 3 intentos fallidos, cambio de contraseña |
| RNF-05 Lecturas sensibles | `AuditObserver::lectura` en participantes y fichas médicas |
| RNF-06 Auditoría | `AuditObserver` en 27 modelos; panel con antes/después |
| RNF-07 Integridad | CHECK, UNIQUE y FK del esquema; transacciones con bloqueo |
| RNF-08 a RNF-10 Rendimiento | Índices, OPcache, Redis, colas, caché de estáticos |
| RNF-12 Respaldo | `respaldo:bd` diario con retención de 30 días |
| RNF-13 Responsive | Revisado a 360 px |
| RNF-14 Idiomas | ES/EN con selector y detección del navegador |
| RNF-15 Sin conexión | Check-in con IndexedDB y service worker |
| RNF-20 Fallo controlado | Chatbot con respaldo local si la IA no responde |

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
