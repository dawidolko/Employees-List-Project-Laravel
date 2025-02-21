#!/bin/bash

echo "Zabijanie działających procesów mysqld..."
sudo pkill mysqld

echo "Uruchamiam mysqld_safe w trybie skip-grant-tables..."
/opt/homebrew/opt/mysql/bin/mysqld_safe --skip-grant-tables --socket=/tmp/mysql.sock &
sleep 5

echo "Resetowanie hasła użytkownika root..."
/opt/homebrew/opt/mysql/bin/mysql --socket=/tmp/mysql.sock -u root <<EOF
FLUSH PRIVILEGES;
ALTER USER 'root'@'localhost' IDENTIFIED BY '';
EXIT;
EOF

echo "Zabijanie mysqld_safe..."
sudo pkill mysqld
sleep 3

echo "Uruchamiam MySQL normalnie..."
/opt/homebrew/opt/mysql/bin/mysql.server start
sleep 5

echo "Importowanie pliku employees.sql do bazy 'employees'..."
/opt/homebrew/opt/mysql/bin/mysql --socket=/tmp/mysql.sock -u root -p employees < employees.sql
# /opt/homebrew/opt/mysql/bin/mysql --socket=/tmp/mysql.sock -u root -p

echo "Gotowe!"
