@extends('layouts.app')

@section('title', 'Featured Products')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Featured Products</h1>
        <p class="text-lg text-gray-600 mt-2">Handpicked products you'll love</p>
    </div>

    @if($products->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($products as $product)
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
                    
                    <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                        {{ Str::limit($product->description, 100) }}
                    </p>
                    
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
                    
                    <div class="flex items-center justify-between">
                        <form action="{{ route('cart.add') }}" method="POST" class="flex-1 mr-2">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-lg hover:bg-indigo-700 transition duration-300">
                                Add to Cart
                            </button>
                        </form>
                        
                        <a href="{{ route('products.show', $product->slug) }}" 
                           class="bg-gray-100 text-gray-700 py-2 px-3 rounded-lg hover:bg-gray-200 transition duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="mt-12">
            {{ $products->links() }}
        </div>
    @else
        <!-- No Featured Products -->
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No featured products yet</h3>
            <p class="mt-1 text-sm text-gray-500">
                Our team is working on selecting the best products for you.
            </p>
            <div class="mt-6">
                <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                    Browse All Products
                </a>
            </div>
        </div>
    @endif
</div>
@endsection