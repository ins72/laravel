<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Course;
use App\Models\Page;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_products' => Product::count(),
            'total_courses' => Course::count(),
            'total_sites' => Site::count(),
            'recent_users' => User::latest()->take(5)->get(),
            'recent_products' => Product::latest()->take(5)->get(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    // User Management CRUD
    public function users()
    {
        $users = User::with('sites')->latest()->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function userShow($id)
    {
        $user = User::with(['sites', 'products', 'courses'])->findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    public function userEdit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function userUpdate(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|integer|in:0,1,2',
            'status' => 'required|integer|in:0,1',
        ]);

        $user->update($request->only(['name', 'email', 'role', 'status']));
        
        return redirect()->route('admin.users.index')->with('success', 'User updated successfully');
    }

    public function userDelete($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully');
    }

    // Product Management CRUD
    public function products()
    {
        $products = Product::with('user')->latest()->paginate(20);
        return view('admin.products.index', compact('products'));
    }

    public function productShow($id)
    {
        $product = Product::with('user')->findOrFail($id);
        return view('admin.products.show', compact('product'));
    }

    public function productEdit($id)
    {
        $product = Product::findOrFail($id);
        $users = User::all();
        return view('admin.products.edit', compact('product', 'users'));
    }

    public function productUpdate(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'status' => 'required|integer|in:0,1',
            'user_id' => 'required|exists:users,id',
        ]);

        $product->update($request->only(['name', 'price', 'status', 'user_id', 'description']));
        
        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully');
    }

    public function productDelete($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        
        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully');
    }

    // Site Management CRUD
    public function sites()
    {
        $sites = Site::with('user')->latest()->paginate(20);
        return view('admin.sites.index', compact('sites'));
    }

    public function siteShow($id)
    {
        $site = Site::with(['user', 'pages'])->findOrFail($id);
        return view('admin.sites.show', compact('site'));
    }

    public function siteEdit($id)
    {
        $site = Site::findOrFail($id);
        $users = User::all();
        return view('admin.sites.edit', compact('site', 'users'));
    }

    public function siteUpdate(Request $request, $id)
    {
        $site = Site::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'domain' => 'nullable|string|max:255',
            'status' => 'required|integer|in:0,1',
            'user_id' => 'required|exists:users,id',
        ]);

        $site->update($request->only(['name', 'domain', 'status', 'user_id', 'settings']));
        
        return redirect()->route('admin.sites.index')->with('success', 'Site updated successfully');
    }

    public function siteDelete($id)
    {
        $site = Site::findOrFail($id);
        $site->delete();
        
        return redirect()->route('admin.sites.index')->with('success', 'Site deleted successfully');
    }

    // Analytics
    public function analytics()
    {
        $analytics = [
            'users_growth' => $this->getUsersGrowth(),
            'products_growth' => $this->getProductsGrowth(),
            'revenue_data' => $this->getRevenueData(),
        ];

        return view('admin.analytics', compact('analytics'));
    }

    private function getUsersGrowth()
    {
        return User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getProductsGrowth()
    {
        return Product::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getRevenueData()
    {
        // This would integrate with your payment system
        return collect();
    }
} 