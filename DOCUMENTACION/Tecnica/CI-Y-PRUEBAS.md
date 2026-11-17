# Verificación reproducible

Se requiere PHP 8.4 (Pest 4), Composer 2, Node 22 y PostgreSQL 17.
La aplicación requiere PostgreSQL: SQLite no sirve para JSONB, vistas y restricciones.

## Con Docker (base de pruebas aislada)

Desde la raíz del repositorio:

```sh
docker build -t turismo-qa -f docker/Dockerfile.qa .
docker network create turismo-qa-net
docker run -d --name turismo-qa-db --network turismo-qa-net \
  -e POSTGRES_PASSWORD=qa -e POSTGRES_DB=agencia_turismo_test postgres:17
cp .env.example .env # solo si todavía no existe; nunca sobrescribir credenciales existentes
docker run --rm -v "$PWD:/app" turismo-qa composer install --no-interaction
docker run --rm -v "$PWD:/app" turismo-qa php artisan key:generate
npm ci
npm run build
docker run --rm --network turismo-qa-net -v "$PWD:/app" \
  -e APP_ENV=testing -e DB_HOST=turismo-qa-db -e DB_PORT=5432 \
  -e DB_USERNAME=postgres -e DB_PASSWORD=qa -e DB_DATABASE=agencia_turismo_test \
  turismo-qa php artisan test
```

`RefreshDatabase` migra y revierte datos en esa base desechable. No apuntar la
suite a una base operativa. Las credenciales `qa` son exclusivamente locales.
Con proxy corporativo, instalar su CA de confianza en el contenedor; no desactivar TLS.
El workflow `.github/workflows/ci.yml` ejecuta instalación, migraciones vía suite y build en cada push/PR.

## Auditoría

Aplicar `php artisan migrate --force` durante el despliegue tras respaldar la base.
La migración nueva instala triggers PostgreSQL en tablas de negocio existentes,
incluidos pivotes, eventos, notificaciones y cambios SQL masivos. No duplica los
observers anteriores. Una escritura y su bitácora son atómicas: si falla la
bitácora, PostgreSQL revierte la escritura. `READ` sigue usando AuditObserver.
El contexto HTTP se limpia al acabar cada petición; tareas CLI se atribuyen a
`sistema/planificador`. Tablas técnicas de sesión/cache/colas de Laravel/tokens,
migraciones y la propia bitácora se excluyen para evitar recursión y secretos.
Nuevas tablas de negocio deben instalar el trigger `auditoria_negocio` en su migración.
El usuario SQL de producción debe carecer de permisos para desactivar triggers.

## Respaldo y restauración de ensayo

Solo dentro del contenedor desechable anterior:

```sh
docker exec turismo-qa-db pg_dump -U postgres -Fc agencia_turismo_test > /tmp/turismo-qa.dump
docker exec turismo-qa-db createdb -U postgres agencia_turismo_restore_qa
docker exec -i turismo-qa-db pg_restore -U postgres -d agencia_turismo_restore_qa --exit-on-error < /tmp/turismo-qa.dump
docker exec turismo-qa-db psql -U postgres -d agencia_turismo_restore_qa -c 'SELECT count(*) FROM migrations;'
```

Esto comprueba recuperación local; no acredita restauración del entorno de entrega,
retención operativa, TLS, persistencia de volúmenes ni disponibilidad mensual.

El script `scripts/qa-pagos-concurrencia.php` permite preparar una reserva (`setup`),
crear intentos (`iniciar ID`), agregar un segundo intento heredado (`legacy ID`),
procesar callbacks (`webhook REFERENCIA`) y comprobar el saldo (`verify ID`).
Solo funciona con `APP_ENV=testing` y base terminada en `_qa`.
Para reproducir la carrera, ejecutar dos procesos `iniciar` del mismo ID al mismo
tiempo; después, dos `webhook` de las referencias devueltas por `legacy`. Cada
proceso debe tener su propia conexión PostgreSQL. Las notificaciones se simulan.

## Verificación de la entrega 14/11

El [informe](ENTREGA-2026-11-14.md) y las [evidencias](evidencias/aceptacion-2026-11-14.txt) documentan SMTP capturado, reapertura offline, fallos de mapas, carga y restauración con firmas. `scripts/qa-concurrencia.php` agrega carreras de cupos, equipo, asignaciones y pago/cancelación; exige APP_ENV=testing y base terminada en `_qa`, simula la cola y crea datos de prueba. No ejecutarlo sobre datos reales.

`setup` devuelve JSON con IDs. Ejecutar dos procesos `reservar ID` sobre la misma salida; para equipo usar `reservar SALIDA TIPO ITEM`; `asignar SALIDA GUIA` en dos salidas simultáneas; y `pagar REFERENCIA` contra `cancelar RESERVA`. Ejecutar primero la carrera de pago/cancelación, luego cupos/equipo/asignación y `verify /ruta/fixture.json`. Cada proceso debe tener su propia conexión.
