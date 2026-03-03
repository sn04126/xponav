@echo off
color 0A
title XpoNav - Laravel Backend
cd /d "%~dp0"

REM --- Auto-add XAMPP PHP to PATH for this session ---
set "PATH=C:\xampp\php;C:\xampp\mysql\bin;%PATH%"

cls
echo.
echo  ============================================================
echo   XpoNav - Laravel Backend Starter
echo  ============================================================
echo.
echo  PREREQUISITES:
echo    1. XAMPP must be installed at C:\xampp
echo    2. MySQL must be running (open XAMPP and start MySQL)
echo    3. .env file must exist in this folder with DB credentials
echo.
echo  TROUBLESHOOTING:
echo    - If DB error, open XAMPP Control Panel and start MySQL
echo    - If port busy, close other servers using port 8000
echo    - Check storage\logs\laravel.log for errors
echo.
echo  ============================================================
echo.

REM --- Check PHP ---
echo  [1/4] Checking PHP...
php -v >nul 2>&1
if %errorlevel% neq 0 (
    color 0C
    echo.
    echo  [ERROR] PHP not found at C:\xampp\php
    echo  Make sure XAMPP is installed at C:\xampp
    echo.
    pause
    exit /b 1
)
for /f "delims=" %%v in ('php -r "echo PHP_VERSION;"') do echo        Found PHP %%v
echo.

REM --- Check .env ---
echo  [2/4] Checking .env file...
if not exist ".env" (
    color 0C
    echo.
    echo  [ERROR] .env file not found!
    echo.
    echo  HOW TO FIX:
    echo    1. Copy .env.example to .env
    echo    2. Update database credentials in .env
    echo    3. Run: php artisan key:generate
    echo    4. Run START.bat again
    echo.
    pause
    exit /b 1
)
echo        Found .env
echo.

REM --- Sync storage to public ---
echo  [3/5] Syncing storage files...
if not exist "public\storage" mkdir "public\storage" >nul 2>&1
xcopy "storage\app\public\*" "public\storage\" /E /Y /Q >nul 2>&1
echo        Done.
echo.

REM --- Clear Caches ---
echo  [4/5] Clearing caches...
php artisan config:clear >nul 2>&1
php artisan cache:clear >nul 2>&1
php artisan route:clear >nul 2>&1
php artisan view:clear >nul 2>&1
echo        Done.
echo.

REM --- Start Server ---
echo  [5/5] Starting server...
echo.
echo  ============================================================
echo.
echo   SERVER RUNNING!
echo.
echo   Admin Panel : http://192.168.1.3:8000/admin
echo   Admin Login : admin@xponav.com / admin123
echo.
echo   API Base    : http://192.168.1.3:8000/api
echo   Test User   : test@test.com / password123
echo.
echo   Verify Codes: storage\logs\verification_codes.txt
echo.
echo   Press Ctrl+C to stop the server.
echo.
echo  ============================================================
echo.

php artisan serve --host=0.0.0.0 --port=8000

REM --- If server stops or fails, don't close ---
echo.
color 0C
echo  ============================================================
echo   SERVER STOPPED OR FAILED TO START
echo  ============================================================
echo.
echo  COMMON FIXES:
echo    - Make sure MySQL is running in XAMPP Control Panel
echo    - Check if port 8000 is already in use:
echo        netstat -ano | findstr :8000
echo    - Check storage\logs\laravel.log for errors
echo.
pause
