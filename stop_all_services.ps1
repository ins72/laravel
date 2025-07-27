# Stop All Services Script
# This script stops all running services and closes all instances

Write-Host "🛑 Stopping All Services and Closing Instances" -ForegroundColor Red
Write-Host "=============================================" -ForegroundColor Red

# Step 1: Stop Laravel server (port 8003)
Write-Host "Step 1: Stopping Laravel server on port 8003..." -ForegroundColor Yellow
try {
    $laravelProcesses = Get-Process | Where-Object { $_.ProcessName -eq "php" -and $_.CommandLine -like "*artisan serve*" }
    if ($laravelProcesses) {
        foreach ($process in $laravelProcesses) {
            Write-Host "Stopping Laravel process (PID: $($process.Id))..." -ForegroundColor Cyan
            Stop-Process -Id $process.Id -Force
        }
        Write-Host "✅ Laravel server stopped" -ForegroundColor Green
    } else {
        Write-Host "ℹ️  No Laravel server processes found" -ForegroundColor Yellow
    }
} catch {
    Write-Host "❌ Error stopping Laravel server: $($_.Exception.Message)" -ForegroundColor Red
}

# Step 2: Stop Vite development server (port 3003)
Write-Host "Step 2: Stopping Vite development server on port 3003..." -ForegroundColor Yellow
try {
    $viteProcesses = Get-Process | Where-Object { $_.ProcessName -eq "node" -and $_.CommandLine -like "*vite*" }
    if ($viteProcesses) {
        foreach ($process in $viteProcesses) {
            Write-Host "Stopping Vite process (PID: $($process.Id))..." -ForegroundColor Cyan
            Stop-Process -Id $process.Id -Force
        }
        Write-Host "✅ Vite development server stopped" -ForegroundColor Green
    } else {
        Write-Host "ℹ️  No Vite server processes found" -ForegroundColor Yellow
    }
} catch {
    Write-Host "❌ Error stopping Vite server: $($_.Exception.Message)" -ForegroundColor Red
}

# Step 3: Stop any remaining PHP processes
Write-Host "Step 3: Stopping any remaining PHP processes..." -ForegroundColor Yellow
try {
    $phpProcesses = Get-Process | Where-Object { $_.ProcessName -eq "php" }
    if ($phpProcesses) {
        foreach ($process in $phpProcesses) {
            Write-Host "Stopping PHP process (PID: $($process.Id))..." -ForegroundColor Cyan
            Stop-Process -Id $process.Id -Force
        }
        Write-Host "✅ PHP processes stopped" -ForegroundColor Green
    } else {
        Write-Host "ℹ️  No PHP processes found" -ForegroundColor Yellow
    }
} catch {
    Write-Host "❌ Error stopping PHP processes: $($_.Exception.Message)" -ForegroundColor Red
}

# Step 4: Stop any remaining Node.js processes
Write-Host "Step 4: Stopping any remaining Node.js processes..." -ForegroundColor Yellow
try {
    $nodeProcesses = Get-Process | Where-Object { $_.ProcessName -eq "node" }
    if ($nodeProcesses) {
        foreach ($process in $nodeProcesses) {
            Write-Host "Stopping Node.js process (PID: $($process.Id))..." -ForegroundColor Cyan
            Stop-Process -Id $process.Id -Force
        }
        Write-Host "✅ Node.js processes stopped" -ForegroundColor Green
    } else {
        Write-Host "ℹ️  No Node.js processes found" -ForegroundColor Yellow
    }
} catch {
    Write-Host "❌ Error stopping Node.js processes: $($_.Exception.Message)" -ForegroundColor Red
}

# Step 5: Check if ports are still in use
Write-Host "Step 5: Checking if ports are still in use..." -ForegroundColor Yellow
Start-Sleep -Seconds 2

$port8003 = netstat -an | findstr :8003
$port3003 = netstat -an | findstr :3003
$port5003 = netstat -an | findstr :5003

Write-Host "Port 8003 (Laravel): $($port8003 ? 'Still in use' : 'Free')" -ForegroundColor $(if($port8003) { 'Red' } else { 'Green' })
Write-Host "Port 3003 (Vite): $($port3003 ? 'Still in use' : 'Free')" -ForegroundColor $(if($port3003) { 'Red' } else { 'Green' })
Write-Host "Port 5003 (MySQL): $($port5003 ? 'Still in use' : 'Free')" -ForegroundColor $(if($port5003) { 'Red' } else { 'Green' })

# Step 6: Force kill any remaining processes on these ports
if ($port8003 -or $port3003 -or $port5003) {
    Write-Host "Step 6: Force killing any remaining processes on these ports..." -ForegroundColor Yellow
    
    # Kill processes on port 8003
    if ($port8003) {
        try {
            $processes = netstat -ano | findstr :8003
            foreach ($line in $processes) {
                $parts = $line -split '\s+'
                $processId = $parts[-1]
                if ($processId -match '^\d+$') {
                    Write-Host "Force killing process on port 8003 (PID: $processId)..." -ForegroundColor Cyan
                    Stop-Process -Id $processId -Force -ErrorAction SilentlyContinue
                }
            }
        } catch {
            Write-Host "❌ Error force killing processes on port 8003" -ForegroundColor Red
        }
    }
    
    # Kill processes on port 3003
    if ($port3003) {
        try {
            $processes = netstat -ano | findstr :3003
            foreach ($line in $processes) {
                $parts = $line -split '\s+'
                $processId = $parts[-1]
                if ($processId -match '^\d+$') {
                    Write-Host "Force killing process on port 3003 (PID: $processId)..." -ForegroundColor Cyan
                    Stop-Process -Id $processId -Force -ErrorAction SilentlyContinue
                }
            }
        } catch {
            Write-Host "❌ Error force killing processes on port 3003" -ForegroundColor Red
        }
    }
    
    # Kill processes on port 5003
    if ($port5003) {
        try {
            $processes = netstat -ano | findstr :5003
            foreach ($line in $processes) {
                $parts = $line -split '\s+'
                $processId = $parts[-1]
                if ($processId -match '^\d+$') {
                    Write-Host "Force killing process on port 5003 (PID: $processId)..." -ForegroundColor Cyan
                    Stop-Process -Id $processId -Force -ErrorAction SilentlyContinue
                }
            }
        } catch {
            Write-Host "❌ Error force killing processes on port 5003" -ForegroundColor Red
        }
    }
}

# Step 7: Final status check
Write-Host "Step 7: Final status check..." -ForegroundColor Yellow
Start-Sleep -Seconds 3

$finalPort8003 = netstat -an | findstr :8003
$finalPort3003 = netstat -an | findstr :3003
$finalPort5003 = netstat -an | findstr :5003

Write-Host ""
Write-Host "🎯 FINAL STATUS:" -ForegroundColor Green
Write-Host "================" -ForegroundColor Green
Write-Host "Port 8003 (Laravel): $($finalPort8003 ? '❌ Still Running' : '✅ Stopped')" -ForegroundColor $(if($finalPort8003) { 'Red' } else { 'Green' })
Write-Host "Port 3003 (Vite): $($finalPort3003 ? '❌ Still Running' : '✅ Stopped')" -ForegroundColor $(if($finalPort3003) { 'Red' } else { 'Green' })
Write-Host "Port 5003 (MySQL): $($finalPort5003 ? '❌ Still Running' : '✅ Stopped')" -ForegroundColor $(if($finalPort5003) { 'Red' } else { 'Green' })

Write-Host ""
Write-Host "🛑 All services have been stopped!" -ForegroundColor Green
Write-Host "To restart services later, run: .\start_all_services.ps1" -ForegroundColor Cyan 