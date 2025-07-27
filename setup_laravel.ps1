# Laravel Application Setup Script
# This script sets up the Laravel application after PHP and Composer are installed

Write-Host "=== Laravel Application Setup ===" -ForegroundColor Green

# Check if PHP is available
try {
    $phpVersion = & php -v 2>&1 | Select-String "PHP" | Select-Object -First 1
    Write-Host "PHP found: $phpVersion" -ForegroundColor Green
} catch {
    Write-Host "PHP not found. Please run install_dependencies.ps1 first." -ForegroundColor Red
    exit 1
}

# Check if Composer is available
try {
    $composerVersion = & composer -V 2>&1 | Select-String "Composer" | Select-Object -First 1
    Write-Host "Composer found: $composerVersion" -ForegroundColor Green
} catch {
    Write-Host "Composer not found. Please run install_dependencies.ps1 first." -ForegroundColor Red
    exit 1
}

# Create .env file if it doesn't exist
if (!(Test-Path ".env")) {
    Write-Host "Creating .env file..." -ForegroundColor Cyan
    $envContent = @"
APP_NAME=Laravel
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_app
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="\${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_APP_NAME="\${APP_NAME}"
VITE_PUSHER_APP_KEY="\${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="\${PUSHER_HOST}"
VITE_PUSHER_PORT="\${PUSHER_PORT}"
VITE_PUSHER_SCHEME="\${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="\${PUSHER_APP_CLUSTER}"
"@
    $envContent | Out-File -FilePath ".env" -Encoding UTF8
    Write-Host ".env file created!" -ForegroundColor Green
} else {
    Write-Host ".env file already exists." -ForegroundColor Yellow
}

# Install PHP dependencies
Write-Host "Installing PHP dependencies..." -ForegroundColor Cyan
try {
    & composer install --no-interaction
    Write-Host "PHP dependencies installed successfully!" -ForegroundColor Green
} catch {
    Write-Host "Failed to install PHP dependencies." -ForegroundColor Red
    Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}

# Install Node.js dependencies
Write-Host "Installing Node.js dependencies..." -ForegroundColor Cyan
try {
    & npm install
    Write-Host "Node.js dependencies installed successfully!" -ForegroundColor Green
} catch {
    Write-Host "Failed to install Node.js dependencies." -ForegroundColor Red
    Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}

# Generate application key
Write-Host "Generating application key..." -ForegroundColor Cyan
try {
    & php artisan key:generate
    Write-Host "Application key generated!" -ForegroundColor Green
} catch {
    Write-Host "Failed to generate application key." -ForegroundColor Red
    Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
}

# Create storage link
Write-Host "Creating storage link..." -ForegroundColor Cyan
try {
    & php artisan storage:link
    Write-Host "Storage link created!" -ForegroundColor Green
} catch {
    Write-Host "Storage link already exists or failed to create." -ForegroundColor Yellow
}

# Clear caches
Write-Host "Clearing caches..." -ForegroundColor Cyan
try {
    & php artisan cache:clear
    & php artisan config:clear
    & php artisan route:clear
    & php artisan view:clear
    Write-Host "Caches cleared!" -ForegroundColor Green
} catch {
    Write-Host "Failed to clear some caches." -ForegroundColor Yellow
}

# Build assets
Write-Host "Building assets..." -ForegroundColor Cyan
try {
    & npm run build
    Write-Host "Assets built successfully!" -ForegroundColor Green
} catch {
    Write-Host "Failed to build assets." -ForegroundColor Red
    Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
}

# Test the application
Write-Host "Testing application..." -ForegroundColor Cyan
try {
    & php test_audit_fixes.php
    Write-Host "Application test completed!" -ForegroundColor Green
} catch {
    Write-Host "Failed to run application test." -ForegroundColor Yellow
}

Write-Host "=== Setup Complete ===" -ForegroundColor Green
Write-Host "Your Laravel application is now set up!" -ForegroundColor Green
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Yellow
Write-Host "1. Set up your database (MySQL/PostgreSQL)" -ForegroundColor White
Write-Host "2. Update .env file with your database credentials" -ForegroundColor White
Write-Host "3. Run: php artisan migrate" -ForegroundColor White
Write-Host "4. Start the development server: php artisan serve" -ForegroundColor White
Write-Host "5. Visit: http://localhost:8000" -ForegroundColor White 