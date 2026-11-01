set -e

# Fix storage + cache permissions for www-data
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache 2>/dev/null || true
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Link storage into public (idempotent)
php artisan storage:link --no-interaction 2>/dev/null || true

# Fix APP_KEY if missing
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:" ]; then
    php artisan key:generate --force
fi

# Clear/refresh app caches from previous runs
php artisan config:clear --no-interaction 2>/dev/null || true
php artisan cache:clear --no-interaction 2>/dev/null || true

exec php-fpm
