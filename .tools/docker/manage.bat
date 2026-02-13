@echo off
REM Docker Manager Script for Windows
REM Employees List Project - Laravel

SETLOCAL EnableDelayedExpansion

SET "SCRIPT_DIR=%~dp0"
cd /d "%SCRIPT_DIR%"

echo =========================================
echo   Employees Project - Docker Manager
echo =========================================
echo.

REM Function to check if Docker is running
docker info >nul 2>&1
if errorlevel 1 (
    echo [ERROR] Docker is not running!
    echo Please start Docker Desktop and try again.
    exit /b 1
)

REM Function to check if docker-compose is available
docker-compose version >nul 2>&1
if errorlevel 1 (
    echo [ERROR] docker-compose is not installed!
    echo Please install docker-compose and try again.
    exit /b 1
)

REM Parse command
if "%1"=="" goto :show_help
if "%1"=="start" goto :start_containers
if "%1"=="stop" goto :stop_containers
if "%1"=="restart" goto :restart_containers
if "%1"=="rebuild" goto :rebuild_containers
if "%1"=="status" goto :show_status
if "%1"=="logs" goto :show_logs
if "%1"=="backup" goto :backup_database
if "%1"=="restore" goto :restore_database
if "%1"=="artisan" goto :run_artisan
if "%1"=="clean" goto :cleanup
if "%1"=="help" goto :show_help
if "%1"=="-h" goto :show_help
if "%1"=="--help" goto :show_help

echo [ERROR] Unknown command: %1
echo.
goto :show_help

:start_containers
echo Starting containers...
docker-compose up -d
if errorlevel 1 (
    echo [ERROR] Failed to start containers
    exit /b 1
)
echo [SUCCESS] Containers started successfully!
echo.
call :show_status
echo.
echo Access points:
echo   Application: http://localhost:8000
echo   PHPMyAdmin:  http://localhost:8080
goto :eof

:stop_containers
echo Stopping containers...
docker-compose stop
if errorlevel 1 (
    echo [ERROR] Failed to stop containers
    exit /b 1
)
echo [SUCCESS] Containers stopped successfully!
goto :eof

:restart_containers
echo Restarting containers...
docker-compose restart
if errorlevel 1 (
    echo [ERROR] Failed to restart containers
    exit /b 1
)
echo [SUCCESS] Containers restarted successfully!
goto :eof

:rebuild_containers
echo [WARNING] This will rebuild all containers from scratch.
set /p CONFIRM="Continue? (y/n): "
if /i not "%CONFIRM%"=="y" (
    echo Rebuild cancelled.
    goto :eof
)
echo Rebuilding containers...
docker-compose down
docker-compose build --no-cache
docker-compose up -d
if errorlevel 1 (
    echo [ERROR] Failed to rebuild containers
    exit /b 1
)
echo [SUCCESS] Containers rebuilt successfully!
goto :eof

:show_status
docker-compose ps
goto :eof

:show_logs
if "%2"=="" (
    echo Showing logs for all containers...
    docker-compose logs -f
) else (
    echo Showing logs for %2...
    docker-compose logs -f %2
)
goto :eof

:backup_database
echo Creating database backup...
set BACKUP_FILE=backup_%date:~-4%%date:~-10,2%%date:~-7,2%_%time:~0,2%%time:~3,2%%time:~6,2%.sql
set BACKUP_FILE=%BACKUP_FILE: =0%
docker-compose exec -T mysql mysqldump -u root -proot_password employees > %BACKUP_FILE%
if errorlevel 1 (
    echo [ERROR] Failed to create backup
    exit /b 1
)
echo [SUCCESS] Database backup created: %BACKUP_FILE%
goto :eof

:restore_database
if "%2"=="" (
    echo [ERROR] Please provide backup file path
    echo Usage: %0 restore backup_file.sql
    exit /b 1
)
if not exist "%2" (
    echo [ERROR] Backup file not found: %2
    exit /b 1
)
echo Restoring database from %2...
docker-compose exec -T mysql mysql -u root -proot_password employees < "%2"
if errorlevel 1 (
    echo [ERROR] Failed to restore database
    exit /b 1
)
echo [SUCCESS] Database restored successfully!
goto :eof

:run_artisan
shift
set ARTISAN_CMD=
:artisan_loop
if "%1"=="" goto :artisan_exec
set ARTISAN_CMD=%ARTISAN_CMD% %1
shift
goto :artisan_loop

:artisan_exec
docker-compose exec app php artisan%ARTISAN_CMD%
goto :eof

:cleanup
echo [WARNING] This will remove all containers and volumes!
echo [WARNING] All data will be lost!
set /p CONFIRM="Are you sure? (y/n): "
if /i not "%CONFIRM%"=="y" (
    echo Cleanup cancelled.
    goto :eof
)
docker-compose down -v
echo [SUCCESS] Cleanup completed!
goto :eof

:show_help
echo Usage: %0 [command] [options]
echo.
echo Commands:
echo   start              Start all containers
echo   stop               Stop all containers
echo   restart            Restart all containers
echo   rebuild            Rebuild containers from scratch
echo   status             Show container status
echo   logs [service]     Show logs (all services or specific)
echo   backup             Backup database
echo   restore ^<file^>     Restore database from backup
echo   artisan [cmd]      Run Laravel artisan command
echo   clean              Remove all containers and volumes
echo   help               Show this help message
echo.
echo Examples:
echo   %0 start
echo   %0 logs app
echo   %0 artisan migrate
echo   %0 restore backup_20260213.sql
goto :eof

ENDLOCAL
