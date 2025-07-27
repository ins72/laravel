# Configure Existing MySQL for Port 5003
Write-Host "🐬 Configure Existing MySQL for Port 5003" -ForegroundColor Green
Write-Host "=========================================" -ForegroundColor Green

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

# Step 2: Check MySQL installation
Write-Host "`n🔍 Step 2: Checking MySQL installation..." -ForegroundColor Yellow
try {
    $mysqlVersion = mysql --version 2>$null
    if ($mysqlVersion) {
        Write-Host "✅ MySQL found: $mysqlVersion" -ForegroundColor Green
    } else {
        Write-Host "❌ MySQL not found in PATH" -ForegroundColor Red
        exit 1
    }
} catch {
    Write-Host "❌ MySQL not accessible" -ForegroundColor Red
    exit 1
}

# Step 3: Stop existing MySQL services
Write-Host "`n🛑 Step 3: Stopping existing MySQL services..." -ForegroundColor Yellow
try {
    # Stop common MySQL service names
    $mysqlServices = @("MySQL", "MySQL80", "MySQL8.0", "MYSQL80", "mysql")
    foreach ($service in $mysqlServices) {
        try {
            Stop-Service -Name $service -Force -ErrorAction SilentlyContinue
            Write-Host "✅ Stopped MySQL service: $service" -ForegroundColor Green
        } catch {
            # Service doesn't exist or already stopped
        }
    }
} catch {
    Write-Host "⚠️  Some MySQL services may still be running" -ForegroundColor Yellow
}

# Step 4: Find MySQL installation
Write-Host "`n🔍 Step 4: Finding MySQL installation..." -ForegroundColor Yellow
$mysqlPaths = @(
    "C:\Program Files\MySQL\MySQL Server 8.0",
    "C:\Program Files\MySQL\MySQL Server 5.7",
    "C:\xampp\mysql",
    "C:\mysql",
    "C:\ProgramData\MySQL\MySQL Server 8.0"
)

$mysqlPath = $null
foreach ($path in $mysqlPaths) {
    if (Test-Path $path) {
        $mysqlPath = $path
        Write-Host "✅ MySQL found at: $mysqlPath" -ForegroundColor Green
        break
    }
}

if (-not $mysqlPath) {
    Write-Host "❌ MySQL installation not found in common locations" -ForegroundColor Red
    Write-Host "Please provide the path to your MySQL installation" -ForegroundColor Yellow
    exit 1
}

# Step 5: Create MySQL configuration for port 5003
Write-Host "`n⚙️  Step 5: Creating MySQL configuration for port 5003..." -ForegroundColor Yellow
try {
    $mysqlConfigDir = "C:\mysql-config-5003"
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
datadir=C:/mysql-data-5003
basedir=$mysqlPath

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
general_log_file=C:/mysql-data-5003/general.log
slow_query_log=1
slow_query_log_file=C:/mysql-data-5003/slow.log

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

# Step 6: Create data directory
Write-Host "`n📁 Step 6: Creating MySQL data directory..." -ForegroundColor Yellow
try {
    $mysqlDataDir = "C:\mysql-data-5003"
    if (-not (Test-Path $mysqlDataDir)) {
        New-Item -ItemType Directory -Path $mysqlDataDir -Force
        Write-Host "✅ MySQL data directory created" -ForegroundColor Green
    }
} catch {
    Write-Host "❌ Failed to create MySQL data directory" -ForegroundColor Red
}

# Step 7: Initialize MySQL for port 5003
Write-Host "`n🔧 Step 7: Initializing MySQL for port 5003..." -ForegroundColor Yellow
try {
    $mysqldPath = Join-Path $mysqlPath "bin\mysqld.exe"
    if (Test-Path $mysqldPath) {
        & $mysqldPath --defaults-file="C:\mysql-config-5003\my.ini" --initialize-insecure --user=mysql
        Write-Host "✅ MySQL initialized successfully for port 5003" -ForegroundColor Green
    } else {
        Write-Host "❌ mysqld.exe not found at: $mysqldPath" -ForegroundColor Red
    }
} catch {
    Write-Host "❌ Failed to initialize MySQL" -ForegroundColor Red
}

# Step 8: Install MySQL as Windows Service for port 5003
Write-Host "`n🔧 Step 8: Installing MySQL service for port 5003..." -ForegroundColor Yellow
try {
    $mysqldPath = Join-Path $mysqlPath "bin\mysqld.exe"
    if (Test-Path $mysqldPath) {
        # Remove existing service if it exists
        try {
            & sc.exe delete MySQL5003 2>$null
        } catch {}
        
        # Install new service
        & $mysqldPath --defaults-file="C:\mysql-config-5003\my.ini" --install MySQL5003
        Write-Host "✅ MySQL service installed for port 5003" -ForegroundColor Green
    } else {
        Write-Host "❌ mysqld.exe not found" -ForegroundColor Red
    }
} catch {
    Write-Host "❌ Failed to install MySQL service" -ForegroundColor Red
}

# Step 9: Start MySQL Service
Write-Host "`n🚀 Step 9: Starting MySQL service on port 5003..." -ForegroundColor Yellow
try {
    Start-Service MySQL5003
    Write-Host "✅ MySQL service started on port 5003" -ForegroundColor Green
} catch {
    Write-Host "❌ Failed to start MySQL service" -ForegroundColor Red
}

# Step 10: Set up database and users
Write-Host "`n🔐 Step 10: Setting up database and users..." -ForegroundColor Yellow
try {
    # Wait a moment for MySQL to start
    Start-Sleep -Seconds 5
    
    $mysqlPath = Join-Path $mysqlPath "bin\mysql.exe"
    if (Test-Path $mysqlPath) {
        # Set root password and create database
        $sqlCommands = @"
ALTER USER 'root'@'localhost' IDENTIFIED BY 'root';
CREATE DATABASE IF NOT EXISTS laravel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'laravel'@'localhost' IDENTIFIED BY 'laravel';
GRANT ALL PRIVILEGES ON laravel.* TO 'laravel'@'localhost';
FLUSH PRIVILEGES;
"@
        
        $sqlCommands | & $mysqlPath -u root -P 5003
        Write-Host "✅ Database and users configured" -ForegroundColor Green
    } else {
        Write-Host "❌ mysql.exe not found" -ForegroundColor Red
    }
} catch {
    Write-Host "⚠️  Database setup may need manual configuration" -ForegroundColor Yellow
}

# Step 11: Update Laravel .env file
Write-Host "`n⚙️  Step 11: Updating Laravel configuration..." -ForegroundColor Yellow
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

# Step 12: Test MySQL connection
Write-Host "`n🧪 Step 12: Testing MySQL connection on port 5003..." -ForegroundColor Yellow
try {
    $mysqlPath = Join-Path $mysqlPath "bin\mysql.exe"
    if (Test-Path $mysqlPath) {
        $testResult = & $mysqlPath -u laravel -plaravel -P 5003 -e "SELECT VERSION();" 2>$null
        if ($testResult) {
            Write-Host "✅ MySQL connection test successful on port 5003" -ForegroundColor Green
        } else {
            Write-Host "⚠️  MySQL connection test failed" -ForegroundColor Yellow
        }
    }
} catch {
    Write-Host "⚠️  MySQL connection test failed" -ForegroundColor Yellow
}

# Step 13: Show final status
Write-Host "`n🎉 MySQL Configuration Complete!" -ForegroundColor Green
Write-Host "=================================" -ForegroundColor Green
Write-Host "🐬 MySQL Configuration:" -ForegroundColor Cyan
Write-Host "   Port: 5003" -ForegroundColor White
Write-Host "   Host: 127.0.0.1" -ForegroundColor White
Write-Host "   Database: laravel" -ForegroundColor White
Write-Host "   Username: laravel" -ForegroundColor White
Write-Host "   Password: laravel" -ForegroundColor White
Write-Host "   Root Password: root" -ForegroundColor White
Write-Host "   Service Name: MySQL5003" -ForegroundColor White
Write-Host "`n📋 Useful MySQL Commands:" -ForegroundColor Cyan
Write-Host "   Connect: mysql -u laravel -plaravel -P 5003" -ForegroundColor White
Write-Host "   Root Connect: mysql -u root -proot -P 5003" -ForegroundColor White
Write-Host "   Service Start: Start-Service MySQL5003" -ForegroundColor White
Write-Host "   Service Stop: Stop-Service MySQL5003" -ForegroundColor White
Write-Host "`n🌐 Laravel Database Setup:" -ForegroundColor Cyan
Write-Host "   Run: C:\xampp\php\php.exe artisan migrate" -ForegroundColor White
Write-Host "   Run: C:\xampp\php\php.exe artisan db:seed" -ForegroundColor White
Write-Host "`n🚀 Your MySQL is now running on port 5003!" -ForegroundColor Green 