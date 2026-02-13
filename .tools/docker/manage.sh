#!/bin/bash

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Script directory
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

echo -e "${BLUE}=========================================${NC}"
echo -e "${BLUE}  Employees Project - Docker Manager   ${NC}"
echo -e "${BLUE}=========================================${NC}"
echo ""

# Function to check if Docker is running
check_docker() {
    if ! docker info > /dev/null 2>&1; then
        echo -e "${RED}Error: Docker is not running!${NC}"
        echo "Please start Docker Desktop and try again."
        exit 1
    fi
}

# Function to check if docker-compose is available
check_docker_compose() {
    if ! command -v docker-compose &> /dev/null; then
        echo -e "${RED}Error: docker-compose is not installed!${NC}"
        echo "Please install docker-compose and try again."
        exit 1
    fi
}

# Function to start containers
start_containers() {
    echo -e "${YELLOW}Starting containers...${NC}"
    cd "$SCRIPT_DIR"
    docker-compose up -d
    
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✓ Containers started successfully!${NC}"
        echo ""
        show_status
        echo ""
        echo -e "${GREEN}Access points:${NC}"
        echo -e "  Application: ${BLUE}http://localhost:8000${NC}"
        echo -e "  PHPMyAdmin:  ${BLUE}http://localhost:8080${NC}"
    else
        echo -e "${RED}✗ Failed to start containers${NC}"
        exit 1
    fi
}

# Function to stop containers
stop_containers() {
    echo -e "${YELLOW}Stopping containers...${NC}"
    cd "$SCRIPT_DIR"
    docker-compose stop
    
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✓ Containers stopped successfully!${NC}"
    else
        echo -e "${RED}✗ Failed to stop containers${NC}"
        exit 1
    fi
}

# Function to restart containers
restart_containers() {
    echo -e "${YELLOW}Restarting containers...${NC}"
    cd "$SCRIPT_DIR"
    docker-compose restart
    
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✓ Containers restarted successfully!${NC}"
    else
        echo -e "${RED}✗ Failed to restart containers${NC}"
        exit 1
    fi
}

# Function to rebuild containers
rebuild_containers() {
    echo -e "${YELLOW}Rebuilding containers...${NC}"
    echo -e "${RED}Warning: This will rebuild all containers from scratch.${NC}"
    read -p "Continue? (y/n) " -n 1 -r
    echo
    
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        cd "$SCRIPT_DIR"
        docker-compose down
        docker-compose build --no-cache
        docker-compose up -d
        
        if [ $? -eq 0 ]; then
            echo -e "${GREEN}✓ Containers rebuilt successfully!${NC}"
        else
            echo -e "${RED}✗ Failed to rebuild containers${NC}"
            exit 1
        fi
    else
        echo "Rebuild cancelled."
    fi
}

# Function to show container status
show_status() {
    cd "$SCRIPT_DIR"
    docker-compose ps
}

# Function to show logs
show_logs() {
    cd "$SCRIPT_DIR"
    
    if [ -z "$1" ]; then
        echo -e "${YELLOW}Showing logs for all containers...${NC}"
        docker-compose logs -f
    else
        echo -e "${YELLOW}Showing logs for $1...${NC}"
        docker-compose logs -f "$1"
    fi
}

# Function to clean up
cleanup() {
    echo -e "${RED}Warning: This will remove all containers and volumes!${NC}"
    echo -e "${RED}All data will be lost!${NC}"
    read -p "Are you sure? (y/n) " -n 1 -r
    echo
    
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        cd "$SCRIPT_DIR"
        docker-compose down -v
        echo -e "${GREEN}✓ Cleanup completed!${NC}"
    else
        echo "Cleanup cancelled."
    fi
}

# Function to backup database
backup_database() {
    echo -e "${YELLOW}Creating database backup...${NC}"
    cd "$SCRIPT_DIR"
    
    BACKUP_FILE="backup_$(date +%Y%m%d_%H%M%S).sql"
    
    docker-compose exec -T mysql mysqldump -u root -proot_password employees > "$BACKUP_FILE"
    
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✓ Database backup created: $BACKUP_FILE${NC}"
    else
        echo -e "${RED}✗ Failed to create backup${NC}"
        exit 1
    fi
}

# Function to restore database
restore_database() {
    if [ -z "$1" ]; then
        echo -e "${RED}Error: Please provide backup file path${NC}"
        echo "Usage: $0 restore <backup_file.sql>"
        exit 1
    fi
    
    if [ ! -f "$1" ]; then
        echo -e "${RED}Error: Backup file not found: $1${NC}"
        exit 1
    fi
    
    echo -e "${YELLOW}Restoring database from $1...${NC}"
    cd "$SCRIPT_DIR"
    
    docker-compose exec -T mysql mysql -u root -proot_password employees < "$1"
    
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✓ Database restored successfully!${NC}"
    else
        echo -e "${RED}✗ Failed to restore database${NC}"
        exit 1
    fi
}

# Function to run artisan command
run_artisan() {
    cd "$SCRIPT_DIR"
    docker-compose exec app php artisan "$@"
}

# Function to show help
show_help() {
    echo "Usage: $0 [command] [options]"
    echo ""
    echo "Commands:"
    echo -e "  ${GREEN}start${NC}              Start all containers"
    echo -e "  ${GREEN}stop${NC}               Stop all containers"
    echo -e "  ${GREEN}restart${NC}            Restart all containers"
    echo -e "  ${GREEN}rebuild${NC}            Rebuild containers from scratch"
    echo -e "  ${GREEN}status${NC}             Show container status"
    echo -e "  ${GREEN}logs [service]${NC}     Show logs (all services or specific)"
    echo -e "  ${GREEN}backup${NC}             Backup database"
    echo -e "  ${GREEN}restore <file>${NC}     Restore database from backup"
    echo -e "  ${GREEN}artisan [cmd]${NC}      Run Laravel artisan command"
    echo -e "  ${GREEN}clean${NC}              Remove all containers and volumes"
    echo -e "  ${GREEN}help${NC}               Show this help message"
    echo ""
    echo "Examples:"
    echo "  $0 start"
    echo "  $0 logs app"
    echo "  $0 artisan migrate"
    echo "  $0 restore backup_20260213.sql"
}

# Main script logic
check_docker
check_docker_compose

case "$1" in
    start)
        start_containers
        ;;
    stop)
        stop_containers
        ;;
    restart)
        restart_containers
        ;;
    rebuild)
        rebuild_containers
        ;;
    status)
        show_status
        ;;
    logs)
        show_logs "$2"
        ;;
    backup)
        backup_database
        ;;
    restore)
        restore_database "$2"
        ;;
    artisan)
        shift
        run_artisan "$@"
        ;;
    clean)
        cleanup
        ;;
    help|--help|-h)
        show_help
        ;;
    *)
        echo -e "${RED}Error: Unknown command '$1'${NC}"
        echo ""
        show_help
        exit 1
        ;;
esac
