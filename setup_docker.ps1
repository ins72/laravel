# Laravel Docker Setup Script
Write-Host "🐳 Laravel Docker Setup Script" -ForegroundColor Green
Write-Host "================================" -ForegroundColor Green

# Step 1: Stop all running servers
Write-Host "`n🛑 Step 1: Stopping running servers..." -ForegroundColor Yellow
try {
    # Stop PHP processes
    Get-Process -Name "php" -ErrorAction SilentlyContinue | Stop-Process -Force
    Write-Host "✅ PHP servers stopped" -ForegroundColor Green
    
    # Stop Node processes
    Get-Process -Name "node" -ErrorAction SilentlyContinue | Stop-Process -Force
    Write-Host "✅ Node servers stopped" -ForegroundColor Green
    
    # Kill processes on specific ports
    $ports = @(8003, 3003, 3004, 3005)
    foreach ($port in $ports) {
        $processes = netstat -ano | Select-String ":$port\s" | ForEach-Object { ($_ -split '\s+')[4] }
        foreach ($processId in $processes) {
            if ($processId -ne "") {
                try { Stop-Process -Id $processId -Force -ErrorAction SilentlyContinue } catch {}
            }
        }
    }
    Write-Host "✅ Port processes cleared" -ForegroundColor Green
} catch {
    Write-Host "⚠️  Some processes may still be running" -ForegroundColor Yellow
}

# Step 2: Verify Docker installation
Write-Host "`n🔍 Step 2: Verifying Docker installation..." -ForegroundColor Yellow
try {
    $dockerVersion = docker --version
    Write-Host "✅ Docker found: $dockerVersion" -ForegroundColor Green
    
    $composeVersion = docker-compose --version
    Write-Host "✅ Docker Compose found: $composeVersion" -ForegroundColor Green
} catch {
    Write-Host "❌ Docker not found. Please install Docker Desktop first." -ForegroundColor Red
    exit 1
}

# Step 3: Build and start Docker services
Write-Host "`n🚀 Step 3: Building and starting Docker services..." -ForegroundColor Yellow
try {
    docker-compose up -d --build
    Write-Host "✅ Docker services started" -ForegroundColor Green
} catch {
    Write-Host "❌ Failed to start Docker services" -ForegroundColor Red
    exit 1
}

# Step 4: Wait for services to be ready
Write-Host "`n⏳ Step 4: Waiting for services to be ready..." -ForegroundColor Yellow
Start-Sleep -Seconds 10

# Step 5: Install PHP dependencies
Write-Host "`n📦 Step 5: Installing PHP dependencies..." -ForegroundColor Yellow
try {
    docker-compose exec app composer install
    Write-Host "✅ PHP dependencies installed" -ForegroundColor Green
} catch {
    Write-Host "❌ Failed to install PHP dependencies" -ForegroundColor Red
}

# Step 6: Install Node.js dependencies
Write-Host "`n📦 Step 6: Installing Node.js dependencies..." -ForegroundColor Yellow
try {
    docker-compose exec vite npm install --legacy-peer-deps
    Write-Host "✅ Node.js dependencies installed" -ForegroundColor Green
} catch {
    Write-Host "❌ Failed to install Node.js dependencies" -ForegroundColor Red
}

# Step 7: Set up Laravel
Write-Host "`n⚙️  Step 7: Setting up Laravel..." -ForegroundColor Yellow
try {
    docker-compose exec app cp docker.env .env
    docker-compose exec app php artisan key:generate
    docker-compose exec app php artisan storage:link
    Write-Host "✅ Laravel setup completed" -ForegroundColor Green
} catch {
    Write-Host "❌ Failed to set up Laravel" -ForegroundColor Red
}

# Step 8: Set permissions
Write-Host "`n🔐 Step 8: Setting permissions..." -ForegroundColor Yellow
try {
    docker-compose exec app chown -R www-data:www-data /var/www/storage
    docker-compose exec app chown -R www-data:www-data /var/www/bootstrap/cache
    Write-Host "✅ Permissions set" -ForegroundColor Green
} catch {
    Write-Host "⚠️  Permission setting may have failed (this is normal on Windows)" -ForegroundColor Yellow
}

# Step 9: Run migrations
Write-Host "`n🗄️  Step 9: Running database migrations..." -ForegroundColor Yellow
try {
    docker-compose exec app php artisan migrate
    Write-Host "✅ Database migrations completed" -ForegroundColor Green
} catch {
    Write-Host "⚠️  Database migrations may have failed (check database connection)" -ForegroundColor Yellow
}

# Step 10: Show status
Write-Host "`n🎉 Setup Complete!" -ForegroundColor Green
Write-Host "================================" -ForegroundColor Green
Write-Host "🌐 Access your application:" -ForegroundColor Cyan
Write-Host "   Frontend (Vite): http://localhost:3003/" -ForegroundColor White
Write-Host "   Backend (Laravel): http://localhost:8003" -ForegroundColor White
Write-Host "   Database: localhost:5003 (MySQL)" -ForegroundColor White
Write-Host "`n📋 Useful commands:" -ForegroundColor Cyan
Write-Host "   View logs: docker-compose logs" -ForegroundColor White
Write-Host "   Stop services: docker-compose down" -ForegroundColor White
Write-Host "   Restart services: docker-compose restart" -ForegroundColor White
Write-Host "`n🚀 Your Laravel application is now running with Docker!" -ForegroundColor Green 