@echo off

echo Zabijanie działających procesów mysqld...
taskkill /F /IM mysqld.exe

echo Uruchamiam mysqld w trybie skip-grant-tables...
start "mysqld_skip" "C:\Program Files\MySQL\MySQL Server X.Y\bin\mysqld.exe" --skip-grant-tables --socket="C:\MySQL\mysql.sock"
timeout /T 5

echo Resetowanie hasła użytkownika root...
"C:\Program Files\MySQL\MySQL Server X.Y\bin\mysql.exe" --socket="C:\MySQL\mysql.sock" -u root -e "FLUSH PRIVILEGES; ALTER USER 'root'@'localhost' IDENTIFIED BY '';"
REM Jeśli chcesz ustawić inne hasło, zastąp pusty ciąg odpowiednią wartością.

echo Zabijanie mysqld (tryb skip-grant-tables)...
taskkill /F /IM mysqld.exe
timeout /T 3

echo Uruchamiam MySQL normalnie...
net start MySQL

timeout /T 5

echo Importowanie pliku employees.sql do bazy "employees"...
"C:\Program Files\MySQL\MySQL Server X.Y\bin\mysql.exe" --socket="C:\MySQL\mysql.sock" -u root -p employees < "C:\ścieżka\do\employees.sql"

echo Gotowe.
pause
