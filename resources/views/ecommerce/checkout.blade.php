@extends('layouts.ecommerce')

@section('title', 'Checkout')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Checkout</h1>
        <p class="mt-2 text-gray-600">Complete your purchase</p>
    </div>

    <form id="checkout-form" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Checkout Form -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Shipping Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Shipping Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="shipping_address" class="block text-sm font-medium text-gray-700 mb-1">Address *</label>
                        <input type="text" id="shipping_address" name="shipping_address" required value="{{ $user->address ?? '' }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    
                    <div>
                        <label for="shipping_city" class="block text-sm font-medium text-gray-700 mb-1">City *</label>
                        <input type="text" id="shipping_city" name="shipping_city" required value="{{ $user->city ?? '' }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    
                    <div>
                        <label for="shipping_state" class="block text-sm font-medium text-gray-700 mb-1">State/Province *</label>
                        <input type="text" id="shipping_state" name="shipping_state" required value="{{ $user->state ?? '' }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    
                    <div>
                        <label for="shipping_zip" class="block text-sm font-medium text-gray-700 mb-1">ZIP/Postal Code *</label>
                        <input type="text" id="shipping_zip" name="shipping_zip" required value="{{ $user->zip ?? '' }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label for="shipping_country" class="block text-sm font-medium text-gray-700 mb-1">Country *</label>
                        <select id="shipping_country" name="shipping_country" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Country</option>
                            <option value="US" {{ ($user->country ?? '') == 'US' ? 'selected' : '' }}>United States</option>
                            <option value="CA" {{ ($user->country ?? '') == 'CA' ? 'selected' : '' }}>Canada</option>
                            <option value="GB" {{ ($user->country ?? '') == 'GB' ? 'selected' : '' }}>United Kingdom</option>
                            <option value="AU" {{ ($user->country ?? '') == 'AU' ? 'selected' : '' }}>Australia</option>
                            <option value="DE" {{ ($user->country ?? '') == 'DE' ? 'selected' : '' }}>Germany</option>
                            <option value="FR" {{ ($user->country ?? '') == 'FR' ? 'selected' : '' }}>France</option>
                            <option value="JP" {{ ($user->country ?? '') == 'JP' ? 'selected' : '' }}>Japan</option>
                            <option value="IN" {{ ($user->country ?? '') == 'IN' ? 'selected' : '' }}>India</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Payment Method -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Payment Method</h2>
                
                <div class="space-y-4">
                    <!-- Stripe -->
                    <div class="flex items-center">
                        <input type="radio" id="payment_stripe" name="payment_method" value="stripe" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300" checked>
                        <label for="payment_stripe" class="ml-3 flex items-center">
                            <i class="fab fa-cc-stripe text-2xl text-blue-600 mr-3"></i>
                            <div>
                                <span class="text-sm font-medium text-gray-900">Credit/Debit Card (Stripe)</span>
                                <p class="text-xs text-gray-500">Secure payment processing</p>
                            </div>
                        </label>
                    </div>
                    
                    <!-- Razorpay -->
                    <div class="flex items-center">
                        <input type="radio" id="payment_razorpay" name="payment_method" value="razorpay" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                        <label for="payment_razorpay" class="ml-3 flex items-center">
                            <i class="fas fa-credit-card text-2xl text-blue-800 mr-3"></i>
                            <div>
                                <span class="text-sm font-medium text-gray-900">Credit/Debit Card (Razorpay)</span>
                                <p class="text-xs text-gray-500">Alternative payment gateway</p>
                            </div>
                        </label>
                    </div>
                    
                    <!-- Wallet -->
                    @if($user->wallet && $user->wallet->balance >= $cartTotal)
                        <div class="flex items-center">
                            <input type="radio" id="payment_wallet" name="payment_method" value="wallet" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                            <label for="payment_wallet" class="ml-3 flex items-center">
                                <i class="fas fa-wallet text-2xl text-green-600 mr-3"></i>
                                <div>
                                    <span class="text-sm font-medium text-gray-900">Wallet Balance</span>
                                    <p class="text-xs text-gray-500">Available: ${{ number_format($user->wallet->balance, 2) }}</p>
                                </div>
                            </label>
                        </div>
                    @endif
                </div>
                
                <!-- Stripe Payment Form -->
                <div id="stripe-form" class="mt-6 p-4 bg-gray-50 rounded-lg">
                    <div class="space-y-4">
                        <div>
                            <label for="card_number" class="block text-sm font-medium text-gray-700 mb-1">Card Number</label>
                            <input type="text" id="card_number" placeholder="1234 5678 9012 3456" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="expiry" class="block text-sm font-medium text-gray-700 mb-1">Expiry Date</label>
                                <input type="text" id="expiry" placeholder="MM/YY" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            
                            <div>
                                <label for="cvv" class="block text-sm font-medium text-gray-700 mb-1">CVV</label>
                                <input type="text" id="cvv" placeholder="123" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Notes -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Order Notes (Optional)</h2>
                <textarea name="order_notes" rows="3" placeholder="Add any special instructions or notes for your order..." class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sticky top-8">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Order Summary</h2>
                
                <!-- Cart Items -->
                <div class="space-y-3 mb-6">
                    @foreach($cartItems as $item)
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0 w-12 h-12 bg-gray-200 rounded-lg overflow-hidden">
                                @if($item->attributes['image'])
                                    <img src="{{ $item->attributes['image'] }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                        <i class="fas fa-image text-sm"></i>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="flex-1 min-w-0">
                                <h3 class="text-sm font-medium text-gray-900 truncate">{{ $item->name }}</h3>
                                <p class="text-xs text-gray-500">Qty: {{ $item->quantity }}</p>
                            </div>
                            
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">${{ number_format($item->price * $item->quantity, 2) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Price Breakdown -->
                <div class="border-t border-gray-200 pt-4 space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="text-gray-900">${{ number_format($cartTotal, 2) }}</span>
                    </div>
                    
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Shipping</span>
                        <span class="text-gray-900">Free</span>
                    </div>
                    
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Tax</span>
                        <span class="text-gray-900">Calculated at checkout</span>
                    </div>
                    
                    @if(session('applied_coupon'))
                        <div class="flex justify-between text-sm text-green-600">
                            <span>Discount ({{ session('applied_coupon')->code }})</span>
                            <span>-${{ number_format(session('applied_coupon')->discount_amount, 2) }}</span>
                        </div>
                    @endif
                    
                    <div class="border-t border-gray-200 pt-3">
                        <div class="flex justify-between text-base font-medium">
                            <span class="text-gray-900">Total</span>
                            <span class="text-gray-900">${{ number_format($cartTotal, 2) }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Place Order Button -->
                <button type="submit" id="place-order-btn" class="w-full bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors text-lg font-medium mt-6">
                    <span id="btn-text">Place Order</span>
                    <span id="btn-loading" class="hidden">
                        <i class="fas fa-spinner fa-spin mr-2"></i>
                        Processing...
                    </span>
                </button>
                
                <!-- Security Notice -->
                <div class="mt-4 text-center">
                    <div class="flex items-center justify-center text-green-600 mb-2">
                        <i class="fas fa-lock mr-2"></i>
                        <span class="text-sm font-medium">Secure Checkout</span>
                    </div>
                    <p class="text-xs text-gray-500">Your payment information is encrypted and secure</p>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkoutForm = document.getElementById('checkout-form');
    const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
    const stripeForm = document.getElementById('stripe-form');
    const placeOrderBtn = document.getElementById('place-order-btn');
    const btnText = document.getElementById('btn-text');
    const btnLoading = document.getElementById('btn-loading');

    // Show/hide Stripe form based on payment method
    function togglePaymentForms() {
        const selectedMethod = document.querySelector('input[name="payment_method"]:checked').value;
        
        if (selectedMethod === 'stripe') {
            stripeForm.classList.remove('hidden');
        } else {
            stripeForm.classList.add('hidden');
        }
    }

    // Initialize payment form display
    togglePaymentForms();

    // Listen for payment method changes
    paymentMethods.forEach(method => {
        method.addEventListener('change', togglePaymentForms);
    });

    // Form submission
    checkoutForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validate form
        if (!validateForm()) {
            return;
        }
        
        // Show loading state
        placeOrderBtn.disabled = true;
        btnText.classList.add('hidden');
        btnLoading.classList.remove('hidden');
        
        // Collect form data
        const formData = new FormData(checkoutForm);
        const data = Object.fromEntries(formData);
        
        // Process checkout
        fetch('{{ route('ecommerce.processCheckout') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                
                // Redirect to order details
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 1500);
            } else {
                showNotification(data.message, 'error');
                
                // Reset button state
                placeOrderBtn.disabled = false;
                btnText.classList.remove('hidden');
                btnLoading.classList.add('hidden');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred. Please try again.', 'error');
            
            // Reset button state
            placeOrderBtn.disabled = false;
            btnText.classList.remove('hidden');
            btnLoading.classList.add('hidden');
        });
    });
});

function validateForm() {
    const requiredFields = [
        'shipping_address',
        'shipping_city',
        'shipping_state',
        'shipping_zip',
        'shipping_country'
    ];
    
    let isValid = true;
    
    requiredFields.forEach(fieldName => {
        const field = document.getElementById(fieldName);
        if (!field.value.trim()) {
            field.classList.add('border-red-500');
            isValid = false;
        } else {
            field.classList.remove('border-red-500');
        }
    });
    
    if (!isValid) {
        showNotification('Please fill in all required fields.', 'error');
    }
    
    return isValid;
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
    }, 5000);
}
</script>
@endpush

@push('styles')
<style>
.sticky {
    position: sticky;
    top: 2rem;
}
</style>
@endpush
@endsection