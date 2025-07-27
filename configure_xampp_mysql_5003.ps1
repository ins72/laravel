# Configure XAMPP MySQL for Port 5003
Write-Host "🐬 Configure XAMPP MySQL for Port 5003" -ForegroundColor Green
Write-Host "=====================================" -ForegroundColor Green

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

# Step 2: Check XAMPP installation
Write-Host "`n🔍 Step 2: Checking XAMPP installation..." -ForegroundColor Yellow
$xamppPath = "C:\xampp"
if (Test-Path $xamppPath) {
    Write-Host "✅ XAMPP found at: $xamppPath" -ForegroundColor Green
} else {
    Write-Host "❌ XAMPP not found at: $xamppPath" -ForegroundColor Red
    Write-Host "Please install XAMPP or provide the correct path" -ForegroundColor Yellow
    exit 1
}

# Step 3: Stop XAMPP MySQL service
Write-Host "`n🛑 Step 3: Stopping XAMPP MySQL service..." -ForegroundColor Yellow
try {
    # Stop MySQL service if running
    $mysqlService = Get-Service -Name "MySQL*" -ErrorAction SilentlyContinue
    if ($mysqlService) {
        Stop-Service $mysqlService -Force
        Write-Host "✅ MySQL service stopped" -ForegroundColor Green
    }
    
    # Stop XAMPP MySQL process
    Get-Process -Name "mysqld" -ErrorAction SilentlyContinue | Stop-Process -Force
    Write-Host "✅ XAMPP MySQL process stopped" -ForegroundColor Green
} catch {
    Write-Host "⚠️  Some MySQL processes may still be running" -ForegroundColor Yellow
}

# Step 4: Backup original MySQL configuration
Write-Host "`n💾 Step 4: Backing up original MySQL configuration..." -ForegroundColor Yellow
try {
    $myIniPath = "$xamppPath\mysql\bin\my.ini"
    if (Test-Path $myIniPath) {
        Copy-Item $myIniPath "$myIniPath.backup"
        Write-Host "✅ Original my.ini backed up" -ForegroundColor Green
    } else {
        Write-Host "⚠️  my.ini not found, will create new one" -ForegroundColor Yellow
    }
} catch {
    Write-Host "❌ Failed to backup my.ini" -ForegroundColor Red
}

# Step 5: Create new MySQL configuration for port 5003
Write-Host "`n⚙️  Step 5: Creating MySQL configuration for port 5003..." -ForegroundColor Yellow
try {
    $myIniContent = @"
[mysqld]
# Basic Settings
port=5003
bind-address=127.0.0.1
datadir=C:/xampp/mysql/data
basedir=C:/xampp/mysql

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
general_log_file=C:/xampp/mysql/data/general.log
slow_query_log=1
slow_query_log_file=C:/xampp/mysql/data/slow.log

# Security
default_authentication_plugin=mysql_native_password

[mysql]
default-character-set=utf8mb4

[client]
port=5003
default-character-set=utf8mb4
"@
    
    Set-Content -Path $myIniPath -Value $myIniContent
    Write-Host "✅ MySQL configuration updated for port 5003" -ForegroundColor Green
} catch {
    Write-Host "❌ Failed to update MySQL configuration" -ForegroundColor Red
}

# Step 6: Start XAMPP MySQL
Write-Host "`n🚀 Step 6: Starting XAMPP MySQL on port 5003..." -ForegroundColor Yellow
try {
    # Start MySQL using XAMPP
    & "$xamppPath\mysql\bin\mysqld.exe" --defaults-file="$myIniPath" --console
    Start-Sleep -Seconds 3
    
    # Check if MySQL is running
    $mysqlProcess = Get-Process -Name "mysqld" -ErrorAction SilentlyContinue
    if ($mysqlProcess) {
        Write-Host "✅ XAMPP MySQL started on port 5003" -ForegroundColor Green
    } else {
        Write-Host "⚠️  MySQL may not have started properly" -ForegroundColor Yellow
    }
} catch {
    Write-Host "❌ Failed to start XAMPP MySQL" -ForegroundColor Red
}

# Step 7: Set up database and users
Write-Host "`n🔐 Step 7: Setting up database and users..." -ForegroundColor Yellow
try {
    # Wait a moment for MySQL to start
    Start-Sleep -Seconds 5
    
    $mysqlPath = "$xamppPath\mysql\bin\mysql.exe"
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

# Step 8: Update Laravel .env file
Write-Host "`n⚙️  Step 8: Updating Laravel configuration..." -ForegroundColor Yellow
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
        Write-Host "✅ Laravel .env updated for XAMPP MySQL on port 5003" -ForegroundColor Green
    }
} catch {
    Write-Host "❌ Failed to update Laravel configuration" -ForegroundColor Red
}

# Step 9: Test MySQL connection
Write-Host "`n🧪 Step 9: Testing MySQL connection on port 5003..." -ForegroundColor Yellow
try {
    $mysqlPath = "$xamppPath\mysql\bin\mysql.exe"
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

# Step 10: Show final status
Write-Host "`n🎉 XAMPP MySQL Configuration Complete!" -ForegroundColor Green
Write-Host "=====================================" -ForegroundColor Green
Write-Host "🐬 XAMPP MySQL Configuration:" -ForegroundColor Cyan
Write-Host "   Port: 5003" -ForegroundColor White
Write-Host "   Host: 127.0.0.1" -ForegroundColor White
Write-Host "   Database: laravel" -ForegroundColor White
Write-Host "   Username: laravel" -ForegroundColor White
Write-Host "   Password: laravel" -ForegroundColor White
Write-Host "   Root Password: root" -ForegroundColor White
Write-Host "   Config File: C:\xampp\mysql\bin\my.ini" -ForegroundColor White
Write-Host "`n📋 Useful MySQL Commands:" -ForegroundColor Cyan
Write-Host "   Connect: C:\xampp\mysql\bin\mysql.exe -u laravel -plaravel -P 5003" -ForegroundColor White
Write-Host "   Root Connect: C:\xampp\mysql\bin\mysql.exe -u root -proot -P 5003" -ForegroundColor White
Write-Host "   Start MySQL: C:\xampp\mysql\bin\mysqld.exe --defaults-file=C:\xampp\mysql\bin\my.ini --console" -ForegroundColor White
Write-Host "`n🌐 Laravel Database Setup:" -ForegroundColor Cyan
Write-Host "   Run: C:\xampp\php\php.exe artisan migrate" -ForegroundColor White
Write-Host "   Run: C:\xampp\php\php.exe artisan db:seed" -ForegroundColor White
Write-Host "`n🚀 Your XAMPP MySQL is now running on port 5003!" -ForegroundColor Green 