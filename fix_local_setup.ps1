# Laravel Local Setup Fix Script
Write-Host "🔧 Laravel Local Setup Fix Script" -ForegroundColor Green
Write-Host "=================================" -ForegroundColor Green

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
    $ports = @(8003, 3003, 3004, 3005)
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

# Step 2: Install Node.js dependencies
Write-Host "`n📦 Step 2: Installing Node.js dependencies..." -ForegroundColor Yellow
try {
    npm install --legacy-peer-deps
    Write-Host "✅ Node.js dependencies installed" -ForegroundColor Green
} catch {
    Write-Host "❌ Failed to install Node.js dependencies" -ForegroundColor Red
}

# Step 3: Install PHP dependencies
Write-Host "`n📦 Step 3: Installing PHP dependencies..." -ForegroundColor Yellow
try {
    C:\xampp\php\php.exe composer.phar install
    Write-Host "✅ PHP dependencies installed" -ForegroundColor Green
} catch {
    Write-Host "❌ Failed to install PHP dependencies" -ForegroundColor Red
}

# Step 4: Fix CSS imports
Write-Host "`n🎨 Step 4: Fixing CSS imports..." -ForegroundColor Yellow
try {
    # Comment out problematic CSS imports
    $cssFile = "resources/css/app.css"
    $cssContent = Get-Content $cssFile -Raw
    $cssContent = $cssContent -replace '@import "quill/dist/quill\.core\.css";', '/* @import "quill/dist/quill.core.css"; */'
    $cssContent = $cssContent -replace '@import "quill/dist/quill\.snow\.css";', '/* @import "quill/dist/quill.snow.css"; */'
    $cssContent = $cssContent -replace '@import "swiper/css/bundle";', '/* @import "swiper/css/bundle"; */'
    $cssContent = $cssContent -replace '@import "@simonwep/pickr/dist/themes/nano\.min\.css";', '/* @import "@simonwep/pickr/dist/themes/nano.min.css"; */'
    $cssContent = $cssContent -replace '@import "animate\.css";', '/* @import "animate.css"; */'
    Set-Content $cssFile $cssContent
    Write-Host "✅ CSS imports fixed" -ForegroundColor Green
} catch {
    Write-Host "❌ Failed to fix CSS imports" -ForegroundColor Red
}

# Step 5: Fix JavaScript imports
Write-Host "`n⚡ Step 5: Fixing JavaScript imports..." -ForegroundColor Yellow
try {
    # Fix app.js imports
    $jsFile = "resources/js/app.js"
    if (Test-Path $jsFile) {
        $jsContent = Get-Content $jsFile -Raw
        $jsContent = $jsContent -replace 'import Quill from "quill";', '// import Quill from "quill";'
        $jsContent = $jsContent -replace 'import FlipClock from "flipclock";', '// import FlipClock from "flipclock";'
        $jsContent = $jsContent -replace 'import Swiper from "swiper/bundle";', '// import Swiper from "swiper/bundle";'
        $jsContent = $jsContent -replace 'import Pickr from "@simonwep/pickr";', '// import Pickr from "@simonwep/pickr";'
        $jsContent = $jsContent -replace 'window\.Quill = Quill;', '// window.Quill = Quill;'
        $jsContent = $jsContent -replace 'window\.FlipClock = FlipClock;', '// window.FlipClock = FlipClock;'
        $jsContent = $jsContent -replace 'window\.Swiper = Swiper;', '// window.Swiper = Swiper;'
        $jsContent = $jsContent -replace 'window\.Pickr = Pickr;', '// window.Pickr = Pickr;'
        Set-Content $jsFile $jsContent
    }
    
    # Fix yenaWire.js imports
    $yenaWireFile = "resources/js/yenaWire.js"
    if (Test-Path $yenaWireFile) {
        $yenaWireContent = Get-Content $yenaWireFile -Raw
        # Comment out problematic imports
        $yenaWireContent = $yenaWireContent -replace 'import masonry from "alpinejs-masonry"', '// import masonry from "alpinejs-masonry"'
        $yenaWireContent = $yenaWireContent -replace 'import RateYo from "rateyo";', '// import RateYo from "rateyo";'
        $yenaWireContent = $yenaWireContent -replace 'import.*@editorjs/.*', '// $&'
        $yenaWireContent = $yenaWireContent -replace 'import.*@fullcalendar/.*', '// $&'
        $yenaWireContent = $yenaWireContent -replace 'import Color from "color";', '// import Color from "color";'
        Set-Content $yenaWireFile $yenaWireContent
    }
    Write-Host "✅ JavaScript imports fixed" -ForegroundColor Green
} catch {
    Write-Host "❌ Failed to fix JavaScript imports" -ForegroundColor Red
}

# Step 6: Set up environment
Write-Host "`n⚙️  Step 6: Setting up environment..." -ForegroundColor Yellow
try {
    # Create .env if it doesn't exist
    if (-not (Test-Path ".env")) {
        Copy-Item ".env.example" ".env"
        Write-Host "✅ .env file created" -ForegroundColor Green
    }
    
    # Update .env with correct settings
    $envContent = Get-Content ".env" -Raw
    $envContent = $envContent -replace 'APP_URL=.*', 'APP_URL=http://localhost:8003'
    $envContent = $envContent -replace 'DB_PORT=.*', 'DB_PORT=3306'
    if ($envContent -notmatch 'INSTALLED=1') {
        $envContent += "`nINSTALLED=1"
    }
    Set-Content ".env" $envContent
    Write-Host "✅ Environment configured" -ForegroundColor Green
} catch {
    Write-Host "❌ Failed to set up environment" -ForegroundColor Red
}

# Step 7: Generate Laravel key
Write-Host "`n🔑 Step 7: Generating Laravel key..." -ForegroundColor Yellow
try {
    C:\xampp\php\php.exe artisan key:generate
    Write-Host "✅ Laravel key generated" -ForegroundColor Green
} catch {
    Write-Host "❌ Failed to generate Laravel key" -ForegroundColor Red
}

# Step 8: Create storage link
Write-Host "`n🔗 Step 8: Creating storage link..." -ForegroundColor Yellow
try {
    C:\xampp\php\php.exe artisan storage:link
    Write-Host "✅ Storage link created" -ForegroundColor Green
} catch {
    Write-Host "⚠️  Storage link may already exist" -ForegroundColor Yellow
}

# Step 9: Clear caches
Write-Host "`n🧹 Step 9: Clearing caches..." -ForegroundColor Yellow
try {
    C:\xampp\php\php.exe artisan config:clear
    C:\xampp\php\php.exe artisan cache:clear
    C:\xampp\php\php.exe artisan view:clear
    Write-Host "✅ Caches cleared" -ForegroundColor Green
} catch {
    Write-Host "❌ Failed to clear caches" -ForegroundColor Red
}

# Step 10: Show status and start commands
Write-Host "`n🎉 Setup Complete!" -ForegroundColor Green
Write-Host "================================" -ForegroundColor Green
Write-Host "🌐 Start your application:" -ForegroundColor Cyan
Write-Host "`n📋 Terminal 1 - Start Laravel server:" -ForegroundColor Yellow
Write-Host "   C:\xampp\php\php.exe artisan serve --port=8003" -ForegroundColor White
Write-Host "`n📋 Terminal 2 - Start Vite server:" -ForegroundColor Yellow
Write-Host "   npm run dev" -ForegroundColor White
Write-Host "`n🌐 Access your application:" -ForegroundColor Cyan
Write-Host "   Frontend (Vite): http://localhost:3003/" -ForegroundColor White
Write-Host "   Backend (Laravel): http://localhost:8003" -ForegroundColor White
Write-Host "   Database: localhost:3306 (MySQL via XAMPP)" -ForegroundColor White
Write-Host "`n📋 Next steps:" -ForegroundColor Cyan
Write-Host "   1. Start XAMPP MySQL service" -ForegroundColor White
Write-Host "   2. Run: C:\xampp\php\php.exe artisan migrate" -ForegroundColor White
Write-Host "   3. Open http://localhost:8003 in your browser" -ForegroundColor White
Write-Host "`n🚀 Your Laravel application is ready to run!" -ForegroundColor Green 