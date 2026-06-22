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

# Diagnostic: show what env vars are available at entrypoint time (Render logs)
echo "[entrypoint] APP_ENV=${APP_ENV:-NOT_SET}" >&2
echo "[entrypoint] APP_DEBUG=${APP_DEBUG:-NOT_SET}" >&2
echo "[entrypoint] DB_CONNECTION=${DB_CONNECTION:-NOT_SET}" >&2
echo "[entrypoint] DB_PATH=$DB_PATH" >&2
echo "[entrypoint] APP_KEY_SET=$([ -n "$APP_KEY" ] && echo YES || echo NO)" >&2

# Write .env so Apache/PHP can read all configuration via phpdotenv.
cat > /var/www/html/.env <<EOF
APP_NAME="${APP_NAME:-Sistema Associados}"
APP_ENV="${APP_ENV:-production}"
APP_DEBUG="${APP_DEBUG:-false}"
APP_KEY="${APP_KEY}"
APP_URL="${APP_URL:-http://localhost}"
DB_CONNECTION="${DB_CONNECTION:-sqlite}"
DB_DATABASE="${DB_PATH}"
SESSION_DRIVER="${SESSION_DRIVER:-database}"
CACHE_STORE="${CACHE_STORE:-database}"
QUEUE_CONNECTION="${QUEUE_CONNECTION:-database}"
FILESYSTEM_DISK="${FILESYSTEM_DISK:-local}"
LOG_CHANNEL="${LOG_CHANNEL:-stderr}"
EOF

chown www-data:www-data /var/www/html/.env
chmod 640 /var/www/html/.env

# If APP_KEY is empty, generate one (writes directly into .env)
if [ -z "$APP_KEY" ]; then
  echo "[entrypoint] APP_KEY missing - generating now" >&2
  php artisan key:generate --force
fi

# Clear stale config/cache to avoid bootstrap failures
php artisan config:clear || true
php artisan cache:clear  || true

# Run database migrations on startup to create required SQLite tables.
php artisan migrate --force

# Start Apache.
exec apache2-foreground
