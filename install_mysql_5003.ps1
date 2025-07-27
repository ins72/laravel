# MySQL Installation and Configuration Script for Port 5003
Write-Host "🐬 MySQL Installation and Configuration Script" -ForegroundColor Green
Write-Host "=============================================" -ForegroundColor Green

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
    $ports = @(8003, 3003, 3004, 3005, 3306, 5003)
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

# Step 2: Check if MySQL is already installed
Write-Host "`n🔍 Step 2: Checking MySQL installation..." -ForegroundColor Yellow
try {
    $mysqlVersion = mysql --version 2>$null
    if ($mysqlVersion) {
        Write-Host "✅ MySQL already installed: $mysqlVersion" -ForegroundColor Green
    } else {
        Write-Host "📦 MySQL not found, installing..." -ForegroundColor Yellow
        # Install MySQL using winget
        winget install Oracle.MySQL --accept-source-agreements --accept-package-agreements
        Write-Host "✅ MySQL installed successfully" -ForegroundColor Green
    }
} catch {
    Write-Host "❌ Failed to install MySQL" -ForegroundColor Red
    exit 1
}

# Step 3: Create MySQL configuration directory
Write-Host "`n⚙️  Step 3: Creating MySQL configuration..." -ForegroundColor Yellow
try {
    $mysqlConfigDir = "C:\mysql-config"
    if (-not (Test-Path $mysqlConfigDir)) {
        New-Item -ItemType Directory -Path $mysqlConfigDir -Force
        Write-Host "✅ MySQL config directory created" -ForegroundColor Green
    }
    
    # Create my.ini configuration file
    $myIniContent = @"
[mysqld]
# Basic Settings
port=5003
bind-address=127.0.0.1
datadir=C:/mysql-data
basedir=C:/Program Files/MySQL/MySQL Server 8.0

# Character Set
character-set-server=utf8mb4
collation-server=utf8mb4_unicode_ci

# InnoDB Settings
innodb_buffer_pool_size=256M
innodb_log_file_size=64M

# Connection Settings
max_connections=200
max_connect_errors=10

# Logging
general_log=1
general_log_file=C:/mysql-data/general.log
slow_query_log=1
slow_query_log_file=C:/mysql-data/slow.log

# Security
default_authentication_plugin=mysql_native_password

[mysql]
default-character-set=utf8mb4

[client]
port=5003
default-character-set=utf8mb4
"@
    
    Set-Content -Path "$mysqlConfigDir\my.ini" -Value $myIniContent
    Write-Host "✅ MySQL configuration file created" -ForegroundColor Green
} catch {
    Write-Host "❌ Failed to create MySQL configuration" -ForegroundColor Red
}

# Step 4: Create data directory
Write-Host "`n📁 Step 4: Creating MySQL data directory..." -ForegroundColor Yellow
try {
    $mysqlDataDir = "C:\mysql-data"
    if (-not (Test-Path $mysqlDataDir)) {
        New-Item -ItemType Directory -Path $mysqlDataDir -Force
        Write-Host "✅ MySQL data directory created" -ForegroundColor Green
    }
} catch {
    Write-Host "❌ Failed to create MySQL data directory" -ForegroundColor Red
}

# Step 5: Initialize MySQL
Write-Host "`n🔧 Step 5: Initializing MySQL..." -ForegroundColor Yellow
try {
    # Initialize MySQL with custom configuration
    & "C:\Program Files\MySQL\MySQL Server 8.0\bin\mysqld.exe" --defaults-file="C:\mysql-config\my.ini" --initialize-insecure --user=mysql
    Write-Host "✅ MySQL initialized successfully" -ForegroundColor Green
} catch {
    Write-Host "❌ Failed to initialize MySQL" -ForegroundColor Red
}

# Step 6: Install MySQL as Windows Service
Write-Host "`n🔧 Step 6: Installing MySQL as Windows Service..." -ForegroundColor Yellow
try {
    # Install MySQL service
    & "C:\Program Files\MySQL\MySQL Server 8.0\bin\mysqld.exe" --defaults-file="C:\mysql-config\my.ini" --install MySQL5003
    Write-Host "✅ MySQL service installed" -ForegroundColor Green
} catch {
    Write-Host "❌ Failed to install MySQL service" -ForegroundColor Red
}

# Step 7: Start MySQL Service
Write-Host "`n🚀 Step 7: Starting MySQL service..." -ForegroundColor Yellow
try {
    Start-Service MySQL5003
    Write-Host "✅ MySQL service started" -ForegroundColor Green
} catch {
    Write-Host "❌ Failed to start MySQL service" -ForegroundColor Red
}

# Step 8: Set up root password and create database
Write-Host "`n🔐 Step 8: Setting up MySQL root password..." -ForegroundColor Yellow
try {
    # Wait a moment for MySQL to start
    Start-Sleep -Seconds 5
    
    # Set root password and create database
    $sqlCommands = @"
ALTER USER 'root'@'localhost' IDENTIFIED BY 'root';
CREATE DATABASE IF NOT EXISTS laravel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'laravel'@'localhost' IDENTIFIED BY 'laravel';
GRANT ALL PRIVILEGES ON laravel.* TO 'laravel'@'localhost';
FLUSH PRIVILEGES;
"@
    
    $sqlCommands | & "C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe" -u root -P 5003
    Write-Host "✅ MySQL root password set and database created" -ForegroundColor Green
} catch {
    Write-Host "⚠️  MySQL setup may need manual configuration" -ForegroundColor Yellow
}

# Step 9: Update Laravel .env file
Write-Host "`n⚙️  Step 9: Updating Laravel configuration..." -ForegroundColor Yellow
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
        Write-Host "✅ Laravel .env updated for MySQL on port 5003" -ForegroundColor Green
    }
} catch {
    Write-Host "❌ Failed to update Laravel configuration" -ForegroundColor Red
}

# Step 10: Test MySQL connection
Write-Host "`n🧪 Step 10: Testing MySQL connection..." -ForegroundColor Yellow
try {
    $testResult = & "C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe" -u laravel -plaravel -P 5003 -e "SELECT VERSION();" 2>$null
    if ($testResult) {
        Write-Host "✅ MySQL connection test successful" -ForegroundColor Green
    } else {
        Write-Host "⚠️  MySQL connection test failed" -ForegroundColor Yellow
    }
} catch {
    Write-Host "⚠️  MySQL connection test failed" -ForegroundColor Yellow
}

# Step 11: Show final status
Write-Host "`n🎉 MySQL Setup Complete!" -ForegroundColor Green
Write-Host "================================" -ForegroundColor Green
Write-Host "🐬 MySQL Configuration:" -ForegroundColor Cyan
Write-Host "   Port: 5003" -ForegroundColor White
Write-Host "   Host: 127.0.0.1" -ForegroundColor White
Write-Host "   Database: laravel" -ForegroundColor White
Write-Host "   Username: laravel" -ForegroundColor White
Write-Host "   Password: laravel" -ForegroundColor White
Write-Host "   Root Password: root" -ForegroundColor White
Write-Host "`n📋 Useful MySQL Commands:" -ForegroundColor Cyan
Write-Host "   Connect: mysql -u laravel -plaravel -P 5003" -ForegroundColor White
Write-Host "   Root Connect: mysql -u root -proot -P 5003" -ForegroundColor White
Write-Host "   Service Start: Start-Service MySQL5003" -ForegroundColor White
Write-Host "   Service Stop: Stop-Service MySQL5003" -ForegroundColor White
Write-Host "`n🌐 Laravel Database Setup:" -ForegroundColor Cyan
Write-Host "   Run: C:\xampp\php\php.exe artisan migrate" -ForegroundColor White
Write-Host "   Run: C:\xampp\php\php.exe artisan db:seed" -ForegroundColor White
Write-Host "`n🚀 Your MySQL is now running on port 5003!" -ForegroundColor Green 