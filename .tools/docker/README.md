# 🐳 Docker Installation Guide - Employees List Project

> **Complete Docker setup for Laravel Employee Directory Application with MySQL Employees Database**

This guide provides detailed manual instructions for deploying the entire Employees List Project using Docker and Docker Compose. The setup includes the Laravel application, MySQL database with sample data, Nginx web server, and PHPMyAdmin for database management.

**Docker Project Name**: `employeeslist-project`

**Port Configuration**: MySQL runs on port **3307** (not 3306) to avoid conflicts with local MySQL installations.

All containers, networks, and volumes will be prefixed with this project name for easy identification and management.

---

## 📋 Table of Contents

- [Prerequisites](#prerequisites)
- [Project Structure](#project-structure)
- [Quick Start](#quick-start)
- [Manual Installation Steps](#manual-installation-steps)
- [Configuration](#configuration)
- [Database Setup](#database-setup)
- [Accessing the Application](#accessing-the-application)
- [Troubleshooting](#troubleshooting)
- [Advanced Usage](#advanced-usage)
- [Maintenance](#maintenance)
- [Security Considerations](#security-considerations)

---

## 🔧 Prerequisites

Before starting, ensure you have the following installed on your system:

### Required Software

1. **Docker Engine** (version 20.10 or higher)
   - Download: https://docs.docker.com/get-docker/
   - Verify installation: `docker --version`

2. **Docker Compose** (version 2.0 or higher)
   - Usually included with Docker Desktop
   - Verify installation: `docker-compose --version`

3. **Git** (for cloning the repository)
   - Download: https://git-scm.com/downloads
   - Verify installation: `git --version`

### System Requirements

- **OS**: Linux, macOS, or Windows 10/11 with WSL2
- **RAM**: Minimum 4GB (8GB recommended)
- **Disk Space**: At least 5GB free space
- **CPU**: 2+ cores recommended
- **Network**: Internet connection for initial setup

---

## 📁 Project Structure

```
Employees-List-Project-Laravel/
├── .tools/
│   └── docker/
│       ├── Dockerfile              # Laravel app container configuration
│       ├── docker-compose.yml      # Multi-container orchestration
│       ├── nginx.conf              # Nginx web server configuration
│       ├── php.ini                 # PHP runtime settings
│       ├── supervisord.conf        # Process manager configuration
│       ├── mysql-init.sh           # Database initialization script
│       └── README.md               # This file
├── backend/                        # Laravel application source code
├── database/                       # SQL scripts and database dumps
│   ├── employees.sql               # Main database schema and data
│   ├── load_*.dump                 # Individual table data dumps
│   └── ...
└── ...
```

---

## 🚀 Quick Start

For experienced users, here's the quickest way to get started:

```bash
# 1. Clone the repository
git clone https://github.com/dawidolko/Employees-List-Project-Laravel.git
cd Employees-List-Project-Laravel

# 2. Navigate to Docker directory
cd .tools/docker

# 3. Make scripts executable (Linux/macOS)
chmod +x mysql-init.sh

# 4. Build and start containers
docker-compose up -d --build

# 6. Wait for initialization (2-3 minutes)
docker-compose logs -f app

# 7. Access the application
# Open browser: http://localhost:8000
```

**⚠️ Important for macOS/Linux users:**
If you have local MySQL running, you must stop it first:

```bash
# macOS (Homebrew):
brew services stop mysql

# Linux:
sudo systemctl stop mysql

# Or change port 3306 to 3307 in docker-compose.yml if you need both running
```

---

## 📖 Manual Installation Steps

### Step 1: Clone the Repository

```bash
# Clone the project repository
git clone https://github.com/dawidolko/Employees-List-Project-Laravel.git

# Navigate to the project directory
cd Employees-List-Project-Laravel
```

### Step 2: Prepare the Environment

```bash
# Navigate to the Docker configuration directory
cd .tools/docker

# Make the MySQL initialization script executable (Linux/macOS)
chmod +x mysql-init.sh
```

**For Windows users:**

- Right-click on `mysql-init.sh`
- Properties → Security → Unblock
- Or use Git Bash for chmod command

### Step 3: Configure Environment Variables (Optional)

Edit `docker-compose.yml` to customize database credentials:

```yaml
environment:
  MYSQL_ROOT_PASSWORD: your_secure_root_password
  MYSQL_DATABASE: employees
  MYSQL_USER: your_username
  MYSQL_PASSWORD: your_secure_password
```

**Security Note**: Change default passwords in production environments!

### Step 4: Build Docker Images

```bash
# Build the Laravel application image
docker-compose build

# This process may take 5-10 minutes on first run
# Docker will download base images and install all dependencies
```

**What happens during build:**

- Downloads PHP 8.2-FPM base image
- Installs system dependencies (git, curl, libraries)
- Installs PHP extensions (pdo_mysql, gd, zip, etc.)
- Installs Composer and PHP dependencies
- Configures Nginx and Supervisor

### Step 5: Start the Containers

```bash
# Start all containers in detached mode
docker-compose up -d

# Verify containers are running
docker-compose ps
```

**Expected output:**

```
NAME                       STATUS              PORTS
employeeslist-mysql        Up (healthy)        0.0.0.0:3307->3306/tcp
employeeslist-app          Up                  0.0.0.0:8000->80/tcp
employeeslist-phpmyadmin   Up                  0.0.0.0:8080->80/tcp
```

### Step 6: Monitor Initialization

```bash
# Watch the application logs
docker-compose logs -f app

# Watch MySQL logs
docker-compose logs -f mysql
```

**Wait for these messages:**

- MySQL: `MySQL init process done. Ready for start up.`
- App: `Application setup completed!`

This process typically takes **2-3 minutes**.

### Step 7: Verify Database Import

```bash
# Connect to MySQL container
docker-compose exec mysql mysql -u root -proot_password

# Check if employees database exists
mysql> SHOW DATABASES;
mysql> USE employees;
mysql> SHOW TABLES;
mysql> SELECT COUNT(*) FROM employees;
# Should return 300,024 rows
mysql> EXIT;
```

### Step 8: Verify Application Setup

```bash
# Access the Laravel application container
docker-compose exec app bash

# Check Laravel installation
php artisan --version

# Verify database connection
php artisan migrate:status

# Check environment configuration
cat .env | grep DB_

# Exit container
exit
```

---

## ⚙️ Configuration

### Docker Project Configuration

**Project Name**: `employeeslist-project`

This name is defined in `docker-compose.yml` and will be used as a prefix for:

- Container names: `employeeslist-mysql`, `employeeslist-app`, `employeeslist-phpmyadmin`
- Network name: `employeeslist-network`
- Volume names: `employeeslist-project_mysql_data`, `employeeslist-project_storage_data`
- **MySQL Port**: 3307 (mapped from container's 3306)

To change the project name or ports, edit `docker-compose.yml`.

### Database Configuration

**Default Credentials:**

- **Host**: `mysql` (internal) or `localhost:3307` (external from host machine)
- **Database**: `employees`
- **Username**: `laravel_user`
- **Password**: `laravel_password`
- **Root Password**: `root_password`

**Important**: External connections use port **3307**, internal (between containers) use standard port 3306.

### PHP Configuration

Edit `.tools/docker/php.ini` to customize PHP settings:

```ini
memory_limit = 512M
upload_max_filesize = 256M
max_execution_time = 300
```

Apply changes:

```bash
docker-compose restart app
```

### Nginx Configuration

Edit `.tools/docker/nginx.conf` for web server settings:

```nginx
client_max_body_size 256M;
fastcgi_read_timeout 300;
```

Apply changes:

```bash
docker-compose restart app
```

### Laravel Environment

The `.env` file is automatically configured during container initialization. To modify:

```bash
# Edit .env in backend directory
nano ../backend/.env

# Restart application to apply changes
docker-compose restart app

# Clear Laravel cache
docker-compose exec app php artisan config:cache
```

---

## 💾 Database Setup

### Automatic Import

The database is automatically imported during first initialization using:

- `mysql-init.sh` script
- `employees.sql` file from the `database/` directory

### Manual Database Import

If automatic import fails:

```bash
# Method 1: Using docker-compose exec
docker-compose exec -T mysql mysql -u root -proot_password employees < ../../database/employees.sql

# Method 2: Copy file and import inside container
docker cp ../../database/employees.sql employeeslist-mysql:/tmp/
docker-compose exec mysql bash
mysql -u root -proot_password employees < /tmp/employees.sql
exit
```

### Verify Database Content

```bash
# Connect to database
docker-compose exec mysql mysql -u root -proot_password employees

# Check tables
SHOW TABLES;

# Verify data
SELECT COUNT(*) FROM employees;      # Should be ~300,000
SELECT COUNT(*) FROM departments;    # Should be 9
SELECT COUNT(*) FROM salaries;       # Should be ~2,800,000
SELECT COUNT(*) FROM titles;         # Should be ~440,000

# Test a query
SELECT e.first_name, e.last_name, d.dept_name
FROM employees e
JOIN dept_emp de ON e.emp_no = de.emp_no
JOIN departments d ON de.dept_no = d.dept_no
LIMIT 10;
```

---

## 🌐 Accessing the Application

### Main Application

**URL**: http://localhost:8000

**Features:**

- Employee directory listing
- Advanced filtering (salary, department, gender)
- Data export to CSV
- Salary analysis

### PHPMyAdmin

**URL**: http://localhost:8080

**Login:**

- **Server**: `mysql`
- **Username**: `root`
- **Password**: `root_password`

**Use PHPMyAdmin to:**

- Browse database tables
- Execute SQL queries
- Export/import data
- Monitor database performance

### MySQL Direct Connection

**Connection Details:**

```
Host: localhost
Port: 3307
Username: laravel_user
Password: laravel_password
Database: employees
```

**Connect using MySQL client:**

```bash
mysql -h localhost -P 3307 -u laravel_user -plaravel_password employees
```

---

## 🔍 Troubleshooting

### Issue: Port 3306 already in use (Most Common)

**Error:** `Error response from daemon: Ports are not available: exposing port TCP 0.0.0.0:3306 -> 127.0.0.1:0: listen tcp 0.0.0.0:3306: bind: address already in use`

**Cause:** Local MySQL server is running and using port 3306.

**Solution 1: Stop local MySQL (Recommended)**

```bash
# macOS (Homebrew):
brew services stop mysql
# Verify it stopped:
brew services list | grep mysql

# Linux:
sudo systemctl stop mysql
# Verify it stopped:
sudo systemctl status mysql

# Windows:
# Stop MySQL service from Services panel or:
net stop MySQL80

# Then start Docker containers:
docker-compose up -d
```

**Solution 2: Change Docker MySQL port**

If you need to keep local MySQL running, edit `docker-compose.yml`:

```yaml
mysql:
  ports:
    - "3307:3306" # Change from 3306:3306 to 3307:3306
```

Then update connection in your app:

```bash
# Connect to Docker MySQL on new port:
mysql -h localhost -P 3307 -u laravel_user -plaravel_password employees
```

**Verify port is free:**

```bash
lsof -i :3306  # Should return nothing if free
```

### Issue: Containers won't start

**Solution:**

```bash
# Check Docker service status
docker info

# Check port availability
lsof -i :8000  # Application port
lsof -i :3307  # MySQL port
lsof -i :8080  # PHPMyAdmin port

# Stop conflicting services if needed
```

### Issue: Database import failed

**Symptoms:** Empty database, missing tables

**Solution:**

```bash
# Check MySQL logs
docker-compose logs mysql

# Manually import database
docker-compose exec -T mysql mysql -u root -proot_password employees < ../../database/employees.sql

# Restart containers
docker-compose restart
```

### Issue: Permission denied errors

**Solution:**

```bash
# Fix storage permissions
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
docker-compose exec app chmod -R 775 storage bootstrap/cache

# On macOS/Linux: Fix script permissions
chmod +x mysql-init.sh
```

### Issue: Application shows 500 error

**Solution:**

```bash
# Check application logs
docker-compose logs app

# Clear Laravel cache
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan view:clear

# Regenerate application key
docker-compose exec app php artisan key:generate

# Restart application
docker-compose restart app
```

### Issue: Cannot connect to database

**Solution:**

```bash
# Verify MySQL is running
docker-compose ps mysql

# Check database credentials in .env
docker-compose exec app cat .env | grep DB_

# Test database connection
docker-compose exec app php artisan migrate:status

# Restart database
docker-compose restart mysql
```

### Issue: Slow performance

**Solution:**

```bash
# Increase Docker resources
# Docker Desktop → Settings → Resources
# Recommended: 4GB RAM, 2 CPUs

# Optimize Laravel
docker-compose exec app php artisan optimize
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
```

### Issue: Port already in use

**Error:** `Bind for 0.0.0.0:8000 failed: port is already allocated`

**Solution:**

```bash
# Option 1: Kill process using the port
# macOS/Linux:
lsof -ti:8000 | xargs kill -9

# Windows:
netstat -ano | findstr :8000
taskkill /PID <PID> /F

# Option 2: Change port in docker-compose.yml
# Edit: "8001:80" instead of "8000:80"
```

---

## 🔧 Advanced Usage

### Running Artisan Commands

```bash
# Run any artisan command
docker-compose exec app php artisan [command]

# Examples:
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
docker-compose exec app php artisan tinker
docker-compose exec app php artisan route:list
docker-compose exec app php artisan queue:work
```

### Accessing Container Shell

```bash
# Access Laravel app container
docker-compose exec app bash

# Access MySQL container
docker-compose exec mysql bash

# Access as root
docker-compose exec -u root app bash
```

### Database Backup

```bash
# Create backup
docker-compose exec mysql mysqldump -u root -proot_password employees > backup_$(date +%Y%m%d).sql

# Restore backup
docker-compose exec -T mysql mysql -u root -proot_password employees < backup_20260213.sql
```

### Viewing Logs

```bash
# All containers
docker-compose logs

# Specific container
docker-compose logs app
docker-compose logs mysql

# Follow logs in real-time
docker-compose logs -f app

# Last 100 lines
docker-compose logs --tail=100 app
```

### Installing Additional PHP Packages

```bash
# Access container
docker-compose exec app bash

# Install package
composer require vendor/package

# Update dependencies
composer update

# Exit
exit
```

### Performance Optimization

```bash
# Enable Laravel caching
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

# Enable OPcache (already configured in php.ini)

# Use production build
docker-compose build --no-cache app
```

---

## 🛠️ Maintenance

### Starting Containers

```bash
# Start all containers
docker-compose up -d

# Start specific container
docker-compose up -d app
```

### Stopping Containers

```bash
# Stop all containers
docker-compose stop

# Stop specific container
docker-compose stop app
```

### Restarting Containers

```bash
# Restart all containers
docker-compose restart

# Restart specific container
docker-compose restart app
```

### Updating the Application

```bash
# Pull latest changes
git pull origin main

# Rebuild containers
docker-compose build --no-cache

# Restart with new build
docker-compose up -d --force-recreate
```

### Removing Containers

```bash
# Stop and remove containers (data persists)
docker-compose down

# Remove containers and volumes (⚠️ destroys database)
docker-compose down -v

# Remove everything including images
docker-compose down -v --rmi all
```

### Clearing Docker Cache

```bash
# Remove unused images
docker image prune -a

# Remove unused volumes
docker volume prune

# Remove everything unused
docker system prune -a --volumes
```

---

## 🔒 Security Considerations

### Production Deployment

**Before deploying to production:**

1. **Change Default Passwords**

   ```yaml
   MYSQL_ROOT_PASSWORD: use_strong_password_here
   MYSQL_PASSWORD: use_another_strong_password
   ```

2. **Disable Debug Mode**

   ```env
   APP_DEBUG=false
   APP_ENV=production
   ```

3. **Use HTTPS**
   - Add SSL/TLS certificates
   - Configure Nginx for HTTPS
   - Redirect HTTP to HTTPS

4. **Disable PHPMyAdmin**

   ```bash
   # Comment out phpmyadmin service in docker-compose.yml
   ```

5. **Restrict Database Access**

   ```yaml
   # Don't expose MySQL port in production
   # Remove: "3306:3306"
   ```

6. **Use Environment Variables**
   - Store sensitive data in `.env` file
   - Never commit `.env` to Git
   - Use Docker secrets for production

7. **Enable Firewall**
   - Only expose necessary ports
   - Use reverse proxy (Nginx, Traefik)

8. **Regular Updates**
   ```bash
   # Update base images regularly
   docker-compose pull
   docker-compose up -d
   ```

### Access Control

```bash
# Restrict container network access
# Use custom networks in docker-compose.yml

# Implement authentication in Laravel
# Use Laravel Sanctum or Passport

# Enable Laravel's built-in security features
# CSRF protection, XSS prevention, etc.
```

---

## 📞 Support & Resources

### Official Documentation

- **Docker**: https://docs.docker.com/
- **Docker Compose**: https://docs.docker.com/compose/
- **Laravel**: https://laravel.com/docs
- **MySQL**: https://dev.mysql.com/doc/
- **Nginx**: https://nginx.org/en/docs/

### Useful Commands Reference

```bash
# Container Management
docker-compose up -d              # Start containers
docker-compose down               # Stop and remove containers
docker-compose ps                 # List running containers
docker-compose logs -f            # View logs

# Application Management
docker-compose exec app php artisan migrate    # Run migrations
docker-compose exec app php artisan cache:clear # Clear cache
docker-compose exec app composer install       # Install dependencies

# Database Management
docker-compose exec mysql mysql -u root -proot_password  # MySQL CLI
docker-compose exec mysql mysqldump -u root -proot_password employees > backup.sql

# System Cleanup
docker-compose down -v            # Remove with volumes
docker system prune -a            # Clean everything
```

### Getting Help

- **Project Issues**: https://github.com/dawidolko/Employees-List-Project-Laravel/issues
- **Docker Community**: https://forums.docker.com/
- **Laravel Community**: https://laracasts.com/discuss

---

## ✅ Verification Checklist

After installation, verify everything works:

- [ ] Containers are running: `docker-compose ps`
- [ ] Application accessible at http://localhost:8000
- [ ] PHPMyAdmin accessible at http://localhost:8080
- [ ] Database has ~300,000 employees: `SELECT COUNT(*) FROM employees;`
- [ ] Can view employee list on main page
- [ ] Can apply filters (department, salary, gender)
- [ ] Can export data to CSV
- [ ] No errors in logs: `docker-compose logs`

---

## 📝 Notes

- **First startup** takes longer (2-5 minutes) due to database import
- **Database import** includes ~300,000 employees and ~2.8M salary records
- **Total storage** usage: approximately 3-4GB
- **Default timezone**: UTC (configurable in php.ini)
- **PHP version**: 8.2
- **MySQL version**: 8.0
- **Laravel version**: Check composer.json

---

## 🎉 Success!

If you've completed all steps successfully, you now have a fully functional Employee Directory application running in Docker containers!

**What's next?**

1. Explore the employee directory
2. Test advanced filtering features
3. Export employee data to CSV
4. Customize the application
5. Deploy to production (see Security Considerations)

---

## 📄 License

This project is open source and available under the MIT License.

## 👨‍💻 Author

Created by **Dawid Olko** - Docker configuration and deployment automation.

---

**Happy Coding! 🚀**
