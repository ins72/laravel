# 🚀 Laravel Ecommerce Platform - Deployment Guide

This guide will walk you through deploying your Laravel ecommerce platform to production.

## 📋 Prerequisites

Before deploying, ensure you have:

- **Server**: Ubuntu 20.04+ or CentOS 8+ (recommended)
- **Docker**: Version 20.10+
- **Docker Compose**: Version 2.0+
- **Domain**: A registered domain name
- **SSL Certificate**: From Let's Encrypt or your preferred CA
- **Payment Gateway Accounts**: Stripe, Razorpay, etc.

## 🏗️ **Option 1: Quick Development Deployment**

For local development and testing:

```bash
# Make script executable
chmod +x deploy-dev.sh

# Run development deployment
./deploy-dev.sh
```

This will:
- Start all services on localhost:8003
- Seed the database with sample data
- Create test user account
- Build frontend assets

## 🌐 **Option 2: Production Deployment**

### Step 1: Prepare Your Server

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Install Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/download/v2.20.0/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose

# Add user to docker group
sudo usermod -aG docker $USER
newgrp docker
```

### Step 2: Clone Your Project

```bash
# Clone your repository
git clone <your-repo-url>
cd <your-project-directory>

# Make deployment scripts executable
chmod +x deploy.sh
chmod +x deploy-dev.sh
```

### Step 3: Configure Environment

```bash
# Copy and edit production environment file
cp .env.production .env

# Edit the .env file with your actual values
nano .env
```

**Key configurations to update:**

```env
# Application
APP_URL=https://yourdomain.com
APP_KEY=base64:your-generated-key

# Database
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_secure_password
DB_ROOT_PASSWORD=your_secure_root_password

# Payment Gateways
STRIPE_KEY=your_stripe_publishable_key
STRIPE_SECRET=your_stripe_secret_key
RAZORPAY_KEY=your_razorpay_key
RAZORPAY_SECRET=your_razorpay_secret

# Email
MAIL_HOST=your_smtp_host
MAIL_USERNAME=your_smtp_username
MAIL_PASSWORD=your_smtp_password
MAIL_FROM_ADDRESS=noreply@yourdomain.com
```

### Step 4: Deploy to Production

```bash
# Deploy with your domain
./deploy.sh yourdomain.com production
```

The deployment script will:
- ✅ Check prerequisites
- 🔒 Setup SSL certificates
- 🐳 Build and start Docker containers
- 🗄️ Run database migrations
- 🌱 Seed the database
- ⚡ Optimize the application
- 📊 Setup monitoring
- 🔍 Perform health checks

## 🔒 **SSL Certificate Setup**

### Option A: Let's Encrypt (Recommended)

```bash
# Install Certbot
sudo apt install certbot

# Generate certificate
sudo certbot certonly --standalone -d yourdomain.com

# Copy certificates to Docker directory
sudo cp /etc/letsencrypt/live/yourdomain.com/fullchain.pem docker/nginx/ssl/cert.pem
sudo cp /etc/letsencrypt/live/yourdomain.com/privkey.pem docker/nginx/ssl/key.pem

# Set proper permissions
sudo chown $USER:$USER docker/nginx/ssl/*
sudo chmod 600 docker/nginx/ssl/*
```

### Option B: Self-Signed (Development Only)

The deployment script will create self-signed certificates automatically for testing.

## 🗄️ **Database Setup**

### Create Production Database

```bash
# Access MySQL container
docker-compose -f docker-compose.prod.yml exec db mysql -u root -p

# Create database and user
CREATE DATABASE laravel_prod CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'laravel_prod'@'%' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON laravel_prod.* TO 'laravel_prod'@'%';
FLUSH PRIVILEGES;
EXIT;
```

## 💳 **Payment Gateway Configuration**

### Stripe Setup

1. Create account at [stripe.com](https://stripe.com)
2. Get your API keys from the dashboard
3. Update `.env` file with your keys
4. Test with Stripe's test cards

### Razorpay Setup

1. Create account at [razorpay.com](https://razorpay.com)
2. Get your API keys from the dashboard
3. Update `.env` file with your keys

## 📧 **Email Configuration**

### SMTP Setup

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
```

### Gmail App Password

If using Gmail:
1. Enable 2-factor authentication
2. Generate app password
3. Use app password instead of regular password

## 🚀 **Post-Deployment Steps**

### 1. Domain DNS Configuration

Point your domain to your server's IP address:
- **A Record**: `@` → `your-server-ip`
- **CNAME**: `www` → `@`

### 2. Test Your Application

```bash
# Check if application is running
curl -I https://yourdomain.com

# Test ecommerce functionality
curl -I https://yourdomain.com/shop
```

### 3. Monitor Logs

```bash
# View application logs
docker-compose -f docker-compose.prod.yml logs -f app

# View nginx logs
docker-compose -f docker-compose.prod.yml logs -f webserver

# View database logs
docker-compose -f docker-compose.prod.yml logs -f db
```

### 4. Setup Backups

```bash
# Create backup script
cat > backup.sh << 'EOF'
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups"

# Database backup
docker-compose -f docker-compose.prod.yml exec db mysqldump -u root -p${DB_ROOT_PASSWORD} laravel_prod > ${BACKUP_DIR}/db_${DATE}.sql

# Application backup
tar -czf ${BACKUP_DIR}/app_${DATE}.tar.gz --exclude=node_modules --exclude=storage/logs .

# Keep only last 7 days of backups
find ${BACKUP_DIR} -name "*.sql" -mtime +7 -delete
find ${BACKUP_DIR} -name "*.tar.gz" -mtime +7 -delete
EOF

chmod +x backup.sh

# Add to crontab (daily at 2 AM)
echo "0 2 * * * /path/to/your/project/backup.sh" | crontab -
```

## 🔧 **Maintenance Commands**

### Update Application

```bash
# Pull latest changes
git pull origin main

# Rebuild and restart
docker-compose -f docker-compose.prod.yml down
docker-compose -f docker-compose.prod.yml up -d --build

# Run migrations
docker-compose -f docker-compose.prod.yml exec app php artisan migrate --force

# Clear caches
docker-compose -f docker-compose.prod.yml exec app php artisan config:cache
docker-compose -f docker-compose.prod.yml exec app php artisan route:cache
docker-compose -f docker-compose.prod.yml exec app php artisan view:cache
```

### Scale Services

```bash
# Scale queue workers
docker-compose -f docker-compose.prod.yml up -d --scale queue=3

# Scale web servers (if using load balancer)
docker-compose -f docker-compose.prod.yml up -d --scale app=2
```

## 📊 **Monitoring & Health Checks**

### Health Check Endpoint

Your application includes a health check at `/health`:

```bash
curl https://yourdomain.com/health
# Should return: healthy
```

### Performance Monitoring

```bash
# Check container resource usage
docker stats

# Check application performance
docker-compose -f docker-compose.prod.yml exec app php artisan route:list --compact
```

## 🚨 **Troubleshooting**

### Common Issues

1. **Application not accessible**
   ```bash
   # Check container status
   docker-compose -f docker-compose.prod.yml ps
   
   # Check logs
   docker-compose -f docker-compose.prod.yml logs webserver
   ```

2. **Database connection failed**
   ```bash
   # Check database container
   docker-compose -f docker-compose.prod.yml exec db mysqladmin ping -h localhost -u root -p
   ```

3. **SSL certificate issues**
   ```bash
   # Check certificate validity
   openssl x509 -in docker/nginx/ssl/cert.pem -text -noout
   ```

### Performance Issues

1. **Slow page loads**
   - Check Redis connection
   - Verify OPcache is enabled
   - Monitor database query performance

2. **High memory usage**
   - Adjust PHP memory limits in `docker/php/production.ini`
   - Optimize MySQL buffer pool size

## 🔐 **Security Checklist**

- [ ] SSL certificates properly configured
- [ ] Database passwords are strong and unique
- [ ] Payment gateway keys are secure
- [ ] File permissions are properly set
- [ ] Environment variables are secure
- [ ] Regular security updates applied
- [ ] Firewall rules configured
- [ ] Backup strategy implemented

## 📞 **Support**

If you encounter issues:

1. Check the logs: `docker-compose -f docker-compose.prod.yml logs -f`
2. Verify environment configuration
3. Check service health: `docker-compose -f docker-compose.prod.yml ps`
4. Review the `ECOMMERCE_README.md` file
5. Check Laravel documentation

---

**🎉 Congratulations! Your Laravel ecommerce platform is now deployed and ready for business!**

**Next Steps:**
1. Add your products through the admin panel
2. Configure payment gateways
3. Test the complete purchase flow
4. Set up your marketing and analytics
5. Launch your online store!