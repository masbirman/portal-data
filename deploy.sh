#!/bin/bash

# Deploy Script untuk VPS
# Jalankan script ini setelah git pull di server

echo "🚀 Starting deployment..."

# Maintenance mode
php artisan down || true

# Update dependencies
echo "📦 Installing dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# Build assets
echo "🎨 Building assets..."
npm install
npm run build

# Run migrations
echo "🗄️  Running migrations..."
php artisan migrate --force

# Clear and cache config
echo "⚡ Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan filament:optimize

# Clear application cache
php artisan cache:clear

# Restart queue workers
echo "🔄 Restarting queue workers..."
php artisan queue:restart

# Set permissions
echo "🔐 Setting permissions..."
chmod -R 775 storage bootstrap/cache

# Exit maintenance mode
php artisan up

echo "✅ Deployment completed successfully!"
