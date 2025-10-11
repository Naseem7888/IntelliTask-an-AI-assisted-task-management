#!/usr/bin/env sh
set -e

# Ensure PHP CLI exists
if [ -x /usr/bin/php82 ]; then
  ln -sf /usr/bin/php82 /usr/bin/php || true
fi

cd /var/www/html

# Only run artisan commands if vendor and artisan exist
if [ -f artisan ] && [ -d vendor ]; then
  # If using sqlite, ensure the database file exists
  if [ "${DB_CONNECTION}" = "sqlite" ]; then
    mkdir -p database
    if [ ! -f database/database.sqlite ]; then
      echo "[entrypoint] Creating SQLite database file..."
      touch database/database.sqlite
      chown nginx:nginx database/database.sqlite || true
    fi
  fi

  # Adjust Nginx to listen on Render's provided PORT if present
  if [ -n "${PORT}" ]; then
    sed -i "s/listen 8080;/listen ${PORT};/" /etc/nginx/nginx.conf || true
  fi

  echo "[entrypoint] Running migrations..."
  php artisan migrate --force || echo "[entrypoint] migrate failed (continuing)"
  echo "[entrypoint] Ensuring storage symlink..."
  php artisan storage:link || echo "[entrypoint] storage:link failed (continuing)"
fi

echo "[entrypoint] Starting supervisord"
exec /usr/bin/supervisord -c /etc/supervisord.conf
