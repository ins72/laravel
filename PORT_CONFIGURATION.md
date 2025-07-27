# 🚀 **Port Configuration Guide - Your Exact Requirements**

## 🎯 **Target Configuration:**
- **Frontend (Vite)**: Port 3003
- **Backend (Laravel)**: Port 8003  
- **Database (MySQL)**: Port 5003

## ✅ **Current Status:**
- ✅ **Laravel Backend**: Already running on http://127.0.0.1:8003
- ✅ **Vite Frontend**: Already running on http://localhost:3003/
- ⏳ **Database**: Need to configure for port 5003

## 🔧 **Configuration Steps:**

### **1. Frontend (Vite) - Port 3003**
✅ **Already Configured** in `vite.config.js`:
```javascript
server: {
    port: 3003,
    host: true,
    cors: true,
    hmr: {
        host: 'localhost',
    },
},
```

### **2. Backend (Laravel) - Port 8003**
✅ **Already Running** on port 8003:
```bash
C:\xampp\php\php.exe artisan serve --port=8003
```

### **3. Database (MySQL) - Port 5003**

#### **Step A: Configure XAMPP MySQL for Port 5003**

1. **Open XAMPP Control Panel**
2. **Click "Config" next to MySQL**
3. **Select "my.ini"**
4. **Find the line with `port=3306`**
5. **Change it to `port=5003`**
6. **Save the file**
7. **Restart MySQL in XAMPP**

#### **Step B: Update .env File**

Create or update your `.env` file with:
```env
APP_URL=http://localhost:8003
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=5003
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
INSTALLED=1
```

#### **Step C: Test Database Connection**

After configuring, test the connection:
```bash
C:\xampp\php\php.exe artisan migrate:status
```

## 🌐 **Final Access URLs:**

- **Frontend**: http://localhost:3003/
- **Backend**: http://127.0.0.1:8003
- **Database**: localhost:5003 (via phpMyAdmin or MySQL client)

## 🔄 **Restart Commands:**

### **Frontend:**
```bash
npm run dev
```

### **Backend:**
```bash
C:\xampp\php\php.exe artisan serve --port=8003
```

### **Database:**
- Restart MySQL in XAMPP Control Panel

## 📝 **Important Notes:**

1. **MySQL Port Change**: Changing MySQL from 3306 to 5003 requires restarting the MySQL service
2. **phpMyAdmin**: Will be available at http://localhost/phpmyadmin (still uses port 5003 for MySQL)
3. **Environment**: Make sure to restart Laravel server after updating `.env` file

## 🎯 **Next Steps:**

1. **Configure MySQL port** in XAMPP
2. **Update .env file** with new database port
3. **Restart all services**
4. **Run database migrations**

---

**Configuration Target:** Frontend 3003 | Backend 8003 | Database 5003 