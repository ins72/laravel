@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Breadcrumb -->
    <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('home') }}" class="text-gray-700 hover:text-indigo-600">
                    Home
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <a href="{{ route('products.index') }}" class="text-gray-700 hover:text-indigo-600">
                        Products
                    </a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-gray-500">{{ $product->name }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
        <!-- Product Images -->
        <div class="space-y-4">
            @if($product->featured_img && isset($product->featured_img['url']))
                <div class="aspect-w-1 aspect-h-1 bg-gray-200 rounded-lg overflow-hidden">
                    <img src="{{ $product->featured_img['url'] }}" alt="{{ $product->name }}" 
                         class="w-full h-96 object-cover">
                </div>
            @else
                <div class="aspect-w-1 aspect-h-1 bg-gray-300 rounded-lg flex items-center justify-center">
                    <svg class="w-24 h-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            @endif
            
            @if($product->media && count($product->media) > 0)
                <div class="grid grid-cols-4 gap-2">
                    @foreach($product->media as $media)
                        <div class="aspect-w-1 aspect-h-1 bg-gray-200 rounded-lg overflow-hidden">
                            <img src="{{ $media['url'] ?? '' }}" alt="{{ $product->name }}" 
                                 class="w-full h-20 object-cover cursor-pointer hover:opacity-75">
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Product Details -->
        <div class="space-y-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $product->name }}</h1>
                <p class="text-gray-600">SKU: {{ $product->sku ?? 'N/A' }}</p>
            </div>

            <!-- Price -->
            <div class="flex items-center space-x-4">
                @if($product->isOnSale)
                    <span class="text-3xl font-bold text-red-600">${{ number_format($product->salePrice, 2) }}</span>
                    <span class="text-xl text-gray-500 line-through">${{ number_format($product->price, 2) }}</span>
                    <span class="bg-red-500 text-white text-sm px-2 py-1 rounded-full">Sale</span>
                @else
                    <span class="text-3xl font-bold text-gray-900">{{ $product->formattedPrice }}</span>
                @endif
            </div>

            <!-- Deal Timer -->
            @if($product->isOnSale && $product->dealEnds)
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <p class="text-red-800 font-medium mb-2">Deal ends in:</p>
                    <div class="flex space-x-2">
                        <div class="text-center">
                            <span class="text-2xl font-bold text-red-600" id="days">00</span>
                            <p class="text-xs text-red-600">Days</p>
                        </div>
                        <div class="text-center">
                            <span class="text-2xl font-bold text-red-600" id="hours">00</span>
                            <p class="text-xs text-red-600">Hours</p>
                        </div>
                        <div class="text-center">
                            <span class="text-2xl font-bold text-red-600" id="minutes">00</span>
                            <p class="text-xs text-red-600">Minutes</p>
                        </div>
                        <div class="text-center">
                            <span class="text-2xl font-bold text-red-600" id="seconds">00</span>
                            <p class="text-xs text-red-600">Seconds</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Stock Status -->
            @if($product->stock !== null)
                <div class="flex items-center space-x-2">
                    @if($product->stock > 0)
                        <span class="text-green-600 font-medium">In Stock ({{ $product->stock }} available)</span>
                    @else
                        <span class="text-red-600 font-medium">Out of Stock</span>
                    @endif
                </div>
            @endif

            <!-- Add to Cart Form -->
            @if($product->stock === null || $product->stock > 0)
                <form action="{{ route('cart.add') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    
                    <div>
                        <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                        <input type="number" name="quantity" id="quantity" value="1" min="1" 
                               max="{{ $product->stock ?? 999 }}" 
                               class="w-20 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>
                    
                    <button type="submit" class="w-full bg-indigo-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-indigo-700 transition duration-300">
                        Add to Cart
                    </button>
                </form>
            @else
                <button disabled class="w-full bg-gray-400 text-white py-3 px-4 rounded-lg font-semibold cursor-not-allowed">
                    Out of Stock
                </button>
            @endif

            <!-- Product Meta -->
            <div class="border-t border-gray-200 pt-6 space-y-4">
                @if($product->user)
                    <div class="flex items-center space-x-2">
                        <span class="text-gray-600">Seller:</span>
                        <span class="font-medium">{{ $product->user->name }}</span>
                    </div>
                @endif
                
                @if($product->created_at)
                    <div class="flex items-center space-x-2">
                        <span class="text-gray-600">Listed:</span>
                        <span class="font-medium">{{ $product->created_at->format('M j, Y') }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Product Description -->
    @if($product->description)
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Description</h2>
            <div class="prose max-w-none text-gray-600">
                {!! nl2br(e($product->description)) !!}
            </div>
        </div>
    @endif

    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-semibold text-gray-900 mb-6">Related Products</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($relatedProducts as $relatedProduct)
                <div class="bg-gray-50 rounded-lg overflow-hidden hover:shadow-md transition duration-300">
                    <div class="aspect-w-1 aspect-h-1 bg-gray-200">
                        @if($relatedProduct->featured_img && isset($relatedProduct->featured_img['url']))
                            <img src="{{ $relatedProduct->featured_img['url'] }}" alt="{{ $relatedProduct->name }}" class="w-full h-32 object-cover">
                        @else
                            <div class="w-full h-32 bg-gray-300 flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        @endif
                    </div>
                    
                    <div class="p-4">
                        <h3 class="font-semibold text-gray-900 mb-2">
                            <a href="{{ route('products.show', $relatedProduct->slug) }}" class="hover:text-indigo-600">
                                {{ $relatedProduct->name }}
                            </a>
                        </h3>
                        
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-gray-900">
                                @if($relatedProduct->isOnSale)
                                    ${{ number_format($relatedProduct->salePrice, 2) }}
                                @else
                                    {{ $relatedProduct->formattedPrice }}
                                @endif
                            </span>
                            
                            <a href="{{ route('products.show', $relatedProduct->slug) }}" 
                               class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                View →
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

@if($product->isOnSale && $product->dealEnds)
<script>
function updateTimer() {
    const now = new Date().getTime();
    const dealEnds = new Date('{{ $product->dealEnds }}').getTime();
    const distance = dealEnds - now;
    
    if (distance < 0) {
        // Deal has ended
        document.getElementById('days').textContent = '00';
        document.getElementById('hours').textContent = '00';
        document.getElementById('minutes').textContent = '00';
        document.getElementById('seconds').textContent = '00';
        return;
    }
    
    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((distance % (1000 * 60)) / 1000);
    
    document.getElementById('days').textContent = days.toString().padStart(2, '0');
    document.getElementById('hours').textContent = hours.toString().padStart(2, '0');
    document.getElementById('minutes').textContent = minutes.toString().padStart(2, '0');
    document.getElementById('seconds').textContent = seconds.toString().padStart(2, '0');
}

// Update timer every second
setInterval(updateTimer, 1000);
updateTimer(); // Initial call
</script>
@endif
@endsection