@echo off
title SpeakUp Launcher
cd /d "%~dp0"
echo ====================================================
echo        Menjalankan Aplikasi SpeakUp Fullstack
echo ====================================================
echo.
echo 1. Menjalankan Backend Laravel (Port 8000)...
start "SpeakUp Backend API" cmd /k "cd /d "%~dp0backend-api" && php artisan serve --host=127.0.0.1 --port=8000"

echo 2. Menjalankan Frontend Vue.js (Port 5173)...
start "SpeakUp Frontend Vue" cmd /k "cd /d "%~dp0frontend-vue" && npm run dev"

echo.
echo Kedua server sedang berjalan di jendela Command Prompt masing-masing!
echo Silakan buka browser di: http://localhost:5173
echo.
pause
