# API Testing Script
Write-Host "API Testing Script" -ForegroundColor Green
Write-Host "==================" -ForegroundColor Green

# Test 1: Health Check
Write-Host "`nTest 1: Health Check" -ForegroundColor Yellow
try {
    $response = Invoke-RestMethod -Uri "http://localhost:8003/api/v1/health" -Method GET
    Write-Host "Health Check: SUCCESS" -ForegroundColor Green
    Write-Host "Response: $($response | ConvertTo-Json)" -ForegroundColor Cyan
} catch {
    Write-Host "Health Check: FAILED" -ForegroundColor Red
    Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
}

# Test 2: User Registration
Write-Host "`nTest 2: User Registration" -ForegroundColor Yellow
try {
    $registerData = @{
        name = "Test User"
        email = "test@example.com"
        password = "password123"
        password_confirmation = "password123"
    } | ConvertTo-Json

    $response = Invoke-RestMethod -Uri "http://localhost:8003/api/v1/auth/register" -Method POST -Body $registerData -ContentType "application/json"
    Write-Host "User Registration: SUCCESS" -ForegroundColor Green
    Write-Host "User ID: $($response.data.user.id)" -ForegroundColor Cyan
    Write-Host "Token: $($response.data.access_token)" -ForegroundColor Cyan
    
    # Store token for later tests
    $token = $response.data.access_token
} catch {
    Write-Host "User Registration: FAILED" -ForegroundColor Red
    Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
    $token = $null
}

# Test 3: User Login
Write-Host "`nTest 3: User Login" -ForegroundColor Yellow
try {
    $loginData = @{
        email = "test@example.com"
        password = "password123"
    } | ConvertTo-Json

    $response = Invoke-RestMethod -Uri "http://localhost:8003/api/v1/auth/login" -Method POST -Body $loginData -ContentType "application/json"
    Write-Host "User Login: SUCCESS" -ForegroundColor Green
    Write-Host "Token: $($response.data.access_token)" -ForegroundColor Cyan
    
    if (!$token) {
        $token = $response.data.access_token
    }
} catch {
    Write-Host "User Login: FAILED" -ForegroundColor Red
    Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
}

# Test 4: Get Current User (Protected Route)
Write-Host "`nTest 4: Get Current User" -ForegroundColor Yellow
if ($token) {
    try {
        $headers = @{
            "Authorization" = "Bearer $token"
            "Content-Type" = "application/json"
        }
        
        $response = Invoke-RestMethod -Uri "http://localhost:8003/api/v1/auth/me" -Method GET -Headers $headers
        Write-Host "Get Current User: SUCCESS" -ForegroundColor Green
        Write-Host "User Name: $($response.data.user.name)" -ForegroundColor Cyan
        Write-Host "User Email: $($response.data.user.email)" -ForegroundColor Cyan
    } catch {
        Write-Host "Get Current User: FAILED" -ForegroundColor Red
        Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
    }
} else {
    Write-Host "Get Current User: SKIPPED (No token available)" -ForegroundColor Yellow
}

# Test 5: Get Products (Public Route)
Write-Host "`nTest 5: Get Products" -ForegroundColor Yellow
try {
    $response = Invoke-RestMethod -Uri "http://localhost:8003/api/v1/products" -Method GET
    Write-Host "Get Products: SUCCESS" -ForegroundColor Green
    Write-Host "Products Count: $($response.data.Count)" -ForegroundColor Cyan
} catch {
    Write-Host "Get Products: FAILED" -ForegroundColor Red
    Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
}

# Test 6: Create Product (Protected Route)
Write-Host "`nTest 6: Create Product" -ForegroundColor Yellow
if ($token) {
    try {
        $productData = @{
            name = "Test Product"
            price = 99.99
            price_type = 1
            description = "This is a test product"
            status = 1
        } | ConvertTo-Json

        $headers = @{
            "Authorization" = "Bearer $token"
            "Content-Type" = "application/json"
        }
        
        $response = Invoke-RestMethod -Uri "http://localhost:8003/api/v1/products" -Method POST -Body $productData -Headers $headers
        Write-Host "Create Product: SUCCESS" -ForegroundColor Green
        Write-Host "Product ID: $($response.data.id)" -ForegroundColor Cyan
        Write-Host "Product Name: $($response.data.name)" -ForegroundColor Cyan
        
        # Store product ID for later tests
        $productId = $response.data.id
    } catch {
        Write-Host "Create Product: FAILED" -ForegroundColor Red
        Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
        $productId = $null
    }
} else {
    Write-Host "Create Product: SKIPPED (No token available)" -ForegroundColor Yellow
}

# Test 7: Get Product Details
Write-Host "`nTest 7: Get Product Details" -ForegroundColor Yellow
if ($productId) {
    try {
        $response = Invoke-RestMethod -Uri "http://localhost:8003/api/v1/products/$productId" -Method GET
        Write-Host "Get Product Details: SUCCESS" -ForegroundColor Green
        Write-Host "Product Name: $($response.data.name)" -ForegroundColor Cyan
        Write-Host "Product Price: $($response.data.price)" -ForegroundColor Cyan
    } catch {
        Write-Host "Get Product Details: FAILED" -ForegroundColor Red
        Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
    }
} else {
    Write-Host "Get Product Details: SKIPPED (No product ID available)" -ForegroundColor Yellow
}

# Test 8: Search Products
Write-Host "`nTest 8: Search Products" -ForegroundColor Yellow
try {
    $response = Invoke-RestMethod -Uri "http://localhost:8003/api/v1/products/search?q=test" -Method GET
    Write-Host "Search Products: SUCCESS" -ForegroundColor Green
    Write-Host "Search Results Count: $($response.data.Count)" -ForegroundColor Cyan
} catch {
    Write-Host "Search Products: FAILED" -ForegroundColor Red
    Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
}

# Test 9: Get API Documentation
Write-Host "`nTest 9: API Documentation" -ForegroundColor Yellow
try {
    $response = Invoke-RestMethod -Uri "http://localhost:8003/api/v1/docs" -Method GET
    Write-Host "API Documentation: SUCCESS" -ForegroundColor Green
    Write-Host "Documentation loaded successfully" -ForegroundColor Cyan
} catch {
    Write-Host "API Documentation: FAILED" -ForegroundColor Red
    Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
}

# Test 10: User Logout (Protected Route)
Write-Host "`nTest 10: User Logout" -ForegroundColor Yellow
if ($token) {
    try {
        $headers = @{
            "Authorization" = "Bearer $token"
            "Content-Type" = "application/json"
        }
        
        $response = Invoke-RestMethod -Uri "http://localhost:8003/api/v1/auth/logout" -Method POST -Headers $headers
        Write-Host "User Logout: SUCCESS" -ForegroundColor Green
        Write-Host "Message: $($response.message)" -ForegroundColor Cyan
    } catch {
        Write-Host "User Logout: FAILED" -ForegroundColor Red
        Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
    }
} else {
    Write-Host "User Logout: SKIPPED (No token available)" -ForegroundColor Yellow
}

Write-Host "`nAPI Testing Complete!" -ForegroundColor Green
Write-Host "=====================" -ForegroundColor Green
Write-Host "All API endpoints have been tested." -ForegroundColor Cyan
Write-Host "Check the results above for any failures." -ForegroundColor Cyan 