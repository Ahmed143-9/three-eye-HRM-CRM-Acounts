@echo off
echo Setting up Laravel Docker Environment...

REM Create .env file if it doesn't exist
if not exist .env (
    echo Creating .env file...
    copy .env.example .env
)

REM Install PHP dependencies
echo Installing PHP dependencies...
docker-compose run --rm app composer install

REM Generate application key
echo Generating application key...
docker-compose run --rm app php artisan key:generate

REM Run database migrations
echo Running database migrations...
docker-compose run --rm app php artisan migrate --force

REM Optimize Laravel
echo Optimizing Laravel...
docker-compose run --rm app php artisan config:cache
docker-compose run --rm app php artisan route:cache
docker-compose run --rm app php artisan view:cache

echo.
echo Setup complete! You can now access your application at http://localhost:8000
echo.
pause
