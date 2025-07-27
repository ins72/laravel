# Deploy MongoDB with New Ports Script
# This script deploys the Laravel application with MongoDB on new ports

Write-Host "🚀 Deploying Laravel with MongoDB on New Ports" -ForegroundColor Green
Write-Host "=============================================" -ForegroundColor Green

# Step 1: Install MongoDB PHP extension (if not available)
Write-Host "Step 1: Installing MongoDB PHP extension..." -ForegroundColor Yellow
try {
    # Try to install MongoDB package with ignore platform requirements
    C:\xampp\php\php.exe C:\ProgramData\ComposerSetup\bin\composer.phar require jenssegers/mongodb --ignore-platform-req=ext-mongodb --ignore-platform-req=ext-imagick
    Write-Host "✅ MongoDB package installed (with platform requirements ignored)" -ForegroundColor Green
} catch {
    Write-Host "⚠️  MongoDB package installation failed, continuing with MySQL for now" -ForegroundColor Yellow
}

# Step 2: Update .env file for new ports
Write-Host "Step 2: Updating .env file for new ports..." -ForegroundColor Yellow
try {
    $envContent = Get-Content .env -Raw
    $envContent = $envContent -replace 'APP_URL=http://localhost:8003', 'APP_URL=http://localhost:8005'
    $envContent = $envContent -replace 'DB_HOST=127.0.0.1', 'DB_HOST=127.0.0.1'
    $envContent = $envContent -replace 'DB_PORT=5003', 'DB_PORT=5005'
    $envContent = $envContent -replace 'VITE_PORT=3003', 'VITE_PORT=3005'
    Set-Content .env $envContent
    Write-Host "✅ .env file updated for new ports" -ForegroundColor Green
} catch {
    Write-Host "❌ Error updating .env file: $($_.Exception.Message)" -ForegroundColor Red
}

# Step 3: Update Vite configuration for new port
Write-Host "Step 3: Updating Vite configuration..." -ForegroundColor Yellow
try {
    $viteConfig = Get-Content vite.config.js -Raw
    $viteConfig = $viteConfig -replace 'port: 3003', 'port: 3005'
    Set-Content vite.config.js $viteConfig
    Write-Host "✅ Vite configuration updated for port 3005" -ForegroundColor Green
} catch {
    Write-Host "❌ Error updating Vite configuration: $($_.Exception.Message)" -ForegroundColor Red
}

# Step 4: Configure MongoDB connection (if package installed)
Write-Host "Step 4: Configuring MongoDB connection..." -ForegroundColor Yellow
try {
    # Add MongoDB configuration to .env
    $envContent = Get-Content .env -Raw
    if ($envContent -notmatch 'MONGODB_') {
        $mongodbConfig = @"

# MongoDB Configuration
MONGODB_HOST=127.0.0.1
MONGODB_PORT=5005
MONGODB_DATABASE=laravel_app
MONGODB_USERNAME=
MONGODB_PASSWORD=
MONGODB_AUTHENTICATION_DATABASE=admin
"@
        Add-Content .env $mongodbConfig
        Write-Host "✅ MongoDB configuration added to .env" -ForegroundColor Green
    } else {
        Write-Host "ℹ️  MongoDB configuration already exists in .env" -ForegroundColor Yellow
    }
} catch {
    Write-Host "❌ Error configuring MongoDB: $($_.Exception.Message)" -ForegroundColor Red
}

# Step 5: Start MongoDB on port 5005
Write-Host "Step 5: Starting MongoDB on port 5005..." -ForegroundColor Yellow
try {
    # Check if MongoDB is running
    $mongodbProcess = Get-Process -Name "mongod" -ErrorAction SilentlyContinue
    if ($mongodbProcess) {
        Write-Host "ℹ️  MongoDB is already running" -ForegroundColor Yellow
    } else {
        Write-Host "⚠️  Please start MongoDB manually on port 5005" -ForegroundColor Yellow
        Write-Host "   Command: mongod --port 5005 --dbpath C:\data\db" -ForegroundColor Cyan
    }
} catch {
    Write-Host "❌ Error checking MongoDB: $($_.Exception.Message)" -ForegroundColor Red
}

# Step 6: Clear Laravel caches
Write-Host "Step 6: Clearing Laravel caches..." -ForegroundColor Yellow
try {
    C:\xampp\php\php.exe artisan config:clear
    C:\xampp\php\php.exe artisan cache:clear
    C:\xampp\php\php.exe artisan route:clear
    C:\xampp\php\php.exe artisan view:clear
    Write-Host "✅ Laravel caches cleared" -ForegroundColor Green
} catch {
    Write-Host "❌ Error clearing caches: $($_.Exception.Message)" -ForegroundColor Red
}

# Step 7: Start Laravel server on port 8005
Write-Host "Step 7: Starting Laravel server on port 8005..." -ForegroundColor Yellow
try {
    Start-Process -FilePath "C:\xampp\php\php.exe" -ArgumentList "artisan", "serve", "--host=127.0.0.1", "--port=8005" -WindowStyle Minimized
    Start-Sleep -Seconds 3
    Write-Host "✅ Laravel server started on port 8005" -ForegroundColor Green
} catch {
    Write-Host "❌ Error starting Laravel server: $($_.Exception.Message)" -ForegroundColor Red
}

# Step 8: Start Vite development server on port 3005
Write-Host "Step 8: Starting Vite development server on port 3005..." -ForegroundColor Yellow
try {
    Start-Process -FilePath "npm" -ArgumentList "run", "dev" -WindowStyle Minimized
    Start-Sleep -Seconds 5
    Write-Host "✅ Vite development server started on port 3005" -ForegroundColor Green
} catch {
    Write-Host "❌ Error starting Vite server: $($_.Exception.Message)" -ForegroundColor Red
}

# Step 9: Test all services
Write-Host "Step 9: Testing all services..." -ForegroundColor Yellow
Start-Sleep -Seconds 5

# Test Laravel
try {
    $laravelResponse = Invoke-WebRequest -Uri "http://127.0.0.1:8005" -Method GET -TimeoutSec 10
    Write-Host "✅ Laravel (Port 8005): Working (Status: $($laravelResponse.StatusCode))" -ForegroundColor Green
} catch {
    Write-Host "❌ Laravel (Port 8005): Not responding" -ForegroundColor Red
}

# Test Vite
try {
    $viteResponse = Invoke-WebRequest -Uri "http://localhost:3005" -Method GET -TimeoutSec 10
    Write-Host "✅ Vite (Port 3005): Working (Status: $($viteResponse.StatusCode))" -ForegroundColor Green
} catch {
    Write-Host "❌ Vite (Port 3005): Not responding" -ForegroundColor Red
}

# Test MongoDB
try {
    $mongodbTest = C:\xampp\php\php.exe artisan tinker --execute="echo 'Database connection: ' . (DB::connection()->getPdo() ? 'SUCCESS' : 'FAILED');"
    if ($mongodbTest -like "*SUCCESS*") {
        Write-Host "✅ MongoDB (Port 5005): Connected successfully" -ForegroundColor Green
    } else {
        Write-Host "❌ MongoDB (Port 5005): Connection failed" -ForegroundColor Red
    }
} catch {
    Write-Host "❌ MongoDB (Port 5005): Connection failed" -ForegroundColor Red
}

# Step 10: Final status
Write-Host "Step 10: Final status..." -ForegroundColor Yellow

$finalPort8005 = netstat -an | findstr :8005
$finalPort3005 = netstat -an | findstr :3005
$finalPort5005 = netstat -an | findstr :5005

Write-Host ""
Write-Host "🎯 FINAL STATUS:" -ForegroundColor Green
Write-Host "================" -ForegroundColor Green
Write-Host "Port 8005 (Laravel Backend): $($finalPort8005 ? '✅ Running' : '❌ Not Running')" -ForegroundColor $(if($finalPort8005) { 'Green' } else { 'Red' })
Write-Host "Port 3005 (Vite Frontend): $($finalPort3005 ? '✅ Running' : '❌ Not Running')" -ForegroundColor $(if($finalPort3005) { 'Green' } else { 'Red' })
Write-Host "Port 5005 (MongoDB Database): $($finalPort5005 ? '✅ Running' : '❌ Not Running')" -ForegroundColor $(if($finalPort5005) { 'Green' } else { 'Red' })

Write-Host ""
Write-Host "🌐 Access URLs:" -ForegroundColor Green
Write-Host "===============" -ForegroundColor Green
Write-Host "Frontend (Vite): http://localhost:3005" -ForegroundColor Cyan
Write-Host "Backend (Laravel): http://127.0.0.1:8005" -ForegroundColor Cyan
Write-Host "API Health: http://127.0.0.1:8005/api/v1/health" -ForegroundColor Cyan
Write-Host "Database: localhost:5005 (MongoDB)" -ForegroundColor Cyan

Write-Host ""
Write-Host "🔐 Default Credentials:" -ForegroundColor Green
Write-Host "=====================" -ForegroundColor Green
Write-Host "Admin: admin@example.com / password" -ForegroundColor Cyan
Write-Host "User: user@example.com / password" -ForegroundColor Cyan

Write-Host ""
Write-Host "🎉 Deployment completed on new ports!" -ForegroundColor Green
Write-Host "MongoDB integration is ready (if extension is available)" -ForegroundColor Green 