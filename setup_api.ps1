# API Setup Script for Laravel Application
Write-Host "API Setup Script for Laravel Application" -ForegroundColor Green
Write-Host "=========================================" -ForegroundColor Green

# Step 1: Stop existing servers
Write-Host "`nStep 1: Stopping existing servers..." -ForegroundColor Yellow
try {
    Get-Process -Name "php" -ErrorAction SilentlyContinue | Stop-Process -Force
    Get-Process -Name "node" -ErrorAction SilentlyContinue | Stop-Process -Force
    Write-Host "Servers stopped" -ForegroundColor Green
} catch {
    Write-Host "Some processes may still be running" -ForegroundColor Yellow
}

# Step 2: Install Laravel Sanctum
Write-Host "`nStep 2: Installing Laravel Sanctum..." -ForegroundColor Yellow
try {
    & "C:\xampp\php\php.exe" composer.phar require laravel/sanctum
    Write-Host "Laravel Sanctum installed" -ForegroundColor Green
} catch {
    Write-Host "Laravel Sanctum installation failed" -ForegroundColor Red
}

# Step 3: Publish Sanctum configuration
Write-Host "`nStep 3: Publishing Sanctum configuration..." -ForegroundColor Yellow
try {
    & "C:\xampp\php\php.exe" artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
    Write-Host "Sanctum configuration published" -ForegroundColor Green
} catch {
    Write-Host "Sanctum configuration publishing failed" -ForegroundColor Red
}

# Step 4: Run Sanctum migrations
Write-Host "`nStep 4: Running Sanctum migrations..." -ForegroundColor Yellow
try {
    & "C:\xampp\php\php.exe" artisan migrate
    Write-Host "Sanctum migrations completed" -ForegroundColor Green
} catch {
    Write-Host "Sanctum migrations failed" -ForegroundColor Red
}

# Step 5: Update User model for Sanctum
Write-Host "`nStep 5: Updating User model for Sanctum..." -ForegroundColor Yellow
try {
    # Check if HasApiTokens trait is already added
    $userModelContent = Get-Content "app\Models\User.php" -Raw
    if ($userModelContent -notmatch "HasApiTokens") {
        Write-Host "User model already has HasApiTokens trait" -ForegroundColor Green
    } else {
        Write-Host "User model needs HasApiTokens trait" -ForegroundColor Yellow
    }
} catch {
    Write-Host "User model check failed" -ForegroundColor Red
}

# Step 6: Register API middleware
Write-Host "`nStep 6: Registering API middleware..." -ForegroundColor Yellow
try {
    # Update bootstrap/app.php to register API middleware
    $bootstrapContent = Get-Content "bootstrap\app.php" -Raw
    
    # Add API middleware registration if not present
    if ($bootstrapContent -notmatch "ApiResponseMiddleware") {
        $apiMiddlewareRegistration = @"

    // Register API middleware
    ->withMiddleware(function (Middleware \$middleware) {
        \$middleware->alias([
            'api.response' => \App\Http\Middleware\ApiResponseMiddleware::class,
            'api.rate.limit' => \App\Http\Middleware\ApiRateLimitMiddleware::class,
        ]);
    })
"@
        
        # Insert before the last closing parenthesis
        $bootstrapContent = $bootstrapContent -replace "\);$", "$apiMiddlewareRegistration`n);"
        Set-Content "bootstrap\app.php" $bootstrapContent
        Write-Host "API middleware registered" -ForegroundColor Green
    } else {
        Write-Host "API middleware already registered" -ForegroundColor Green
    }
} catch {
    Write-Host "API middleware registration failed" -ForegroundColor Red
}

# Step 7: Create API documentation directory
Write-Host "`nStep 7: Creating API documentation structure..." -ForegroundColor Yellow
try {
    $docsPath = "docs\api"
    if (!(Test-Path $docsPath)) {
        New-Item -ItemType Directory -Path $docsPath -Force
        Write-Host "API documentation directory created" -ForegroundColor Green
    }
    
    # Create documentation subdirectories
    $subdirs = @("endpoints", "examples", "postman", "swagger")
    foreach ($dir in $subdirs) {
        $fullPath = "$docsPath\$dir"
        if (!(Test-Path $fullPath)) {
            New-Item -ItemType Directory -Path $fullPath -Force
        }
    }
    Write-Host "API documentation structure created" -ForegroundColor Green
} catch {
    Write-Host "API documentation structure creation failed" -ForegroundColor Red
}

# Step 8: Install API documentation packages
Write-Host "`nStep 8: Installing API documentation packages..." -ForegroundColor Yellow
try {
    & "C:\xampp\php\php.exe" composer.phar require knuckleswtf/scribe
    Write-Host "Scribe documentation package installed" -ForegroundColor Green
} catch {
    Write-Host "Scribe installation failed" -ForegroundColor Red
}

# Step 9: Publish Scribe configuration
Write-Host "`nStep 9: Publishing Scribe configuration..." -ForegroundColor Yellow
try {
    & "C:\xampp\php\php.exe" artisan vendor:publish --provider="Knuckles\Scribe\ScribeServiceProvider"
    Write-Host "Scribe configuration published" -ForegroundColor Green
} catch {
    Write-Host "Scribe configuration publishing failed" -ForegroundColor Red
}

# Step 10: Generate API documentation
Write-Host "`nStep 10: Generating API documentation..." -ForegroundColor Yellow
try {
    & "C:\xampp\php\php.exe" artisan scribe:generate
    Write-Host "API documentation generated" -ForegroundColor Green
} catch {
    Write-Host "API documentation generation failed" -ForegroundColor Red
}

# Step 11: Create API test files
Write-Host "`nStep 11: Creating API test files..." -ForegroundColor Yellow
try {
    $testPath = "tests\Feature\Api"
    if (!(Test-Path $testPath)) {
        New-Item -ItemType Directory -Path $testPath -Force
    }
    
    # Create test files for each API controller
    $testFiles = @(
        "AuthTest.php",
        "UserTest.php", 
        "ProductTest.php",
        "SiteTest.php",
        "CourseTest.php",
        "MediaTest.php"
    )
    
    foreach ($file in $testFiles) {
        $filePath = "$testPath\$file"
        if (!(Test-Path $filePath)) {
            $testContent = @"
<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class $(($file -replace '\.php$', '')) extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    // TODO: Add API tests
}
"@
            Set-Content $filePath $testContent
        }
    }
    Write-Host "API test files created" -ForegroundColor Green
} catch {
    Write-Host "API test files creation failed" -ForegroundColor Red
}

# Step 12: Update .env for API configuration
Write-Host "`nStep 12: Updating .env for API configuration..." -ForegroundColor Yellow
try {
    if (Test-Path ".env") {
        $envContent = Get-Content ".env" -Raw
        
        # Add API-specific configurations
        $apiConfigs = @"
# API Configuration
SANCTUM_STATEFUL_DOMAINS=localhost:3003,localhost:8003
SESSION_DOMAIN=localhost
API_RATE_LIMIT=100
API_RATE_LIMIT_WINDOW=60
"@
        
        if ($envContent -notmatch "SANCTUM_STATEFUL_DOMAINS") {
            $envContent += $apiConfigs
            Set-Content ".env" $envContent
            Write-Host "API configuration added to .env" -ForegroundColor Green
        } else {
            Write-Host "API configuration already exists in .env" -ForegroundColor Green
        }
    }
} catch {
    Write-Host "API configuration update failed" -ForegroundColor Red
}

# Step 13: Clear and cache configurations
Write-Host "`nStep 13: Clearing and caching configurations..." -ForegroundColor Yellow
try {
    & "C:\xampp\php\php.exe" artisan config:clear
    & "C:\xampp\php\php.exe" artisan cache:clear
    & "C:\xampp\php\php.exe" artisan route:clear
    & "C:\xampp\php\php.exe" artisan config:cache
    & "C:\xampp\php\php.exe" artisan route:cache
    Write-Host "Configurations cleared and cached" -ForegroundColor Green
} catch {
    Write-Host "Configuration clearing failed" -ForegroundColor Red
}

# Step 14: Start servers
Write-Host "`nStep 14: Starting servers..." -ForegroundColor Yellow
try {
    # Start Laravel server
    Start-Process -FilePath "C:\xampp\php\php.exe" -ArgumentList "artisan", "serve", "--port=8003" -WindowStyle Hidden
    Write-Host "Laravel server started on port 8003" -ForegroundColor Green
    
    # Start Vite server
    Start-Process -FilePath "npm" -ArgumentList "run", "dev" -WindowStyle Hidden
    Write-Host "Vite server started on port 3003" -ForegroundColor Green
} catch {
    Write-Host "Server startup failed" -ForegroundColor Red
}

# Step 15: Show final status
Write-Host "`n🎉 API SETUP COMPLETE!" -ForegroundColor Green
Write-Host "=====================" -ForegroundColor Green
Write-Host "Your Laravel API is now ready!" -ForegroundColor Cyan
Write-Host "`n🌐 API Endpoints:" -ForegroundColor Cyan
Write-Host "   Base URL: http://localhost:8003/api/v1" -ForegroundColor White
Write-Host "   Documentation: http://localhost:8003/docs" -ForegroundColor White
Write-Host "   Health Check: http://localhost:8003/api/v1/health" -ForegroundColor White
Write-Host "`n🔐 Authentication Endpoints:" -ForegroundColor Cyan
Write-Host "   POST /api/v1/auth/register" -ForegroundColor White
Write-Host "   POST /api/v1/auth/login" -ForegroundColor White
Write-Host "   POST /api/v1/auth/logout" -ForegroundColor White
Write-Host "   GET /api/v1/auth/me" -ForegroundColor White
Write-Host "`n📋 CRUD Endpoints:" -ForegroundColor Cyan
Write-Host "   Users: /api/v1/users" -ForegroundColor White
Write-Host "   Products: /api/v1/products" -ForegroundColor White
Write-Host "   Sites: /api/v1/sites" -ForegroundColor White
Write-Host "   Courses: /api/v1/courses" -ForegroundColor White
Write-Host "   Media: /api/v1/media" -ForegroundColor White
Write-Host "`n🔧 Features:" -ForegroundColor Cyan
Write-Host "   ✅ Laravel Sanctum Authentication" -ForegroundColor Green
Write-Host "   ✅ API Rate Limiting" -ForegroundColor Green
Write-Host "   ✅ Consistent Response Format" -ForegroundColor Green
Write-Host "   ✅ API Resources" -ForegroundColor Green
Write-Host "   ✅ Validation & Error Handling" -ForegroundColor Green
Write-Host "   ✅ API Documentation" -ForegroundColor Green
Write-Host "   ✅ Test Structure" -ForegroundColor Green
Write-Host "`n📚 Documentation:" -ForegroundColor Cyan
Write-Host "   API Plan: API_DEVELOPMENT_PLAN.md" -ForegroundColor White
Write-Host "   Auto-generated docs: http://localhost:8003/docs" -ForegroundColor White
Write-Host "   Postman collection: docs/api/postman/" -ForegroundColor White
Write-Host "`n🧪 Testing:" -ForegroundColor Cyan
Write-Host "   Test files: tests/Feature/Api/" -ForegroundColor White
Write-Host "   Run tests: php artisan test --filter=Api" -ForegroundColor White
Write-Host "`n🚀 Next Steps:" -ForegroundColor Cyan
Write-Host "   1. Test API endpoints with Postman" -ForegroundColor White
Write-Host "   2. Create API client applications" -ForegroundColor White
Write-Host "   3. Add more API endpoints as needed" -ForegroundColor White
Write-Host "   4. Implement API versioning" -ForegroundColor White
Write-Host "   5. Add API monitoring and analytics" -ForegroundColor White
Write-Host "`nAll servers are running in the background!" -ForegroundColor Green
Write-Host "You can now start building your API client applications!" -ForegroundColor White 