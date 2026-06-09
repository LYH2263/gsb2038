#!/bin/sh
set -e
mkdir -p bootstrap/cache storage/framework/cache storage/framework/sessions storage/framework/views storage/logs
chmod -R 775 bootstrap/cache storage
cp .env.example .env
composer install --no-interaction --prefer-dist
php artisan key:generate --force --ansi
echo "Waiting for MySQL..."
for i in 1 2 3 4 5 6 7 8 9 10; do
  if php artisan migrate --force 2>/dev/null; then
    echo "Migrate OK."
    break
  fi
  if [ $i -eq 10 ]; then
    echo "MySQL not ready, exiting."
    exit 1
  fi
  sleep 2
done
php artisan db:seed --force 2>/dev/null || true
php artisan config:clear 2>/dev/null || true
exec php artisan serve --host=0.0.0.0 --port=8000
