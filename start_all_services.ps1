# Start All Services Script
# This script starts all three services on the correct ports

Write-Host "🚀 Starting All Services on Correct Ports" -ForegroundColor Green
Write-Host "=========================================" -ForegroundColor Green

# Step 1: Check current port status
Write-Host "Step 1: Checking current port status..." -ForegroundColor Yellow

$port8003 = netstat -an | findstr :8003
$port3003 = netstat -an | findstr :3003
$port5003 = netstat -an | findstr :5003

Write-Host "Port 8003 (Laravel): $($port8003 ? 'Running' : 'Not Running')" -ForegroundColor $(if($port8003) { 'Green' } else { 'Red' })
Write-Host "Port 3003 (Vite): $($port3003 ? 'Running' : 'Not Running')" -ForegroundColor $(if($port3003) { 'Green' } else { 'Red' })
Write-Host "Port 5003 (MySQL): $($port5003 ? 'Running' : 'Not Running')" -ForegroundColor $(if($port5003) { 'Green' } else { 'Red' })

# Step 2: Configure MySQL for port 5003
Write-Host "Step 2: Configuring MySQL for port 5003..." -ForegroundColor Yellow

Write-Host "⚠️  IMPORTANT: You need to manually configure MySQL in XAMPP:" -ForegroundColor Yellow
Write-Host "   1. Open XAMPP Control Panel" -ForegroundColor Cyan
Write-Host "   2. Click 'Config' next to MySQL" -ForegroundColor Cyan
Write-Host "   3. Select 'my.ini'" -ForegroundColor Cyan
Write-Host "   4. Find 'port=3306' and change to 'port=5003'" -ForegroundColor Cyan
Write-Host "   5. Save and restart MySQL" -ForegroundColor Cyan

# Step 3: Start Laravel server on port 8003
Write-Host "Step 3: Starting Laravel server on port 8003..." -ForegroundColor Yellow

if (-not $port8003) {
    Write-Host "Starting Laravel server..." -ForegroundColor Cyan
    Start-Process -FilePath "C:\xampp\php\php.exe" -ArgumentList "artisan", "serve", "--host=127.0.0.1", "--port=8003" -WindowStyle Minimized
    Start-Sleep -Seconds 3
} else {
    Write-Host "✅ Laravel server already running on port 8003" -ForegroundColor Green
}

# Step 4: Start Vite development server on port 3003
Write-Host "Step 4: Starting Vite development server on port 3003..." -ForegroundColor Yellow

if (-not $port3003) {
    Write-Host "Starting Vite development server..." -ForegroundColor Cyan
    Start-Process -FilePath "npm" -ArgumentList "run", "dev" -WindowStyle Minimized
    Start-Sleep -Seconds 5
} else {
    Write-Host "✅ Vite development server already running on port 3003" -ForegroundColor Green
}

# Step 5: Test all services
Write-Host "Step 5: Testing all services..." -ForegroundColor Yellow

Start-Sleep -Seconds 5

# Test Laravel
try {
    $laravelResponse = Invoke-WebRequest -Uri "http://127.0.0.1:8003" -Method GET -TimeoutSec 10
    Write-Host "✅ Laravel (Port 8003): Working (Status: $($laravelResponse.StatusCode))" -ForegroundColor Green
} catch {
    Write-Host "❌ Laravel (Port 8003): Not responding" -ForegroundColor Red
}

# Test Vite
try {
    $viteResponse = Invoke-WebRequest -Uri "http://localhost:3003" -Method GET -TimeoutSec 10
    Write-Host "✅ Vite (Port 3003): Working (Status: $($viteResponse.StatusCode))" -ForegroundColor Green
} catch {
    Write-Host "❌ Vite (Port 3003): Not responding" -ForegroundColor Red
}

# Test MySQL
try {
    $mysqlTest = C:\xampp\php\php.exe artisan tinker --execute="echo 'Database connection: ' . (DB::connection()->getPdo() ? 'SUCCESS' : 'FAILED');"
    if ($mysqlTest -like "*SUCCESS*") {
        Write-Host "✅ MySQL (Port 5003): Connected successfully" -ForegroundColor Green
    } else {
        Write-Host "❌ MySQL (Port 5003): Connection failed" -ForegroundColor Red
    }
} catch {
    Write-Host "❌ MySQL (Port 5003): Connection failed" -ForegroundColor Red
}

# Step 6: Final status
Write-Host "Step 6: Final status..." -ForegroundColor Yellow

$finalPort8003 = netstat -an | findstr :8003
$finalPort3003 = netstat -an | findstr :3003
$finalPort5003 = netstat -an | findstr :5003

Write-Host ""
Write-Host "🎯 FINAL STATUS:" -ForegroundColor Green
Write-Host "================" -ForegroundColor Green
Write-Host "Port 8003 (Laravel Backend): $($finalPort8003 ? '✅ Running' : '❌ Not Running')" -ForegroundColor $(if($finalPort8003) { 'Green' } else { 'Red' })
Write-Host "Port 3003 (Vite Frontend): $($finalPort3003 ? '✅ Running' : '❌ Not Running')" -ForegroundColor $(if($finalPort3003) { 'Green' } else { 'Red' })
Write-Host "Port 5003 (MySQL Database): $($finalPort5003 ? '✅ Running' : '❌ Not Running')" -ForegroundColor $(if($finalPort5003) { 'Green' } else { 'Red' })

Write-Host ""
Write-Host "🌐 Access URLs:" -ForegroundColor Green
Write-Host "===============" -ForegroundColor Green
Write-Host "Frontend (Vite): http://localhost:3003" -ForegroundColor Cyan
Write-Host "Backend (Laravel): http://127.0.0.1:8003" -ForegroundColor Cyan
Write-Host "API Health: http://127.0.0.1:8003/api/v1/health" -ForegroundColor Cyan
Write-Host "Database: localhost:5003 (via phpMyAdmin)" -ForegroundColor Cyan

Write-Host ""
Write-Host "🔐 Default Credentials:" -ForegroundColor Green
Write-Host "=====================" -ForegroundColor Green
Write-Host "Admin: admin@example.com / password" -ForegroundColor Cyan
Write-Host "User: user@example.com / password" -ForegroundColor Cyan

Write-Host ""
Write-Host "🎉 All services should now be running on the correct ports!" -ForegroundColor Green 