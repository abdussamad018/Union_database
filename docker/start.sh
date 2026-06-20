#!/bin/sh

php artisan package:discover --ansi 2>/dev/null || true
php artisan config:clear

echo "=== Database config check ==="
echo "DATABASE_URL set: $([ -n "$DATABASE_URL" ] && echo YES || echo NO)"
echo "MYSQL_URL set:    $([ -n "$MYSQL_URL" ] && echo YES || echo NO)"
echo "DB_HOST:          ${DB_HOST:-NOT SET}"
echo "DB_PORT:          ${DB_PORT:-NOT SET}"
echo "DB_DATABASE:      ${DB_DATABASE:-NOT SET}"
echo "DB_USERNAME:      ${DB_USERNAME:-NOT SET}"
echo "DB_PASSWORD set:  $([ -n "$DB_PASSWORD" ] && echo YES || echo NO)"

echo "=== Waiting for MySQL (up to 4 min) ==="

ready=0
i=1
while [ "$i" -le 120 ]; do
  if php -r "
    \$host = getenv('DB_HOST') ?: getenv('MYSQLHOST') ?: '';
    \$port = getenv('DB_PORT') ?: getenv('MYSQLPORT') ?: '3306';
    \$user = getenv('DB_USERNAME') ?: getenv('MYSQLUSER') ?: 'root';
    \$pass = getenv('DB_PASSWORD') ?: getenv('MYSQLPASSWORD') ?: '';
    \$db   = getenv('DB_DATABASE') ?: getenv('MYSQLDATABASE') ?: 'railway';
    \$url  = getenv('DATABASE_URL') ?: getenv('MYSQL_URL') ?: '';
    if (\$url) {
      \$p = parse_url(\$url);
      \$host = \$p['host'] ?? \$host;
      \$port = \$p['port'] ?? \$port;
      \$user = urldecode(\$p['user'] ?? \$user);
      \$pass = urldecode(\$p['pass'] ?? \$pass);
      \$db   = ltrim(\$p['path'] ?? '/railway', '/');
    }
    if (!\$host) { exit(1); }
    try {
      \$pdo = new PDO(\"mysql:host=\$host;port=\$port;dbname=\$db\", \$user, \$pass, [
        PDO::ATTR_TIMEOUT => 5,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      ]);
      exit(0);
    } catch (Exception \$e) {
      exit(1);
    }
  " 2>/dev/null; then
    ready=1
    echo "Database connected on attempt $i."
    break
  fi
  if [ "$i" = "1" ] || [ $((i % 10)) -eq 0 ]; then
    echo "Attempt $i/120: still waiting for MySQL..."
  fi
  sleep 2
  i=$((i + 1))
done

if [ "$ready" = "1" ]; then
  php artisan config:cache
  php artisan route:cache
  php artisan view:cache
  php artisan migrate --force
  echo "Migrations complete."
else
  echo "WARNING: MySQL not reachable. Server will start anyway."
  echo "Fix Union_database Variables, then Redeploy:"
  echo "  DATABASE_URL=\${{MySQL.MYSQL_URL}}"
  echo "Or use MySQL service -> Connect -> select Union_database"
fi

exec php artisan serve --host=0.0.0.0 --port="${PORT:-8000}"
