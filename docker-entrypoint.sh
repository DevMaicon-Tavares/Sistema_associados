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

# Apache does not pass shell environment variables to PHP.
# Write a .env file so Laravel can read all runtime configuration via vlucas/phpdotenv.
cat > /var/www/html/.env <<ENV
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
ENV

chown www-data:www-data /var/www/html/.env
chmod 640 /var/www/html/.env

# Run database migrations on startup to create required SQLite tables.
php artisan migrate --force

# Start Apache.
exec apache2-foreground
