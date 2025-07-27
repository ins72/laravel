@echo off
echo ========================================
echo Laravel Application Setup - Manual Guide
echo ========================================
echo.

echo Step 1: Install XAMPP (includes PHP, MySQL, Apache)
echo Download from: https://www.apachefriends.org/download.html
echo Install to: C:\xampp
echo.
echo After installation, add PHP to PATH:
echo 1. Open System Properties > Environment Variables
echo 2. Add C:\xampp\php to your PATH variable
echo 3. Restart your terminal
echo.

echo Step 2: Install Composer
echo Download from: https://getcomposer.org/download/
echo Run the installer
echo.

echo Step 3: Verify installations
echo Run these commands after installation:
echo   php --version
echo   composer --version
echo   mysql --version
echo.

echo Step 4: Set up the Laravel application
echo Run: setup_laravel.ps1
echo.

echo Step 5: Start the development server
echo Run: php artisan serve
echo.

pause 