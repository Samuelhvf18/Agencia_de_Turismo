#!/usr/bin/env bash
# -----------------------------------------------------------------------------
# Script de instalación para el entorno remoto (sandbox Linux).
# Instala PHP, Composer, Node y PostgreSQL, configura .env, migra y compila.
#
# Uso:  bash scripts/setup.sh
# -----------------------------------------------------------------------------
set -euo pipefail

# Ubicarse en la raíz del repositorio, sin importar desde dónde se invoque.
cd "$(dirname "$0")/.."
RAIZ="$(pwd)"
task_entorno_nuevo=1
if [ -f .env ]; then
    task_entorno_nuevo=0
    if ! sed -n 's/^APP_ENV=//p' .env | tr -d '\"\r' | grep -Eq '^(local|testing)$'; then
        echo "El instalador automático solo admite APP_ENV=local/testing. Consulte docs/DESPLIEGUE.md."
        exit 1
    fi
fi
echo "==> Repositorio: $RAIZ"

DB_NOMBRE="agencia_turismo_db"
DB_TEST="agencia_turismo_test"
DB_USUARIO="pipocas"
DB_CLAVE="pipocas"

# ---------------------------------------------------------------------------
# 0. Privilegios (funciona como root o con sudo)
# ---------------------------------------------------------------------------
if [ "$(id -u)" -eq 0 ]; then
    SUDO=""
    PG_RUN="runuser -u postgres --"
else
    SUDO="sudo"
    PG_RUN="sudo -u postgres"
fi

# ---------------------------------------------------------------------------
# 1. Dependencias del sistema
# ---------------------------------------------------------------------------
if command -v apt-get >/dev/null 2>&1; then
    export DEBIAN_FRONTEND=noninteractive
    echo "==> Actualizando índices de apt..."
    $SUDO apt-get update -y

    echo "==> Instalando utilidades base..."
    $SUDO apt-get install -y git unzip curl ca-certificates

    # PHP 8.4 para la aplicación y Pest 4.
    PHP_OK=0
    if command -v php >/dev/null 2>&1; then
        if [ "$(php -r 'echo PHP_VERSION_ID;')" -ge 80400 ]; then PHP_OK=1; fi
    fi
    if [ "$PHP_OK" -eq 0 ]; then
        echo "==> Instalando PHP..."
        $SUDO apt-get install -y php-cli php-mbstring php-xml php-curl php-zip \
            php-bcmath php-intl php-pgsql php-sqlite3 || true
        if ! command -v php >/dev/null 2>&1 || [ "$(php -r 'echo PHP_VERSION_ID;')" -lt 80400 ]; then
            echo "==> PHP por defecto es antiguo; instalando PHP 8.4 desde el PPA..."
            $SUDO apt-get install -y software-properties-common
            $SUDO add-apt-repository -y ppa:ondrej/php
            $SUDO apt-get update -y
            $SUDO apt-get install -y php8.4-cli php8.4-mbstring php8.4-xml php8.4-curl \
                php8.4-zip php8.4-bcmath php8.4-intl php8.4-pgsql php8.4-sqlite3
            $SUDO update-alternatives --set php /usr/bin/php8.4 >/dev/null 2>&1 || true
        fi
    fi

    # Composer
    if ! command -v composer >/dev/null 2>&1; then
        echo "==> Instalando Composer..."
        curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php
        $SUDO php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer
        rm -f /tmp/composer-setup.php
    fi

    # Node.js (18+ para Vite 6)
    if ! command -v node >/dev/null 2>&1 || [ "$(node -v | sed 's/v\([0-9]*\).*/\1/')" -lt 22 ]; then
        echo "==> Instalando Node.js 20..."
        if [ -n "$SUDO" ]; then
            curl -fsSL https://deb.nodesource.com/setup_22.x | sudo -E bash -
        else
            curl -fsSL https://deb.nodesource.com/setup_22.x | bash -
        fi
        $SUDO apt-get install -y nodejs
    fi

    # PostgreSQL 16
    if ! command -v psql >/dev/null 2>&1; then
        echo "==> Instalando PostgreSQL..."
        $SUDO apt-get install -y postgresql postgresql-contrib
    fi
else
    echo "!! apt-get no disponible: se asume que PHP, Composer, Node y PostgreSQL ya están instalados."
fi

# ---------------------------------------------------------------------------
# 2. PostgreSQL: arrancar y crear la base de datos
# ---------------------------------------------------------------------------
echo "==> Iniciando PostgreSQL..."
if [ -n "$SUDO" ] || [ "$(id -u)" -eq 0 ]; then
    $SUDO service postgresql start >/dev/null 2>&1 \
        || $SUDO pg_ctlcluster 16 main start >/dev/null 2>&1 \
        || $SUDO pg_ctlcluster 15 main start >/dev/null 2>&1 \
        || true
fi

# Esperar a que acepte conexiones (máx. ~30 s).
for _ in $(seq 1 30); do
    if $PG_RUN psql -tAc 'SELECT 1' >/dev/null 2>&1; then break; fi
    sleep 1
done

echo "==> Creando usuario y bases de datos..."
if [ "$task_entorno_nuevo" -eq 1 ]; then
$PG_RUN psql -tAc "SELECT 1 FROM pg_roles WHERE rolname='$DB_USUARIO'" | grep -q 1 \
    || $PG_RUN psql -c "CREATE USER $DB_USUARIO WITH PASSWORD '$DB_CLAVE' SUPERUSER;"
$PG_RUN psql -tAc "SELECT 1 FROM pg_database WHERE datname='$DB_NOMBRE'" | grep -q 1 \
    || $PG_RUN createdb -O "$DB_USUARIO" "$DB_NOMBRE"
$PG_RUN psql -tAc "SELECT 1 FROM pg_database WHERE datname='$DB_TEST'" | grep -q 1 \
    || $PG_RUN createdb -O "$DB_USUARIO" "$DB_TEST"

fi

# ---------------------------------------------------------------------------
# 3. Dependencias PHP
# ---------------------------------------------------------------------------
echo "==> composer install..."
composer install --no-interaction --prefer-dist

# ---------------------------------------------------------------------------
# 4. Configuración de .env
# ---------------------------------------------------------------------------
echo "==> Configurando .env..."
if [ ! -f .env ]; then
    cp .env.example .env
fi

if [ "$task_entorno_nuevo" -eq 1 ]; then
php -r '
$f = ".env";
$s = file_get_contents($f);
$set = function ($k, $v) use (&$s) {
    $patron = "/^" . preg_quote($k, "/") . "=.*/m";
    if (preg_match($patron, $s)) {
        $s = preg_replace($patron, $k . "=" . $v, $s);
    } else {
        $s .= PHP_EOL . $k . "=" . $v;
    }
};
$set("APP_ENV", "local");
$set("APP_DEBUG", "true");
$set("APP_URL", "http://127.0.0.1:8000");
$set("DB_CONNECTION", "pgsql");
$set("DB_HOST", "127.0.0.1");
$set("DB_PORT", "5432");
$set("DB_DATABASE", "agencia_turismo_db");
$set("DB_USERNAME", "pipocas");
$set("DB_PASSWORD", "pipocas");
$set("SESSION_DRIVER", "database");
$set("CACHE_STORE", "database");
$set("QUEUE_CONNECTION", "database");
file_put_contents($f, $s);
'
fi

# Generar APP_KEY solo si está vacío (no invalidar sesiones en reinicios).
if ! php -r 'require "vendor/autoload.php"; $v = Dotenv\Dotenv::parse(file_get_contents(".env")); exit(empty($v["APP_KEY"]) ? 1 : 0);'; then
    php artisan key:generate --force
fi

# ---------------------------------------------------------------------------
# 5. Esquema y datos demo
# ---------------------------------------------------------------------------
echo "==> Migrando y sembrando datos demo..."
php artisan migrate --force
if [ "$task_entorno_nuevo" -eq 1 ]; then php artisan db:seed --force; fi
php artisan storage:link >/dev/null 2>&1 || true

# ---------------------------------------------------------------------------
# 6. Assets del frontend
# ---------------------------------------------------------------------------
echo "==> Instalando dependencias de Node y compilando..."
npm install
npm run build

echo ""
echo "=============================================================="
echo " Instalación completa."
echo " Arranca el servidor con:  bash scripts/start.sh"
echo " Cuentas demo (APP_ENV=local, clave Pipocas2026):"
echo "   admin@pipocas.test · operaciones@pipocas.test"
echo "   guia@pipocas.test · chofer@pipocas.test · turista@pipocas.test"
echo "=============================================================="
