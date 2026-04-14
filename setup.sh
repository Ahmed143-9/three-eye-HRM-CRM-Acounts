#!/bin/bash

# Create .env file if it doesn't exist
if [ ! -f .env ]; then
    echo "Creating .env file..."
    cp .env.example .env
fi

# Install PHP dependencies
echo "Installing PHP dependencies..."
docker-compose run --rm app composer install

# Generate application key
echo "Generating application key..."
docker-compose run --rm app php artisan key:generate

# Set proper permissions
echo "Setting permissions..."
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Run database migrations
echo "Running database migrations..."
docker-compose run --rm app php artisan migrate --force

# Optimize Laravel
echo "Optimizing Laravel..."
docker-compose run --rm app php artisan config:cache
docker-compose run --rm app php artisan route:cache
docker-compose run --rm app php artisan view:cache

echo "Setup complete! You can now access your application at http://localhost:8000"
