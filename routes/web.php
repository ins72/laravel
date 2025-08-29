<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\User\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Ecommerce Routes
Route::get('/products', [App\Http\Controllers\ProductController::class, 'index'])->name('products.index');
Route::get('/products/featured', [App\Http\Controllers\ProductController::class, 'featured'])->name('products.featured');
Route::get('/products/deals', [App\Http\Controllers\ProductController::class, 'deals'])->name('products.deals');
Route::get('/products/{slug}', [App\Http\Controllers\ProductController::class, 'show'])->name('products.show');

// Cart Routes
Route::get('/cart', [App\Http\Controllers\CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [App\Http\Controllers\CartController::class, 'add'])->name('cart.add');
Route::put('/cart/update', [App\Http\Controllers\CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{productId}', [App\Http\Controllers\CartController::class, 'remove'])->name('cart.remove');
Route::delete('/cart/clear', [App\Http\Controllers\CartController::class, 'clear'])->name('cart.clear');
Route::get('/cart/count', [App\Http\Controllers\CartController::class, 'getCartCount'])->name('cart.count');

// Static Pages
Route::get('/about', [App\Http\Controllers\HomeController::class, 'about'])->name('about');
Route::get('/contact', [App\Http\Controllers\HomeController::class, 'contact'])->name('contact');
Route::get('/terms', [App\Http\Controllers\HomeController::class, 'terms'])->name('terms');
Route::get('/privacy', [App\Http\Controllers\HomeController::class, 'privacy'])->name('privacy');

// Admin Routes
Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/analytics', [DashboardController::class, 'analytics'])->name('analytics');
    
    // User Management
    Route::get('/users', [DashboardController::class, 'users'])->name('users.index');
    Route::get('/users/{id}', [DashboardController::class, 'userShow'])->name('users.show');
    Route::get('/users/{id}/edit', [DashboardController::class, 'userEdit'])->name('users.edit');
    Route::put('/users/{id}', [DashboardController::class, 'userUpdate'])->name('users.update');
    Route::delete('/users/{id}', [DashboardController::class, 'userDelete'])->name('users.delete');
    
    // Product Management
    Route::get('/products', [DashboardController::class, 'products'])->name('products.index');
    Route::get('/products/{id}', [DashboardController::class, 'productShow'])->name('products.show');
    Route::get('/products/{id}/edit', [DashboardController::class, 'productEdit'])->name('products.edit');
    Route::put('/products/{id}', [DashboardController::class, 'productUpdate'])->name('products.update');
    Route::delete('/products/{id}', [DashboardController::class, 'productDelete'])->name('products.delete');
    
    // Site Management
    Route::get('/sites', [DashboardController::class, 'sites'])->name('sites.index');
    Route::get('/sites/{id}', [DashboardController::class, 'siteShow'])->name('sites.show');
    Route::get('/sites/{id}/edit', [DashboardController::class, 'siteEdit'])->name('sites.edit');
    Route::put('/sites/{id}', [DashboardController::class, 'siteUpdate'])->name('sites.update');
    Route::delete('/sites/{id}', [DashboardController::class, 'siteDelete'])->name('sites.delete');
});

// User Profile Routes
Route::prefix('user')->middleware('auth')->name('user.')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
    Route::get('/profile/settings', [ProfileController::class, 'settings'])->name('profile.settings');
    Route::put('/profile/settings', [ProfileController::class, 'updateSettings'])->name('profile.update-settings');
    
    // Site Management
    Route::get('/sites', [ProfileController::class, 'sites'])->name('sites.index');
    Route::get('/sites/create', [ProfileController::class, 'siteCreate'])->name('sites.create');
    Route::post('/sites', [ProfileController::class, 'siteStore'])->name('sites.store');
    Route::get('/sites/{id}/edit', [ProfileController::class, 'siteEdit'])->name('sites.edit');
    Route::put('/sites/{id}', [ProfileController::class, 'siteUpdate'])->name('sites.update');
    Route::delete('/sites/{id}', [ProfileController::class, 'siteDelete'])->name('sites.delete');
    
    // Product Management
    Route::get('/products', [ProfileController::class, 'products'])->name('products.index');
    Route::get('/products/create', [ProfileController::class, 'productCreate'])->name('products.create');
    Route::post('/products', [ProfileController::class, 'productStore'])->name('products.store');
    Route::get('/products/{id}/edit', [ProfileController::class, 'productEdit'])->name('products.edit');
    Route::put('/products/{id}', [ProfileController::class, 'productUpdate'])->name('products.update');
    Route::delete('/products/{id}', [ProfileController::class, 'productDelete'])->name('products.delete');
    
    // Course Management
    Route::get('/courses', [ProfileController::class, 'courses'])->name('courses.index');
    Route::get('/courses/create', [ProfileController::class, 'courseCreate'])->name('courses.create');
    Route::post('/courses', [ProfileController::class, 'courseStore'])->name('courses.store');
    Route::get('/courses/{id}/edit', [ProfileController::class, 'courseEdit'])->name('courses.edit');
    Route::put('/courses/{id}', [ProfileController::class, 'courseUpdate'])->name('courses.update');
    Route::delete('/courses/{id}', [ProfileController::class, 'courseDelete'])->name('courses.delete');
    
    // Order Management
    Route::get('/orders', [App\Http\Controllers\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [App\Http\Controllers\OrderController::class, 'show'])->name('orders.show');
    Route::delete('/orders/{id}/cancel', [App\Http\Controllers\OrderController::class, 'cancel'])->name('orders.cancel');
});

// Checkout Routes (require authentication)
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [App\Http\Controllers\OrderController::class, 'checkout'])->name('checkout');
    Route::post('/checkout', [App\Http\Controllers\OrderController::class, 'store'])->name('orders.store');
});

require __DIR__.'/auth.php';