@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Checkout</h1>
        <p class="text-lg text-gray-600 mt-2">Complete your order</p>
    </div>

    <form action="{{ route('orders.store') }}" method="POST" class="space-y-8">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Billing Information -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Billing Information</h2>
                
                <div class="space-y-4">
                    <div>
                        <label for="billing_name" class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                        <input type="text" name="billing_name" id="billing_name" required
                               value="{{ old('billing_name', auth()->user()->name ?? '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        @error('billing_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="billing_email" class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                        <input type="email" name="billing_email" id="billing_email" required
                               value="{{ old('billing_email', auth()->user()->email ?? '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        @error('billing_email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="billing_phone" class="block text-sm font-medium text-gray-700 mb-2">Phone *</label>
                        <input type="tel" name="billing_phone" id="billing_phone" required
                               value="{{ old('billing_phone') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        @error('billing_phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="billing_address" class="block text-sm font-medium text-gray-700 mb-2">Address *</label>
                        <textarea name="billing_address" id="billing_address" rows="3" required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">{{ old('billing_address') }}</textarea>
                        @error('billing_address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="billing_city" class="block text-sm font-medium text-gray-700 mb-2">City *</label>
                            <input type="text" name="billing_city" id="billing_city" required
                                   value="{{ old('billing_city') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            @error('billing_city')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="billing_state" class="block text-sm font-medium text-gray-700 mb-2">State *</label>
                            <input type="text" name="billing_state" id="billing_state" required
                                   value="{{ old('billing_state') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            @error('billing_state')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="billing_zip" class="block text-sm font-medium text-gray-700 mb-2">ZIP Code *</label>
                            <input type="text" name="billing_zip" id="billing_zip" required
                                   value="{{ old('billing_zip') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            @error('billing_zip')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="billing_country" class="block text-sm font-medium text-gray-700 mb-2">Country *</label>
                            <select name="billing_country" id="billing_country" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                <option value="">Select Country</option>
                                <option value="US" {{ old('billing_country') == 'US' ? 'selected' : '' }}>United States</option>
                                <option value="CA" {{ old('billing_country') == 'CA' ? 'selected' : '' }}>Canada</option>
                                <option value="UK" {{ old('billing_country') == 'UK' ? 'selected' : '' }}>United Kingdom</option>
                                <option value="AU" {{ old('billing_country') == 'AU' ? 'selected' : '' }}>Australia</option>
                                <option value="DE" {{ old('billing_country') == 'DE' ? 'selected' : '' }}>Germany</option>
                                <option value="FR" {{ old('billing_country') == 'FR' ? 'selected' : '' }}>France</option>
                                <option value="Other" {{ old('billing_country') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('billing_country')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Shipping Information -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-gray-900">Shipping Information</h2>
                    <label class="flex items-center">
                        <input type="checkbox" id="same_as_billing" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">Same as billing</span>
                    </label>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <label for="shipping_name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                        <input type="text" name="shipping_name" id="shipping_name"
                               value="{{ old('shipping_name') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        @error('shipping_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="shipping_email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="shipping_email" id="shipping_email"
                               value="{{ old('shipping_email') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        @error('shipping_email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="shipping_phone" class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                        <input type="tel" name="shipping_phone" id="shipping_phone"
                               value="{{ old('shipping_phone') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        @error('shipping_phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="shipping_address" class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                        <textarea name="shipping_address" id="shipping_address" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">{{ old('shipping_address') }}</textarea>
                        @error('shipping_address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="shipping_city" class="block text-sm font-medium text-gray-700 mb-2">City</label>
                            <input type="text" name="shipping_city" id="shipping_city"
                                   value="{{ old('shipping_city') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            @error('shipping_city')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="shipping_state" class="block text-sm font-medium text-gray-700 mb-2">State</label>
                            <input type="text" name="shipping_state" id="shipping_state"
                                   value="{{ old('shipping_state') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            @error('shipping_state')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="shipping_zip" class="block text-sm font-medium text-gray-700 mb-2">ZIP Code</label>
                            <input type="text" name="shipping_zip" id="shipping_zip"
                                   value="{{ old('shipping_zip') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            @error('shipping_zip')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="shipping_country" class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                            <select name="shipping_country" id="shipping_country"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                <option value="">Select Country</option>
                                <option value="US" {{ old('shipping_country') == 'US' ? 'selected' : '' }}>United States</option>
                                <option value="CA" {{ old('shipping_country') == 'CA' ? 'selected' : '' }}>Canada</option>
                                <option value="UK" {{ old('shipping_country') == 'UK' ? 'selected' : '' }}>United Kingdom</option>
                                <option value="AU" {{ old('shipping_country') == 'AU' ? 'selected' : '' }}>Australia</option>
                                <option value="DE" {{ old('shipping_country') == 'DE' ? 'selected' : '' }}>Germany</option>
                                <option value="FR" {{ old('shipping_country') == 'FR' ? 'selected' : '' }}>France</option>
                                <option value="Other" {{ old('shipping_country') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('shipping_country')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Order Notes -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Order Notes</h2>
            <textarea name="notes" rows="3" placeholder="Any special instructions or notes for your order..."
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">{{ old('notes') }}</textarea>
        </div>
        
        <!-- Order Summary -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Order Summary</h2>
            
            <div class="space-y-3">
                @foreach($cartItems as $item)
                <div class="flex justify-between items-center py-2">
                    <div class="flex items-center space-x-3">
                        <span class="text-gray-600">{{ $item->quantity }}x</span>
                        <span class="font-medium">{{ $item->name }}</span>
                    </div>
                    <span class="font-medium">${{ number_format($item->price * $item->quantity, 2) }}</span>
                </div>
                @endforeach
                
                <div class="border-t border-gray-200 pt-3">
                    <div class="flex justify-between text-lg font-semibold">
                        <span>Total</span>
                        <span>${{ number_format($total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Submit Button -->
        <div class="flex justify-between items-center">
            <a href="{{ route('cart.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                ← Back to Cart
            </a>
            
            <button type="submit" class="bg-indigo-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition duration-300">
                Place Order
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sameAsBillingCheckbox = document.getElementById('same_as_billing');
    const shippingFields = ['shipping_name', 'shipping_email', 'shipping_phone', 'shipping_address', 'shipping_city', 'shipping_state', 'shipping_zip', 'shipping_country'];
    
    function copyBillingToShipping() {
        shippingFields.forEach(field => {
            const billingField = document.getElementById(field.replace('shipping_', 'billing_'));
            const shippingField = document.getElementById(field);
            if (billingField && shippingField) {
                shippingField.value = billingField.value;
            }
        });
    }
    
    function clearShippingFields() {
        shippingFields.forEach(field => {
            const shippingField = document.getElementById(field);
            if (shippingField) {
                shippingField.value = '';
            }
        });
    }
    
    sameAsBillingCheckbox.addEventListener('change', function() {
        if (this.checked) {
            copyBillingToShipping();
        } else {
            clearShippingFields();
        }
    });
});
</script>
@endsection