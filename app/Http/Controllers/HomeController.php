<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;

class HomeController extends Controller
{
    public function index()
    {
        $featuredProducts = Product::featured()
                                  ->active()
                                  ->inStock()
                                  ->take(8)
                                  ->get();

        $latestProducts = Product::active()
                                ->inStock()
                                ->latest()
                                ->take(8)
                                ->get();

        $deals = Product::where('isDeal', true)
                       ->where('dealEnds', '>', now())
                       ->active()
                       ->inStock()
                       ->take(4)
                       ->get();

        return view('home', compact('featuredProducts', 'latestProducts', 'deals'));
    }

    public function about()
    {
        return view('pages.about');
    }

    public function contact()
    {
        return view('pages.contact');
    }

    public function terms()
    {
        return view('pages.terms');
    }

    public function privacy()
    {
        return view('pages.privacy');
    }
}