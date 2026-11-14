# Avance sobre el plan de WAAAAAA

Revisión: 13 de noviembre de 2026 (Bolivia). Base: `1f8e551`.
Este documento distingue código verificado de aceptación externa. No declara terminado
el plan completo ni sustituye aprobación del PO/docente.

| Tarea | Avance y evidencia | Pendiente real |
|---|---|---|
| T01 | PHP 8.4/Composer/PostgreSQL 17 en Docker; workflow CI; suite y build ejecutados | Ver resultados al final; CI remoto por comprobar |
| T02 | Un solo intento pendiente por reserva, también frente a efectivo; bloqueos reserva→comprobante→pago; rechazo de excedentes y pagos tardíos | Carrera real verificada con dos procesos; falta conciliación con proveedor real |
| T03 | Webhook repetido no vuelve a notificar; boletos con trabajo único y registro reutilizado | SMTP no garantiza exactamente un envío si el proceso cae tras la aceptación del servidor |
| T04 | Finanzas/analítica exclusivas del admin; dashboard operaciones sin importes ni series de ingresos; manifiesto del chofer sin salud/documento | Auditoría integral de cada endpoint de lectura sensible |
| T05 | Triggers transaccionales en todas las tablas de negocio actuales; SQL masivo y pivotes incluidos; fallo cerrado; secretos excluidos | Revisar volumen/retención de auditoría y privilegios SQL de producción |
| T06 | Historial propio paginado, enlace y redirección del turista; prueba negativa por titular | No se vinculan reservas invitadas automáticamente por correo |
| T07 | Rutas públicas de categorías y guías, backfill de slugs de guía, perfil con lista explícita de campos públicos | Revisión visual y contenido público editorial |
| T08 | Canceladas excluidas; reserva impaga no cuenta como reservado; fidelizado tiene precedencia; filas redondeadas conservan suma 1 | Última actividad usa alta, pago aprobado y salida completada; falta reconstrucción histórica/cohortes con datos reales |
| T09 | Panel e histórico usan 30 días inclusivos y μ configurado; cero eventos produce λ=0 | Aprobación docente de K en cupos frente a solicitudes grupales; rotular todos los períodos en UI |
| T10 | D usa vista_demanda_equipo_anual del año calendario; historial mensual del mismo año; advertencia de uso académico visible | D cuenta alquileres, no desgaste: validar tasa de reemplazo antes de comprar; σ diaria sigue aproximada |
| T11 | Pruebas con HTTP/Mail simulados y respaldo local del chatbot | Instancia n8n, credenciales SMTP/IA, correo recibido y tareas reales no disponibles |
| T12 | Se conserva alcance existente: simulación, comprobante pendiente al iniciar intento, correo Laravel | PO/docente debe aprobar alcance; 2FA y verificación efectiva de correo siguen pendientes |
| T13 | Sin caché SW de HTML/JSON privados; IndexedDB separado por sesión/usuario, expiración 12h, sin salud/documentos; limpieza al logout | Reabrir desde cero sin red requiere shell offline seguro; prueba real de cambio de cuenta/navegadores |
| T14 | Desglose de cada tour/mes con versión y generación, además del total | Selector por tour implementado; falta contraste con datos de operación |
| T15 | Matriz del repositorio corregida y limitaciones documentadas | PDF/Drive, horas reales de sprints, UML completo y aprobación externa no modificados |
| T16 | Textos principales del wizard/estado/consulta externalizados; traducciones EN y conservación de sesión probada | Literales secundarios, validaciones personalizadas, placeholders y algunos mensajes JS requieren revisión bilingüe integral |
| T17 | Suite de aceptación automatizada de reservas, pagos, PDF/QR, reseñas, operaciones, IO, roles y chatbot | Concurrencia real, carga normal/3×, p95, mapas 4G, volumen y matriz Safari/Firefox/Edge/Chrome/360px |
| T18 | Dump y restauración local ejecutados en otra base PostgreSQL 17, sin errores | TLS real, worker/cron, recuperación en entrega, monitoreo 99% durante un mes, retención de 30 días y volúmenes |

## Decisiones técnicas aplicadas

- Consulta pública requiere código **y correo**; no se reduce esa protección.
- Pago simulado tardío/cancelado o excedente queda fallido; no se registra un
  reembolso bancario como realizado. Pagos ya cobrados y cancelados conservan
  el estado histórico de reembolso del prototipo, sujeto a conciliación externa.
- El comprobante pendiente es un registro interno; no es factura fiscal certificada.
- Correo de tickets sale por Laravel en cola. n8n es configurable para otras
  notificaciones/IA; los mocks no demuestran integración real.
- La auditoría usa triggers PostgreSQL, con exclusiones técnicas explícitas en
  `CI-Y-PRUEBAS.md`; requiere aceptar este alcance para cerrar RNF-06.
- EOQ es una aproximación académica de usos de equipo. No se interpreta cada
  alquiler como una unidad nueva necesaria para reposición.

## Riesgos de entrega

No desplegar como sistema bancario/fiscal ni dar por acreditadas disponibilidad,
seguridad de producción, offline completo o integraciones reales por una suite local.
Los responsables de integración/QA y PO/docente deben cerrar los pendientes de la tabla.

## Resultados ejecutados

- `php artisan test`: **168 aprobadas, 4 omitidas, 0 fallidas**, 856 aserciones,
  70,76 s. Las omisiones son las tres pruebas de 2FA desactivado y el caso de
  registro deshabilitado (el registro está habilitado). [Salida completa](evidencias/suite-2026-11-13.txt).
- `npm ci` y `npm run build`: Vite 6.4.3, 58 módulos, compilación correcta.
- Dos procesos PHP simultáneos: un intento creado y otro rechazado; dos callbacks
  de intentos heredados del saldo completo: uno completado y otro fallido;
  total contabilizado **Bs 100 de Bs 100**. `scripts/qa-pagos-concurrencia.php`.
- PostgreSQL 17: migración/seed en `turismo_restore_source_qa`, dump formato
  custom y `pg_restore --exit-on-error` en `turismo_restored_qa`: sin errores.
  La copia restaurada conserva 6 tours, 223 registros de auditoría y 18 migraciones.
  La muestra no tenía reservas; no acredita recuperación de un volumen operativo.
- No se usó `migrate:fresh` contra la base del usuario. Todos los datos de QA
  residen en un contenedor nuevo. No se publican credenciales ni respaldos.
