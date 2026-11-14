# Despliegue y operación

## Desarrollo local (Windows/XAMPP o similar)

```bash
composer install
npm install && npm run build      # o: npm run dev
cp .env.example .env              # configurar DB_*, APP_KEY (php artisan key:generate), MAIL_*
php artisan migrate:fresh --seed  # esquema v5 + datos demo
php artisan storage:link          # fotos subidas desde el panel
php artisan serve                 # http://127.0.0.1:8000
php artisan queue:work            # correos, boletos y notificaciones
php artisan schedule:work         # expiración de reservas, recordatorios y respaldo
```

Cuentas de prueba (solo `APP_ENV=local`, clave `Pipocas2026`): `admin@`, `operaciones@`, `guia@`, `chofer@` y `turista@pipocas.test`. En producción el administrador se crea con `PIPOCAS_ADMIN_EMAIL` y `PIPOCAS_ADMIN_PASSWORD`.

### Pruebas

```bash
# Requiere la base de datos agencia_turismo_test (PostgreSQL), configurada en phpunit.xml
php artisan test
```

## Entorno remoto (Codespaces / sandbox Linux)

El repositorio incluye dos scripts para levantar el proyecto en un entorno
remoto sin intervención manual:

| Campo del panel | Valor a pegar |
|---|---|
| **Script de instalación** | `bash scripts/setup.sh` |
| **Habilidad de inicio** (startup) | `bash scripts/start.sh` |

`scripts/setup.sh` instala PHP 8.2+, Composer, Node 20 y PostgreSQL 16, crea
las bases `agencia_turismo_db` y `agencia_turismo_test`, genera `.env`,
ejecuta `migrate:fresh --seed`, enlaza `storage` y compila los assets con Vite.

`scripts/start.sh` arranca PostgreSQL y sirve la aplicación con
`php artisan serve --host=0.0.0.0 --port=8000`. Para elegir otro puerto:
`PORT=8080 bash scripts/start.sh`.

Los scripts funcionan tanto si el sandbox ejecuta como `root` como si usa
`sudo`, y son idempotentes: pueden ejecutarse varias veces sin romper nada.

## Producción con Docker

Servicios de `docker-compose.yml`:

| Servicio | Imagen | Función |
|---|---|---|
| `web` | nginx 1.27 | HTTPS (TLS 1.2/1.3), HSTS, gzip y estáticos |
| `app` | PHP-FPM 8.4 (`docker/Dockerfile`) | Laravel |
| `queue` | la misma imagen | `queue:work` (correos, boletos, notificaciones) |
| `scheduler` | la misma imagen | `schedule:work` (expiración de reservas, recordatorios y respaldo) |
| `db` | postgres 16 | Base de datos |
| `redis` | redis 7 | Caché, sesiones y colas |
| `n8n` | n8nio/n8n | Planificador de IO II, notificaciones y chatbot |

Pasos:

1. Copiar `.env.example` a `.env` y completar al menos: `APP_KEY`, `APP_URL=https://…`, `APP_ENV=production`, `APP_DEBUG=false`, `DB_*`, `MAIL_*`, `PIPOCAS_ADMIN_*`, `PASARELA_SECRETO`, `N8N_TOKEN` y `N8N_WEBHOOK_*`. Con `APP_ENV=production` se fuerza HTTPS (RNF-01); conviene además `SESSION_SECURE_COOKIE=true` y `PASARELA_WEBHOOK_HTTP=true`.
2. Colocar los certificados en `docker/certs/fullchain.pem` y `docker/certs/privkey.pem` (p. ej. con certbot).
3. Levantar y preparar:

   ```bash
   docker compose up -d --build
   docker compose exec app php artisan migrate --force --seed
   docker compose exec app php artisan storage:link
   docker compose exec app php artisan config:cache && docker compose exec app php artisan route:cache && docker compose exec app php artisan view:cache
   ```

4. En n8n (`:5678`), importar los flujos de `docs/n8n/` y configurar las credenciales SMTP y de Anthropic (ver `docs/n8n/README.md`).

## Respaldo (RNF-12)

`php artisan respaldo:bd` ejecuta `pg_dump -Fc` hacia `storage/backups/` y borra los respaldos de más de 30 días. El planificador lo corre todos los días a las 02:30. Fuera de Docker, si `pg_dump` no está en el PATH, se indica con `PG_DUMP_PATH`.

Para restaurar:

```bash
pg_restore -h HOST -U USUARIO -d agencia_turismo_db --clean --if-exists storage/backups/agencia_turismo_db_AAAAMMDD_HHMMSS.dump
```

Se recomienda copiar `storage/backups` a otro equipo o a la nube (p. ej. con `rclone` en el cron del servidor).

## Rendimiento (RNF-08 a RNF-10)

- Índices de apoyo del esquema v5 (migración `2026_10_19_160000`), incluido el GIN sobre `tours.trz_geo`.
- OPcache sin revalidación (`docker/php.ini`) y cachés de configuración, rutas y vistas.
- Redis para caché, sesiones y colas; los correos y avisos se envían en segundo plano.
- Nginx sirve los estáticos con caché de 30 días.
- Prueba de carga sugerida (k6 o `ab`) sobre `/`, `/tours/{ruta}` y `/tours/{ruta}/reservar`.
