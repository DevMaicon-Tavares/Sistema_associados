#!/bin/sh
set -e

# Use the configured DB path or default to the persistent /var/data location.
DB_PATH=${DB_DATABASE:-/var/data/database.sqlite}

mkdir -p "$(dirname "$DB_PATH")"
touch "$DB_PATH"
chown -R www-data:www-data "$(dirname "$DB_PATH")"

# Ensure the Laravel local path points to the persistent database file.
if [ "${DB_PATH}" != "/var/www/html/database/database.sqlite" ]; then
  mkdir -p /var/www/html/database
  ln -sf "$DB_PATH" /var/www/html/database/database.sqlite
fi

echo "[entrypoint] APP_ENV=${APP_ENV:-NOT_SET}" >&2
echo "[entrypoint] APP_KEY_SET=$([ -n "$APP_KEY" ] && echo YES || echo NO)" >&2
echo "[entrypoint] APP_URL=${APP_URL:-NOT_SET}" >&2

# Write .env - NOTE: APP_KEY must NOT be quoted or key:generate will break it
cat > /var/www/html/.env <<EOF
APP_NAME=Sistema Associados
APP_ENV=${APP_ENV:-production}
APP_DEBUG=${APP_DEBUG:-false}
APP_KEY=${APP_KEY}
APP_URL=${APP_URL:-https://sistema-associados-bci0.onrender.com}
DB_CONNECTION=${DB_CONNECTION:-sqlite}
DB_DATABASE=${DB_PATH}
SESSION_DRIVER=${SESSION_DRIVER:-database}
CACHE_STORE=${CACHE_STORE:-database}
QUEUE_CONNECTION=${QUEUE_CONNECTION:-database}
FILESYSTEM_DISK=${FILESYSTEM_DISK:-local}
LOG_CHANNEL=${LOG_CHANNEL:-stderr}
EOF

chown www-data:www-data /var/www/html/.env
chmod 640 /var/www/html/.env

echo "[entrypoint] .env written, APP_KEY line: $(grep '^APP_KEY=' /var/www/html/.env)" >&2

# Generate APP_KEY if missing (artisan will write it unquoted into .env)
if [ -z "$APP_KEY" ]; then
  echo "[entrypoint] APP_KEY missing - generating" >&2
  php artisan key:generate --force
fi

php artisan config:clear || true
php artisan cache:clear  || true
php artisan migrate --force

exec apache2-foreground
