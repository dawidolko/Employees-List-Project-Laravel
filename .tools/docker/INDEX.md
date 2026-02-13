# Docker Setup - File Index

This directory contains all necessary files for Docker-based deployment of the Employees List Project.

**Docker Project Name**: `employeeslist-project`

All containers, networks, and volumes are prefixed with this project name.

## 📁 Directory Structure

```
.tools/docker/
├── 📋 README.md              Main installation guide (detailed manual)
├── 🚀 QUICKSTART.md          Quick reference guide
├── ❓ FAQ.md                 Frequently Asked Questions
├── 🏗️ ARCHITECTURE.md        System architecture diagrams
├── 📑 INDEX.md               This file - complete file index
│
├── 🐳 docker-compose.yml     Multi-container orchestration
├── 📦 Dockerfile             Laravel application container
├── 🚫 .dockerignore          Files to exclude from Docker build
│
├── ⚙️ nginx.conf            Nginx web server configuration
├── ⚙️ php.ini               PHP runtime settings
├── ⚙️ supervisord.conf      Process manager configuration
│
├── 🗄️ mysql-init.sh         Database initialization script
├── 🔧 .env.docker           Environment configuration template
│
├── 🛠️ manage.sh             Management script (Linux/macOS)
└── 🛠️ manage.bat            Management script (Windows)
```

---

## 📄 File Descriptions

### Documentation Files

#### [`README.md`](README.md)

**Purpose**: Complete installation and setup guide

**Contains**:

- Detailed step-by-step installation instructions
- Configuration options
- Troubleshooting guide
- Security considerations
- Maintenance procedures
- Advanced usage examples

**When to use**:

- First-time setup
- Detailed configuration
- Troubleshooting issues
- Learning about the system

---

#### [`QUICKSTART.md`](QUICKSTART.md)

**Purpose**: Fast reference for experienced users

**Contains**:

- Condensed installation steps
- Quick commands reference
- Common tasks
- Essential troubleshooting

**When to use**:

- Quick setup
- Command reference
- Fast lookups

---

#### [`FAQ.md`](FAQ.md)

**Purpose**: Common questions and answers

**Contains**:

- General questions
- Installation issues
- Database questions
- Performance tips
- Security concerns
- Troubleshooting

**When to use**:

- Looking for specific answers
- Troubleshooting problems
- Understanding concepts
- Before asking for help

---

#### [`ARCHITECTURE.md`](ARCHITECTURE.md)

**Purpose**: System architecture documentation

**Contains**:

- Architecture diagrams (Mermaid)
- Container communication flow
- Data flow architecture
- Network architecture
- Deployment process
- Scaling possibilities

**When to use**:

- Understanding system design
- Planning modifications
- Learning Docker concepts
- Troubleshooting complex issues

---

#### [`INDEX.md`](INDEX.md)

**Purpose**: This file - complete file directory

**Contains**: Description of all files in this directory

**When to use**: Understanding what each file does

---

### Configuration Files

#### [`docker-compose.yml`](docker-compose.yml)

**Purpose**: Multi-container application orchestration

**Project Name**: `employeeslist-project`

This name identifies the Docker project and prefixes all containers, networks, and volumes.

**Defines**:

- **Services**:
  - `mysql` - MySQL 8.0 database
  - `app` - Laravel application with Nginx and PHP-FPM
  - `phpmyadmin` - Database management interface
- **Networks**: `employeeslist-network` (bridge)
- **Volumes**: `mysql_data`, `storage_data`
- **Environment variables** for all services
- **Port mappings**: 8000 (app), 3306 (mysql), 8080 (phpmyadmin)
- **Health checks** and dependencies
- **Startup commands** and initialization
- **Container names**:
  - `employeeslist-mysql` - MySQL database server
  - `employeeslist-app` - Laravel web application
  - `employeeslist-phpmyadmin` - Database admin interface

**Key Features**:

- Automatic database import
- Laravel auto-configuration
- Service dependencies with health checks
- Data persistence with volumes

**Customization Points**:

- Change ports (e.g., 8000 → 8001)
- Modify database credentials
- Add/remove services (Redis, Mailhog, etc.)
- Adjust resource limits

---

#### [`Dockerfile`](Dockerfile)

**Purpose**: Define Laravel application container image

**Base Image**: `php:8.2-fpm`

**Installs**:

- System dependencies (git, curl, libraries)
- PHP extensions (pdo_mysql, gd, zip, bcmath, etc.)
- Composer (latest version)
- Nginx web server
- Supervisor process manager

**Configuration**:

- Sets working directory: `/var/www/html`
- Copies application code
- Installs PHP dependencies
- Sets proper permissions for Laravel
- Configures Nginx and Supervisor
- Exposes port 80

**Build Process**:

1. Install system packages
2. Install PHP extensions
3. Install Composer
4. Copy and install PHP dependencies
5. Copy application files
6. Set permissions
7. Configure services

**Optimization**:

- Multi-stage build structure
- Composer autoload optimization
- Minimal layer count
- Proper caching

---

#### [`.dockerignore`](.dockerignore)

**Purpose**: Exclude files from Docker build context

**Excludes**:

- `.env` files (secrets)
- `vendor/` and `node_modules/` (reinstalled in container)
- `storage/` contents (regenerated)
- IDE files (`.vscode/`, `.idea/`)
- Git repository (`.git/`)
- Tests and documentation
- Database dumps (imported separately)

**Benefits**:

- Faster build times
- Smaller build context
- Improved security (no secrets in image)
- Cleaner images

---

#### [`nginx.conf`](nginx.conf)

**Purpose**: Nginx web server configuration

**Configuration**:

- **Listen**: Port 80 (IPv4 and IPv6)
- **Root**: `/var/www/html/public` (Laravel public directory)
- **PHP Processing**: FastCGI to PHP-FPM on port 9000
- **Timeouts**: 300 seconds for long requests
- **Buffer Sizes**: 256MB for large uploads
- **Security Headers**: X-Frame-Options, X-XSS-Protection, etc.
- **Static Asset Caching**: 1 year for images, CSS, JS
- **Laravel Routing**: Try files then fallback to index.php

**Features**:

- Optimized for Laravel
- Large file upload support
- Security headers
- Static file caching
- Clean URLs

**Customization Points**:

- Change server name
- Adjust timeouts
- Modify buffer sizes
- Add SSL/TLS (HTTPS)

---

#### [`php.ini`](php.ini)

**Purpose**: PHP runtime configuration

**Key Settings**:

- **Memory**: 512M limit
- **Uploads**: 256M max file size
- **Execution**: 300s timeout
- **Errors**: Log to file, don't display
- **Timezone**: UTC
- **OPcache**: Enabled for performance
- **Security**: expose_php = Off

**Performance**:

- OPcache enabled with 256MB
- 10,000 max accelerated files
- Fast shutdown enabled

**Customization Points**:

- Increase memory for large operations
- Adjust upload limits
- Change timezone
- Enable/disable extensions

---

#### [`supervisord.conf`](supervisord.conf)

**Purpose**: Process manager configuration

**Manages**:

1. **PHP-FPM** (priority 5)
   - Runs PHP FastCGI Process Manager
   - Listens on port 9000
   - Auto-restart enabled

2. **Nginx** (priority 10)
   - Runs in foreground mode
   - Serves HTTP requests
   - Auto-restart enabled

**Features**:

- Automatic process restart
- Proper startup order (PHP-FPM before Nginx)
- Log output to stdout/stderr
- Runs as root (required for port 80)

**Why Supervisor?**

- Single container, multiple processes
- Automatic restart on failure
- Centralized log management
- Proper shutdown handling

---

#### [`.env.docker`](.env.docker)

**Purpose**: Environment variables template

**Contains**:

- Application settings (name, env, debug)
- Database connection (host, port, credentials)
- Cache and session drivers
- Mail configuration
- AWS settings
- Pusher configuration

**Usage**:
This is a template. The actual `.env` file is created automatically during container startup with proper database credentials from `docker-compose.yml`.

**Default Values**:

- `DB_HOST=mysql` (Docker service name)
- `DB_DATABASE=employees`
- `DB_USERNAME=laravel_user`
- `DB_PASSWORD=laravel_password`

---

### Scripts

#### [`mysql-init.sh`](mysql-init.sh)

**Purpose**: Initialize MySQL database on first startup

**Execution**: Automatically runs when MySQL container first starts

**Process**:

1. Wait for MySQL to be ready
2. Check for `employees.sql` file
3. Import main database file
4. Fallback: Import individual `.dump` files
5. Grant privileges to Laravel user
6. Report status

**Exit Codes**:

- Success: Database imported and ready
- Logs: Available via `docker-compose logs mysql`

**File Location**:

- Script: `/docker-entrypoint-initdb.d/00-init.sh`
- Data: `/docker-entrypoint-initdb.d/*.sql`

**Note**: Only runs on first container creation. To re-run, remove the MySQL volume:

```bash
docker-compose down -v
docker-compose up -d
```

---

#### [`manage.sh`](manage.sh)

**Purpose**: Management script for Linux/macOS

**Requirements**:

- Bash shell
- Docker and docker-compose installed
- Execute permissions (`chmod +x manage.sh`)

**Commands**:

- `start` - Start all containers
- `stop` - Stop all containers
- `restart` - Restart all containers
- `rebuild` - Rebuild containers from scratch
- `status` - Show container status
- `logs [service]` - View logs
- `backup` - Create database backup
- `restore <file>` - Restore database from backup
- `artisan [cmd]` - Run Laravel artisan command
- `clean` - Remove all containers and volumes
- `help` - Show help message

**Features**:

- Color-coded output
- Input validation
- Error handling
- Safety confirmations for destructive operations

**Examples**:

```bash
./manage.sh start
./manage.sh logs app
./manage.sh artisan migrate
./manage.sh backup
```

---

#### [`manage.bat`](manage.bat)

**Purpose**: Management script for Windows

**Requirements**:

- Windows Command Prompt or PowerShell
- Docker Desktop for Windows
- docker-compose available

**Commands**: Same as `manage.sh`

**Usage**: Same commands, different file:

```cmd
manage.bat start
manage.bat logs app
manage.bat artisan migrate
```

**Note**: Uses Windows batch scripting syntax. Functionality identical to Linux/macOS version.

---

## 🔄 File Relationships

```
docker-compose.yml
    ├── Uses: Dockerfile (to build app container)
    ├── Uses: nginx.conf (mounted into app container)
    ├── Uses: php.ini (mounted into app container)
    ├── Uses: supervisord.conf (mounted into app container)
    ├── Uses: mysql-init.sh (mounted into mysql container)
    └── References: .env.docker (template for environment)

Dockerfile
    ├── Copies: backend/ directory
    ├── Excludes: Files in .dockerignore
    └── Configured by: nginx.conf, php.ini, supervisord.conf

manage.sh / manage.bat
    └── Executes: docker-compose commands
```

---

## 📊 Typical Workflow

### Initial Setup

1. Read `README.md` for understanding
2. Review `docker-compose.yml` configuration
3. Customize settings if needed
4. Run `manage.sh start` or `docker-compose up -d`
5. Check `logs` for initialization progress

### Daily Development

1. `manage.sh start` - Start environment
2. Code in `backend/` - Changes reflected immediately
3. `manage.sh artisan <command>` - Run Laravel commands
4. `manage.sh logs` - Debug issues
5. `manage.sh stop` - Stop when done

### Troubleshooting

1. Check `FAQ.md` for common issues
2. View logs: `manage.sh logs`
3. Check status: `manage.sh status`
4. Refer to `README.md` troubleshooting section
5. Review `ARCHITECTURE.md` for understanding

### Maintenance

1. `manage.sh backup` - Regular backups
2. `manage.sh restart` - Apply configuration changes
3. `manage.sh rebuild` - Update to latest code
4. `manage.sh clean` - Full cleanup (careful!)

---

## 🔍 Quick Reference Table

| File                 | Primary Purpose         | Modify When...                                         |
| -------------------- | ----------------------- | ------------------------------------------------------ |
| `docker-compose.yml` | Container orchestration | Adding services, changing ports, environment variables |
| `Dockerfile`         | App container build     | Installing new dependencies, changing PHP version      |
| `nginx.conf`         | Web server config       | Adjusting upload limits, timeouts, SSL/HTTPS           |
| `php.ini`            | PHP runtime config      | Memory limits, execution time, timezone                |
| `supervisord.conf`   | Process management      | Adding background workers, changing priorities         |
| `mysql-init.sh`      | Database setup          | Custom database initialization logic                   |
| `.dockerignore`      | Build optimization      | Adding files to exclude from build                     |
| `.env.docker`        | Environment template    | Default configuration values                           |
| `manage.sh` / `.bat` | Daily operations        | Never (just use as-is)                                 |

---

## 📚 Where to Start?

### New to Docker?

1. Start with `README.md` - Read thoroughly
2. Follow installation step-by-step
3. Refer to `FAQ.md` when stuck
4. Review `ARCHITECTURE.md` to understand

### Experienced with Docker?

1. Quick scan of `QUICKSTART.md`
2. Review `docker-compose.yml`
3. Run `manage.sh start`
4. Customize as needed

### Want to Customize?

1. Understand current setup (this file)
2. Review `ARCHITECTURE.md`
3. Modify configuration files
4. Test with `docker-compose up`
5. Document your changes

---

## 🎯 Key Concepts

### Container Lifecycle

1. **Build**: `Dockerfile` → Docker Image
2. **Create**: Image → Container
3. **Start**: Container runs
4. **Stop**: Container pauses
5. **Remove**: Container deleted (data in volumes persists)

### Data Persistence

- **Volumes**: mysql_data, storage_data (persist between container restarts)
- **Mounts**: backend/ directory (live sync with host)

### Networking

- **Bridge Network**: employeeslist-network
- **Container Names**: Used as hostnames (e.g., `mysql`, `app`)
- **Port Mapping**: Container port → Host port

### Initialization Order

1. Network created
2. MySQL starts and imports database
3. MySQL health check passes
4. App container starts (waits for MySQL)
5. App initializes Laravel
6. PHPMyAdmin starts
7. All services ready

---

## 📝 Notes

- All scripts use UNIX line endings (LF)
- Configuration files are mounted as read-only where possible
- Logs are accessible via `docker-compose logs`
- Data persists in Docker volumes (survives container restarts)
- Containers can be rebuilt without losing database data

---

## 🆘 Need Help?

1. **Quick issue**: Check `FAQ.md`
2. **Setup problem**: See `README.md` Troubleshooting section
3. **Understanding architecture**: Read `ARCHITECTURE.md`
4. **Command reference**: Use `QUICKSTART.md`
5. **Still stuck**: Open GitHub issue

---

**Last Updated**: February 2026
**Version**: 1.0
