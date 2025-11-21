@echo off
setlocal

:: Configuración
set MYSQL_PATH=C:\xampp\mysql\bin
set USER=root
set PASSWORD=
set DATABASE=bcs_floragames
set BACKUP_DIR=C:\xampp\htdocs\BCS_FloraGames\config\backups
set PORT=3309

:: Si password está vacío, no usar -p
if "%PASSWORD%"=="" (
    set PASS_PARAM=
) else (
    set PASS_PARAM=-p%PASSWORD%
)

:: Fecha actual (YYYY-MM-DD)
for /f "tokens=1-3 delims=/" %%a in ("%date%") do (
    set YYYY=%%c
    set MM=%%a
    set DD=%%b
)
set DATE=%YYYY%-%MM%-%DD%

:: Archivo respaldo
set FILE=%BACKUP_DIR%\%DATABASE%_%DATE%.sql

:: Crear respaldo
"%MYSQL_PATH%\mysqldump.exe" -u %USER% %PASS_PARAM% --port=%PORT% %DATABASE% > "%FILE%"

echo Backup creado: %FILE%

:: Eliminar archivos con más de 7 días
forfiles /p "%BACKUP_DIR%" /m *.sql /d -7 /c "cmd /c del @file"

echo Backups mayores a 7 días eliminados.

endlocal
exit
