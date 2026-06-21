#!/bin/sh
set -e

# Ensure the SQLite runtime directory and database file exist.
mkdir -p /var/data
if [ ! -f /var/data/database.sqlite ]; then
  touch /var/data/database.sqlite
fi
chown -R www-data:www-data /var/data

# Run database migrations on startup to create required SQLite tables.
php artisan migrate --force

# Start Apache.
exec apache2-foreground
