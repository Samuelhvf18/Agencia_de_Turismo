# Despliegue y operación

## Desarrollo local (Windows/XAMPP o similar)

```bash
composer install
npm install && npm run build      # o: npm run dev
cp .env.example .env              # configurar DB_*, APP_KEY (php artisan key:generate), MAIL_*
php artisan migrate --seed       # base local nueva: esquema y datos demo
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

`scripts/setup.sh` instala PHP 8.4+, Composer, Node 22 y PostgreSQL, crea
las bases `agencia_turismo_db` y `agencia_turismo_test`, crea `.env` solo si no existe,
aplica `migrate` y si el entorno es nuevo agrega semillas demo, enlaza `storage` y compila los assets con Vite.

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
| `db` | postgres 17 | Base de datos |
| `redis` | redis 7 | Caché, sesiones y colas |
| `n8n` | n8nio/n8n | Planificador de IO II, notificaciones y chatbot |

Pasos:

1. Copiar `.env.example` a `.env` y completar al menos: `APP_KEY`, `APP_URL=https://…`, `APP_ENV=production`, `APP_DEBUG=false`, `DB_*`, `MAIL_*`, `PIPOCAS_ADMIN_*`, `PASARELA_SECRETO`, `N8N_TOKEN` y `N8N_WEBHOOK_*`. Con `APP_ENV=production` se fuerza HTTPS (RNF-01); conviene además `SESSION_SECURE_COOKIE=true` y `PASARELA_WEBHOOK_HTTP=true`.
2. Colocar los certificados en `docker/certs/fullchain.pem` y `docker/certs/privkey.pem` (p. ej. con certbot).
3. Si ya existe un volumen PostgreSQL 16, migrarlo mediante dump/restore a un volumen nuevo antes de usar la imagen 17; no conectar la versión nueva directamente al volumen anterior. Conservar y verificar la copia previa. Los estáticos ahora están en la imagen web del mismo build; el volumen público antiguo ya no se usa y no es necesario borrarlo para desplegar.

4. Levantar y preparar:

   ```bash
   docker compose up -d --build
   docker compose exec app php artisan migrate --force
   docker compose exec app php artisan storage:link
   docker compose exec app php artisan config:cache && docker compose exec app php artisan route:cache && docker compose exec app php artisan view:cache
   ```

5. En n8n (`:5678`), importar los flujos de `docs/n8n/` y configurar las credenciales SMTP y de Anthropic (ver `docs/n8n/README.md`).

## Respaldo (RNF-12)

`php artisan respaldo:bd` crea dump, ZIP de adjuntos privados y manifiesto SHA-256, con retención de treinta días. El procedimiento de corte, restauración segura en base vacía, archivos públicos, permisos, secretos y evidencia está en [OPERACION.md](OPERACION.md). Configurar `pg_dump` en PATH o `PG_DUMP_PATH` y copiar las copias fuera del servidor.

Si la red del build usa una CA corporativa, suministrarla sin desactivar TLS:

```sh
docker build --secret id=corp_ca,src=/ruta/ca-confiable.pem --target app -t pipocas-app -f docker/Dockerfile .
docker build --secret id=corp_ca,src=/ruta/ca-confiable.pem --target web -t pipocas-web -f docker/Dockerfile .
```

La CA es una cadena PEM válida del entorno. No subir secretos o certificados privados al repositorio. El build comprueba los requisitos de plataforma de Composer en PHP 8.4. Las imágenes no incluyen `.env`, firmas de QA ni respaldos. Las fotos cargadas y adjuntos pertenecen al volumen `storage` y se conservan separadamente del build.

## Rendimiento (RNF-08 a RNF-10)

- Índices de apoyo del esquema v5 (migración `2026_10_19_160000`), incluido el GIN sobre `tours.trz_geo`.
- OPcache sin revalidación (`docker/php.ini`) y cachés de configuración, rutas y vistas.
- Redis para caché, sesiones y colas; los correos y avisos se envían en segundo plano.
- Nginx sirve los estáticos con caché de 30 días.
- Prueba de carga sugerida (k6 o `ab`) sobre `/`, `/tours/{ruta}` y `/tours/{ruta}/reservar`.
