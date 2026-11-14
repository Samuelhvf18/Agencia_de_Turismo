#!/usr/bin/env bash
# -----------------------------------------------------------------------------
# Comando de arranque del entorno remoto (se ejecuta cada vez que se inicia).
# Asegura PostgreSQL y levanta el servidor de Laravel en el puerto 8000.
#
# Uso:  bash scripts/start.sh
#      PORT=8080 bash scripts/start.sh   (para cambiar el puerto)
# -----------------------------------------------------------------------------
set -euo pipefail

cd "$(dirname "$0")/.."

PUERTO="${PORT:-8000}"

if [ "$(id -u)" -eq 0 ]; then SUDO=""; else SUDO="sudo"; fi

# PostgreSQL debe estar activo tras cada reinicio del contenedor.
if command -v service >/dev/null 2>&1; then
    $SUDO service postgresql start >/dev/null 2>&1 || true
else
    $SUDO pg_ctlcluster 16 main start >/dev/null 2>&1 || true
fi

# Limpiar cachés de configuración/rutas (evita valores viejos tras cambios de .env).
php artisan optimize:clear >/dev/null 2>&1 || true

echo "==> Servidor Laravel en http://0.0.0.0:${PUERTO}"
exec php artisan serve --host=0.0.0.0 --port="${PUERTO}"
