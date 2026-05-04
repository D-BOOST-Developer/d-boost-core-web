#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")"

echo "==> Pull latest code"
git pull origin main

echo "==> Install PHP deps (production)"
composer install --no-dev --optimize-autoloader --no-interaction

echo "==> Run migrations"
php artisan migrate --force

echo "==> Refresh caches"
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Storage symlink"
php artisan storage:link 2>/dev/null || true

echo "==> Done."
