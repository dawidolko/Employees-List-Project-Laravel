#!/bin/bash
set -e

echo "========================================="
echo "Starting MySQL Database Initialization"
echo "========================================="

# Wait for MySQL to be fully ready
echo "Waiting for MySQL to be ready..."
until mysql -u root -p"${MYSQL_ROOT_PASSWORD}" -e "SELECT 1" &>/dev/null; do
  echo "MySQL is unavailable - waiting..."
  sleep 2
done

echo "MySQL is ready!"

# Import the employees database
echo "Importing employees database..."

# Check if employees.sql exists
if [ -f /docker-entrypoint-initdb.d/employees.sql ]; then
    echo "Found employees.sql, importing..."
    mysql -u root -p"${MYSQL_ROOT_PASSWORD}" "${MYSQL_DATABASE}" < /docker-entrypoint-initdb.d/employees.sql
    echo "employees.sql imported successfully!"
else
    echo "WARNING: employees.sql not found in /docker-entrypoint-initdb.d/"
    echo "Checking for individual dump files..."
    
    # Import individual dump files if they exist
    for dump_file in /docker-entrypoint-initdb.d/load_*.dump; do
        if [ -f "$dump_file" ]; then
            echo "Importing $(basename $dump_file)..."
            mysql -u root -p"${MYSQL_ROOT_PASSWORD}" "${MYSQL_DATABASE}" < "$dump_file"
        fi
    done
fi

# Grant privileges to Laravel user
echo "Granting privileges to Laravel user..."
mysql -u root -p"${MYSQL_ROOT_PASSWORD}" <<-EOSQL
    GRANT ALL PRIVILEGES ON ${MYSQL_DATABASE}.* TO '${MYSQL_USER}'@'%';
    FLUSH PRIVILEGES;
EOSQL

echo "========================================="
echo "Database initialization completed!"
echo "========================================="
