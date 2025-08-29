#!/bin/bash

# Laravel Ecommerce Platform - Development Deployment
# Quick deployment for development and testing

set -e

# Colors for output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}🚀 Deploying Ecommerce Platform for Development${NC}"
echo ""

# Function to print status
print_status() {
    echo -e "${GREEN}✅ $1${NC}"
}

# Check if running as root
if [[ $EUID -eq 0 ]]; then
   echo "❌ This script should not be run as root"
   exit 1
fi

# Stop existing containers
print_status "Stopping existing containers..."
docker-compose down --remove-orphans

# Build and start development environment
print_status "Building and starting development environment..."
docker-compose up -d --build

# Wait for services to be ready
print_status "Waiting for services to be ready..."
sleep 20

# Run database migrations
print_status "Running database migrations..."
docker-compose exec app php artisan migrate --force

# Seed database
print_status "Seeding database with sample data..."
docker-compose exec app php artisan db:seed --force

# Create storage link
print_status "Creating storage link..."
docker-compose exec app php artisan storage:link

# Build frontend assets
print_status "Building frontend assets..."
docker-compose exec vite npm run build

# Clear and cache configuration
print_status "Optimizing application..."
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear

print_status "Development deployment completed!"
echo ""
echo -e "${BLUE}Your ecommerce platform is now running at:${NC}"
echo -e "${GREEN}🌐 http://localhost:8003${NC}"
echo -e "${GREEN}🛍️  Shop: http://localhost:8003/shop${NC}"
echo -e "${GREEN}📊 Admin: http://localhost:8003/admin${NC}"
echo ""
echo -e "${BLUE}Test Account:${NC}"
echo -e "${GREEN}Email: test@example.com${NC}"
echo -e "${GREEN}Password: password${NC}"
echo ""
echo -e "${BLUE}To view logs:${NC}"
echo "docker-compose logs -f"
echo ""
echo -e "${BLUE}To stop services:${NC}"
echo "docker-compose down"