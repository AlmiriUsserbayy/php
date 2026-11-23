@echo off
chcp 65001 >nul
cd /d "%~dp0"
php -r "exit(extension_loaded('pdo_sqlite')?0:1);"
if errorlevel 1 (
  echo pdo_sqlite is not enabled. Run: php --ini , open php.ini and remove the semicolon before extension=pdo_sqlite
  pause
  exit /b 1
)
start "" cmd /c "timeout /t 2 >nul & start http://localhost:8000/items"
echo Server: http://localhost:8000/items  (Ctrl+C to stop)
php -S localhost:8000 -t public
