# Laravel Ecommerce Platform

A comprehensive ecommerce platform built with Laravel 10, featuring modern design, secure payment processing, and full-featured shopping experience.

## Features

### 🛍️ **Product Management**
- Product catalog with categories
- Product search and filtering
- Product reviews and ratings
- Stock management
- Product images and media galleries
- Product variants and options

### 🛒 **Shopping Experience**
- Shopping cart functionality
- Add to cart with quantity selection
- Cart persistence across sessions
- Coupon code system
- Wishlist functionality

### 💳 **Payment & Checkout**
- Multiple payment gateways (Stripe, Razorpay)
- Wallet payment system
- Secure checkout process
- Order management
- Invoice generation

### 📱 **User Management**
- User registration and authentication
- User profiles and order history
- Address management
- Wallet system for credits

### 🎨 **Modern UI/UX**
- Responsive design with Tailwind CSS
- Alpine.js for interactive components
- Font Awesome icons
- Mobile-first approach
- Beautiful product displays

## Installation & Setup

### Prerequisites
- PHP 8.1 or higher
- Composer
- MySQL/PostgreSQL
- Node.js & NPM (for frontend assets)

### 1. Clone and Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 2. Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 3. Database Setup
```bash
# Configure your database in .env file
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Run migrations
php artisan migrate

# Seed the database with sample data
php artisan db:seed
```

### 4. Payment Gateway Configuration
Add your payment gateway credentials to `.env`:

```env
# Stripe
STRIPE_KEY=your_stripe_publishable_key
STRIPE_SECRET=your_stripe_secret_key

# Razorpay
RAZORPAY_KEY=your_razorpay_key
RAZORPAY_SECRET=your_razorpay_secret
```

### 5. Storage Setup
```bash
# Create storage link
php artisan storage:link

# Set proper permissions
chmod -R 775 storage bootstrap/cache
```

### 6. Build Frontend Assets
```bash
# Development
npm run dev

# Production
npm run build
```

### 7. Start the Application
```bash
# Start Laravel development server
php artisan serve

# Or use Laravel Sail (Docker)
./vendor/bin/sail up
```

## Usage

### Accessing the Platform

1. **Main Shop**: Visit `/shop` to browse products
2. **Product Details**: Click on any product to view details
3. **Shopping Cart**: Add products and manage cart at `/shop/cart`
4. **Checkout**: Complete purchases at `/shop/checkout`
5. **User Dashboard**: Access orders and profile at `/user/profile`

### Test Account

After running the seeder, you can use:
- **Email**: `test@example.com`
- **Password**: `password`

### Sample Data

The seeder creates:
- 6 sample products across different categories
- 2 coupon codes (`WELCOME10`, `SAVE20`)
- 8 shipping locations
- Test user account

## File Structure

```
app/
├── Http/Controllers/
│   └── EcommerceController.php      # Main ecommerce logic
├── Models/
│   ├── Product.php                  # Product model
│   ├── ProductOrder.php             # Order model
│   ├── ProductReview.php            # Review model
│   └── ProductCouponCode.php        # Coupon model

resources/views/
├── layouts/
│   └── ecommerce.blade.php          # Ecommerce layout
└── ecommerce/
    ├── index.blade.php              # Product catalog
    ├── show.blade.php               # Product details
    ├── cart.blade.php               # Shopping cart
    ├── checkout.blade.php           # Checkout process
    ├── orders.blade.php             # Order history
    └── order-details.blade.php      # Order details

routes/
└── web.php                          # Ecommerce routes

database/seeders/
└── EcommerceSeeder.php              # Sample data seeder
```

## Routes

| Route | Method | Description |
|-------|--------|-------------|
| `/shop` | GET | Product catalog |
| `/shop/product/{slug}` | GET | Product details |
| `/shop/cart` | GET | Shopping cart |
| `/shop/cart/add` | POST | Add to cart |
| `/shop/cart/update` | PUT | Update cart |
| `/shop/cart/remove/{id}` | DELETE | Remove from cart |
| `/shop/checkout` | GET | Checkout page |
| `/shop/checkout/process` | POST | Process checkout |
| `/shop/orders` | GET | User orders |
| `/shop/orders/{id}` | GET | Order details |
| `/shop/review/{id}` | POST | Add product review |
| `/shop/coupon/apply` | POST | Apply coupon |
| `/shop/coupon/remove` | DELETE | Remove coupon |

## Key Features Explained

### Shopping Cart
- Uses `darryldecode/cart` package
- Persistent across sessions
- Quantity management
- Real-time updates

### Payment Processing
- **Stripe**: Credit/debit card processing
- **Razorpay**: Alternative payment gateway
- **Wallet**: Internal credit system
- Secure transaction handling

### Product Management
- Category-based organization
- Advanced filtering (price, category, search)
- Stock tracking
- Image galleries
- Product variants

### Order Management
- Order status tracking
- Timeline updates
- Invoice generation
- Shipping information

## Customization

### Adding New Payment Gateways
1. Create payment method in `EcommerceController`
2. Add configuration to `.env`
3. Update checkout form
4. Handle payment processing

### Custom Product Fields
1. Modify `Product` model
2. Update database migration
3. Modify product forms and views
4. Update validation rules

### Styling Changes
- Modify Tailwind classes in Blade templates
- Update CSS in `resources/css/app.css`
- Customize component styles

## Security Features

- CSRF protection on all forms
- Input validation and sanitization
- Secure payment processing
- User authentication middleware
- Order ownership verification

## Performance Optimization

- Database query optimization
- Image lazy loading
- Caching strategies
- Asset minification
- Database indexing

## Troubleshooting

### Common Issues

1. **Cart not working**: Check if `darryldecode/cart` package is installed
2. **Payment errors**: Verify payment gateway credentials
3. **Image uploads**: Check storage permissions and links
4. **Database errors**: Ensure migrations are run and database is configured

### Debug Mode
```bash
# Enable debug mode in .env
APP_DEBUG=true

# Check logs
tail -f storage/logs/laravel.log
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

For support and questions:
- Check the Laravel documentation
- Review the code comments
- Open an issue on GitHub

---

**Built with ❤️ using Laravel 10, Tailwind CSS, and Alpine.js**