# Laravel Ecommerce Platform

A modern, feature-rich ecommerce platform built with Laravel 10, featuring a beautiful UI with Tailwind CSS and comprehensive shopping functionality.

## 🚀 Features

### Core Ecommerce Features
- **Product Management**: Full CRUD operations for products with images, pricing, and inventory
- **Shopping Cart**: Persistent cart with quantity management and real-time updates
- **Order Management**: Complete order lifecycle from checkout to delivery
- **User Authentication**: Secure user registration, login, and profile management
- **Search & Filtering**: Advanced product search with price and category filters
- **Deals & Promotions**: Special offers with countdown timers
- **Responsive Design**: Mobile-first design that works on all devices

### Advanced Features
- **Stock Management**: Real-time inventory tracking
- **Sale Pricing**: Support for discounted prices and limited-time offers
- **Related Products**: Smart product recommendations
- **Order Timeline**: Track order progress with detailed history
- **Billing & Shipping**: Separate billing and shipping addresses
- **Admin Dashboard**: Comprehensive admin panel for managing the store

### Technical Features
- **Laravel 10**: Latest Laravel framework with modern PHP practices
- **Tailwind CSS**: Utility-first CSS framework for beautiful designs
- **Shopping Cart Package**: Uses `darryldecode/cart` for cart functionality
- **Payment Integration**: Ready for Stripe and Razorpay integration
- **Database Relationships**: Proper Eloquent relationships and database design
- **Security**: CSRF protection, input validation, and secure authentication

## 📁 Project Structure

```
app/
├── Http/Controllers/
│   ├── HomeController.php          # Homepage and static pages
│   ├── ProductController.php       # Product listing and details
│   ├── CartController.php          # Shopping cart operations
│   └── OrderController.php         # Order management
├── Models/
│   ├── Product.php                 # Product model with relationships
│   ├── ProductOrder.php            # Order model
│   ├── ProductOrderItem.php        # Order items
│   └── User.php                    # User model
└── ...

resources/views/
├── layouts/
│   └── app.blade.php              # Main layout with navigation
├── home.blade.php                 # Homepage with featured products
├── products/
│   ├── index.blade.php            # Product listing with filters
│   ├── show.blade.php             # Product detail page
│   ├── featured.blade.php         # Featured products
│   └── deals.blade.php            # Special deals page
├── cart/
│   └── index.blade.php            # Shopping cart
├── orders/
│   ├── index.blade.php            # Order history
│   ├── show.blade.php             # Order details
│   └── checkout.blade.php         # Checkout form
└── pages/
    ├── about.blade.php             # About page
    ├── contact.blade.php           # Contact page
    ├── terms.blade.php             # Terms of service
    └── privacy.blade.php           # Privacy policy

routes/
└── web.php                        # All ecommerce routes
```

## 🛠️ Installation & Setup

### Prerequisites
- PHP 8.1 or higher
- Composer
- MySQL/PostgreSQL
- Node.js & NPM (for frontend assets)

### 1. Clone the Repository
```bash
git clone <repository-url>
cd laravel-ecommerce
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```

Configure your database settings in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 4. Database Setup
```bash
php artisan migrate
php artisan db:seed
```

### 5. Build Frontend Assets
```bash
npm run build
```

### 6. Start the Application
```bash
php artisan serve
```

Visit `http://localhost:8000` to see your ecommerce platform!

## 🔧 Configuration

### Cart Configuration
The platform uses the `darryldecode/cart` package. Configure it in `config/cart.php`:

```php
'storage' => 'session', // or 'database'
'events' => [
    'created' => 'App\Events\CartCreated',
    'updated' => 'App\Events\CartUpdated',
    'deleted' => 'App\Events\CartDeleted',
],
```

### Payment Gateway Setup
Configure your payment gateways in `.env`:

```env
STRIPE_KEY=your_stripe_public_key
STRIPE_SECRET=your_stripe_secret_key
RAZORPAY_KEY=your_razorpay_key
RAZORPAY_SECRET=your_razorpay_secret
```

## 📱 Usage

### For Customers

1. **Browse Products**: Visit the homepage or products page to see available items
2. **Add to Cart**: Click "Add to Cart" on any product
3. **Manage Cart**: View cart, update quantities, or remove items
4. **Checkout**: Complete the checkout process with billing and shipping information
5. **Track Orders**: View order history and track current orders

### For Administrators

1. **Access Admin Panel**: Navigate to `/admin` (requires admin role)
2. **Manage Products**: Add, edit, or remove products
3. **Process Orders**: Update order statuses and manage inventory
4. **User Management**: Monitor user accounts and manage permissions

### For Developers

1. **Customize Views**: Modify Blade templates in `resources/views/`
2. **Add Features**: Extend controllers and models as needed
3. **Styling**: Customize Tailwind CSS classes for your brand
4. **API Integration**: Add new API endpoints for mobile apps

## 🎨 Customization

### Styling
The platform uses Tailwind CSS. Customize colors and components in `tailwind.config.js`:

```javascript
module.exports = {
  theme: {
    extend: {
      colors: {
        primary: {
          50: '#eff6ff',
          500: '#3b82f6',
          900: '#1e3a8a',
        }
      }
    }
  }
}
```

### Adding New Features
1. **Create Controller**: `php artisan make:controller NewFeatureController`
2. **Add Routes**: Define routes in `routes/web.php`
3. **Create Views**: Add Blade templates in `resources/views/`
4. **Update Models**: Extend existing models or create new ones

## 🔒 Security Features

- **CSRF Protection**: All forms include CSRF tokens
- **Input Validation**: Comprehensive validation rules for all inputs
- **SQL Injection Prevention**: Uses Eloquent ORM with parameter binding
- **XSS Protection**: Output is properly escaped in Blade templates
- **Authentication**: Secure user authentication with Laravel Breeze
- **Authorization**: Role-based access control for admin functions

## 📊 Database Schema

### Key Tables
- `users` - User accounts and authentication
- `products` - Product information and inventory
- `product_orders` - Order headers and customer information
- `product_order_items` - Individual items in orders
- `product_order_timeline` - Order status history
- `carts` - Shopping cart data (if using database storage)

### Relationships
- Users can have multiple products and orders
- Products belong to users (sellers)
- Orders have multiple order items
- Orders track timeline events

## 🚀 Deployment

### Production Checklist
- [ ] Set `APP_ENV=production` in `.env`
- [ ] Configure production database
- [ ] Set up SSL certificate
- [ ] Configure queue workers for background jobs
- [ ] Set up file storage (S3, etc.)
- [ ] Configure caching (Redis, Memcached)
- [ ] Set up monitoring and logging

### Deployment Commands
```bash
composer install --optimize-autoloader --no-dev
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## 📝 License

This project is open-sourced software licensed under the [MIT license](LICENSE).

## 🆘 Support

For support and questions:
- Create an issue in the repository
- Check the Laravel documentation
- Review the code comments and documentation

## 🔮 Future Enhancements

- **Multi-language Support**: Internationalization (i18n)
- **Advanced Analytics**: Sales reports and customer insights
- **Email Marketing**: Newsletter and promotional emails
- **Mobile App API**: RESTful API for mobile applications
- **Advanced Search**: Elasticsearch integration
- **Inventory Management**: Advanced stock tracking and alerts
- **Customer Reviews**: Product rating and review system
- **Wishlist**: Save products for later
- **Gift Cards**: Digital gift card system
- **Subscription Products**: Recurring billing support

---

**Built with ❤️ using Laravel and Tailwind CSS**