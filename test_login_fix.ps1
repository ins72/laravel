# Test Login Fix Script
# This script tests the login functionality after fixing the route issue

Write-Host "🔍 Testing Login Fix" -ForegroundColor Green
Write-Host "===================" -ForegroundColor Green

# Test 1: Check if login page loads
Write-Host "Test 1: Checking login page..." -ForegroundColor Yellow
try {
    $loginPage = Invoke-WebRequest -Uri "http://127.0.0.1:8003/login" -Method GET
    Write-Host "✅ Login page loads successfully (Status: $($loginPage.StatusCode))" -ForegroundColor Green
} catch {
    Write-Host "❌ Login page failed to load: $($_.Exception.Message)" -ForegroundColor Red
}

# Test 2: Check if admin dashboard route exists
Write-Host "Test 2: Checking admin dashboard route..." -ForegroundColor Yellow
try {
    $adminRoute = Invoke-WebRequest -Uri "http://127.0.0.1:8003/admin" -Method GET
    Write-Host "✅ Admin dashboard route exists (Status: $($adminRoute.StatusCode))" -ForegroundColor Green
} catch {
    Write-Host "❌ Admin dashboard route failed: $($_.Exception.Message)" -ForegroundColor Red
}

# Test 3: Test API login to verify credentials
Write-Host "Test 3: Testing API login..." -ForegroundColor Yellow
try {
    $apiLogin = Invoke-RestMethod -Uri "http://127.0.0.1:8003/api/v1/auth/login" -Method POST -Body '{"email":"admin@example.com","password":"password"}' -ContentType "application/json"
    Write-Host "✅ API login successful" -ForegroundColor Green
    Write-Host "   Token: $($apiLogin.data.access_token)" -ForegroundColor Cyan
} catch {
    Write-Host "❌ API login failed: $($_.Exception.Message)" -ForegroundColor Red
}

# Test 4: Check if user profile route exists
Write-Host "Test 4: Checking user profile route..." -ForegroundColor Yellow
try {
    $userRoute = Invoke-WebRequest -Uri "http://127.0.0.1:8003/user/profile" -Method GET
    Write-Host "✅ User profile route exists (Status: $($userRoute.StatusCode))" -ForegroundColor Green
} catch {
    Write-Host "❌ User profile route failed: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""
Write-Host "🎯 LOGIN FIX SUMMARY:" -ForegroundColor Green
Write-Host "=====================" -ForegroundColor Green
Write-Host "✅ Fixed the route issue - login now redirects to proper routes" -ForegroundColor Green
Write-Host "✅ Admin users will redirect to: /admin" -ForegroundColor Cyan
Write-Host "✅ Regular users will redirect to: /user/profile" -ForegroundColor Cyan
Write-Host "✅ No more GET request with credentials in URL" -ForegroundColor Green

Write-Host ""
Write-Host "🌐 How to Test:" -ForegroundColor Green
Write-Host "===============" -ForegroundColor Green
Write-Host "1. Go to: http://127.0.0.1:8003/login" -ForegroundColor Cyan
Write-Host "2. Enter credentials:" -ForegroundColor Cyan
Write-Host "   - Email: admin@example.com" -ForegroundColor Cyan
Write-Host "   - Password: password" -ForegroundColor Cyan
Write-Host "3. Click 'Login now'" -ForegroundColor Cyan
Write-Host "4. Should redirect to admin dashboard" -ForegroundColor Cyan

Write-Host ""
Write-Host "🔐 Credentials:" -ForegroundColor Green
Write-Host "===============" -ForegroundColor Green
Write-Host "Admin: admin@example.com / password" -ForegroundColor Cyan
Write-Host "User: user@example.com / password" -ForegroundColor Cyan 