<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Site;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Get admin dashboard statistics
     */
    public function dashboard(Request $request)
    {
        // Check if user is admin
        if ($request->user()->role !== 1) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'Access denied. Admin privileges required.'
                ]
            ], 403);
        }

        // Get statistics
        $stats = [
            'users' => [
                'total' => User::count(),
                'active' => User::where('status', 1)->count(),
                'new_this_month' => User::whereMonth('created_at', now()->month)->count(),
            ],
            'products' => [
                'total' => Product::count(),
                'active' => Product::where('status', 1)->count(),
                'new_this_month' => Product::whereMonth('created_at', now()->month)->count(),
            ],
            'sites' => [
                'total' => Site::count(),
                'published' => Site::where('published', 1)->count(),
                'new_this_month' => Site::whereMonth('created_at', now()->month)->count(),
            ],
            'courses' => [
                'total' => Course::count(),
                'active' => Course::where('status', 1)->count(),
                'new_this_month' => Course::whereMonth('created_at', now()->month)->count(),
            ],
        ];

        // Get recent activity
        $recentUsers = User::latest()->take(5)->get();
        $recentProducts = Product::with(['user'])->latest()->take(5)->get();
        $recentSites = Site::with(['user'])->latest()->take(5)->get();

        return response()->json([
            'success' => true,
            'data' => [
                'statistics' => $stats,
                'recent_activity' => [
                    'users' => $recentUsers,
                    'products' => $recentProducts,
                    'sites' => $recentSites,
                ]
            ]
        ]);
    }

    /**
     * Get admin users management data
     */
    public function users(Request $request)
    {
        // Check if user is admin
        if ($request->user()->role !== 1) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'Access denied. Admin privileges required.'
                ]
            ], 403);
        }

        $query = User::with(['sites', 'products']);

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Sort functionality
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $users = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $users->items(),
            'meta' => [
                'pagination' => [
                    'current_page' => $users->currentPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                    'last_page' => $users->lastPage(),
                ]
            ]
        ]);
    }

    /**
     * Get admin products management data
     */
    public function products(Request $request)
    {
        // Check if user is admin
        if ($request->user()->role !== 1) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'Access denied. Admin privileges required.'
                ]
            ], 403);
        }

        $query = Product::with(['user', 'site']);

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by price type
        if ($request->has('price_type')) {
            $query->where('price_type', $request->price_type);
        }

        // Sort functionality
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $products = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $products->items(),
            'meta' => [
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                    'last_page' => $products->lastPage(),
                ]
            ]
        ]);
    }

    /**
     * Get admin sites management data
     */
    public function sites(Request $request)
    {
        // Check if user is admin
        if ($request->user()->role !== 1) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'Access denied. Admin privileges required.'
                ]
            ], 403);
        }

        $query = Site::with(['user', 'products', 'courses']);

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by published status
        if ($request->has('published')) {
            $query->where('published', $request->published);
        }

        // Sort functionality
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $sites = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $sites->items(),
            'meta' => [
                'pagination' => [
                    'current_page' => $sites->currentPage(),
                    'per_page' => $sites->perPage(),
                    'total' => $sites->total(),
                    'last_page' => $sites->lastPage(),
                ]
            ]
        ]);
    }

    /**
     * Get admin courses management data
     */
    public function courses(Request $request)
    {
        // Check if user is admin
        if ($request->user()->role !== 1) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'Access denied. Admin privileges required.'
                ]
            ], 403);
        }

        $query = Course::with(['user', 'site', 'lessons']);

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by published status
        if ($request->has('published')) {
            $query->where('published', $request->published);
        }

        // Sort functionality
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $courses = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $courses->items(),
            'meta' => [
                'pagination' => [
                    'current_page' => $courses->currentPage(),
                    'per_page' => $courses->perPage(),
                    'total' => $courses->total(),
                    'last_page' => $courses->lastPage(),
                ]
            ]
        ]);
    }

    /**
     * Get admin analytics data
     */
    public function analytics(Request $request)
    {
        // Check if user is admin
        if ($request->user()->role !== 1) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'Access denied. Admin privileges required.'
                ]
            ], 403);
        }

        // Get analytics data
        $analytics = [
            'user_growth' => $this->getUserGrowth(),
            'product_activity' => $this->getProductActivity(),
            'site_performance' => $this->getSitePerformance(),
            'course_enrollments' => $this->getCourseEnrollments(),
        ];

        return response()->json([
            'success' => true,
            'data' => $analytics
        ]);
    }

    /**
     * Get admin reports
     */
    public function reports(Request $request)
    {
        // Check if user is admin
        if ($request->user()->role !== 1) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'Access denied. Admin privileges required.'
                ]
            ], 403);
        }

        $type = $request->get('type', 'summary');
        $dateRange = $request->get('date_range', 'month');

        $reports = [
            'type' => $type,
            'date_range' => $dateRange,
            'generated_at' => now()->toISOString(),
            'data' => $this->generateReport($type, $dateRange),
        ];

        return response()->json([
            'success' => true,
            'data' => $reports
        ]);
    }

    /**
     * Update system settings
     */
    public function updateSettings(Request $request)
    {
        // Check if user is admin
        if ($request->user()->role !== 1) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'Access denied. Admin privileges required.'
                ]
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'settings' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'VALIDATION_ERROR',
                    'message' => 'The given data was invalid.',
                    'details' => $validator->errors()
                ]
            ], 422);
        }

        // Update settings (implement based on your settings system)
        // This is a placeholder - implement based on your actual settings storage
        $settings = $request->settings;

        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully',
            'data' => $settings
        ]);
    }

    /**
     * Get system settings
     */
    public function getSettings(Request $request)
    {
        // Check if user is admin
        if ($request->user()->role !== 1) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'Access denied. Admin privileges required.'
                ]
            ], 403);
        }

        // Get settings (implement based on your settings system)
        // This is a placeholder - implement based on your actual settings storage
        $settings = [
            'system' => [
                'maintenance_mode' => false,
                'registration_enabled' => true,
                'email_verification_required' => true,
            ],
            'limits' => [
                'max_file_size' => 10240, // 10MB
                'max_products_per_user' => 100,
                'max_sites_per_user' => 10,
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }

    /**
     * Impersonate a user (Admin only)
     */
    public function impersonate(Request $request, $id)
    {
        // Check if user is admin
        if ($request->user()->role !== 1) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'Access denied. Admin privileges required.'
                ]
            ], 403);
        }

        $targetUser = User::findOrFail($id);

        // Create impersonation token
        $token = $targetUser->createToken('impersonation_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $targetUser,
                'impersonation_token' => $token,
                'message' => 'Impersonation started successfully'
            ]
        ]);
    }

    /**
     * Ban a user (Admin only)
     */
    public function banUser(Request $request, $id)
    {
        // Check if user is admin
        if ($request->user()->role !== 1) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'Access denied. Admin privileges required.'
                ]
            ], 403);
        }

        $user = User::findOrFail($id);

        // Prevent admin from banning themselves
        if ($user->id === $request->user()->id) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'INVALID_OPERATION',
                    'message' => 'You cannot ban your own account.'
                ]
            ], 400);
        }

        $user->update(['status' => 0]);

        return response()->json([
            'success' => true,
            'message' => 'User banned successfully'
        ]);
    }

    /**
     * Unban a user (Admin only)
     */
    public function unbanUser(Request $request, $id)
    {
        // Check if user is admin
        if ($request->user()->role !== 1) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'Access denied. Admin privileges required.'
                ]
            ], 403);
        }

        $user = User::findOrFail($id);
        $user->update(['status' => 1]);

        return response()->json([
            'success' => true,
            'message' => 'User unbanned successfully'
        ]);
    }

    /**
     * Helper methods for analytics
     */
    private function getUserGrowth()
    {
        return User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [now()->subDays(30), now()])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getProductActivity()
    {
        return Product::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [now()->subDays(30), now()])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getSitePerformance()
    {
        return Site::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [now()->subDays(30), now()])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getCourseEnrollments()
    {
        // This would depend on your enrollment system
        return collect([]);
    }

    private function generateReport($type, $dateRange)
    {
        // Implement report generation based on type and date range
        return [
            'summary' => 'Report data would be generated here',
            'type' => $type,
            'date_range' => $dateRange,
        ];
    }
} 