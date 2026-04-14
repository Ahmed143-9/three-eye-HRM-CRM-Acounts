# Docker Setup Guide for Laravel HRM Project

## Prerequisites

Before running this project, you need to have the following installed on your Windows system:

1. **Docker Desktop** (Required)
   - Download from: https://www.docker.com/products/docker-desktop/
   - Install and start Docker Desktop
   - Make sure it's running (you should see the Docker icon in your system tray)

2. **Docker Compose** (Usually comes with Docker Desktop)

## Setup Instructions

### Step 1: Ensure Docker Desktop is Running
- Open Docker Desktop from your Start Menu
- Wait for it to start completely (green status indicator)
- You should see the Docker whale icon in your system tray

### Step 2: Open PowerShell in Project Directory
```powershell
cd c:\Users\dell\Downloads\three-eye-hrm-main\three-eye-hrm-main
```

### Step 3: Build and Start Containers
Run this command to build and start all Docker containers:
```powershell
docker-compose up -d --build
```

This will create:
- **app**: PHP-FPM container (Laravel application)
- **nginx**: Web server container (accessible on port 8000)
- **db**: MySQL database container
- **redis**: Redis container
- **node**: Node.js container for compiling assets

### Step 4: Install Dependencies and Setup Laravel
After the containers are running, execute these commands:

```powershell
# Install PHP dependencies
docker-compose run --rm app composer install

# Generate application key
docker-compose run --rm app php artisan key:generate

# Run database migrations
docker-compose run --rm app php artisan migrate --force

# Install Node dependencies and build assets
docker-compose run --rm node npm install
docker-compose run --rm node npm run build

# Set proper permissions (if needed)
docker-compose run --rm app chmod -R 775 storage bootstrap/cache
```

### Step 5: Access Your Application
Once everything is set up, you can access your application at:
- **URL**: http://localhost:8000

## Useful Docker Commands

### View Running Containers
```powershell
docker-compose ps
```

### View Logs
```powershell
# View all logs
docker-compose logs -f

# View specific service logs
docker-compose logs -f app
docker-compose logs -f nginx
docker-compose logs -f db
```

### Stop Containers
```powershell
docker-compose stop
```

### Stop and Remove Containers
```powershell
docker-compose down
```

### Stop and Remove Containers + Volumes (WARNING: Deletes database)
```powershell
docker-compose down -v
```

### Rebuild Containers
```powershell
docker-compose up -d --build
```

### Run Artisan Commands
```powershell
docker-compose run --rm app php artisan [command]
```

### Access Container Shell
```powershell
# Access PHP container
docker-compose exec app bash

# Access MySQL container
docker-compose exec db bash

# Access MySQL database directly
docker-compose exec db mysql -u laravel -psecret laravel
```

## Database Connection Details

- **Host**: localhost (from your machine) or db (from within containers)
- **Port**: 3306
- **Database**: laravel
- **Username**: laravel
- **Password**: secret

## Redis Connection Details

- **Host**: localhost (from your machine) or redis (from within containers)
- **Port**: 6379

## Troubleshooting

### Docker Desktop Not Running
If you see an error like "The system cannot find the file specified":
1. Start Docker Desktop
2. Wait for it to fully initialize
3. Try the commands again

### Port Already in Use
If port 8000 or 3306 is already in use:
1. Edit `docker-compose.yml`
2. Change the port mapping (e.g., "8080:80" instead of "8000:80")
3. Run `docker-compose up -d`

### Permission Issues
If you encounter permission errors:
```powershell
docker-compose run --rm app chmod -R 775 storage bootstrap/cache
docker-compose run --rm app chown -R www-data:www-data storage bootstrap/cache
```

### Database Connection Issues
Make sure the database container is running:
```powershell
docker-compose ps
```

If not, restart it:
```powershell
docker-compose restart db
```

### Clear All and Start Fresh
If something goes wrong:
```powershell
docker-compose down -v
docker-compose up -d --build
docker-compose run --rm app composer install
docker-compose run --rm app php artisan key:generate
docker-compose run --rm app php artisan migrate --force
```

## File Structure

```
three-eye-hrm-main/
├── docker/
│   └── nginx/
│       └── default.conf      # Nginx configuration
├── docker-compose.yml         # Docker services configuration
├── Dockerfile                 # PHP-FPM container configuration
├── .dockerignore             # Files to exclude from Docker build
├── setup.bat                 # Windows setup script
└── setup.sh                  # Linux/Mac setup script
```

## Notes

- The `vendor` directory is mounted as a separate volume to avoid permission issues
- Database data is persisted in a Docker volume named `dbdata`
- The Node container runs once to compile assets and then exits
- All containers are connected via `laravel_network` for internal communication
