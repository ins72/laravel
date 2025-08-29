@extends('layouts.app')

@section('title', 'Home')

@section('content')
<!-- Hero Section -->
<div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6">
                Welcome to {{ config('app.name', 'Laravel') }}
            </h1>
            <p class="text-xl md:text-2xl mb-8 text-indigo-100">
                Discover amazing products at unbeatable prices
            </p>
            <div class="space-x-4">
                <a href="{{ route('products.index') }}" class="bg-white text-indigo-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition duration-300">
                    Shop Now
                </a>
                <a href="{{ route('products.deals') }}" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-indigo-600 transition duration-300">
                    View Deals
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Featured Products Section -->
@if($featuredProducts->count() > 0)
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-gray-900 mb-4">Featured Products</h2>
        <p class="text-lg text-gray-600">Handpicked products you'll love</p>
    </div>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($featuredProducts as $product)
        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300">
            <div class="aspect-w-1 aspect-h-1 bg-gray-200">
                @if($product->featured_img && isset($product->featured_img['url']))
                    <img src="{{ $product->featured_img['url'] }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                @else
                    <div class="w-full h-48 bg-gray-300 flex items-center justify-center">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                @endif
            </div>
            
            <div class="p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                    <a href="{{ route('products.show', $product->slug) }}" class="hover:text-indigo-600">
                        {{ $product->name }}
                    </a>
                </h3>
                
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center space-x-2">
                        @if($product->isOnSale)
                            <span class="text-lg font-bold text-red-600">${{ number_format($product->salePrice, 2) }}</span>
                            <span class="text-sm text-gray-500 line-through">${{ number_format($product->price, 2) }}</span>
                        @else
                            <span class="text-lg font-bold text-gray-900">{{ $product->formattedPrice }}</span>
                        @endif
                    </div>
                    
                    @if($product->isOnSale)
                        <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full">Sale</span>
                    @endif
                </div>
                
                <form action="{{ route('cart.add') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-lg hover:bg-indigo-700 transition duration-300">
                        Add to Cart
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    
    <div class="text-center mt-8">
        <a href="{{ route('products.featured') }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">
            View All Featured Products →
        </a>
    </div>
</div>
@endif

<!-- Deals Section -->
@if($deals->count() > 0)
<div class="bg-gray-50 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Special Deals</h2>
            <p class="text-lg text-gray-600">Limited time offers you don't want to miss</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($deals as $product)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300 border-2 border-red-200">
                <div class="relative">
                    <div class="aspect-w-1 aspect-h-1 bg-gray-200">
                        @if($product->featured_img && isset($product->featured_img['url']))
                            <img src="{{ $product->featured_img['url'] }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                        @else
                            <div class="w-full h-48 bg-gray-300 flex items-center justify-center">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        @endif
                    </div>
                    
                    <div class="absolute top-2 right-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                        {{ $product->dealEnds->diffForHumans() }} left
                    </div>
                </div>
                
                <div class="p-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">
                        <a href="{{ route('products.show', $product->slug) }}" class="hover:text-indigo-600">
                            {{ $product->name }}
                        </a>
                    </h3>
                    
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="text-lg font-bold text-red-600">${{ number_format($product->salePrice, 2) }}</span>
                            <span class="text-sm text-gray-500 line-through">${{ number_format($product->price, 2) }}</span>
                        </div>
                        
                        <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full">Deal</span>
                    </div>
                    
                    <form action="{{ route('cart.add') }}" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="w-full bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition duration-300">
                            Add to Cart
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="text-center mt-8">
            <a href="{{ route('products.deals') }}" class="text-red-600 hover:text-red-800 font-semibold">
                View All Deals →
            </a>
        </div>
    </div>
</div>
@endif

<!-- Latest Products Section -->
@if($latestProducts->count() > 0)
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-gray-900 mb-4">Latest Products</h2>
        <p class="text-lg text-gray-600">Fresh arrivals just for you</p>
    </div>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($latestProducts as $product)
        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300">
            <div class="aspect-w-1 aspect-h-1 bg-gray-200">
                @if($product->featured_img && isset($product->featured_img['url']))
                    <img src="{{ $product->featured_img['url'] }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                @else
                    <div class="w-full h-48 bg-gray-300 flex items-center justify-center">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                @endif
            </div>
            
            <div class="p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                    <a href="{{ route('products.show', $product->slug) }}" class="hover:text-indigo-600">
                        {{ $product->name }}
                    </a>
                </h3>
                
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center space-x-2">
                        @if($product->isOnSale)
                            <span class="text-lg font-bold text-red-600">${{ number_format($product->salePrice, 2) }}</span>
                            <span class="text-sm text-gray-500 line-through">${{ number_format($product->price, 2) }}</span>
                        @else
                            <span class="text-lg font-bold text-gray-900">{{ $product->formattedPrice }}</span>
                        @endif
                    </div>
                    
                    @if($product->isOnSale)
                        <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full">Sale</span>
                    @endif
                </div>
                
                <form action="{{ route('cart.add') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-lg hover:bg-indigo-700 transition duration-300">
                        Add to Cart
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    
    <div class="text-center mt-8">
        <a href="{{ route('products.index') }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">
            View All Products →
        </a>
    </div>
</div>
@endif

<!-- Features Section -->
<div class="bg-gray-50 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Why Choose Us?</h2>
            <p class="text-lg text-gray-600">We're committed to providing the best shopping experience</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="bg-indigo-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Fast Shipping</h3>
                <p class="text-gray-600">Get your products delivered quickly and securely</p>
            </div>
            
            <div class="text-center">
                <div class="bg-indigo-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Quality Guarantee</h3>
                <p class="text-gray-600">All products are carefully selected for quality</p>
            </div>
            
            <div class="text-center">
                <div class="bg-indigo-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 2.25a9.75 9.75 0 100 19.5 9.75 9.75 0 000-19.5z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">24/7 Support</h3>
                <p class="text-gray-600">Our customer support team is always here to help</p>
            </div>
        </div>
    </div>
</div>
@endsection