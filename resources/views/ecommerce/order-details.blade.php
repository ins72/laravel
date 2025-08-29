@extends('layouts.ecommerce')

@section('title', 'Order #' . $order->order_number)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Breadcrumb -->
    <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('ecommerce.orders') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">
                    <i class="fas fa-box mr-2"></i>
                    My Orders
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="text-sm font-medium text-gray-500">Order #{{ $order->order_number }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Order Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Order #{{ $order->order_number }}</h1>
                <p class="text-gray-600">Placed on {{ $order->created_at->format('M d, Y \a\t g:i A') }}</p>
            </div>
            
            <div class="mt-4 lg:mt-0 flex items-center space-x-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    @switch($order->status)
                        @case('pending')
                            bg-yellow-100 text-yellow-800
                            @break
                        @case('processing')
                            bg-blue-100 text-blue-800
                            @break
                        @case('shipped')
                            bg-purple-100 text-purple-800
                            @break
                        @case('delivered')
                            bg-green-100 text-green-800
                            @break
                        @case('cancelled')
                            bg-red-100 text-red-800
                            @break
                        @default
                            bg-gray-100 text-gray-800
                    @endswitch">
                    @switch($order->status)
                        @case('pending')
                            <i class="fas fa-clock mr-2"></i>
                            @break
                        @case('processing')
                            <i class="fas fa-cog mr-2"></i>
                            @break
                        @case('shipped')
                            <i class="fas fa-shipping-fast mr-2"></i>
                            @break
                        @case('delivered')
                            <i class="fas fa-check mr-2"></i>
                            @break
                        @case('cancelled')
                            <i class="fas fa-times mr-2"></i>
                            @break
                        @default
                            <i class="fas fa-question mr-2"></i>
                    @endswitch
                    {{ ucfirst($order->status) }}
                </span>
                
                @if($order->status === 'delivered')
                    <button onclick="downloadInvoice({{ $order->id }})" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors text-sm">
                        <i class="fas fa-download mr-1"></i>
                        Download Invoice
                    </button>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Order Details -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Order Items -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Order Items</h2>
                </div>
                
                <div class="divide-y divide-gray-200">
                    @foreach($order->items as $item)
                        <div class="p-6">
                            <div class="flex items-center space-x-4">
                                <!-- Product Image -->
                                <div class="flex-shrink-0 w-20 h-20 bg-gray-200 rounded-lg overflow-hidden">
                                    @if($item->product && $item->product->featured_img && isset($item->product->featured_img['url']))
                                        <img src="{{ $item->product->featured_img['url'] }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                                            <i class="fas fa-image text-xl"></i>
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Product Info -->
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm font-medium text-gray-900">
                                        @if($item->product)
                                            <a href="{{ route('ecommerce.show', $item->product->slug) }}" class="hover:text-indigo-600">
                                                {{ $item->product->name }}
                                            </a>
                                        @else
                                            {{ $item->name }}
                                        </a>
                                        @endif
                                    </h3>
                                    <p class="text-sm text-gray-500">SKU: {{ $item->product_id }}</p>
                                    <p class="text-sm text-gray-500">Quantity: {{ $item->quantity }}</p>
                                </div>
                                
                                <!-- Price -->
                                <div class="text-right">
                                    <p class="text-lg font-medium text-gray-900">${{ number_format($item->total, 2) }}</p>
                                    <p class="text-sm text-gray-500">${{ number_format($item->price, 2) }} each</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Order Timeline -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Order Timeline</h2>
                </div>
                
                <div class="p-6">
                    @if($order->timeline && $order->timeline->count() > 0)
                        <div class="flow-root">
                            <ul role="list" class="-mb-8">
                                @foreach($order->timeline->sortBy('timestamp') as $index => $event)
                                    <li>
                                        <div class="relative pb-8">
                                            @if($index < $order->timeline->count() - 1)
                                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                            @endif
                                            
                                            <div class="relative flex space-x-3">
                                                <div>
                                                    <span class="h-8 w-8 rounded-full bg-indigo-500 flex items-center justify-center ring-8 ring-white">
                                                        <i class="fas fa-check text-white text-sm"></i>
                                                    </span>
                                                </div>
                                                
                                                <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                    <div>
                                                        <p class="text-sm text-gray-500">
                                                            {{ $event->description }}
                                                        </p>
                                                    </div>
                                                    <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                        <time datetime="{{ $event->timestamp->toISOString() }}">
                                                            {{ $event->timestamp->format('M d, Y g:i A') }}
                                                        </time>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-history text-4xl text-gray-400 mb-4"></i>
                            <p class="text-gray-500">No timeline events yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sticky top-8">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Order Summary</h2>
                
                <!-- Price Breakdown -->
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="text-gray-900">${{ number_format($order->total_amount, 2) }}</span>
                    </div>
                    
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Shipping</span>
                        <span class="text-gray-900">Free</span>
                    </div>
                    
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Tax</span>
                        <span class="text-gray-900">$0.00</span>
                    </div>
                    
                    <div class="border-t border-gray-200 pt-3">
                        <div class="flex justify-between text-base font-medium">
                            <span class="text-gray-900">Total</span>
                            <span class="text-gray-900">${{ number_format($order->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Information -->
                <div class="border-t border-gray-200 pt-4 mb-6">
                    <h3 class="text-sm font-medium text-gray-900 mb-3">Payment Information</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Payment Method:</span>
                            <span class="text-gray-900">{{ ucfirst($order->payment_method) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Payment Status:</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @switch($order->payment_status)
                                    @case('pending')
                                        bg-yellow-100 text-yellow-800
                                        @break
                                    @case('processing')
                                        bg-blue-100 text-blue-800
                                        @break
                                    @case('completed')
                                        bg-green-100 text-green-800
                                        @break
                                    @case('failed')
                                        bg-red-100 text-red-800
                                        @break
                                    @default
                                        bg-gray-100 text-gray-800
                                @endswitch">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </div>
                        @if($order->payment_intent_id)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Payment ID:</span>
                                <span class="text-gray-900 text-xs font-mono">{{ $order->payment_intent_id }}</span>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Shipping Information -->
                <div class="border-t border-gray-200 pt-4">
                    <h3 class="text-sm font-medium text-gray-900 mb-3">Shipping Address</h3>
                    <div class="text-sm text-gray-600 space-y-1">
                        <p>{{ $order->shipping_address }}</p>
                        <p>{{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_zip }}</p>
                        <p>{{ $order->shipping_country }}</p>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="mt-6 space-y-3">
                    @if($order->status === 'pending')
                        <button onclick="cancelOrder({{ $order->id }})" class="w-full bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors text-sm">
                            <i class="fas fa-times mr-2"></i>
                            Cancel Order
                        </button>
                    @endif
                    
                    @if($order->status === 'delivered')
                        <button onclick="reorder({{ $order->id }})" class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors text-sm">
                            <i class="fas fa-redo mr-2"></i>
                            Reorder
                        </button>
                    @endif
                    
                    <a href="{{ route('ecommerce.orders') }}" class="w-full bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors text-sm text-center block">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Orders
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function downloadInvoice(orderId) {
    // This would typically generate and download an invoice PDF
    showNotification('Invoice download feature coming soon!', 'info');
}

function cancelOrder(orderId) {
    if (confirm('Are you sure you want to cancel this order? This action cannot be undone.')) {
        // This would typically make an API call to cancel the order
        showNotification('Order cancellation feature coming soon!', 'info');
    }
}

function reorder(orderId) {
    if (confirm('Would you like to add all items from this order to your cart?')) {
        // This would typically add all items to the cart
        showNotification('Reorder feature coming soon!', 'info');
    }
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg ${
        type === 'success' ? 'bg-green-500 text-white' : 
        type === 'error' ? 'bg-red-500 text-white' : 
        type === 'info' ? 'bg-blue-500 text-white' : 'bg-gray-500 text-white'
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
.sticky {
    position: sticky;
    top: 2rem;
}
</style>
@endpush
@endsection