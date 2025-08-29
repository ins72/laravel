@extends('layouts.ecommerce')

@section('title', 'Shopping Cart')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Shopping Cart</h1>
        <p class="mt-2 text-gray-600">Review your items and proceed to checkout</p>
    </div>

    @if($cartItems->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Cart Items -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900">Cart Items ({{ $cartItems->count() }})</h2>
                    </div>
                    
                    <div class="divide-y divide-gray-200">
                        @foreach($cartItems as $item)
                            <div class="p-6" x-data="{ loading: false, quantity: {{ $item->quantity }} }">
                                <div class="flex items-center space-x-4">
                                    <!-- Product Image -->
                                    <div class="flex-shrink-0 w-20 h-20 bg-gray-200 rounded-lg overflow-hidden">
                                        @if($item->attributes['image'])
                                            <img src="{{ $item->attributes['image'] }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                <i class="fas fa-image text-xl"></i>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Product Info -->
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-sm font-medium text-gray-900">
                                            <a href="{{ route('ecommerce.show', $item->attributes['slug']) }}" class="hover:text-indigo-600">
                                                {{ $item->name }}
                                            </a>
                                        </h3>
                                        <p class="text-sm text-gray-500">SKU: {{ $item->id }}</p>
                                        @if($item->attributes['stock'] !== null)
                                            <p class="text-sm {{ $item->attributes['stock'] > 0 ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $item->attributes['stock'] > 0 ? 'In Stock' : 'Out of Stock' }}
                                            </p>
                                        @endif
                                    </div>
                                    
                                    <!-- Quantity Controls -->
                                    <div class="flex items-center space-x-2">
                                        <label for="quantity-{{ $item->id }}" class="text-sm font-medium text-gray-700">Qty:</label>
                                        <div class="flex items-center border border-gray-300 rounded-md">
                                            <button @click="quantity = Math.max(1, quantity - 1)" class="px-2 py-1 text-gray-600 hover:text-gray-800">
                                                <i class="fas fa-minus text-xs"></i>
                                            </button>
                                            <input type="number" id="quantity-{{ $item->id }}" x-model="quantity" min="1" max="{{ $item->attributes['stock'] ?? 999 }}" class="w-12 text-center border-0 focus:ring-0 text-sm">
                                            <button @click="quantity = Math.min({{ $item->attributes['stock'] ?? 999 }}, quantity + 1)" class="px-2 py-1 text-gray-600 hover:text-gray-800">
                                                <i class="fas fa-plus text-xs"></i>
                                            </button>
                                        </div>
                                        <button @click="updateCartItem({{ $item->id }}, quantity)" :disabled="loading" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                            <span x-show="!loading">Update</span>
                                            <span x-show="loading" class="flex items-center">
                                                <i class="fas fa-spinner fa-spin mr-1"></i>
                                                Updating...
                                            </span>
                                        </button>
                                    </div>
                                    
                                    <!-- Price -->
                                    <div class="text-right">
                                        <p class="text-lg font-medium text-gray-900">${{ number_format($item->price * $item->quantity, 2) }}</p>
                                        <p class="text-sm text-gray-500">${{ number_format($item->price, 2) }} each</p>
                                    </div>
                                    
                                    <!-- Remove Button -->
                                    <button @click="removeCartItem({{ $item->id }})" :disabled="loading" class="text-red-600 hover:text-red-800 p-2">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <!-- Continue Shopping -->
                <div class="mt-6">
                    <a href="{{ route('ecommerce.index') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Continue Shopping
                    </a>
                </div>
            </div>
            
            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sticky top-8">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Order Summary</h2>
                    
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal ({{ $cartItems->count() }} items)</span>
                            <span class="text-gray-900">${{ number_format($cartTotal, 2) }}</span>
                        </div>
                        
                        <!-- Coupon Code -->
                        <div class="border-t border-gray-200 pt-3">
                            <div x-data="{ couponCode: '', loading: false, appliedCoupon: null }">
                                @if(session('applied_coupon'))
                                    <div class="mb-3 p-3 bg-green-50 border border-green-200 rounded-md">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm font-medium text-green-800">Coupon Applied</p>
                                                <p class="text-xs text-green-600">{{ session('applied_coupon')->code }}</p>
                                            </div>
                                            <button @click="removeCoupon()" class="text-green-600 hover:text-green-800">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <div class="mb-3">
                                        <label for="coupon" class="block text-sm font-medium text-gray-700 mb-1">Coupon Code</label>
                                        <div class="flex space-x-2">
                                            <input type="text" id="coupon" x-model="couponCode" placeholder="Enter code" class="flex-1 border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                            <button @click="applyCoupon()" :disabled="loading || !couponCode" class="bg-indigo-600 text-white px-3 py-2 rounded-md hover:bg-indigo-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-sm">
                                                <span x-show="!loading">Apply</span>
                                                <span x-show="loading" class="flex items-center">
                                                    <i class="fas fa-spinner fa-spin"></i>
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="border-t border-gray-200 pt-3">
                            <div class="flex justify-between text-base font-medium">
                                <span class="text-gray-900">Total</span>
                                <span class="text-gray-900">${{ number_format($cartTotal, 2) }}</span>
                            </div>
                            <p class="text-sm text-gray-500">Shipping and taxes calculated at checkout</p>
                        </div>
                    </div>
                    
                    <!-- Checkout Button -->
                    <a href="{{ route('ecommerce.checkout') }}" class="w-full bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors text-lg font-medium text-center block">
                        Proceed to Checkout
                    </a>
                    
                    <!-- Payment Methods -->
                    <div class="mt-4 text-center">
                        <p class="text-xs text-gray-500 mb-2">We accept</p>
                        <div class="flex justify-center space-x-2">
                            <i class="fab fa-cc-visa text-2xl text-blue-600"></i>
                            <i class="fab fa-cc-mastercard text-2xl text-red-600"></i>
                            <i class="fab fa-cc-amex text-2xl text-blue-800"></i>
                            <i class="fab fa-cc-paypal text-2xl text-blue-700"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Empty Cart -->
        <div class="text-center py-16">
            <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                <i class="fas fa-shopping-cart text-3xl text-gray-400"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Your cart is empty</h2>
            <p class="text-gray-600 mb-8">Looks like you haven't added any products to your cart yet.</p>
            <a href="{{ route('ecommerce.index') }}" class="inline-flex items-center bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors text-lg font-medium">
                <i class="fas fa-shopping-bag mr-2"></i>
                Start Shopping
            </a>
        </div>
    @endif
</div>

@push('scripts')
<script>
function updateCartItem(productId, quantity) {
    const loadingState = event.target.closest('[x-data]').__x.$data;
    loadingState.loading = true;

    fetch('{{ route('ecommerce.updateCart') }}', {
        method: 'PUT',
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
            // Update cart count
            updateCartCount(data.cartCount);
            
            // Show success message
            showNotification(data.message, 'success');
            
            // Reload page to update totals
            setTimeout(() => location.reload(), 1000);
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

function removeCartItem(productId) {
    if (!confirm('Are you sure you want to remove this item from your cart?')) {
        return;
    }

    fetch('{{ route('ecommerce.removeFromCart', '') }}/' + productId, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update cart count
            updateCartCount(data.cartCount);
            
            // Show success message
            showNotification(data.message, 'success');
            
            // Reload page to update cart
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred. Please try again.', 'error');
    });
}

function applyCoupon() {
    const loadingState = event.target.closest('[x-data]').__x.$data;
    const couponCode = loadingState.couponCode;
    
    if (!couponCode) return;
    
    loadingState.loading = true;

    fetch('{{ route('ecommerce.applyCoupon') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            coupon_code: couponCode
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            loadingState.couponCode = '';
            // Reload page to show applied coupon
            setTimeout(() => location.reload(), 1000);
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

function removeCoupon() {
    fetch('{{ route('ecommerce.removeCoupon') }}', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            // Reload page to remove coupon
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred. Please try again.', 'error');
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
@endsection