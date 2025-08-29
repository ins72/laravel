@extends('layouts.ecommerce')

@section('title', $product->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Breadcrumb -->
    <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('ecommerce.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">
                    <i class="fas fa-home mr-2"></i>
                    Shop
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="text-sm font-medium text-gray-500">{{ $product->name }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-12">
        <!-- Product Images -->
        <div class="space-y-4">
            <!-- Main Image -->
            <div class="aspect-w-1 aspect-h-1 bg-gray-200 rounded-lg overflow-hidden">
                @if($product->featured_img && isset($product->featured_img['url']))
                    <img id="main-image" src="{{ $product->featured_img['url'] }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                        <i class="fas fa-image text-6xl"></i>
                    </div>
                @endif
            </div>

            <!-- Thumbnail Images -->
            @if($product->media && count($product->media) > 0)
                <div class="grid grid-cols-4 gap-2">
                    @foreach($product->media as $media)
                        @if(isset($media['url']))
                            <button onclick="changeMainImage('{{ $media['url'] }}')" class="aspect-w-1 aspect-h-1 bg-gray-200 rounded-lg overflow-hidden border-2 border-transparent hover:border-indigo-500 transition-colors">
                                <img src="{{ $media['url'] }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                            </button>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Product Info -->
        <div class="space-y-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $product->name }}</h1>
                
                <!-- Rating -->
                @if($product->reviews && $product->reviews->count() > 0)
                    @php
                        $avgRating = $product->reviews->avg('rating');
                        $reviewCount = $product->reviews->count();
                    @endphp
                    <div class="flex items-center mb-4">
                        <div class="flex items-center">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $avgRating)
                                    <i class="fas fa-star text-yellow-400"></i>
                                @elseif($i - $avgRating < 1)
                                    <i class="fas fa-star-half-alt text-yellow-400"></i>
                                @else
                                    <i class="far fa-star text-yellow-400"></i>
                                @endif
                            @endfor
                        </div>
                        <span class="ml-2 text-sm text-gray-600">{{ number_format($avgRating, 1) }} ({{ $reviewCount }} reviews)</span>
                        <a href="#reviews" class="ml-4 text-sm text-indigo-600 hover:text-indigo-800">View all reviews</a>
                    </div>
                @endif

                <!-- Price -->
                <div class="mb-6">
                    @if($product->isOnSale && $product->dealPrice)
                        <div class="flex items-center space-x-3">
                            <span class="text-3xl font-bold text-indigo-600">${{ number_format($product->dealPrice, 2) }}</span>
                            <span class="text-xl text-gray-500 line-through">${{ number_format($product->price, 2) }}</span>
                            <span class="bg-red-100 text-red-800 text-sm px-2 py-1 rounded-full">
                                {{ round((($product->price - $product->dealPrice) / $product->price) * 100) }}% OFF
                            </span>
                        </div>
                        @if($product->dealEnds)
                            <p class="text-sm text-gray-600 mt-2">
                                <i class="fas fa-clock mr-1"></i>
                                Sale ends {{ $product->dealEnds->format('M d, Y') }}
                            </p>
                        @endif
                    @else
                        <span class="text-3xl font-bold text-indigo-600">{{ $product->formattedPrice }}</span>
                    @endif
                </div>

                <!-- Stock Status -->
                @if($product->stock !== null)
                    <div class="mb-6">
                        @if($product->stock > 0)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-2"></i>
                                In Stock ({{ $product->stock }} available)
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                <i class="fas fa-times-circle mr-2"></i>
                                Out of Stock
                            </span>
                        @endif
                    </div>
                @endif

                <!-- Product Options -->
                @if($product->enableOptions && $product->options && count($product->options) > 0)
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Options</h3>
                        <div class="space-y-3">
                            @foreach($product->options as $option)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $option->name }}</label>
                                    <select class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        @foreach($option->values as $value)
                                            <option value="{{ $value->id }}">{{ $value->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Add to Cart -->
                <div class="mb-6" x-data="{ quantity: 1, loading: false }">
                    <div class="flex items-center space-x-4 mb-4">
                        <label for="quantity" class="text-sm font-medium text-gray-700">Quantity:</label>
                        <div class="flex items-center border border-gray-300 rounded-md">
                            <button @click="quantity = Math.max(1, quantity - 1)" class="px-3 py-2 text-gray-600 hover:text-gray-800">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" id="quantity" x-model="quantity" min="1" max="{{ $product->stock ?? 999 }}" class="w-16 text-center border-0 focus:ring-0">
                            <button @click="quantity = Math.min({{ $product->stock ?? 999 }}, quantity + 1)" class="px-3 py-2 text-gray-600 hover:text-gray-800">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>

                    <button 
                        onclick="addToCart({{ $product->id }}, quantity)"
                        :disabled="loading || {{ $product->stock === 0 ? 'true' : 'false' }}"
                        class="w-full bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 disabled:bg-gray-400 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors text-lg font-medium"
                        :class="{ 'opacity-75': loading }">
                        <span x-show="!loading">
                            <i class="fas fa-shopping-cart mr-2"></i>
                            {{ $product->stock === 0 ? 'Out of Stock' : 'Add to Cart' }}
                        </span>
                        <span x-show="loading" class="flex items-center justify-center">
                            <i class="fas fa-spinner fa-spin mr-2"></i>
                            Adding to Cart...
                        </span>
                    </button>
                </div>

                <!-- Product Meta -->
                <div class="border-t border-gray-200 pt-6">
                    <dl class="space-y-3">
                        @if($product->sku)
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">SKU:</dt>
                                <dd class="text-sm text-gray-900">{{ $product->sku }}</dd>
                            </div>
                        @endif
                        @if($product->productType)
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Category:</dt>
                                <dd class="text-sm text-gray-900">{{ ucfirst($product->productType) }}</dd>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500">Seller:</dt>
                            <dd class="text-sm text-gray-900">{{ $product->user->name }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Details Tabs -->
    <div class="mb-12">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <button onclick="showTab('description')" class="tab-button border-b-2 border-indigo-500 py-2 px-1 text-sm font-medium text-indigo-600" data-tab="description">
                    Description
                </button>
                <button onclick="showTab('specifications')" class="tab-button border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="specifications">
                    Specifications
                </button>
                <button onclick="showTab('reviews')" class="tab-button border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="reviews">
                    Reviews ({{ $product->reviews ? $product->reviews->count() : 0 }})
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="mt-8">
            <!-- Description Tab -->
            <div id="description" class="tab-content">
                <div class="prose max-w-none">
                    {!! $product->description ?: '<p class="text-gray-500">No description available.</p>' !!}
                </div>
            </div>

            <!-- Specifications Tab -->
            <div id="specifications" class="tab-content hidden">
                <div class="bg-gray-50 rounded-lg p-6">
                    @if($product->extra && count($product->extra) > 0)
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            @foreach($product->extra as $key => $value)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ ucfirst(str_replace('_', ' ', $key)) }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $value }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    @else
                        <p class="text-gray-500">No specifications available.</p>
                    @endif
                </div>
            </div>

            <!-- Reviews Tab -->
            <div id="reviews" class="tab-content hidden">
                <div class="space-y-6">
                    <!-- Review Summary -->
                    @if($product->reviews && $product->reviews->count() > 0)
                        <div class="bg-gray-50 rounded-lg p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Customer Reviews</h3>
                                <div class="flex items-center">
                                    <span class="text-2xl font-bold text-gray-900">{{ number_format($avgRating, 1) }}</span>
                                    <div class="ml-2">
                                        <div class="flex items-center">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $avgRating)
                                                    <i class="fas fa-star text-yellow-400"></i>
                                                @elseif($i - $avgRating < 1)
                                                    <i class="fas fa-star-half-alt text-yellow-400"></i>
                                                @else
                                                    <i class="far fa-star text-yellow-400"></i>
                                                @endif
                                            @endfor
                                        </div>
                                        <p class="text-sm text-gray-600">{{ $reviewCount }} reviews</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Review List -->
                        <div class="space-y-4">
                            @foreach($product->reviews as $review)
                                <div class="bg-white border border-gray-200 rounded-lg p-6">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center">
                                            <img class="h-10 w-10 rounded-full" src="https://ui-avatars.com/api/?name={{ $review->user->name }}&color=7C3AED&background=EBF4FF" alt="{{ $review->user->name }}">
                                            <div class="ml-3">
                                                <p class="text-sm font-medium text-gray-900">{{ $review->user->name }}</p>
                                                <div class="flex items-center">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= $review->rating)
                                                            <i class="fas fa-star text-yellow-400 text-sm"></i>
                                                        @else
                                                            <i class="far fa-star text-yellow-400 text-sm"></i>
                                                        @endif
                                                    @endfor
                                                </div>
                                            </div>
                                        </div>
                                        <span class="text-sm text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-gray-700">{{ $review->comment }}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-comments text-4xl text-gray-400 mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No reviews yet</h3>
                            <p class="text-gray-600">Be the first to review this product!</p>
                        </div>
                    @endif

                    <!-- Add Review Form -->
                    @auth
                        @php
                            $hasPurchased = App\Models\ProductOrder::where('user_id', auth()->id())
                                ->whereHas('items', function($query) use ($product) {
                                    $query->where('product_id', $product->id);
                                })
                                ->where('payment_status', 'completed')
                                ->exists();
                        @endphp

                        @if($hasPurchased)
                            <div class="bg-white border border-gray-200 rounded-lg p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Write a Review</h3>
                                <form id="review-form" class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                                        <div class="flex items-center space-x-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                <button type="button" onclick="setRating({{ $i }})" class="rating-star text-2xl text-gray-300 hover:text-yellow-400" data-rating="{{ $i }}">
                                                    <i class="far fa-star"></i>
                                                </button>
                                            @endfor
                                        </div>
                                    </div>
                                    <div>
                                        <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">Comment</label>
                                        <textarea id="comment" name="comment" rows="4" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Share your thoughts about this product..."></textarea>
                                    </div>
                                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                                        Submit Review
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="bg-gray-50 rounded-lg p-6 text-center">
                                <p class="text-gray-600">You need to purchase this product to write a review.</p>
                            </div>
                        @endif
                    @else
                        <div class="bg-gray-50 rounded-lg p-6 text-center">
                            <p class="text-gray-600">Please <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-800">login</a> to write a review.</p>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Related Products</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($relatedProducts as $relatedProduct)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                        <a href="{{ route('ecommerce.show', $relatedProduct->slug) }}">
                            <div class="aspect-w-1 aspect-h-1 bg-gray-200">
                                @if($relatedProduct->featured_img && isset($relatedProduct->featured_img['url']))
                                    <img src="{{ $relatedProduct->featured_img['url'] }}" alt="{{ $relatedProduct->name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                        <i class="fas fa-image text-4xl"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="p-4">
                                <h3 class="text-sm font-medium text-gray-900 mb-2 line-clamp-2">{{ $relatedProduct->name }}</h3>
                                <p class="text-lg font-bold text-indigo-600">{{ $relatedProduct->formattedPrice }}</p>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
let currentRating = 0;

function changeMainImage(imageUrl) {
    document.getElementById('main-image').src = imageUrl;
}

function showTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remove active state from all tab buttons
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('border-indigo-500', 'text-indigo-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected tab content
    document.getElementById(tabName).classList.remove('hidden');
    
    // Activate selected tab button
    event.target.classList.remove('border-transparent', 'text-gray-500');
    event.target.classList.add('border-indigo-500', 'text-indigo-600');
}

function setRating(rating) {
    currentRating = rating;
    
    // Update star display
    document.querySelectorAll('.rating-star').forEach((star, index) => {
        const starIndex = index + 1;
        const icon = star.querySelector('i');
        
        if (starIndex <= rating) {
            icon.className = 'fas fa-star text-yellow-400';
        } else {
            icon.className = 'far fa-star text-gray-300';
        }
    });
}

function addToCart(productId, quantity) {
    const loadingState = event.target.closest('[x-data]').__x.$data;
    loadingState.loading = true;

    fetch('{{ route('ecommerce.addToCart') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: quantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartCount(data.cartCount);
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

// Review form submission
document.addEventListener('DOMContentLoaded', function() {
    const reviewForm = document.getElementById('review-form');
    if (reviewForm) {
        reviewForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (currentRating === 0) {
                showNotification('Please select a rating', 'error');
                return;
            }
            
            const comment = document.getElementById('comment').value.trim();
            if (!comment) {
                showNotification('Please enter a comment', 'error');
                return;
            }
            
            fetch('{{ route('ecommerce.addReview', $product->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    rating: currentRating,
                    comment: comment
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    reviewForm.reset();
                    currentRating = 0;
                    // Reset stars
                    document.querySelectorAll('.rating-star i').forEach(icon => {
                        icon.className = 'far fa-star text-gray-300';
                    });
                    // Reload page to show new review
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred. Please try again.', 'error');
            });
        });
    }
});

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

.rating-star {
    cursor: pointer;
    transition: color 0.2s;
}
</style>
@endpush
@endsection