#!/bin/sh

php artisan package:discover --ansi 2>/dev/null || true
php artisan config:clear

# Railway: wait for MySQL (app often starts before DB is ready)
echo "Checking database connection..."
echo "DB_HOST=${DB_HOST:-not-set} DB_PORT=${DB_PORT:-3306} DB_DATABASE=${DB_DATABASE:-not-set}"

ready=0
i=1
while [ "$i" -le 45 ]; do
  if php artisan migrate:status >/dev/null 2>&1; then
    ready=1
    echo "Database connection OK."
    break
  fi
  echo "Attempt $i/45: waiting for MySQL..."
  sleep 2
  i=$((i + 1))
done

if [ "$ready" != "1" ]; then
  echo "ERROR: Cannot connect to MySQL."
  echo "Set Railway Variables on your Laravel service (reference MySQL plugin):"
  echo "  DATABASE_URL=\${{MySQL.MYSQL_URL}}"
  echo "  OR individually: DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD"
  exit 1
fi

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force

exec php artisan serve --host=0.0.0.0 --port="${PORT:-8000}"
