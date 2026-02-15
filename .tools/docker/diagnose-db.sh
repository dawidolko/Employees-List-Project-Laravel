#!/bin/bash

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}=========================================${NC}"
echo -e "${BLUE}  Database Diagnostics Tool             ${NC}"
echo -e "${BLUE}=========================================${NC}"
echo ""

# Check if containers are running
echo -e "${YELLOW}1. Checking container status...${NC}"
docker-compose ps

echo ""
echo -e "${YELLOW}2. Checking MySQL container health...${NC}"
MYSQL_HEALTH=$(docker inspect --format='{{.State.Health.Status}}' employeeslist-mysql 2>/dev/null || echo "not running")
echo "MySQL health status: $MYSQL_HEALTH"

if [ "$MYSQL_HEALTH" != "healthy" ]; then
    echo -e "${RED}✗ MySQL container is not healthy!${NC}"
    echo "Please wait for MySQL to become healthy or restart containers."
    exit 1
fi

echo ""
echo -e "${YELLOW}3. Checking if database file exists in container...${NC}"
docker-compose exec -T app ls -lh /tmp/database/employees.sql 2>/dev/null || echo "File not found or not accessible"

echo ""
echo -e "${YELLOW}4. Checking database tables...${NC}"
echo "Tables in 'employees' database:"
docker-compose exec -T mysql mysql -u root -proot_password -e "SHOW TABLES FROM employees;" 2>/dev/null || echo "Cannot connect to database"

echo ""
echo -e "${YELLOW}5. Checking database content...${NC}"

# Get counts
EMP_COUNT=$(docker-compose exec -T mysql mysql -u root -proot_password -se "SELECT COUNT(*) FROM employees.employees;" 2>/dev/null || echo "0")
DEPT_COUNT=$(docker-compose exec -T mysql mysql -u root -proot_password -se "SELECT COUNT(*) FROM employees.departments;" 2>/dev/null || echo "0")
DEPT_EMP_COUNT=$(docker-compose exec -T mysql mysql -u root -proot_password -se "SELECT COUNT(*) FROM employees.dept_emp;" 2>/dev/null || echo "0")
DEPT_MGR_COUNT=$(docker-compose exec -T mysql mysql -u root -proot_password -se "SELECT COUNT(*) FROM employees.dept_manager;" 2>/dev/null || echo "0")
SALARIES_COUNT=$(docker-compose exec -T mysql mysql -u root -proot_password -se "SELECT COUNT(*) FROM employees.salaries;" 2>/dev/null || echo "0")
TITLES_COUNT=$(docker-compose exec -T mysql mysql -u root -proot_password -se "SELECT COUNT(*) FROM employees.titles;" 2>/dev/null || echo "0")

echo "Record counts:"
echo "  - Employees: $EMP_COUNT"
echo "  - Departments: $DEPT_COUNT"
echo "  - Department-Employee relations: $DEPT_EMP_COUNT"
echo "  - Department Managers: $DEPT_MGR_COUNT"
echo "  - Salaries: $SALARIES_COUNT"
echo "  - Titles: $TITLES_COUNT"

echo ""
if [ "$DEPT_COUNT" = "0" ] || [ "$DEPT_COUNT" = "" ]; then
    echo -e "${RED}✗ PROBLEM: No departments found in database!${NC}"
    echo ""
    echo "Possible solutions:"
    echo "  1. Run reset script: ./reset-docker.sh"
    echo "  2. Check if /database/employees.sql exists in your project"
    echo "  3. Check app logs: docker-compose logs app"
else
    echo -e "${GREEN}✓ Database appears to be properly populated${NC}"
fi

echo ""
echo -e "${YELLOW}6. Sample departments:${NC}"
docker-compose exec -T mysql mysql -u root -proot_password -e "SELECT * FROM employees.departments LIMIT 5;" 2>/dev/null || echo "Cannot query departments"

echo ""
echo -e "${YELLOW}7. Laravel database connection test...${NC}"
docker-compose exec app php artisan tinker --execute="DB::connection()->getPdo(); echo 'Connection successful!';" 2>/dev/null || echo "Connection test failed"

echo ""
echo -e "${BLUE}=========================================${NC}"
echo -e "${BLUE}  Diagnostics complete                  ${NC}"
echo -e "${BLUE}=========================================${NC}"