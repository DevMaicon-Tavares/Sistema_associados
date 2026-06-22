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

# Fall back to a persistent database path if no runtime env var is present.
if [ -z "$DB_DATABASE" ]; then
  export DB_DATABASE=/var/data/database.sqlite
fi

# Run database migrations on startup to create required SQLite tables.
php artisan migrate --force

# Start Apache.
exec apache2-foreground
