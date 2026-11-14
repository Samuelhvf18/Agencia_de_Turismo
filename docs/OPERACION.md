# Operación y recuperación

14/11/2026 UTC. Procedimiento para el entorno de entrega; su ejecución en producción necesita los servicios y variables de [DESPLIEGUE.md](DESPLIEGUE.md). No se han instalado certificados ni servicios externos de la agencia en esta sesión.

## Migraciones y procesos

Antes de migrar una base existente, hacer respaldo y verificar que no haya dos juegos de parámetros activos:

```sql
SELECT tur_id, COUNT(*) FROM cola_parametros_tour WHERE act GROUP BY tur_id HAVING COUNT(*) > 1;
```

Resolver cualquier duplicado con el responsable de datos; no se desactiva arbitrariamente una configuración existente. Después aplicar `php artisan migrate --force`. No ejecutar `migrate:fresh` sobre información que se deba conservar. El instalador automático está restringido a local/testing y preserva `.env` y datos previos; solo si crea el entorno agrega las semillas demo.

Ejecutar de forma supervisada:

```sh
php artisan queue:work --tries=3 --backoff=30 --timeout=120
php artisan schedule:work
php artisan notificaciones:recuperar --limite=200
```

El último comando también se agenda cada minuto. Revisar `registros_notificacion` fallidos/máximo de intentos y trabajos fallidos. `schedule:work` no sustituye los flujos IO desactivados que se programan por n8n; importarlos/activarlos o elegir expresamente el scheduler Laravel. Las URLs de callback usan `APP_URL` con HTTPS válido, accesible desde n8n.

## Respaldo completo

```sh
php artisan respaldo:bd --dias=30
```

Crea en `storage/backups` un `.dump`, un `.zip` con `storage/app/private` y un `.json` con hashes SHA-256 por archivo y por contenedor. Los archivos se crean con modo 0600; la carpeta, 0700. `--sin-archivos` excluye adjuntos explícitamente. Si falla cualquier parte se elimina ese conjunto incompleto. No se incluyen fotos públicas: conservar también `storage/app/public` con el volumen de almacenamiento de la entrega. Los boletos/comprobantes se regeneran desde BD; los PDF ya guardados bajo almacenamiento privado sí entran al ZIP.

Para una copia coherente entre PostgreSQL y los archivos, detener nuevas escrituras/trabajos durante el corte, o usar snapshots coordinados del almacenamiento. El comando por sí solo no bloquea todas las escrituras de la aplicación. Conservar una copia fuera del mismo servidor; ZIP y dump contienen datos privados y no se suben a GitHub.

Restaurar primero en una **base nueva y vacía**, con PostgreSQL/pg_restore compatibles:

1. Verificar SHA-256 de dump/ZIP contra el manifiesto; conservar `APP_KEY` mediante el gestor de secretos, para poder leer los secretos cifrados de 2FA.
2. `createdb -h HOST -U USUARIO restauracion_qa` y `pg_restore --exit-on-error --no-owner --no-acl -h HOST -U USUARIO -d restauracion_qa respaldo.dump`.
3. Descomprimir el ZIP en un directorio privado de ensayo; rechazar rutas absolutas o con `..`; verificar cada archivo contra el manifiesto antes de moverlo a `storage/app/private`.
4. Ajustar propietario/permisos del usuario de la aplicación y comprobar participantes, pagos, boletos, auditoría, firmas y un PDF regenerado. Verificar también fotos/volumen público por separado.
5. Probar acceso, recuperación de avisos pendientes y colas con SMTP de prueba antes de habilitar envíos reales.

El ensayo registrado restauró 9 reservas, 8 participantes, 6 pagos, 5 comprobantes, 6 boletos, 356 auditorías y 3 archivos privados; cero relaciones huérfanas comprobadas. La retención se ensayó envejeciendo archivos de QA 31 días: borró tres antiguos y conservó el reciente. No equivale a treinta días de operación ni a una muestra de volumen comercial.

## Matriz de accesos sensibles

| Superficie | Autorización / campos | Evidencia |
|---|---|---|
| `/mis-reservas` | Correo verificado; solo `usr_id` propio | ReservasTest, EmailVerificationTest |
| Estado/boletos/comprobante de reserva y boleto individual | Titular autenticado o acceso de invitado validado por código + correo; administración autorizada; no basta conocer URL | AccesoReserva, ReservasTest |
| Reserva administrativa | Admin/operaciones; datos personales/ficha con READ auditado | ReservasAdminController, RolesTest, NotificacionesAuditoriaTest |
| PDF de boletos/comprobante | Misma autorización del acceso; participantes usados en el PDF producen READ, también en generación por correo | BoletoService, ComprobanteService, ReservasTest |
| Finanzas/dashboard/analítica | Importes y reportes exclusivos de admin | RolesTest |
| Manifiesto/check-in HTML y JSON | Guía/chofer asignado o admin/operaciones; chofer sin documento ni salud; guía con READ auditado | OperacionesTest, NotificacionesAuditoriaTest |
| Validar/sincronizar | Se revalida asignación y estado; boleto ajeno a la salida no revela nombre | OperacionesTest, SalidaService |
| Shell offline | Shell genérico; datos cifrados con clave local; sincronización requiere sesión vigente | Ensayo de navegador en evidencia |
| Herramientas de chatbot | Código + correo para estado; solo resumen, sin documentos ni ficha médica | HerramientasChatbot, ChatbotTest |
| Firmas/respaldos | Disco privado, sin enlace público directo; backups excluidos de Git/contexto Docker | FirmaDigital, RespaldarBaseDatos, .dockerignore |
| Autenticación y perfil | Verificación firmada del correo; 2FA opcional con confirmación de contraseña; recuperación de un uso | EmailVerificationTest, TwoFactorAuthenticationSettingsTest |

Las rutas privadas llevan `Cache-Control: no-store, private`, también para invitados en reservas/pagos/PDF. La matriz debe repetirse en el entorno de entrega, incluidos proxy y almacenamiento.

## Auditoría y límites operativos

El [diccionario](DICCIONARIO-ACTUAL.md) inventaría las 39 tablas auditadas. HTTP registra usuario cuando existe, IP y agente; invitado/sistema pueden tener actor nulo. CLI/colas no se atribuyen a una persona sin evidencia. Se excluyen tablas técnicas de caché/sesión/jobs/tokens/migraciones y la propia bitácora. Secretos de autenticación se eliminan de los valores auditados. Nuevas tablas de negocio necesitan trigger explícito.

Usar en producción un rol de migración propietario y otro de aplicación sin capacidad de alterar tablas/triggers; la aplicación necesita leer/insertar bitácora pero no editar/borrar su historial. Los privilegios reales, política de conservación y volumen requieren verificación del DBA del entorno. No se certifican desde la cuenta propietaria de QA.

`/up`, logs, trabajos fallidos y un monitor HTTPS externo son puntos de supervisión. Definir alertas y medir disponibilidad mensual real. El objetivo ≥99 % permanece por medir. Revisar rendimiento con una infraestructura de varios workers: el servidor `artisan serve` usado en el ensayo de carga no cumple la degradación relativa ≤50 % al triplicar concurrencia.
