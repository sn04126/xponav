@echo off
color 0A
cls
echo.
echo  ============================================================
echo   XpoNav - Quick Start
echo  ============================================================
echo.
echo  This will setup and start your XpoNav system.
echo.
pause

cls
echo.
echo  [1/3] Clearing caches...
php artisan config:clear >nul 2>&1
php artisan cache:clear >nul 2>&1
echo  [OK]
echo.

echo  [2/3] Running migrations...
php artisan migrate:fresh --seed
echo  [OK]
echo.

cls
color 0A
echo.
echo  ============================================================
echo   READY!
echo  ============================================================
echo.
echo  Admin Panel: http://127.0.0.1:8000/admin/login
echo    Email: admin@xponav.com
echo    Password: admin123
echo.
echo  Unity Test User: test@test.com / password123
echo  Verification Codes: storage\logs\verification_codes.txt
echo.
echo  ============================================================
echo.
echo  [3/3] Starting server...
echo.

php artisan serve
