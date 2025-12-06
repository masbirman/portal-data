#!/bin/bash
set -e

echo "Starting Laravel application..."

# Create .env file from environment variables
echo "Creating .env file..."
cat > /var/www/html/.env << EOF
APP_NAME=${APP_NAME:-PortalData}
APP_ENV=${APP_ENV:-production}
APP_KEY=${APP_KEY}
APP_DEBUG=${APP_DEBUG:-false}
APP_URL=${APP_URL:-http://localhost}

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=${DB_CONNECTION:-mysql}
DB_HOST=${DB_HOST:-mysql}
DB_PORT=${DB_PORT:-3306}
DB_DATABASE=${DB_DATABASE:-portal_data}
DB_USERNAME=${DB_USERNAME:-root}
DB_PASSWORD=${DB_PASSWORD}

SESSION_DRIVER=${SESSION_DRIVER:-database}
SESSION_LIFETIME=120

CACHE_STORE=${CACHE_STORE:-database}
QUEUE_CONNECTION=${QUEUE_CONNECTION:-database}

FILESYSTEM_DISK=local

TURNSTILE_SITE_KEY=${TURNSTILE_SITE_KEY:-}
TURNSTILE_SECRET_KEY=${TURNSTILE_SECRET_KEY:-}
EOF

# Set ownership of .env
chown www-data:www-data /var/www/html/.env

# Wait for database
echo "Waiting for database..."
max_tries=30
counter=0
while ! php artisan tinker --execute="DB::connection()->getPdo();" > /dev/null 2>&1; do
    counter=$((counter + 1))
    if [ $counter -ge $max_tries ]; then
        echo "Database connection timeout, continuing..."
        break
    fi
    echo "Waiting for database... ($counter/$max_tries)"
    sleep 2
done

# Run Laravel setup commands
echo "Running Laravel setup..."
php artisan package:discover --ansi || true
php artisan migrate --force || true
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true
php artisan storage:link 2>/dev/null || true
php artisan filament:upgrade --ansi 2>/dev/null || true

# Set final permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "Application ready!"

# Start supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
