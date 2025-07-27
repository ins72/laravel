# Laravel Application Setup Script
# This script will install PHP, Composer, and set up your Laravel application

Write-Host "=== Laravel Application Setup ===" -ForegroundColor Green
Write-Host "Installing dependencies and setting up the application..." -ForegroundColor Yellow

# Create installation directory
$installDir = "C:\laravel-setup"
if (!(Test-Path $installDir)) {
    New-Item -ItemType Directory -Path $installDir -Force
}

Set-Location $installDir

# Download PHP
Write-Host "Downloading PHP..." -ForegroundColor Cyan
$phpUrl = "https://windows.php.net/downloads/releases/php-8.2.15-Win32-vs16-x64.zip"
$phpZip = "php.zip"

try {
    Invoke-WebRequest -Uri $phpUrl -OutFile $phpZip
    Write-Host "PHP downloaded successfully!" -ForegroundColor Green
} catch {
    Write-Host "Failed to download PHP. Please download manually from: https://windows.php.net/download/" -ForegroundColor Red
    Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}

# Extract PHP
Write-Host "Extracting PHP..." -ForegroundColor Cyan
Expand-Archive -Path $phpZip -DestinationPath "php" -Force
Remove-Item $phpZip

# Download Composer
Write-Host "Downloading Composer..." -ForegroundColor Cyan
$composerUrl = "https://getcomposer.org/Composer-Setup.exe"
$composerExe = "Composer-Setup.exe"

try {
    Invoke-WebRequest -Uri $composerUrl -OutFile $composerExe
    Write-Host "Composer downloaded successfully!" -ForegroundColor Green
} catch {
    Write-Host "Failed to download Composer. Please download manually from: https://getcomposer.org/download/" -ForegroundColor Red
    Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}

# Install Composer
Write-Host "Installing Composer..." -ForegroundColor Cyan
Start-Process -FilePath $composerExe -ArgumentList "/S" -Wait
Remove-Item $composerExe

# Add PHP to PATH
Write-Host "Adding PHP to PATH..." -ForegroundColor Cyan
$phpPath = "$installDir\php"
$currentPath = [Environment]::GetEnvironmentVariable("PATH", "User")
if ($currentPath -notlike "*$phpPath*") {
    [Environment]::SetEnvironmentVariable("PATH", "$currentPath;$phpPath", "User")
    Write-Host "PHP added to PATH!" -ForegroundColor Green
}

# Copy php.ini
Write-Host "Configuring PHP..." -ForegroundColor Cyan
Copy-Item "$phpPath\php.ini-development" "$phpPath\php.ini"

# Return to Laravel directory
Set-Location "C:\Users\tmonn\OneDrive\Bureaublad\laravel"

Write-Host "=== Installation Complete ===" -ForegroundColor Green
Write-Host "Please restart your terminal to use the new PATH settings." -ForegroundColor Yellow
Write-Host "Then run: .\setup_laravel.ps1" -ForegroundColor Yellow 