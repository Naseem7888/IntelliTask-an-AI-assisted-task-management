#!/usr/bin/env sh
set -e

# Ensure PHP CLI exists
if [ -x /usr/bin/php82 ]; then
  ln -sf /usr/bin/php82 /usr/bin/php || true
fi

cd /var/www/html

# Only run artisan commands if vendor and artisan exist
if [ -f artisan ] && [ -d vendor ]; then
  echo "[entrypoint] Running migrations..."
  php artisan migrate --force || echo "[entrypoint] migrate failed (continuing)"
  echo "[entrypoint] Ensuring storage symlink..."
  php artisan storage:link || echo "[entrypoint] storage:link failed (continuing)"
fi

echo "[entrypoint] Starting supervisord"
exec /usr/bin/supervisord -c /etc/supervisord.conf
