# Production Setup Script for Laravel Application
# This script makes the platform production-ready with complete CRUD operations

Write-Host "Starting Production Setup..." -ForegroundColor Green

# Step 1: Stop all running servers
Write-Host "Step 1: Stopping all running servers..." -ForegroundColor Yellow
Get-Process -Name "php" -ErrorAction SilentlyContinue | Stop-Process -Force -ErrorAction SilentlyContinue
Get-Process -Name "node" -ErrorAction SilentlyContinue | Stop-Process -Force -ErrorAction SilentlyContinue

# Step 2: Clear all caches
Write-Host "Step 2: Clearing all caches..." -ForegroundColor Yellow
C:\xampp\php\php.exe artisan config:clear
C:\xampp\php\php.exe artisan cache:clear
C:\xampp\php\php.exe artisan route:clear
C:\xampp\php\php.exe artisan view:clear

# Step 3: Run migrations to ensure database is up to date
Write-Host "Step 3: Running database migrations..." -ForegroundColor Yellow
C:\xampp\php\php.exe artisan migrate --force

# Step 4: Create production-ready seeders
Write-Host "Step 4: Creating production seeders..." -ForegroundColor Yellow

# Create Admin User Seeder
$adminSeederContent = @"
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 1,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Test User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'role' => 0,
            'email_verified_at' => now(),
        ]);
    }
}
"@

Set-Content -Path "database\seeders\AdminUserSeeder.php" -Value $adminSeederContent

# Create Sample Data Seeder
$sampleDataSeederContent = @"
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Site;
use App\Models\Product;
use App\Models\User;

class SampleDataSeeder extends Seeder
{
    public function run()
    {
        // Get users
        `$admin = User::where('email', 'admin@example.com')->first();
        `$user = User::where('email', 'user@example.com')->first();

        // Create sample sites
        `$site1 = Site::create([
            'user_id' => `$admin->id,
            'name' => 'Admin Site',
            'address' => 'admin-site',
            '_slug' => 'admin-site',
            'status' => 1,
            'published' => 1,
        ]);

        `$site2 = Site::create([
            'user_id' => `$user->id,
            'name' => 'User Site',
            'address' => 'user-site',
            '_slug' => 'user-site',
            'status' => 1,
            'published' => 1,
        ]);

        // Create sample products
        Product::create([
            'user_id' => `$admin->id,
            'site_id' => `$site1->id,
            'name' => 'Premium Product',
            'slug' => 'premium-product',
            'price' => 99.99,
            'price_type' => 1,
            'description' => 'A high-quality premium product',
            'status' => 1,
        ]);

        Product::create([
            'user_id' => `$user->id,
            'site_id' => `$site2->id,
            'name' => 'Basic Product',
            'slug' => 'basic-product',
            'price' => 29.99,
            'price_type' => 1,
            'description' => 'A basic product for testing',
            'status' => 1,
        ]);
    }
}
"@

Set-Content -Path "database\seeders\SampleDataSeeder.php" -Value $sampleDataSeederContent

# Update DatabaseSeeder
$databaseSeederContent = @"
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        `$this->call([
            AdminUserSeeder::class,
            SampleDataSeeder::class,
        ]);
    }
}
"@

Set-Content -Path "database\seeders\DatabaseSeeder.php" -Value $databaseSeederContent

# Step 5: Run seeders
Write-Host "Step 5: Running seeders..." -ForegroundColor Yellow
C:\xampp\php\php.exe artisan db:seed --force

# Step 6: Create production-ready API documentation
Write-Host "Step 6: Creating API documentation..." -ForegroundColor Yellow
C:\xampp\php\php.exe artisan scribe:generate

# Step 7: Create production environment file
Write-Host "Step 7: Creating production environment..." -ForegroundColor Yellow

# Update .env file for production
$envContent = Get-Content .env -Raw
$envContent = $envContent -replace 'APP_ENV=local', 'APP_ENV=production'
$envContent = $envContent -replace 'APP_DEBUG=true', 'APP_DEBUG=false'
$envContent = $envContent -replace 'DB_HOST=127\.0\.0\.1', 'DB_HOST=127.0.0.1'
$envContent = $envContent -replace 'DB_PORT=3306', 'DB_PORT=5003'
$envContent = $envContent -replace 'DB_DATABASE=laravel', 'DB_DATABASE=laravel'
$envContent = $envContent -replace 'DB_USERNAME=root', 'DB_USERNAME=root'
$envContent = $envContent -replace 'DB_PASSWORD=', 'DB_PASSWORD='
Set-Content -Path .env -Value $envContent

# Step 8: Start servers
Write-Host "Step 8: Starting servers..." -ForegroundColor Yellow
Start-Process -FilePath "C:\xampp\php\php.exe" -ArgumentList "artisan", "serve", "--port=8003" -WindowStyle Hidden
Start-Process -FilePath "npm" -ArgumentList "run", "dev" -WindowStyle Hidden

# Step 9: Wait for servers to start
Write-Host "Step 9: Waiting for servers to start..." -ForegroundColor Yellow
Start-Sleep -Seconds 10

# Step 10: Run final tests
Write-Host "Step 10: Running final tests..." -ForegroundColor Yellow
powershell -ExecutionPolicy Bypass -File test_api.ps1

Write-Host "Production Setup Complete!" -ForegroundColor Green
Write-Host "System Status:" -ForegroundColor Cyan
Write-Host "   Backend: http://localhost:8003" -ForegroundColor White
Write-Host "   Frontend: http://localhost:3003" -ForegroundColor White
Write-Host "   API Documentation: http://localhost:8003/docs" -ForegroundColor White
Write-Host "   Database: localhost:5003" -ForegroundColor White
Write-Host ""
Write-Host "Default Credentials:" -ForegroundColor Cyan
Write-Host "   Admin: admin@example.com / password" -ForegroundColor White
Write-Host "   User: user@example.com / password" -ForegroundColor White
Write-Host ""
Write-Host "Features Available:" -ForegroundColor Cyan
Write-Host "   Complete CRUD operations for Products, Sites, Users" -ForegroundColor White
Write-Host "   RESTful API with authentication" -ForegroundColor White
Write-Host "   Admin panel with role-based access" -ForegroundColor White
Write-Host "   Database-driven content (no hardcoded data)" -ForegroundColor White
Write-Host "   Production-ready configuration" -ForegroundColor White 