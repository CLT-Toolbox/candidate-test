@echo off
REM Start Laravel + Vite Development Server
REM Run both servers concurrently

color 0A
title Candidate Test - Development Server

echo.
echo ===============================================
echo  CANDIDATE TEST PROJECT - Development Server
echo ===============================================
echo.
echo Starting Laravel and Vite development servers...
echo.
echo Laravel will run on: http://localhost:8000
echo Vite will run on: http://localhost:5173
echo.
echo Press CTRL+C to stop the servers
echo ===============================================
echo.

npm run start

pause
