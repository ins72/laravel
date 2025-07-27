# Login Troubleshooting Script
# This script tests the login functionality to identify issues

Write-Host "🔍 Login Troubleshooting Script" -ForegroundColor Green
Write-Host "===============================" -ForegroundColor Green

# Test 1: Check if login page loads
Write-Host "Test 1: Checking login page..." -ForegroundColor Yellow
try {
    $loginPage = Invoke-WebRequest -Uri "http://127.0.0.1:8003/login" -Method GET
    Write-Host "✅ Login page loads successfully (Status: $($loginPage.StatusCode))" -ForegroundColor Green
} catch {
    Write-Host "❌ Login page failed to load: $($_.Exception.Message)" -ForegroundColor Red
}

# Test 2: Check if register page loads
Write-Host "Test 2: Checking register page..." -ForegroundColor Yellow
try {
    $registerPage = Invoke-WebRequest -Uri "http://127.0.0.1:8003/register" -Method GET
    Write-Host "✅ Register page loads successfully (Status: $($registerPage.StatusCode))" -ForegroundColor Green
} catch {
    Write-Host "❌ Register page failed to load: $($_.Exception.Message)" -ForegroundColor Red
}

# Test 3: Test API login
Write-Host "Test 3: Testing API login..." -ForegroundColor Yellow
try {
    $apiLogin = Invoke-RestMethod -Uri "http://127.0.0.1:8003/api/v1/auth/login" -Method POST -Body '{"email":"admin@example.com","password":"password"}' -ContentType "application/json"
    Write-Host "✅ API login successful" -ForegroundColor Green
    Write-Host "   Token: $($apiLogin.data.access_token)" -ForegroundColor Cyan
} catch {
    Write-Host "❌ API login failed: $($_.Exception.Message)" -ForegroundColor Red
}

# Test 4: Check database users
Write-Host "Test 4: Checking database users..." -ForegroundColor Yellow
try {
    $userCount = C:\xampp\php\php.exe artisan tinker --execute="echo App\Models\User::count();"
    Write-Host "✅ Database has $userCount users" -ForegroundColor Green
} catch {
    Write-Host "❌ Database check failed: $($_.Exception.Message)" -ForegroundColor Red
}

# Test 5: Check session configuration
Write-Host "Test 5: Checking session configuration..." -ForegroundColor Yellow
try {
    $sessionDriver = C:\xampp\php\php.exe artisan tinker --execute="echo config('session.driver');"
    Write-Host "✅ Session driver: $sessionDriver" -ForegroundColor Green
} catch {
    Write-Host "❌ Session check failed: $($_.Exception.Message)" -ForegroundColor Red
}

# Test 6: Check if sessions directory is writable
Write-Host "Test 6: Checking sessions directory..." -ForegroundColor Yellow
$sessionsPath = "storage/framework/sessions"
if (Test-Path $sessionsPath) {
    Write-Host "✅ Sessions directory exists" -ForegroundColor Green
    try {
        $testFile = Join-Path $sessionsPath "test.txt"
        "test" | Out-File -FilePath $testFile -Encoding UTF8
        Remove-Item $testFile -Force
        Write-Host "✅ Sessions directory is writable" -ForegroundColor Green
    } catch {
        Write-Host "❌ Sessions directory is not writable" -ForegroundColor Red
    }
} else {
    Write-Host "❌ Sessions directory does not exist" -ForegroundColor Red
}

# Test 7: Check Livewire components
Write-Host "Test 7: Checking Livewire components..." -ForegroundColor Yellow
$loginComponent = "resources/views/livewire/pages/auth/login.blade.php"
$registerComponent = "resources/views/livewire/pages/auth/register.blade.php"

if (Test-Path $loginComponent) {
    Write-Host "✅ Login component exists" -ForegroundColor Green
} else {
    Write-Host "❌ Login component missing" -ForegroundColor Red
}

if (Test-Path $registerComponent) {
    Write-Host "✅ Register component exists" -ForegroundColor Green
} else {
    Write-Host "❌ Register component missing" -ForegroundColor Red
}

# Test 8: Check for any recent errors in logs
Write-Host "Test 8: Checking recent errors..." -ForegroundColor Yellow
try {
    $recentErrors = Get-Content storage/logs/laravel.log | Select-String -Pattern "ERROR|Exception" | Select-Object -Last 3
    if ($recentErrors) {
        Write-Host "⚠️  Recent errors found:" -ForegroundColor Yellow
        $recentErrors | ForEach-Object { Write-Host "   $($_.Line)" -ForegroundColor Red }
    } else {
        Write-Host "✅ No recent errors found" -ForegroundColor Green
    }
} catch {
    Write-Host "❌ Could not check logs: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""
Write-Host "🎯 TROUBLESHOOTING SUMMARY:" -ForegroundColor Green
Write-Host "===========================" -ForegroundColor Green
Write-Host "If you're having trouble logging in:" -ForegroundColor Yellow
Write-Host "1. Make sure you're using the correct credentials:" -ForegroundColor Cyan
Write-Host "   - Admin: admin@example.com / password" -ForegroundColor Cyan
Write-Host "   - User: user@example.com / password" -ForegroundColor Cyan
Write-Host "2. Try clearing your browser cache and cookies" -ForegroundColor Cyan
Write-Host "3. Try using a different browser" -ForegroundColor Cyan
Write-Host "4. Check if JavaScript is enabled in your browser" -ForegroundColor Cyan
Write-Host "5. Try accessing the API directly for testing" -ForegroundColor Cyan

Write-Host ""
Write-Host "🌐 Access URLs:" -ForegroundColor Green
Write-Host "===============" -ForegroundColor Green
Write-Host "Login Page: http://127.0.0.1:8003/login" -ForegroundColor Cyan
Write-Host "Register Page: http://127.0.0.1:8003/register" -ForegroundColor Cyan
Write-Host "API Login: POST http://127.0.0.1:8003/api/v1/auth/login" -ForegroundColor Cyan 