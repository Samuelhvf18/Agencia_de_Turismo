# Ajustes al Informe Final según el estado real del sistema

El código ya cumple lo que el informe da por terminado. Estos son los puntos del `Informe_Final.docx` que hay que actualizar para que coincidan con la implementación:

1. **Base de datos:** el esquema tiene **41 tablas** (no 40) y 7 vistas. La nueva tabla es `propuestas_asignacion` (RF-24). Reemplazar el script por `schema_postgresql_v6.sql` y el diccionario por `diccionario_datos_v5.docx`.
2. **Usuarios y autenticación:** la tabla es `users`. El registro pide nombre y apellidos, pero se guardan juntos en `nom`. El inicio de sesión es por correo. No hay 2FA ni passkeys.
3. **Pagos:** se usa una **pasarela simulada propia** con webhook firmado con HMAC-SHA256. Admite pagos parciales (anticipo mínimo del 30 %) y el comprobante incluye IVA del 13 % dentro del precio.
4. **Planificador de IO II:** lo programa **n8n**, que llama a `POST /api/n8n/tareas/{tarea}` con un token. El cron de Laravel queda solo para expirar reservas, enviar recordatorios y hacer el respaldo.
5. **Arquitectura:** la auditoría la hace `AuditObserver` (no `AuditoriaObserver`) y las notificaciones `NotificacionJob`. Los servicios son `OptimizadorService` + `MetodoHungaro`, `EoqService`, `ColasService`, `MarkovService`, `ChatbotService` y `ProyeccionDemandaService`.
6. **Chatbot:** usa un agente de IA en n8n (Claude) con 4 herramientas de Laravel. Si la IA no responde, contesta un respaldo local por reglas (RNF-20).
7. **Alto riesgo:** se definió como un tour de dificultad difícil o experto, o con altitud máxima de 4.500 m o más. En esos tours la ficha médica firmada es obligatoria para emitir el boleto.
8. **Pruebas:** hay 160 pruebas Pest (156 pasan y 4 se omiten por el 2FA desactivado), en 13 archivos propios: Reservas, Operaciones, IO, Catálogo, Chatbot, Notificaciones y auditoría, Analítica y reseñas, No funcionales, Roles, y las pruebas unitarias de Colas, Markov y Optimización.
9. **Despliegue:** se usa `docker-compose` con Nginx, PHP-FPM 8.4, PostgreSQL 16, Redis, n8n, un worker de colas y el planificador. Ver `DOCUMENTACION/Tecnica/DESPLIEGUE.md`.
10. **Configuración pendiente para la defensa:** importar los flujos de n8n (SMTP y API key de Anthropic), configurar `N8N_*` y `MAIL_*` en `.env`, y los certificados TLS si se publica en un servidor.
