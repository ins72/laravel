<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('user')->active();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Category filter (if you have categories)
        if ($request->filled('category')) {
            // Add category filtering logic here
        }

        // Price filter
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sort products
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'newest':
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(12);
        
        return view('products.index', compact('products'));
    }

    public function show($slug)
    {
        $product = Product::where('slug', $slug)
                         ->with(['user', 'reviews.user'])
                         ->active()
                         ->firstOrFail();

        // Get related products
        $relatedProducts = Product::where('id', '!=', $product->id)
                                ->where('user_id', $product->user_id)
                                ->active()
                                ->take(4)
                                ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }

    public function featured()
    {
        $products = Product::featured()
                          ->active()
                          ->inStock()
                          ->take(8)
                          ->get();

        return view('products.featured', compact('products'));
    }

    public function deals()
    {
        $products = Product::where('isDeal', true)
                          ->where('dealEnds', '>', now())
                          ->active()
                          ->inStock()
                          ->latest()
                          ->paginate(12);

        return view('products.deals', compact('products'));
    }
}