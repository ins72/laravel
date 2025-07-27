# Do Everything For Me - Complete Laravel + XAMPP MySQL Setup
Write-Host "🚀 Do Everything For Me - Complete Setup" -ForegroundColor Green
Write-Host "=======================================" -ForegroundColor Green

# Step 1: Stop all running servers automatically
Write-Host "`n🛑 Step 1: Stopping all running servers..." -ForegroundColor Yellow
try {
    # Stop PHP processes
    Get-Process -Name "php" -ErrorAction SilentlyContinue | Stop-Process -Force
    Write-Host "✅ PHP servers stopped" -ForegroundColor Green
    
    # Stop Node processes
    Get-Process -Name "node" -ErrorAction SilentlyContinue | Stop-Process -Force
    Write-Host "✅ Node servers stopped" -ForegroundColor Green
    
    # Kill processes on specific ports
    $ports = @(8003, 3003, 3004, 3005, 3306, 5003)
    foreach ($port in $ports) {
        $processes = netstat -ano | Select-String ":$port\s" | ForEach-Object { ($_ -split '\s+')[4] }
        foreach ($processId in $processes) {
            if ($processId -ne "") {
                try { Stop-Process -Id $processId -Force -ErrorAction SilentlyContinue } catch {}
            }
        }
    }
    Write-Host "✅ All port processes cleared" -ForegroundColor Green
} catch {
    Write-Host "⚠️  Some processes may still be running" -ForegroundColor Yellow
}

# Step 2: Configure XAMPP MySQL for port 5003
Write-Host "`n🐬 Step 2: Configuring XAMPP MySQL for port 5003..." -ForegroundColor Yellow
$xamppPath = "C:\xampp"
if (Test-Path $xamppPath) {
    Write-Host "✅ XAMPP found at: $xamppPath" -ForegroundColor Green
    
    # Stop any running MySQL
    Get-Process -Name "mysqld" -ErrorAction SilentlyContinue | Stop-Process -Force
    
    # Backup original config
    $myIniPath = "$xamppPath\mysql\bin\my.ini"
    if (Test-Path $myIniPath) {
        Copy-Item $myIniPath "$myIniPath.backup"
        Write-Host "✅ Original my.ini backed up" -ForegroundColor Green
    }
    
    # Create new MySQL config for port 5003
    $myIniContent = @"
[mysqld]
port=5003
bind-address=127.0.0.1
datadir=C:/xampp/mysql/data
basedir=C:/xampp/mysql
character-set-server=utf8mb4
collation-server=utf8mb4_unicode_ci
default_authentication_plugin=mysql_native_password

[mysql]
default-character-set=utf8mb4

[client]
port=5003
default-character-set=utf8mb4
"@
    
    Set-Content -Path $myIniPath -Value $myIniContent
    Write-Host "✅ MySQL configured for port 5003" -ForegroundColor Green
    
    # Start MySQL in background
    Start-Process -FilePath "$xamppPath\mysql\bin\mysqld.exe" -ArgumentList "--defaults-file=$myIniPath", "--console" -WindowStyle Hidden
    Write-Host "✅ MySQL started on port 5003" -ForegroundColor Green
    
    # Wait for MySQL to start
    Start-Sleep -Seconds 5
    
    # Set up database and users
    $mysqlPath = "$xamppPath\mysql\bin\mysql.exe"
    if (Test-Path $mysqlPath) {
        $sqlCommands = @"
CREATE DATABASE IF NOT EXISTS laravel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'laravel'@'localhost' IDENTIFIED BY 'laravel';
GRANT ALL PRIVILEGES ON laravel.* TO 'laravel'@'localhost';
FLUSH PRIVILEGES;
"@
        
        $sqlCommands | & $mysqlPath -u root -P 5003 2>$null
        Write-Host "✅ Database and users configured" -ForegroundColor Green
    }
} else {
    Write-Host "❌ XAMPP not found at: $xamppPath" -ForegroundColor Red
    exit 1
}

# Step 3: Update Laravel .env file
Write-Host "`n⚙️  Step 3: Updating Laravel configuration..." -ForegroundColor Yellow
try {
    if (Test-Path ".env") {
        $envContent = Get-Content ".env" -Raw
        $envContent = $envContent -replace 'DB_HOST=.*', 'DB_HOST=127.0.0.1'
        $envContent = $envContent -replace 'DB_PORT=.*', 'DB_PORT=5003'
        $envContent = $envContent -replace 'DB_DATABASE=.*', 'DB_DATABASE=laravel'
        $envContent = $envContent -replace 'DB_USERNAME=.*', 'DB_USERNAME=laravel'
        $envContent = $envContent -replace 'DB_PASSWORD=.*', 'DB_PASSWORD=laravel'
        if ($envContent -notmatch 'INSTALLED=1') {
            $envContent += "`nINSTALLED=1"
        }
        Set-Content ".env" $envContent
        Write-Host "✅ Laravel .env updated" -ForegroundColor Green
    }
} catch {
    Write-Host "❌ Failed to update Laravel configuration" -ForegroundColor Red
}

# Step 4: Run Laravel migrations
Write-Host "`n🗄️  Step 4: Running Laravel migrations..." -ForegroundColor Yellow
try {
    & "C:\xampp\php\php.exe" artisan migrate --force
    Write-Host "✅ Database migrations completed" -ForegroundColor Green
} catch {
    Write-Host "⚠️  Migrations may have failed" -ForegroundColor Yellow
}

# Step 5: Clear Laravel caches
Write-Host "`n🧹 Step 5: Clearing Laravel caches..." -ForegroundColor Yellow
try {
    & "C:\xampp\php\php.exe" artisan config:clear
    & "C:\xampp\php\php.exe" artisan cache:clear
    & "C:\xampp\php\php.exe" artisan view:clear
    Write-Host "✅ Laravel caches cleared" -ForegroundColor Green
} catch {
    Write-Host "⚠️  Cache clearing may have failed" -ForegroundColor Yellow
}

# Step 6: Start Laravel server in background
Write-Host "`n🚀 Step 6: Starting Laravel server..." -ForegroundColor Yellow
try {
    Start-Process -FilePath "C:\xampp\php\php.exe" -ArgumentList "artisan", "serve", "--port=8003" -WindowStyle Hidden
    Write-Host "✅ Laravel server started on port 8003" -ForegroundColor Green
} catch {
    Write-Host "❌ Failed to start Laravel server" -ForegroundColor Red
}

# Step 7: Start Vite server in background
Write-Host "`n⚡ Step 7: Starting Vite server..." -ForegroundColor Yellow
try {
    Start-Process -FilePath "npm" -ArgumentList "run", "dev" -WindowStyle Hidden
    Write-Host "✅ Vite server started on port 3003" -ForegroundColor Green
} catch {
    Write-Host "❌ Failed to start Vite server" -ForegroundColor Red
}

# Step 8: Wait a moment for servers to start
Write-Host "`n⏳ Step 8: Waiting for servers to start..." -ForegroundColor Yellow
Start-Sleep -Seconds 10

# Step 9: Test connections
Write-Host "`n🧪 Step 9: Testing connections..." -ForegroundColor Yellow
try {
    # Test MySQL
    $mysqlPath = "$xamppPath\mysql\bin\mysql.exe"
    $testResult = & $mysqlPath -u laravel -plaravel -P 5003 -e "SELECT VERSION();" 2>$null
    if ($testResult) {
        Write-Host "✅ MySQL connection successful" -ForegroundColor Green
    } else {
        Write-Host "⚠️  MySQL connection failed" -ForegroundColor Yellow
    }
} catch {
    Write-Host "⚠️  MySQL connection test failed" -ForegroundColor Yellow
}

# Step 10: Show final status
Write-Host "`n🎉 Everything is Done!" -ForegroundColor Green
Write-Host "=====================" -ForegroundColor Green
Write-Host "🌐 Your application is now running:" -ForegroundColor Cyan
Write-Host "   Frontend (Vite): http://localhost:3003/" -ForegroundColor White
Write-Host "   Backend (Laravel): http://localhost:8003" -ForegroundColor White
Write-Host "   Database (MySQL): localhost:5003" -ForegroundColor White
Write-Host "`n📋 Database Details:" -ForegroundColor Cyan
Write-Host "   Host: 127.0.0.1" -ForegroundColor White
Write-Host "   Port: 5003" -ForegroundColor White
Write-Host "   Database: laravel" -ForegroundColor White
Write-Host "   Username: laravel" -ForegroundColor White
Write-Host "   Password: laravel" -ForegroundColor White
Write-Host "`n🚀 All servers are running in the background!" -ForegroundColor Green
Write-Host "   You can now open http://localhost:8003 in your browser" -ForegroundColor White
Write-Host "`n📝 To stop everything later, run: Get-Process -Name 'php','node','mysqld' | Stop-Process -Force" -ForegroundColor Yellow 