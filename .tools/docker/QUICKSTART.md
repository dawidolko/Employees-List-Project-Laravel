# Quick Start Guide - Docker Setup

This is a condensed version of the full installation guide. For detailed instructions, see [README.md](README.md).

**Docker Project Name**: `employeeslist-project`  
**MySQL Port**: 3307 (to avoid conflicts with local MySQL)

## Prerequisites

- Docker Desktop installed and running
- At least 4GB RAM and 5GB free disk space

## Installation Steps

### 1. Clone and Navigate

```bash
git clone https://github.com/dawidolko/Employees-List-Project-Laravel.git
cd Employees-List-Project-Laravel/.tools/docker
```

### 2. Make Scripts Executable (macOS/Linux)

```bash
chmod +x manage.sh mysql-init.sh
```

### 3. Start the Application

```bash
# Using management script
./manage.sh start

# Or directly with docker-compose
docker-compose up -d --build
```

**Note**: MySQL runs on port **3307** (not 3306) to avoid conflicts.

### 4. Wait for Initialization

The first startup takes 2-5 minutes to import the database (300k+ employees).

Monitor progress:

```bash
./manage.sh logs
# or
docker-compose logs -f
```

### 5. Access the Application

- **Web App**: http://localhost:8000
- **PHPMyAdmin**: http://localhost:8080
  - Username: `root`
  - Password: `root_password`

## Management Commands

Using the helper script:

```bash
# Start
./manage.sh start

# Stop
./manage.sh stop

# Restart
./manage.sh restart

# View logs
./manage.sh logs app

# Run artisan command
./manage.sh artisan migrate

# Backup database
./manage.sh backup

# View status
./manage.sh status

# Full cleanup (removes data!)
./manage.sh clean
```

Or directly with docker-compose:

```bash
docker-compose up -d           # Start
docker-compose stop            # Stop
docker-compose ps              # Status
docker-compose logs -f app     # Logs
docker-compose down            # Stop and remove
```

## Verification

Check if everything is working:

```bash
# 1. Verify containers are running
docker-compose ps

# 2. Check database
docker-compose exec mysql mysql -u root -proot_password -e "USE employees; SELECT COUNT(*) FROM employees;"
# Should return ~300,000

# 3. Check Laravel
docker-compose exec app php artisan --version

# 4. Access the web app at http://localhost:8000
```

## Troubleshooting

### Containers won't start

```bash
# Check Docker is running
docker info

# Check ports aren't in use
lsof -i :8000  # macOS/Linux
netstat -ano | findstr :8000  # Windows

# View detailed logs
docker-compose logs
```

### Database import failed

```bash
# Manually import
docker-compose exec -T mysql mysql -u root -proot_password employees < ../../database/employees.sql
```

### Permission errors

```bash
# Fix Laravel permissions
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

### Application shows errors

```bash
# Clear Laravel cache
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan key:generate

# Restart
docker-compose restart app
```

## Default Credentials

### Database (Internal)

- Host: `mysql`
- Database: `employees`
- Username: `laravel_user`
- Password: `laravel_password`

### Database (External/PHPMyAdmin)

- Host: `localhost:3307`
- Username: `root`
- Password: `root_password`

**⚠️ Change these in production!**

## Common Tasks

### Run Laravel Commands

```bash
docker-compose exec app php artisan [command]

# Examples
docker-compose exec app php artisan tinker
docker-compose exec app php artisan route:list
docker-compose exec app php artisan migrate:status
```

### Database Backup/Restore

```bash
# Backup
docker-compose exec mysql mysqldump -u root -proot_password employees > backup.sql

# Restore
docker-compose exec -T mysql mysql -u root -proot_password employees < backup.sql
```

### Access Container Shell

```bash
# Laravel app
docker-compose exec app bash

# MySQL
docker-compose exec mysql bash
```

### Update Application

```bash
git pull
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

## Next Steps

- Read the [full README](README.md) for detailed information
- Explore the employee directory at http://localhost:8000
- Customize the application
- Deploy to production

## Need Help?

- Check the [full documentation](README.md)
- View logs: `docker-compose logs`
- Check container status: `docker-compose ps`
- Open an issue on GitHub

---

**Quick Reference:**

```bash
# Start everything
./manage.sh start

# Stop everything
./manage.sh stop

# View status
./manage.sh status

# View logs
./manage.sh logs

# Help
./manage.sh help
```
