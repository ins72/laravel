@extends('layouts.ecommerce')

@section('title', 'My Orders')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">My Orders</h1>
        <p class="mt-2 text-gray-600">Track your order history and status</p>
    </div>

    @if($orders->count() > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Order History</h2>
            </div>
            
            <div class="divide-y divide-gray-200">
                @foreach($orders as $order)
                    <div class="p-6 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-4">
                                    <!-- Order Status Icon -->
                                    <div class="flex-shrink-0">
                                        @switch($order->status)
                                            @case('pending')
                                                <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-clock text-yellow-600"></i>
                                                </div>
                                                @break
                                            @case('processing')
                                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-cog text-blue-600"></i>
                                                </div>
                                                @break
                                            @case('shipped')
                                                <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-shipping-fast text-purple-600"></i>
                                                </div>
                                                @break
                                            @case('delivered')
                                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-check text-green-600"></i>
                                                </div>
                                                @break
                                            @case('cancelled')
                                                <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-times text-red-600"></i>
                                                </div>
                                                @break
                                            @default
                                                <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-question text-gray-600"></i>
                                                </div>
                                        @endswitch
                                    </div>
                                    
                                    <!-- Order Details -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center space-x-2 mb-1">
                                            <h3 class="text-sm font-medium text-gray-900">Order #{{ $order->order_number }}</h3>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
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
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </div>
                                        
                                        <p class="text-sm text-gray-500 mb-2">
                                            Placed on {{ $order->created_at->format('M d, Y \a\t g:i A') }}
                                        </p>
                                        
                                        <!-- Order Items Preview -->
                                        <div class="flex items-center space-x-2">
                                            @foreach($order->items->take(3) as $item)
                                                <div class="w-8 h-8 bg-gray-200 rounded overflow-hidden">
                                                    @if($item->product && $item->product->featured_img && isset($item->product->featured_img['url']))
                                                        <img src="{{ $item->product->featured_img['url'] }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                                    @else
                                                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                            <i class="fas fa-image text-xs"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                            @if($order->items->count() > 3)
                                                <span class="text-xs text-gray-500">+{{ $order->items->count() - 3 }} more</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Order Total and Actions -->
                            <div class="flex items-center space-x-4">
                                <div class="text-right">
                                    <p class="text-lg font-medium text-gray-900">${{ number_format($order->total_amount, 2) }}</p>
                                    <p class="text-sm text-gray-500">{{ $order->items->count() }} item(s)</p>
                                </div>
                                
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('ecommerce.orderDetails', $order->id) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors text-sm">
                                        View Details
                                    </a>
                                    
                                    @if($order->status === 'delivered')
                                        <button onclick="downloadInvoice({{ $order->id }})" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors text-sm">
                                            <i class="fas fa-download mr-1"></i>
                                            Invoice
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        
        <!-- Pagination -->
        <div class="mt-8">
            {{ $orders->links() }}
        </div>
    @else
        <!-- No Orders -->
        <div class="text-center py-16">
            <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                <i class="fas fa-box text-3xl text-gray-400"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">No orders yet</h2>
            <p class="text-gray-600 mb-8">Start shopping to see your order history here.</p>
            <a href="{{ route('ecommerce.index') }}" class="inline-flex items-center bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors text-lg font-medium">
                <i class="fas fa-shopping-bag mr-2"></i>
                Start Shopping
            </a>
        </div>
    @endif
</div>

@push('scripts')
<script>
function downloadInvoice(orderId) {
    // This would typically generate and download an invoice PDF
    // For now, we'll show a notification
    showNotification('Invoice download feature coming soon!', 'info');
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
@endsection