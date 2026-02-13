# Docker Setup - Frequently Asked Questions (FAQ)

**Docker Project Name**: `employeeslist-project`

All containers and resources are prefixed with this name for easy identification.

## General Questions

### What does this Docker setup include?

The Docker setup includes:

- **Laravel 10+ Application** with PHP 8.2-FPM
- **MySQL 8.0 Database** with the Employees sample database (~300k employees)
- **Nginx Web Server** for serving the application
- **PHPMyAdmin** for database management
- **Supervisor** for process management
- Automatic database import and Laravel configuration
- **Named containers**: `employeeslist-mysql`, `employeeslist-app`, `employeeslist-phpmyadmin`

### How much space does it require?

- Docker images: ~2GB
- Database data: ~1.5GB
- Application storage: ~500MB
- **Total**: Approximately 4-5GB

### How long does the first setup take?

- Image download and build: 5-10 minutes
- Database import: 2-5 minutes
- Total first run: **7-15 minutes**

Subsequent starts are much faster (30-60 seconds).

---

## Installation & Setup

### Do I need to install PHP, Composer, or MySQL on my machine?

No! Everything runs inside Docker containers. You only need:

- Docker Desktop
- Git (for cloning the repository)

### Can I use this on Windows?

Yes! The setup works on:

- Windows 10/11 with WSL2 (recommended)
- macOS (Intel and Apple Silicon/M1/M2/M3)
- Linux (all major distributions)

Use `manage.bat` on Windows or `manage.sh` on macOS/Linux.

**Note for Apple Silicon (M1/M2/M3):**
PHPMyAdmin runs with `platform: linux/amd64` for compatibility. This is normal and works perfectly.

### The installation failed. What should I check?

1. **Docker is running**: `docker info`
2. **Ports are free**: 8000, 3307 (MySQL), 8080
   ```bash
   lsof -i :3307  # Docker MySQL port
   lsof -i :8000
   lsof -i :8080
   ```
3. **Enough disk space**: At least 5GB free
4. **Enough RAM**: At least 4GB allocated to Docker
5. **Check logs**: `docker-compose logs`

**Note:** This project uses port **3307** for MySQL (not 3306) to avoid conflicts with local MySQL installations.

### How do I know when the setup is complete?

Watch the logs:

```bash
docker-compose logs -f app
```

Look for:

- "Application setup completed!"
- "Starting services..."

Also check containers status:

```bash
docker-compose ps
```

All should show "Up" or "Up (healthy)".

---

## Database Questions

### Where is the employee data?

The employee data is automatically imported from `/database/employees.sql` during the first container startup. It contains:

- 300,024 employees
- 9 departments
- ~2.8 million salary records
- ~440,000 title records

### How do I verify the database was imported correctly?

```bash
docker-compose exec mysql mysql -u root -proot_password -e "USE employees; SELECT COUNT(*) FROM employees;"
```

Should return: `300024`

### Can I access the database from outside Docker?

Yes! The MySQL port is exposed:

- **Host**: localhost
- **Port**: 3307
- **Username**: laravel_user (or root)
- **Password**: laravel_password (or root_password)

Use tools like MySQL Workbench, DBeaver, or PHPMyAdmin (http://localhost:8080).

### How do I backup the database?

```bash
# Using management script
./manage.sh backup

# Manual backup
docker-compose exec mysql mysqldump -u root -proot_password employees > backup.sql
```

### How do I restore a backup?

```bash
# Using management script
./manage.sh restore backup.sql

# Manual restore
docker-compose exec -T mysql mysql -u root -proot_password employees < backup.sql
```

### The database import is very slow. Is this normal?

Yes! The employees database is large:

- ~300k employee records
- ~2.8M salary records
- Multiple indexes and foreign keys

First import takes 2-5 minutes depending on your system. Be patient!

---

## Application Questions

### Why do I get a 500 error when accessing the app?

Common causes:

1. **Database not ready yet** - Wait a few more minutes
2. **No APP_KEY** - Check logs for key generation
3. **Permission issues** - Run: `docker-compose exec app chown -R www-data:www-data storage`
4. **Cache issues** - Clear cache: `docker-compose exec app php artisan config:clear`

Check logs: `docker-compose logs app`

### How do I run Laravel artisan commands?

```bash
# Using management script
./manage.sh artisan migrate
./manage.sh artisan tinker

# Direct access
docker-compose exec app php artisan [command]
```

### How do I install additional PHP packages?

```bash
docker-compose exec app composer require vendor/package
```

Then rebuild the container to make it permanent:

```bash
docker-compose build app
docker-compose up -d
```

### Can I modify the Laravel code?

Yes! The `backend/` directory is mounted as a volume. Changes to PHP files are reflected immediately (no rebuild needed).

For configuration changes:

```bash
docker-compose exec app php artisan config:cache
```

### How do I enable debug mode?

Edit `backend/.env`:

```env
APP_DEBUG=true
```

Restart:

```bash
docker-compose restart app
```

**Warning**: Never enable debug in production!

---

## Port & Access Questions

### Port 8000 is already in use. Can I change it?

Yes! Edit `docker-compose.yml`:

```yaml
app:
  ports:
    - "8001:80" # Change 8000 to any free port
```

Then: `docker-compose up -d`

Access at: http://localhost:8001

### Can I use a custom domain?

Yes! Add to your `/etc/hosts`:

```
127.0.0.1  employees.local
```

Edit `nginx.conf`:

```nginx
server_name employees.local;
```

Access at: http://employees.local:8000

### Can I access the app from another computer on my network?

Yes! Use your computer's IP address:

```bash
# Find your IP
ipconfig getifaddr en0  # macOS
ip addr show           # Linux
ipconfig               # Windows
```

Access from other devices: `http://YOUR_IP:8000`

**Note**: Ensure your firewall allows connections on port 8000.

---

## Performance Questions

### The application is slow. How can I improve performance?

1. **Increase Docker resources**:
   - Docker Desktop → Settings → Resources
   - RAM: 4GB minimum, 8GB recommended
   - CPUs: 2 minimum, 4 recommended

2. **Enable Laravel caching**:

   ```bash
   docker-compose exec app php artisan config:cache
   docker-compose exec app php artisan route:cache
   docker-compose exec app php artisan view:cache
   ```

3. **Enable OPcache**: Already configured in `php.ini`

4. **Use production build**: Set `APP_ENV=production` in `.env`

### Database queries are slow. What can I do?

1. The database already has indexes. Check query execution:

   ```sql
   EXPLAIN SELECT ...;
   ```

2. Increase MySQL buffer pool (edit `docker-compose.yml`):

   ```yaml
   command: --innodb_buffer_pool_size=1G
   ```

3. Use Laravel query optimization:
   - Use eager loading: `with()`
   - Limit results: `take()`, `limit()`
   - Add indexes to frequently queried columns

---

## Troubleshooting

### Containers keep restarting

Check logs for errors:

```bash
docker-compose logs
```

Common causes:

- Database connection failure
- Missing environment variables
- Port conflicts
- Insufficient memory

### "No space left on device" error

Clean Docker:

```bash
docker system prune -a --volumes
```

**Warning**: This removes all unused containers, images, and volumes!

### Permission denied errors

Fix Laravel storage permissions:

```bash
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

### MySQL container won't start

Common issues:

1. **Port conflict**: Check if port 3307 is in use

   ```bash
   lsof -i :3307  # Find process using port
   # If needed, change port in docker-compose.yml
   ```

2. **Corrupted data**:
   ```bash
   docker-compose down -v  # Remove volumes
   docker-compose up -d    # Fresh start
   ```

### PHPMyAdmin shows "Connection refused"

Wait for MySQL to be healthy:

```bash
docker-compose ps mysql
```

Should show: `Up (healthy)`

If not, check MySQL logs:

```bash
docker-compose logs mysql
```

---

## Maintenance Questions

### How do I update the application?

```bash
git pull origin main
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

### How do I completely reset everything?

```bash
# Using management script
./manage.sh clean

# Manual cleanup
docker-compose down -v
docker system prune -a
```

**Warning**: This removes all data!

### Do I need to backup before stopping containers?

No! Data persists in Docker volumes. Stopping containers is safe.

But you should backup before:

- Running `docker-compose down -v` (removes volumes)
- Running `./manage.sh clean`
- Major updates

### How much disk space do logs use?

Docker logs can grow large. To limit:

Edit `/etc/docker/daemon.json`:

```json
{
  "log-driver": "json-file",
  "log-opts": {
    "max-size": "10m",
    "max-file": "3"
  }
}
```

Restart Docker Desktop to apply.

---

## Security Questions

### Is this setup secure for production?

**No!** Default configuration is for development only.

For production:

1. Change all default passwords
2. Disable PHPMyAdmin or restrict access
3. Don't expose MySQL port externally in production (remove port mapping)
4. Use HTTPS with SSL/TLS certificates
5. Set `APP_DEBUG=false`
6. Use environment-specific secrets
7. Enable firewall rules
8. Regular security updates

### Should I commit the .env file to Git?

**Never!** The `.env` file contains sensitive credentials.

Always use `.env.example` as a template.

### How do I use custom credentials?

Edit `docker-compose.yml` before first run:

```yaml
environment:
  MYSQL_ROOT_PASSWORD: your_secure_password
  MYSQL_USER: your_username
  MYSQL_PASSWORD: your_password
```

---

## Advanced Questions

### Can I use Redis for caching?

Yes! Add to `docker-compose.yml`:

```yaml
redis:
  image: redis:alpine
  ports:
    - "6379:6379"
  networks:
    - employeeslist-network
```

Update `backend/.env`:

```env
CACHE_DRIVER=redis
REDIS_HOST=redis
```

### Can I add more services?

Yes! Edit `docker-compose.yml` to add:

- Redis for caching
- Mailhog for email testing
- Elasticsearch for search
- Queue workers
- Etc.

### How do I run tests?

```bash
docker-compose exec app php artisan test
# or
docker-compose exec app ./vendor/bin/phpunit
```

### Can I use this with CI/CD?

Yes! Example for GitHub Actions:

```yaml
steps:
  - uses: actions/checkout@v2
  - name: Build and test
    run: |
      cd .tools/docker
      docker-compose up -d
      docker-compose exec -T app php artisan test
```

### How do I debug PHP code?

1. Install Xdebug in Dockerfile
2. Configure Xdebug in php.ini
3. Set up IDE (VS Code, PhpStorm)
4. Rebuild container

See Laravel debugging documentation.

---

## Getting More Help

### Where can I find more documentation?

- [Full README](README.md) - Complete installation guide
- [Quick Start](QUICKSTART.md) - Quick reference
- [Laravel Docs](https://laravel.com/docs) - Laravel framework
- [Docker Docs](https://docs.docker.com/) - Docker reference

### Report Issues

Found a bug? Have suggestions?

Open an issue: https://github.com/dawidolko/Employees-List-Project-Laravel/issues

### Useful Commands Cheat Sheet

```bash
# Start everything
./manage.sh start

# Stop everything
./manage.sh stop

# View logs
./manage.sh logs

# View status
./manage.sh status

# Restart
./manage.sh restart

# Backup database
./manage.sh backup

# Run artisan
./manage.sh artisan migrate

# Clean everything
./manage.sh clean

# Help
./manage.sh help
```

---

**Still having issues?** Check the logs first:

```bash
docker-compose logs
```

Most problems are solved by reading the log messages carefully!
