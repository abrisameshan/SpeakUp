@echo off
title SpeakUp Launcher
echo ====================================================
echo        Menjalankan Aplikasi SpeakUp Fullstack
echo ====================================================
echo.
echo 1. Menjalankan Backend Laravel (Port 8000)...
start "SpeakUp Backend API (Port 8000)" cmd /k "cd backend-api && php artisan serve --port=8000"

echo 2. Menjalankan Frontend Vue.js (Port 5173)...
start "SpeakUp Frontend Vue (Port 5173)" cmd /k "cd frontend-vue && npm run dev"

echo.
echo Kedua server sedang berjalan di jendela masing-masing!
echo Silakan buka browser kamu di: http://localhost:5173
echo.
pause
