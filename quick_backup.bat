@echo off
echo 🏠 Property Scraper App - Quick Database Backup
echo ================================================
echo.

REM Create backup directory
if not exist "database_backups" mkdir "database_backups"

REM Get current timestamp
for /f "tokens=2 delims==" %%a in ('wmic OS Get localdatetime /value') do set "dt=%%a"
set "timestamp=%dt:~0,8%_%dt:~8,6%"

REM Create database backup
echo 📋 Creating database backup...
copy "database\database.sqlite" "database_backups\database_backup_%timestamp%.sqlite"
if %errorlevel% equ 0 (
    echo ✅ Database backup created successfully!
    echo    Backup: database_backups\database_backup_%timestamp%.sqlite
) else (
    echo ❌ Database backup failed!
    pause
    exit /b 1
)

REM Show backup info
echo.
echo 📊 Backup Information:
echo    Original: database\database.sqlite
echo    Backup: database_backups\database_backup_%timestamp%.sqlite
echo    Timestamp: %timestamp%
echo.
echo 🎉 Backup completed! You can now safely proceed with imports.
echo.
pause
