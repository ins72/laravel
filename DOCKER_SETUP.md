# 🐳 **Docker Setup for Laravel Application**

## 🎯 **Your Exact Port Configuration:**
- **Frontend (Vite)**: Port 3003
- **Backend (Laravel)**: Port 8003  
- **Database (MySQL)**: Port 5003

## 🚀 **Quick Start Commands:**

### **1. Build and Start All Services:**
```bash
docker-compose up -d --build
```

### **2. Install Dependencies:**
```bash
# Install PHP dependencies
docker-compose exec app composer install

# Install Node.js dependencies
docker-compose exec vite npm install --legacy-peer-deps
```

### **3. Set Up Laravel:**
```bash
# Copy environment file
docker-compose exec app cp docker.env .env

# Generate application key
docker-compose exec app php artisan key:generate

# Create storage link
docker-compose exec app php artisan storage:link

# Run database migrations
docker-compose exec app php artisan migrate

# Optional: Seed database
docker-compose exec app php artisan db:seed
```

### **4. Set Permissions:**
```bash
docker-compose exec app chown -R www-data:www-data /var/www/storage
docker-compose exec app chown -R www-data:www-data /var/www/bootstrap/cache
```

## 🌐 **Access Your Application:**

- **Frontend (Vite)**: http://localhost:3003/
- **Backend (Laravel)**: http://localhost:8003
- **Database**: localhost:5003 (MySQL)
- **Redis**: localhost:6379

## 🔧 **Useful Commands:**

### **View Logs:**
```bash
# All services
docker-compose logs

# Specific service
docker-compose logs app
docker-compose logs vite
docker-compose logs db
```

### **Stop Services:**
```bash
docker-compose down
```

### **Restart Services:**
```bash
docker-compose restart
```

### **Access Container Shell:**
```bash
# Laravel app
docker-compose exec app bash

# Vite container
docker-compose exec vite sh

# Database
docker-compose exec db mysql -u laravel -p
```

## 📁 **Docker Files Created:**

- `docker-compose.yml` - Main orchestration file
- `Dockerfile` - Laravel PHP application
- `Dockerfile.vite` - Vite development server
- `docker/nginx/conf.d/app.conf` - Nginx configuration
- `docker/php/local.ini` - PHP configuration
- `docker/mysql/my.cnf` - MySQL configuration
- `docker.env` - Environment variables

## 🎯 **Services Included:**

1. **app** - Laravel PHP application (PHP 8.2-FPM)
2. **webserver** - Nginx web server (Port 8003)
3. **db** - MySQL database (Port 5003)
4. **redis** - Redis cache (Port 6379)
5. **vite** - Vite development server (Port 3003)

## 🔄 **Development Workflow:**

1. **Start services**: `docker-compose up -d`
2. **Make changes** to your code
3. **View changes** at http://localhost:8003
4. **Stop services**: `docker-compose down`

## 🎊 **Benefits of Docker Setup:**

- ✅ **Isolated environment** - No conflicts with local PHP/MySQL
- ✅ **Exact port control** - Frontend 3003, Backend 8003, Database 5003
- ✅ **Easy deployment** - Same environment everywhere
- ✅ **No local dependencies** - Everything runs in containers
- ✅ **Easy cleanup** - Just remove containers when done

---

**Ready to deploy! Run `docker-compose up -d --build` to start everything.** 