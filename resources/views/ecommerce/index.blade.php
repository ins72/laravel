@extends('layouts.ecommerce')

@section('title', 'Shop - Product Catalog')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Shop</h1>
        <p class="mt-2 text-gray-600">Discover amazing products at great prices</p>
    </div>

    <!-- Filters and Sort -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
            <!-- Active Filters -->
            <div class="flex flex-wrap items-center space-x-2">
                @if(request('category'))
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                        Category: {{ ucfirst(request('category')) }}
                        <a href="{{ route('ecommerce.index', array_merge(request()->except('category'), ['page' => 1])) }}" class="ml-2 text-indigo-600 hover:text-indigo-800">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                @endif
                @if(request('min_price') || request('max_price'))
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        Price: ${{ request('min_price', '0') }} - ${{ request('max_price', '∞') }}
                        <a href="{{ route('ecommerce.index', array_merge(request()->except(['min_price', 'max_price']), ['page' => 1])) }}" class="ml-2 text-green-600 hover:text-green-800">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                @endif
            </div>

            <!-- Sort Options -->
            <div class="flex items-center space-x-4">
                <label for="sort" class="text-sm font-medium text-gray-700">Sort by:</label>
                <select id="sort" name="sort" onchange="window.location.href='{{ route('ecommerce.index') }}?sort=' + this.value + '&' + new URLSearchParams(window.location.search).toString().replace(/sort=[^&]*&?/g, '')" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest</option>
                    <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                    <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                    <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name: A to Z</option>
                    <option value="featured" {{ request('sort') == 'featured' ? 'selected' : '' }}>Featured</option>
                </select>
            </div>
        </div>

        <!-- Advanced Filters -->
        <div class="mt-6 pt-6 border-t border-gray-200">
            <form action="{{ route('ecommerce.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <input type="hidden" name="search" value="{{ request('search') }}">
                <input type="hidden" name="sort" value="{{ request('sort') }}">
                
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select id="category" name="category" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Categories</option>
                        <option value="electronics" {{ request('category') == 'electronics' ? 'selected' : '' }}>Electronics</option>
                        <option value="clothing" {{ request('category') == 'clothing' ? 'selected' : '' }}>Clothing</option>
                        <option value="books" {{ request('category') == 'books' ? 'selected' : '' }}>Books</option>
                        <option value="home" {{ request('category') == 'home' ? 'selected' : '' }}>Home & Garden</option>
                    </select>
                </div>

                <div>
                    <label for="min_price" class="block text-sm font-medium text-gray-700 mb-1">Min Price</label>
                    <input type="number" id="min_price" name="min_price" value="{{ request('min_price') }}" min="0" step="0.01" placeholder="0.00" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label for="max_price" class="block text-sm font-medium text-gray-700 mb-1">Max Price</label>
                    <input type="number" id="max_price" name="max_price" value="{{ request('max_price') }}" min="0" step="0.01" placeholder="1000.00" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                        <i class="fas fa-filter mr-2"></i> Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Featured Products -->
    @if($featuredProducts->count() > 0)
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Featured Products</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6">
                @foreach($featuredProducts as $product)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                        <div class="aspect-w-1 aspect-h-1 bg-gray-200">
                            @if($product->featured_img && isset($product->featured_img['url']))
                                <img src="{{ $product->featured_img['url'] }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <i class="fas fa-image text-4xl"></i>
                                </div>
                            @endif
                        </div>
                        <div class="p-4">
                            <h3 class="text-sm font-medium text-gray-900 mb-2 line-clamp-2">
                                <a href="{{ route('ecommerce.show', $product->slug) }}" class="hover:text-indigo-600">
                                    {{ $product->name }}
                                </a>
                            </h3>
                            <div class="flex items-center justify-between">
                                <p class="text-lg font-bold text-indigo-600">{{ $product->formattedPrice }}</p>
                                @if($product->isOnSale)
                                    <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full">Sale</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Product Grid -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900">All Products</h2>
            <p class="text-gray-600">{{ $products->total() }} products found</p>
        </div>

        @if($products->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($products as $product)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow" x-data="{ loading: false }">
                        <div class="aspect-w-1 aspect-h-1 bg-gray-200">
                            @if($product->featured_img && isset($product->featured_img['url']))
                                <img src="{{ $product->featured_img['url'] }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <i class="fas fa-image text-4xl"></i>
                                </div>
                            @endif
                            
                            @if($product->isOnSale)
                                <div class="absolute top-2 right-2">
                                    <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full">Sale</span>
                                </div>
                            @endif
                            
                            @if($product->ribbon && isset($product->ribbon['text']))
                                <div class="absolute top-2 left-2">
                                    <span class="bg-indigo-500 text-white text-xs px-2 py-1 rounded-full">{{ $product->ribbon['text'] }}</span>
                                </div>
                            @endif
                        </div>
                        
                        <div class="p-4">
                            <h3 class="text-sm font-medium text-gray-900 mb-2 line-clamp-2">
                                <a href="{{ route('ecommerce.show', $product->slug) }}" class="hover:text-indigo-600">
                                    {{ $product->name }}
                                </a>
                            </h3>
                            
                            <div class="flex items-center mb-2">
                                @if($product->reviews && $product->reviews->count() > 0)
                                    @php
                                        $avgRating = $product->reviews->avg('rating');
                                        $reviewCount = $product->reviews->count();
                                    @endphp
                                    <div class="flex items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $avgRating)
                                                <i class="fas fa-star text-yellow-400 text-sm"></i>
                                            @elseif($i - $avgRating < 1)
                                                <i class="fas fa-star-half-alt text-yellow-400 text-sm"></i>
                                            @else
                                                <i class="far fa-star text-yellow-400 text-sm"></i>
                                            @endif
                                        @endfor
                                        <span class="ml-1 text-xs text-gray-500">({{ $reviewCount }})</span>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">No reviews yet</span>
                                @endif
                            </div>
                            
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center space-x-2">
                                    @if($product->isOnSale && $product->dealPrice)
                                        <p class="text-lg font-bold text-indigo-600">${{ number_format($product->dealPrice, 2) }}</p>
                                        <p class="text-sm text-gray-500 line-through">${{ number_format($product->price, 2) }}</p>
                                    @else
                                        <p class="text-lg font-bold text-indigo-600">{{ $product->formattedPrice }}</p>
                                    @endif
                                </div>
                                
                                @if($product->stock !== null)
                                    <span class="text-xs {{ $product->stock > 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $product->stock > 0 ? 'In Stock' : 'Out of Stock' }}
                                    </span>
                                @endif
                            </div>
                            
                            <button 
                                onclick="addToCart({{ $product->id }})"
                                :disabled="loading || {{ $product->stock === 0 ? 'true' : 'false' }}"
                                class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 disabled:bg-gray-400 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors"
                                :class="{ 'opacity-75': loading }">
                                <span x-show="!loading">
                                    <i class="fas fa-shopping-cart mr-2"></i>
                                    {{ $product->stock === 0 ? 'Out of Stock' : 'Add to Cart' }}
                                </span>
                                <span x-show="loading" class="flex items-center justify-center">
                                    <i class="fas fa-spinner fa-spin mr-2"></i>
                                    Adding...
                                </span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $products->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-search text-4xl text-gray-400 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No products found</h3>
                <p class="text-gray-600">Try adjusting your search criteria or browse our featured products above.</p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function addToCart(productId) {
    const button = event.target;
    const loadingState = button.closest('[x-data]').__x.$data;
    loadingState.loading = true;

    fetch('{{ route('ecommerce.addToCart') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update cart count
            updateCartCount(data.cartCount);
            
            // Show success message
            showNotification(data.message, 'success');
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred. Please try again.', 'error');
    })
    .finally(() => {
        loadingState.loading = false;
    });
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>
@endpush

@push('styles')
<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.aspect-w-1 {
    position: relative;
    padding-bottom: 100%;
}

.aspect-h-1 {
    position: absolute;
    height: 100%;
    width: 100%;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
}
</style>
@endpush
@endsection