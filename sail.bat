@echo off
setlocal enabledelayedexpansion

REM Determine if Sail is currently up
docker compose ps | findstr "laravel.test" > nul
if %errorlevel% equ 0 (
    REM Sail is up, run command inside container
    docker compose exec laravel.test php artisan %*
) else (
    REM Sail is not up, run command using docker compose run
    docker compose run --rm laravel.test %*
)
