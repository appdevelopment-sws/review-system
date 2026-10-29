#!/bin/sh
set -e

# If .env does not exist, copy from .env.docker or .env.example
if [ ! -f "/var/www/html/.env" ]; then
    if [ -f "/var/www/html/.env.docker" ]; then
        echo "Initializing .env from .env.docker..."
        cp /var/www/html/.env.docker /var/www/html/.env
    elif [ -f "/var/www/html/.env.example" ]; then
        echo "Initializing .env from .env.example..."
        cp /var/www/html/.env.example /var/www/html/.env
    fi
fi

# Ensure vendor dependencies are installed
if [ ! -f "/var/www/html/vendor/autoload.php" ]; then
    if [ "$1" = "php-fpm" ]; then
        echo "vendor/autoload.php not found. Running composer install..."
        composer install --no-interaction --prefer-dist --optimize-autoloader
    else
        echo "Waiting for vendor/autoload.php to be installed by app container..."
        while [ ! -f "/var/www/html/vendor/autoload.php" ]; do
            sleep 2
        done
    fi
fi

# Ensure storage and bootstrap/cache directories exist
mkdir -p /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache

# Generate application key if missing or empty
if [ -f "/var/www/html/.env" ] && [ -f "/var/www/html/vendor/autoload.php" ]; then
    if grep -qE '^APP_KEY=\s*$' /var/www/html/.env; then
        echo "Generating Laravel application key..."
        php artisan key:generate --force
    fi
fi

# Adjust permissions for web user
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true

# Execute container command
exec "$@"
