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

# Normalize APP_KEY so we never persist shell quoting artifacts.
APP_KEY_VALUE=$(printf '%s' "${APP_KEY:-}" | tr -d '"')
if [ -z "$APP_KEY_VALUE" ]; then
  APP_KEY_VALUE=$(php -r 'echo "base64:" . base64_encode(random_bytes(32));')
fi

# Write .env with a clean, unquoted APP_KEY value.
cat > /var/www/html/.env <<EOF
APP_NAME=Sistema Associados
APP_ENV=${APP_ENV:-production}
APP_DEBUG=${APP_DEBUG:-false}
APP_KEY=${APP_KEY_VALUE}
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

php artisan config:clear || true
php artisan cache:clear  || true
php artisan migrate --force

exec apache2-foreground
