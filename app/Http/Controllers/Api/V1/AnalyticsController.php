<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Site;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    /**
     * Get dashboard analytics
     */
    public function dashboard(Request $request)
    {
        $user = $request->user();

        // Get user-specific analytics
        $analytics = [
            'overview' => $this->getOverviewStats($user),
            'recent_activity' => $this->getRecentActivity($user),
            'performance_metrics' => $this->getPerformanceMetrics($user),
            'growth_trends' => $this->getGrowthTrends($user),
        ];

        return response()->json([
            'success' => true,
            'data' => $analytics
        ]);
    }

    /**
     * Get user analytics
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

        $period = $request->get('period', '30'); // days
        $startDate = now()->subDays($period);

        $analytics = [
            'user_growth' => $this->getUserGrowth($startDate),
            'user_activity' => $this->getUserActivity($startDate),
            'user_engagement' => $this->getUserEngagement($startDate),
            'user_retention' => $this->getUserRetention($startDate),
        ];

        return response()->json([
            'success' => true,
            'data' => $analytics
        ]);
    }

    /**
     * Get product analytics
     */
    public function products(Request $request)
    {
        $user = $request->user();
        $period = $request->get('period', '30'); // days
        $startDate = now()->subDays($period);

        $analytics = [
            'product_performance' => $this->getProductPerformance($user, $startDate),
            'product_trends' => $this->getProductTrends($user, $startDate),
            'top_products' => $this->getTopProducts($user, $startDate),
            'product_categories' => $this->getProductCategories($user),
        ];

        return response()->json([
            'success' => true,
            'data' => $analytics
        ]);
    }

    /**
     * Get site analytics
     */
    public function sites(Request $request)
    {
        $user = $request->user();
        $period = $request->get('period', '30'); // days
        $startDate = now()->subDays($period);

        $analytics = [
            'site_performance' => $this->getSitePerformance($user, $startDate),
            'site_traffic' => $this->getSiteTraffic($user, $startDate),
            'site_engagement' => $this->getSiteEngagement($user, $startDate),
            'top_sites' => $this->getTopSites($user, $startDate),
        ];

        return response()->json([
            'success' => true,
            'data' => $analytics
        ]);
    }

    /**
     * Get revenue analytics
     */
    public function revenue(Request $request)
    {
        $user = $request->user();
        $period = $request->get('period', '30'); // days
        $startDate = now()->subDays($period);

        $analytics = [
            'revenue_overview' => $this->getRevenueOverview($user, $startDate),
            'revenue_trends' => $this->getRevenueTrends($user, $startDate),
            'revenue_sources' => $this->getRevenueSources($user, $startDate),
            'revenue_forecast' => $this->getRevenueForecast($user, $startDate),
        ];

        return response()->json([
            'success' => true,
            'data' => $analytics
        ]);
    }

    /**
     * Get reports
     */
    public function reports(Request $request)
    {
        $user = $request->user();
        $type = $request->get('type', 'summary');
        $period = $request->get('period', '30'); // days
        $startDate = now()->subDays($period);

        $reports = [
            'type' => $type,
            'period' => $period,
            'generated_at' => now()->toISOString(),
            'data' => $this->generateReport($user, $type, $startDate),
        ];

        return response()->json([
            'success' => true,
            'data' => $reports
        ]);
    }

    /**
     * Helper methods for analytics
     */
    private function getOverviewStats($user)
    {
        $query = $user->role === 1 ? null : $user->id;

        return [
            'total_products' => $query ? Product::where('user_id', $query)->count() : Product::count(),
            'total_sites' => $query ? Site::where('user_id', $query)->count() : Site::count(),
            'total_courses' => $query ? Course::where('user_id', $query)->count() : Course::count(),
            'active_products' => $query ? Product::where('user_id', $query)->where('status', 1)->count() : Product::where('status', 1)->count(),
            'published_sites' => $query ? Site::where('user_id', $query)->where('published', 1)->count() : Site::where('published', 1)->count(),
        ];
    }

    private function getRecentActivity($user)
    {
        $query = $user->role === 1 ? null : $user->id;

        return [
            'recent_products' => $query ? Product::where('user_id', $query)->latest()->take(5)->get() : Product::latest()->take(5)->get(),
            'recent_sites' => $query ? Site::where('user_id', $query)->latest()->take(5)->get() : Site::latest()->take(5)->get(),
            'recent_courses' => $query ? Course::where('user_id', $query)->latest()->take(5)->get() : Course::latest()->take(5)->get(),
        ];
    }

    private function getPerformanceMetrics($user)
    {
        $query = $user->role === 1 ? null : $user->id;

        return [
            'product_views' => $this->getProductViews($query),
            'site_visits' => $this->getSiteVisits($query),
            'course_enrollments' => $this->getCourseEnrollments($query),
            'conversion_rate' => $this->getConversionRate($query),
        ];
    }

    private function getGrowthTrends($user)
    {
        $query = $user->role === 1 ? null : $user->id;
        $startDate = now()->subDays(30);

        return [
            'user_growth' => $this->getUserGrowthTrend($query, $startDate),
            'product_growth' => $this->getProductGrowthTrend($query, $startDate),
            'site_growth' => $this->getSiteGrowthTrend($query, $startDate),
            'revenue_growth' => $this->getRevenueGrowthTrend($query, $startDate),
        ];
    }

    private function getUserGrowth($startDate)
    {
        return User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [$startDate, now()])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getUserActivity($startDate)
    {
        return User::selectRaw('DATE(updated_at) as date, COUNT(*) as count')
            ->whereBetween('updated_at', [$startDate, now()])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getUserEngagement($startDate)
    {
        // This would depend on your engagement tracking system
        return collect([]);
    }

    private function getUserRetention($startDate)
    {
        // This would depend on your retention tracking system
        return collect([]);
    }

    private function getProductPerformance($user, $startDate)
    {
        $query = $user->role === 1 ? null : $user->id;
        $productQuery = Product::query();

        if ($query) {
            $productQuery->where('user_id', $query);
        }

        return $productQuery->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [$startDate, now()])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getProductTrends($user, $startDate)
    {
        $query = $user->role === 1 ? null : $user->id;
        $productQuery = Product::query();

        if ($query) {
            $productQuery->where('user_id', $query);
        }

        return $productQuery->selectRaw('price_type, COUNT(*) as count')
            ->whereBetween('created_at', [$startDate, now()])
            ->groupBy('price_type')
            ->get();
    }

    private function getTopProducts($user, $startDate)
    {
        $query = $user->role === 1 ? null : $user->id;
        $productQuery = Product::query();

        if ($query) {
            $productQuery->where('user_id', $query);
        }

        return $productQuery->whereBetween('created_at', [$startDate, now()])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
    }

    private function getProductCategories($user)
    {
        $query = $user->role === 1 ? null : $user->id;
        $productQuery = Product::query();

        if ($query) {
            $productQuery->where('user_id', $query);
        }

        return $productQuery->selectRaw('price_type, COUNT(*) as count')
            ->groupBy('price_type')
            ->get();
    }

    private function getSitePerformance($user, $startDate)
    {
        $query = $user->role === 1 ? null : $user->id;
        $siteQuery = Site::query();

        if ($query) {
            $siteQuery->where('user_id', $query);
        }

        return $siteQuery->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [$startDate, now()])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getSiteTraffic($user, $startDate)
    {
        // This would depend on your traffic tracking system
        return collect([]);
    }

    private function getSiteEngagement($user, $startDate)
    {
        // This would depend on your engagement tracking system
        return collect([]);
    }

    private function getTopSites($user, $startDate)
    {
        $query = $user->role === 1 ? null : $user->id;
        $siteQuery = Site::query();

        if ($query) {
            $siteQuery->where('user_id', $query);
        }

        return $siteQuery->whereBetween('created_at', [$startDate, now()])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
    }

    private function getRevenueOverview($user, $startDate)
    {
        // This would depend on your revenue tracking system
        return [
            'total_revenue' => 0,
            'monthly_revenue' => 0,
            'average_order_value' => 0,
            'revenue_growth' => 0,
        ];
    }

    private function getRevenueTrends($user, $startDate)
    {
        // This would depend on your revenue tracking system
        return collect([]);
    }

    private function getRevenueSources($user, $startDate)
    {
        // This would depend on your revenue tracking system
        return collect([]);
    }

    private function getRevenueForecast($user, $startDate)
    {
        // This would depend on your revenue tracking system
        return collect([]);
    }

    private function getProductViews($query)
    {
        // This would depend on your view tracking system
        return 0;
    }

    private function getSiteVisits($query)
    {
        // This would depend on your visit tracking system
        return 0;
    }

    private function getCourseEnrollments($query)
    {
        // This would depend on your enrollment tracking system
        return 0;
    }

    private function getConversionRate($query)
    {
        // This would depend on your conversion tracking system
        return 0;
    }

    private function getUserGrowthTrend($query, $startDate)
    {
        $userQuery = User::query();
        
        if ($query) {
            $userQuery->where('id', $query);
        }

        return $userQuery->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [$startDate, now()])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getProductGrowthTrend($query, $startDate)
    {
        $productQuery = Product::query();
        
        if ($query) {
            $productQuery->where('user_id', $query);
        }

        return $productQuery->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [$startDate, now()])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getSiteGrowthTrend($query, $startDate)
    {
        $siteQuery = Site::query();
        
        if ($query) {
            $siteQuery->where('user_id', $query);
        }

        return $siteQuery->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [$startDate, now()])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getRevenueGrowthTrend($query, $startDate)
    {
        // This would depend on your revenue tracking system
        return collect([]);
    }

    private function generateReport($user, $type, $startDate)
    {
        switch ($type) {
            case 'summary':
                return [
                    'overview' => $this->getOverviewStats($user),
                    'recent_activity' => $this->getRecentActivity($user),
                ];
            
            case 'detailed':
                return [
                    'overview' => $this->getOverviewStats($user),
                    'recent_activity' => $this->getRecentActivity($user),
                    'performance_metrics' => $this->getPerformanceMetrics($user),
                    'growth_trends' => $this->getGrowthTrends($user),
                ];
            
            case 'products':
                return [
                    'product_performance' => $this->getProductPerformance($user, $startDate),
                    'product_trends' => $this->getProductTrends($user, $startDate),
                    'top_products' => $this->getTopProducts($user, $startDate),
                ];
            
            case 'sites':
                return [
                    'site_performance' => $this->getSitePerformance($user, $startDate),
                    'top_sites' => $this->getTopSites($user, $startDate),
                ];
            
            default:
                return [
                    'overview' => $this->getOverviewStats($user),
                ];
        }
    }
} 