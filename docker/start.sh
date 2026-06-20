#!/bin/sh
set -e

php artisan package:discover --ansi || true
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true
php artisan migrate --force || true

exec php artisan serve --host=0.0.0.0 --port="${PORT:-8000}"
