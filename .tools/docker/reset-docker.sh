#!/bin/bash

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}=========================================${NC}"
echo -e "${BLUE}  Docker Environment Reset Tool         ${NC}"
echo -e "${BLUE}=========================================${NC}"
echo ""

echo -e "${YELLOW}This script will:${NC}"
echo "  1. Stop all containers"
echo "  2. Remove all containers"
echo "  3. Remove all volumes (DATABASE WILL BE DELETED!)"
echo "  4. Rebuild containers from scratch"
echo "  5. Start fresh environment"
echo ""

echo -e "${RED}⚠️  WARNING: ALL DATA IN DATABASE WILL BE LOST! ⚠️${NC}"
echo ""

read -p "Are you sure you want to continue? (type 'yes' to confirm): " -r
echo

if [[ ! $REPLY == "yes" ]]; then
    echo "Reset cancelled."
    exit 0
fi

echo ""
echo -e "${YELLOW}Step 1: Stopping containers...${NC}"
docker-compose down

echo ""
echo -e "${YELLOW}Step 2: Removing volumes...${NC}"
docker-compose down -v

echo ""
echo -e "${YELLOW}Step 3: Removing old images (optional cleanup)...${NC}"
docker image prune -f

echo ""
echo -e "${YELLOW}Step 4: Rebuilding containers...${NC}"
docker-compose build --no-cache

echo ""
echo -e "${YELLOW}Step 5: Starting fresh environment...${NC}"
docker-compose up -d

echo ""
echo -e "${GREEN}=========================================${NC}"
echo -e "${GREEN}  Environment reset complete!           ${NC}"
echo -e "${GREEN}=========================================${NC}"
echo ""
echo "Waiting for services to initialize (30 seconds)..."
sleep 30

echo ""
echo -e "${BLUE}Checking container status:${NC}"
docker-compose ps

echo ""
echo -e "${BLUE}Checking last 20 lines of app logs:${NC}"
docker-compose logs --tail=20 app

echo ""
echo -e "${GREEN}Access points:${NC}"
echo -e "  Application: ${BLUE}http://localhost:8000${NC}"
echo -e "  PHPMyAdmin:  ${BLUE}http://localhost:8080${NC}"
echo ""
echo -e "${YELLOW}If you see errors above, run:${NC}"
echo -e "  ${BLUE}docker-compose logs -f app${NC}"
echo ""