#!/bin/bash

# Laravel Ecommerce Platform Deployment Script
# This script deploys the application to production

set -e  # Exit on any error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
APP_NAME="Laravel Ecommerce Platform"
DOMAIN=${1:-"yourdomain.com"}
ENVIRONMENT=${2:-"production"}

echo -e "${BLUE}🚀 Deploying ${APP_NAME} to ${ENVIRONMENT}${NC}"
echo -e "${BLUE}Domain: ${DOMAIN}${NC}"
echo ""

# Function to print colored output
print_status() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

# Check if running as root
if [[ $EUID -eq 0 ]]; then
   print_error "This script should not be run as root"
   exit 1
fi

# Check prerequisites
check_prerequisites() {
    print_status "Checking prerequisites..."
    
    # Check Docker
    if ! command -v docker &> /dev/null; then
        print_error "Docker is not installed"
        exit 1
    fi
    
    # Check Docker Compose
    if ! command -v docker-compose &> /dev/null; then
        print_error "Docker Compose is not installed"
        exit 1
    fi
    
    # Check if .env.production exists
    if [ ! -f ".env.production" ]; then
        print_error ".env.production file not found"
        exit 1
    fi
    
    print_status "Prerequisites check passed"
}

# Setup SSL certificates
setup_ssl() {
    print_status "Setting up SSL certificates..."
    
    # Create SSL directory
    mkdir -p docker/nginx/ssl
    
    # Check if SSL certificates exist
    if [ ! -f "docker/nginx/ssl/cert.pem" ] || [ ! -f "docker/nginx/ssl/key.pem" ]; then
        print_warning "SSL certificates not found. Creating self-signed certificates for development..."
        
        # Generate self-signed certificate
        openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
            -keyout docker/nginx/ssl/key.pem \
            -out docker/nginx/ssl/cert.pem \
            -subj "/C=US/ST=State/L=City/O=Organization/CN=${DOMAIN}"
        
        print_status "Self-signed SSL certificates created"
    else
        print_status "SSL certificates found"
    fi
}

# Setup environment
setup_environment() {
    print_status "Setting up environment..."
    
    # Copy production environment file
    if [ -f ".env.production" ]; then
        cp .env.production .env
        print_status "Production environment file copied"
    fi
    
    # Generate application key if not exists
    if ! grep -q "APP_KEY=base64:" .env; then
        print_status "Generating application key..."
        php artisan key:generate --force
    fi
    
    # Update domain in .env
    sed -i "s/yourdomain.com/${DOMAIN}/g" .env
    print_status "Domain updated in environment file"
}

# Build and deploy
deploy_application() {
    print_status "Building and deploying application..."
    
    # Stop existing containers
    print_status "Stopping existing containers..."
    docker-compose -f docker-compose.prod.yml down --remove-orphans
    
    # Build production images
    print_status "Building production Docker images..."
    docker-compose -f docker-compose.prod.yml build --no-cache
    
    # Start services
    print_status "Starting production services..."
    docker-compose -f docker-compose.prod.yml up -d
    
    # Wait for services to be ready
    print_status "Waiting for services to be ready..."
    sleep 30
    
    # Check service health
    check_service_health
}

# Check service health
check_service_health() {
    print_status "Checking service health..."
    
    # Check web server
    if curl -f -s "http://localhost/health" > /dev/null; then
        print_status "Web server is healthy"
    else
        print_warning "Web server health check failed"
    fi
    
    # Check database
    if docker-compose -f docker-compose.prod.yml exec -T db mysqladmin ping -h"localhost" -u"root" -p"${DB_ROOT_PASSWORD}" --silent; then
        print_status "Database is healthy"
    else
        print_warning "Database health check failed"
    fi
    
    # Check Redis
    if docker-compose -f docker-compose.prod.yml exec -T redis redis-cli ping | grep -q "PONG"; then
        print_status "Redis is healthy"
    else
        print_warning "Redis health check failed"
    fi
}

# Run database migrations
run_migrations() {
    print_status "Running database migrations..."
    
    # Wait for database to be ready
    sleep 10
    
    # Run migrations
    docker-compose -f docker-compose.prod.yml exec -T app php artisan migrate --force
    
    # Seed database if needed
    if [ "$ENVIRONMENT" = "production" ]; then
        print_status "Seeding production database..."
        docker-compose -f docker-compose.prod.yml exec -T app php artisan db:seed --force
    fi
    
    print_status "Database setup completed"
}

# Optimize application
optimize_application() {
    print_status "Optimizing application for production..."
    
    # Clear and cache configuration
    docker-compose -f docker-compose.prod.yml exec -T app php artisan config:cache
    docker-compose -f docker-compose.prod.yml exec -T app php artisan route:cache
    docker-compose -f docker-compose.prod.yml exec -T app php artisan view:cache
    
    # Create storage link
    docker-compose -f docker-compose.prod.yml exec -T app php artisan storage:link
    
    print_status "Application optimization completed"
}

# Setup monitoring
setup_monitoring() {
    print_status "Setting up monitoring..."
    
    # Create log directories
    mkdir -p logs/nginx logs/php logs/mysql
    
    # Set proper permissions
    chmod -R 755 logs/
    chmod -R 755 storage/
    chmod -R 755 bootstrap/cache/
    
    print_status "Monitoring setup completed"
}

# Final checks
final_checks() {
    print_status "Performing final checks..."
    
    # Check if application is accessible
    if curl -f -s "https://${DOMAIN}" > /dev/null; then
        print_status "Application is accessible via HTTPS"
    else
        print_warning "HTTPS access check failed, checking HTTP..."
        if curl -f -s "http://${DOMAIN}" > /dev/null; then
            print_status "Application is accessible via HTTP"
        else
            print_error "Application is not accessible"
        fi
    fi
    
    # Display service status
    print_status "Service status:"
    docker-compose -f docker-compose.prod.yml ps
}

# Main deployment function
main() {
    echo -e "${BLUE}Starting deployment process...${NC}"
    echo ""
    
    check_prerequisites
    setup_ssl
    setup_environment
    deploy_application
    run_migrations
    optimize_application
    setup_monitoring
    final_checks
    
    echo ""
    echo -e "${GREEN}🎉 Deployment completed successfully!${NC}"
    echo ""
    echo -e "${BLUE}Your ecommerce platform is now running at:${NC}"
    echo -e "${GREEN}🌐 https://${DOMAIN}${NC}"
    echo -e "${GREEN}🛍️  Shop: https://${DOMAIN}/shop${NC}"
    echo -e "${GREEN}📊 Admin: https://${DOMAIN}/admin${NC}"
    echo ""
    echo -e "${YELLOW}Next steps:${NC}"
    echo "1. Configure your domain DNS to point to this server"
    echo "2. Update payment gateway credentials in .env"
    echo "3. Configure your SMTP settings for email notifications"
    echo "4. Set up SSL certificates from a trusted CA"
    echo "5. Configure your backup strategy"
    echo ""
    echo -e "${BLUE}For support, check the ECOMMERCE_README.md file${NC}"
}

# Run main function
main "$@"