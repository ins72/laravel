@extends('layouts.app')

@section('title', 'Special Deals')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Special Deals</h1>
        <p class="text-lg text-gray-600 mt-2">Limited time offers you don't want to miss</p>
    </div>

    @if($products->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($products as $product)
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
                    
                    <!-- Deal Badge -->
                    <div class="absolute top-2 left-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                        Deal
                    </div>
                    
                    <!-- Countdown Timer -->
                    @if($product->dealEnds)
                    <div class="absolute top-2 right-2 bg-black bg-opacity-75 text-white text-xs px-2 py-1 rounded">
                        {{ $product->dealEnds->diffForHumans() }} left
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
                            <span class="text-lg font-bold text-red-600">${{ number_format($product->salePrice, 2) }}</span>
                            <span class="text-sm text-gray-500 line-through">${{ number_format($product->price, 2) }}</span>
                        </div>
                        
                        <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full">Sale</span>
                    </div>
                    
                    <!-- Countdown Timer -->
                    @if($product->dealEnds)
                    <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-3">
                        <p class="text-red-800 text-xs font-medium mb-2 text-center">Deal ends in:</p>
                        <div class="flex justify-center space-x-1">
                            <div class="text-center">
                                <span class="text-sm font-bold text-red-600" id="days-{{ $product->id }}">00</span>
                                <p class="text-xs text-red-600">D</p>
                            </div>
                            <div class="text-center">
                                <span class="text-sm font-bold text-red-600" id="hours-{{ $product->id }}">00</span>
                                <p class="text-xs text-red-600">H</p>
                            </div>
                            <div class="text-center">
                                <span class="text-sm font-bold text-red-600" id="minutes-{{ $product->id }}">00</span>
                                <p class="text-xs text-red-600">M</p>
                            </div>
                            <div class="text-center">
                                <span class="text-sm font-bold text-red-600" id="seconds-{{ $product->id }}">00</span>
                                <p class="text-xs text-red-600">S</p>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <div class="flex items-center justify-between">
                        <form action="{{ route('cart.add') }}" method="POST" class="flex-1 mr-2">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="w-full bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition duration-300">
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
        <!-- No Deals -->
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No deals available</h3>
            <p class="mt-1 text-sm text-gray-500">
                Check back later for amazing deals and discounts.
            </p>
            <div class="mt-6">
                <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                    Browse All Products
                </a>
            </div>
        </div>
    @endif
</div>

@if($products->count() > 0)
<script>
function updateTimer(productId, dealEnds) {
    const now = new Date().getTime();
    const endTime = new Date(dealEnds).getTime();
    const distance = endTime - now;
    
    if (distance < 0) {
        // Deal has ended
        document.getElementById(`days-${productId}`).textContent = '00';
        document.getElementById(`hours-${productId}`).textContent = '00';
        document.getElementById(`minutes-${productId}`).textContent = '00';
        document.getElementById(`seconds-${productId}`).textContent = '00';
        return;
    }
    
    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((distance % (1000 * 60)) / 1000);
    
    document.getElementById(`days-${productId}`).textContent = days.toString().padStart(2, '0');
    document.getElementById(`hours-${productId}`).textContent = hours.toString().padStart(2, '0');
    document.getElementById(`minutes-${productId}`).textContent = minutes.toString().padStart(2, '0');
    document.getElementById(`seconds-${productId}`).textContent = seconds.toString().padStart(2, '0');
}

// Update all timers every second
setInterval(function() {
    @foreach($products as $product)
        @if($product->dealEnds)
            updateTimer({{ $product->id }}, '{{ $product->dealEnds }}');
        @endif
    @endforeach
}, 1000);

// Initial call
@foreach($products as $product)
    @if($product->dealEnds)
        updateTimer({{ $product->id }}, '{{ $product->dealEnds }}');
    @endif
@endforeach
</script>
@endif
@endsection