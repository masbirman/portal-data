#!/bin/bash
set -e

echo "🚀 Starting Laravel application..."

# Wait for database to be ready
echo "⏳ Waiting for database connection..."
max_tries=30
counter=0
until php artisan db:monitor --databases=mysql > /dev/null 2>&1 || [ $counter -eq $max_tries ]; do
    echo "Waiting for database... ($counter/$max_tries)"
    sleep 2
    counter=$((counter + 1))
done

if [ $counter -eq $max_tries ]; then
    echo "⚠️ Database connection timeout, continuing anyway..."
fi

# Run migrations
echo "🗄️ Running migrations..."
php artisan migrate --force || true

# Cache configuration
echo "⚡ Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize Filament
php artisan filament:optimize 2>/dev/null || true

# Create storage link if not exists
php artisan storage:link 2>/dev/null || true

# Set permissions
echo "🔐 Setting permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "✅ Application ready!"

# Start supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
