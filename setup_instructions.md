# Laravel Application Setup Instructions

## Environment Setup

### 1. Install PHP
Since PHP is not available in your PATH, you need to install it:

**Option A: Install XAMPP (Recommended for Windows)**
1. Download XAMPP from https://www.apachefriends.org/
2. Install XAMPP (includes PHP, MySQL, Apache)
3. Add PHP to your PATH:
   - Open System Properties > Environment Variables
   - Add `C:\xampp\php` to your PATH variable
   - Restart your terminal

**Option B: Install PHP directly**
1. Download PHP from https://windows.php.net/download/
2. Extract to `C:\php`
3. Add `C:\php` to your PATH variable

### 2. Install Composer
1. Download Composer from https://getcomposer.org/download/
2. Run the installer
3. Verify installation: `composer --version`

### 3. Install Node.js
1. Download Node.js from https://nodejs.org/
2. Install Node.js (version 18.19.0 as specified in package.json)
3. Verify installation: `node --version` and `npm --version`

## Application Setup

### 1. Create Environment File
```bash
# Copy the example environment file
copy .env.example .env
```

If `.env.example` doesn't exist, create `.env` with these basic settings:
```env
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
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password

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
MAIL_FROM_NAME="${APP_NAME}"

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

VITE_APP_NAME="${APP_NAME}"
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

### 2. Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 3. Generate Application Key
```bash
php artisan key:generate
```

### 4. Set Up Database
```bash
# Run migrations
php artisan migrate

# (Optional) Seed the database
php artisan db:seed
```

### 5. Create Storage Link
```bash
php artisan storage:link
```

### 6. Build Assets
```bash
# For development
npm run dev

# For production
npm run build
```

### 7. Clear Caches
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Testing the Application

### 1. Start Development Server
```bash
php artisan serve
```

### 2. Run the Audit Test
```bash
php test_audit_fixes.php
```

### 3. Manual Testing Checklist
- [ ] Visit http://localhost:8000
- [ ] Test user registration
- [ ] Test user login
- [ ] Test admin panel (if you have admin user)
- [ ] Test file uploads
- [ ] Test payment functionality
- [ ] Test Livewire components

## Troubleshooting

### Common Issues

**1. PHP not found**
- Make sure PHP is installed and in your PATH
- Try using the full path: `C:\xampp\php\php.exe artisan serve`

**2. Composer not found**
- Install Composer from https://getcomposer.org/download/
- Make sure it's in your PATH

**3. Node.js not found**
- Install Node.js from https://nodejs.org/
- Make sure it's in your PATH

**4. Database connection errors**
- Make sure MySQL is running
- Check your database credentials in `.env`
- Make sure the database exists

**5. Permission errors**
- Make sure the `storage` and `bootstrap/cache` directories are writable
- On Windows, right-click the folder > Properties > Security > Edit > Add your user with Full Control

### Getting Help

If you encounter issues:
1. Check the Laravel logs in `storage/logs/laravel.log`
2. Check the browser console for JavaScript errors
3. Make sure all dependencies are installed correctly
4. Verify your environment configuration

## Production Deployment

When ready for production:

1. Set `APP_ENV=production` in `.env`
2. Set `APP_DEBUG=false` in `.env`
3. Run `npm run build` for production assets
4. Set up a proper web server (Apache/Nginx)
5. Configure your database for production
6. Set up proper file permissions
7. Configure SSL certificates

For detailed deployment instructions, see the Laravel documentation: https://laravel.com/docs/deployment 