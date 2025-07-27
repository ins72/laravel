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

Route::get('/', function () {
    return view('welcome');
});

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
});

require __DIR__.'/auth.php';