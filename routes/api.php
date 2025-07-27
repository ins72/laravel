<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\SiteController;
use App\Http\Controllers\Api\V1\CourseController;
use App\Http\Controllers\Api\V1\PageController;
use App\Http\Controllers\Api\V1\MediaController;
use App\Http\Controllers\Api\V1\AdminController;
use App\Http\Controllers\Api\V1\AnalyticsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::prefix('v1')->group(function () {
    
    // Authentication routes
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
        Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('reset-password', [AuthController::class, 'resetPassword']);
    });

    // Public product routes
    Route::get('products/search', [ProductController::class, 'search']);
    Route::get('products/featured', [ProductController::class, 'featured']);
    Route::get('products/{id}', [ProductController::class, 'show']);

    // Public site routes
    Route::get('sites/{id}', [SiteController::class, 'show']);
    Route::get('sites/{id}/pages', [SiteController::class, 'pages']);

    // Public course routes
    Route::get('courses/{id}', [CourseController::class, 'show']);
    Route::get('courses/{id}/lessons', [CourseController::class, 'lessons']);
});

// Protected routes
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    
    // Authentication routes
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
        Route::put('profile', [AuthController::class, 'updateProfile']);
        Route::put('password', [AuthController::class, 'changePassword']);
    });

    // User management routes
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->middleware('admin');
        Route::post('/', [UserController::class, 'store'])->middleware('admin');
        Route::get('{id}', [UserController::class, 'show']);
        Route::put('{id}', [UserController::class, 'update']);
        Route::delete('{id}', [UserController::class, 'destroy'])->middleware('admin');
        Route::get('{id}/sites', [UserController::class, 'sites']);
        Route::get('{id}/products', [UserController::class, 'products']);
        Route::get('{id}/courses', [UserController::class, 'courses']);
    });

    // Product management routes
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index']);
        Route::post('/', [ProductController::class, 'store']);
        Route::put('{id}', [ProductController::class, 'update']);
        Route::delete('{id}', [ProductController::class, 'destroy']);
        Route::post('bulk', [ProductController::class, 'bulkOperations']);
        Route::get('{id}/reviews', [ProductController::class, 'reviews']);
        Route::post('{id}/reviews', [ProductController::class, 'storeReview']);
    });

    // Site management routes
    Route::prefix('sites')->group(function () {
        Route::get('/', [SiteController::class, 'index']);
        Route::post('/', [SiteController::class, 'store']);
        Route::put('{id}', [SiteController::class, 'update']);
        Route::delete('{id}', [SiteController::class, 'destroy']);
        Route::post('{id}/pages', [SiteController::class, 'storePage']);
        Route::get('{id}/analytics', [SiteController::class, 'analytics']);
        Route::put('{id}/publish', [SiteController::class, 'publish']);
        Route::put('{id}/unpublish', [SiteController::class, 'unpublish']);
    });

    // Course management routes
    Route::prefix('courses')->group(function () {
        Route::get('/', [CourseController::class, 'index']);
        Route::post('/', [CourseController::class, 'store']);
        Route::put('{id}', [CourseController::class, 'update']);
        Route::delete('{id}', [CourseController::class, 'destroy']);
        Route::post('{id}/enroll', [CourseController::class, 'enroll']);
        Route::get('{id}/progress', [CourseController::class, 'progress']);
        Route::post('{id}/lessons', [CourseController::class, 'storeLesson']);
        Route::put('{id}/lessons/{lessonId}', [CourseController::class, 'updateLesson']);
        Route::delete('{id}/lessons/{lessonId}', [CourseController::class, 'destroyLesson']);
    });

    // Page management routes
    Route::prefix('pages')->group(function () {
        Route::get('/', [PageController::class, 'index']);
        Route::post('/', [PageController::class, 'store']);
        Route::get('{id}', [PageController::class, 'show']);
        Route::put('{id}', [PageController::class, 'update']);
        Route::delete('{id}', [PageController::class, 'destroy']);
        Route::post('{id}/sections', [PageController::class, 'storeSection']);
        Route::put('{id}/sections/{sectionId}', [PageController::class, 'updateSection']);
        Route::delete('{id}/sections/{sectionId}', [PageController::class, 'destroySection']);
    });

    // Media management routes
    Route::prefix('media')->group(function () {
        Route::get('/', [MediaController::class, 'index']);
        Route::post('upload', [MediaController::class, 'upload']);
        Route::post('bulk-upload', [MediaController::class, 'bulkUpload']);
        Route::get('{id}', [MediaController::class, 'show']);
        Route::delete('{id}', [MediaController::class, 'destroy']);
        Route::get('user/{userId}', [MediaController::class, 'userFiles']);
    });

    // Analytics routes
    Route::prefix('analytics')->group(function () {
        Route::get('dashboard', [AnalyticsController::class, 'dashboard']);
        Route::get('users', [AnalyticsController::class, 'users']);
        Route::get('products', [AnalyticsController::class, 'products']);
        Route::get('sites', [AnalyticsController::class, 'sites']);
        Route::get('revenue', [AnalyticsController::class, 'revenue']);
        Route::get('reports', [AnalyticsController::class, 'reports']);
    });

    // Admin routes
    Route::prefix('admin')->middleware('admin')->group(function () {
        Route::get('dashboard', [AdminController::class, 'dashboard']);
        Route::get('users', [AdminController::class, 'users']);
        Route::get('products', [AdminController::class, 'products']);
        Route::get('sites', [AdminController::class, 'sites']);
        Route::get('courses', [AdminController::class, 'courses']);
        Route::get('analytics', [AdminController::class, 'analytics']);
        Route::get('reports', [AdminController::class, 'reports']);
        Route::post('settings', [AdminController::class, 'updateSettings']);
        Route::get('settings', [AdminController::class, 'getSettings']);
        Route::post('users/{id}/impersonate', [AdminController::class, 'impersonate']);
        Route::post('users/{id}/ban', [AdminController::class, 'banUser']);
        Route::post('users/{id}/unban', [AdminController::class, 'unbanUser']);
    });
});

// Health check route
Route::get('v1/health', function () {
    return response()->json([
        'success' => true,
        'message' => 'API is running',
        'timestamp' => now()->toISOString(),
        'version' => '1.0.0'
    ]);
});

// API documentation route
Route::get('v1/docs', function () {
    return response()->json([
        'success' => true,
        'message' => 'API Documentation',
        'endpoints' => [
            'authentication' => [
                'POST /api/v1/auth/register' => 'Register new user',
                'POST /api/v1/auth/login' => 'Login user',
                'POST /api/v1/auth/logout' => 'Logout user',
                'GET /api/v1/auth/me' => 'Get current user',
                'PUT /api/v1/auth/profile' => 'Update profile',
                'PUT /api/v1/auth/password' => 'Change password',
            ],
            'users' => [
                'GET /api/v1/users' => 'List users (admin)',
                'POST /api/v1/users' => 'Create user (admin)',
                'GET /api/v1/users/{id}' => 'Get user details',
                'PUT /api/v1/users/{id}' => 'Update user',
                'DELETE /api/v1/users/{id}' => 'Delete user (admin)',
            ],
            'products' => [
                'GET /api/v1/products' => 'List products',
                'POST /api/v1/products' => 'Create product',
                'GET /api/v1/products/{id}' => 'Get product details',
                'PUT /api/v1/products/{id}' => 'Update product',
                'DELETE /api/v1/products/{id}' => 'Delete product',
                'GET /api/v1/products/search' => 'Search products',
                'GET /api/v1/products/featured' => 'Get featured products',
            ],
            'sites' => [
                'GET /api/v1/sites' => 'List sites',
                'POST /api/v1/sites' => 'Create site',
                'GET /api/v1/sites/{id}' => 'Get site details',
                'PUT /api/v1/sites/{id}' => 'Update site',
                'DELETE /api/v1/sites/{id}' => 'Delete site',
                'GET /api/v1/sites/{id}/pages' => 'Get site pages',
                'GET /api/v1/sites/{id}/analytics' => 'Get site analytics',
            ],
            'courses' => [
                'GET /api/v1/courses' => 'List courses',
                'POST /api/v1/courses' => 'Create course',
                'GET /api/v1/courses/{id}' => 'Get course details',
                'PUT /api/v1/courses/{id}' => 'Update course',
                'DELETE /api/v1/courses/{id}' => 'Delete course',
                'POST /api/v1/courses/{id}/enroll' => 'Enroll in course',
                'GET /api/v1/courses/{id}/progress' => 'Get enrollment progress',
            ],
            'media' => [
                'GET /api/v1/media' => 'List media files',
                'POST /api/v1/media/upload' => 'Upload file',
                'POST /api/v1/media/bulk-upload' => 'Bulk upload files',
                'GET /api/v1/media/{id}' => 'Get file details',
                'DELETE /api/v1/media/{id}' => 'Delete file',
            ],
            'admin' => [
                'GET /api/v1/admin/dashboard' => 'Admin dashboard',
                'GET /api/v1/admin/users' => 'Admin user management',
                'GET /api/v1/admin/analytics' => 'System analytics',
                'GET /api/v1/admin/reports' => 'Generate reports',
                'POST /api/v1/admin/settings' => 'Update system settings',
            ],
        ],
        'authentication' => 'Bearer token required for protected routes',
        'rate_limiting' => '100 requests per minute per user',
        'pagination' => 'All list endpoints support pagination with ?page=1&per_page=15',
        'versioning' => 'API versioning via URL prefix /api/v1/',
    ]);
});
