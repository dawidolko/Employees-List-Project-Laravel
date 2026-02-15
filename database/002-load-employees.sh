#!/bin/bash
set -e

echo "=========================================="
echo "Loading Employees Database"
echo "=========================================="

cd /docker-entrypoint-initdb.d

echo "Loading departments..."
mysql -u root -p"${MYSQL_ROOT_PASSWORD}" employees < load_departments.dump

echo "Loading employees..."
mysql -u root -p"${MYSQL_ROOT_PASSWORD}" employees < load_employees.dump

echo "Loading dept_emp..."
mysql -u root -p"${MYSQL_ROOT_PASSWORD}" employees < load_dept_emp.dump

echo "Loading dept_manager..."
mysql -u root -p"${MYSQL_ROOT_PASSWORD}" employees < load_dept_manager.dump

echo "Loading titles..."
mysql -u root -p"${MYSQL_ROOT_PASSWORD}" employees < load_titles.dump

echo "Loading salaries (1/3)..."
mysql -u root -p"${MYSQL_ROOT_PASSWORD}" employees < load_salaries1.dump

echo "Loading salaries (2/3)..."
mysql -u root -p"${MYSQL_ROOT_PASSWORD}" employees < load_salaries2.dump

echo "Loading salaries (3/3)..."
mysql -u root -p"${MYSQL_ROOT_PASSWORD}" employees < load_salaries3.dump

echo "=========================================="
echo "✓ Database loaded successfully!"
echo "=========================================="

EMPLOYEE_COUNT=$(mysql -u root -p"${MYSQL_ROOT_PASSWORD}" -se "SELECT COUNT(*) FROM employees.employees;")
DEPT_COUNT=$(mysql -u root -p"${MYSQL_ROOT_PASSWORD}" -se "SELECT COUNT(*) FROM employees.departments;")

echo "Final counts:"
echo "  - Employees: $EMPLOYEE_COUNT"
echo "  - Departments: $DEPT_COUNT"
