@echo off
REM Docker Environment Reset Tool for Windows

SETLOCAL EnableDelayedExpansion

echo =========================================
echo   Docker Environment Reset Tool
echo =========================================
echo.

echo This script will:
echo   1. Stop all containers
echo   2. Remove all containers
echo   3. Remove all volumes (DATABASE WILL BE DELETED!)
echo   4. Rebuild containers from scratch
echo   5. Start fresh environment
echo.

echo [WARNING] ALL DATA IN DATABASE WILL BE LOST!
echo.

set /p CONFIRM="Are you sure you want to continue? (type 'yes' to confirm): "
if /i not "%CONFIRM%"=="yes" (
    echo Reset cancelled.
    exit /b 0
)

echo.
echo Step 1: Stopping containers...
docker-compose down

echo.
echo Step 2: Removing volumes...
docker-compose down -v

echo.
echo Step 3: Removing old images (optional cleanup)...
docker image prune -f

echo.
echo Step 4: Rebuilding containers...
docker-compose build --no-cache

echo.
echo Step 5: Starting fresh environment...
docker-compose up -d

echo.
echo =========================================
echo   Environment reset complete!
echo =========================================
echo.
echo Waiting for services to initialize (30 seconds)...
timeout /t 30 /nobreak >nul

echo.
echo Checking container status:
docker-compose ps

echo.
echo Checking last 20 lines of app logs:
docker-compose logs --tail=20 app

echo.
echo Access points:
echo   Application: http://localhost:8000
echo   PHPMyAdmin:  http://localhost:8080
echo.
echo If you see errors above, run:
echo   docker-compose logs -f app
echo.

ENDLOCAL