<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductOrder;
use App\Models\ProductOrderNote;
use App\Models\ProductOrderTimeline;
use App\Models\ProductReview;
use App\Models\ProductCouponCode;
use App\Models\ProductCouponHistory;
use App\Models\ProductShipping;
use App\Models\ProductShippingLocation;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class EcommerceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['cart', 'checkout', 'orders', 'orderDetails']);
    }

    // Product Catalog
    public function index(Request $request)
    {
        $query = Product::with(['user', 'site'])
            ->active()
            ->inStock();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        // Category filtering
        if ($request->has('category') && $request->category) {
            $query->where('productType', $request->category);
        }

        // Price filtering
        if ($request->has('min_price') && $request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price') && $request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sorting
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'featured':
                $query->featured();
                break;
            default:
                $query->latest();
        }

        $products = $query->paginate(12);
        $featuredProducts = Product::featured()->active()->inStock()->limit(6)->get();

        return view('ecommerce.index', compact('products', 'featuredProducts'));
    }

    // Product Details
    public function show($slug)
    {
        $product = Product::with(['user', 'site', 'reviews.user'])
            ->where('slug', $slug)
            ->active()
            ->firstOrFail();

        $relatedProducts = Product::where('productType', $product->productType)
            ->where('id', '!=', $product->id)
            ->active()
            ->inStock()
            ->limit(4)
            ->get();

        return view('ecommerce.show', compact('product', 'relatedProducts'));
    }

    // Shopping Cart
    public function cart()
    {
        $cartItems = Cart::getContent();
        $cartTotal = Cart::getTotal();
        
        return view('ecommerce.cart', compact('cartItems', 'cartTotal'));
    }

    // Add to Cart
    public function addToCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        $product = Product::findOrFail($request->product_id);
        
        if (!$product->isActive() || !$product->isInStock()) {
            return response()->json(['success' => false, 'message' => 'Product is not available']);
        }

        // Check stock
        if ($product->stock !== null && $product->stock < $request->quantity) {
            return response()->json(['success' => false, 'message' => 'Insufficient stock']);
        }

        $cartItem = [
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->salePrice,
            'quantity' => $request->quantity,
            'attributes' => [
                'image' => $product->featured_img['url'] ?? null,
                'slug' => $product->slug,
                'stock' => $product->stock,
            ]
        ];

        Cart::add($cartItem);

        return response()->json([
            'success' => true, 
            'message' => 'Product added to cart',
            'cartCount' => Cart::getTotalQuantity()
        ]);
    }

    // Update Cart
    public function updateCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        $product = Product::find($request->product_id);
        if ($product && $product->stock !== null && $product->stock < $request->quantity) {
            return response()->json(['success' => false, 'message' => 'Insufficient stock']);
        }

        Cart::update($request->product_id, [
            'quantity' => [
                'relative' => false,
                'value' => $request->quantity
            ]
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cart updated',
            'cartTotal' => Cart::getTotal(),
            'cartCount' => Cart::getTotalQuantity()
        ]);
    }

    // Remove from Cart
    public function removeFromCart($productId)
    {
        Cart::remove($productId);
        
        return response()->json([
            'success' => true,
            'message' => 'Product removed from cart',
            'cartTotal' => Cart::getTotal(),
            'cartCount' => Cart::getTotalQuantity()
        ]);
    }

    // Checkout
    public function checkout()
    {
        $cartItems = Cart::getContent();
        $cartTotal = Cart::getTotal();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('ecommerce.cart')->with('error', 'Your cart is empty');
        }

        $user = Auth::user();
        $shippingLocations = ProductShippingLocation::all();
        
        return view('ecommerce.checkout', compact('cartItems', 'cartTotal', 'user', 'shippingLocations'));
    }

    // Process Checkout
    public function processCheckout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shipping_address' => 'required|string',
            'shipping_city' => 'required|string',
            'shipping_state' => 'required|string',
            'shipping_zip' => 'required|string',
            'shipping_country' => 'required|string',
            'payment_method' => 'required|in:stripe,razorpay,wallet',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        $cartItems = Cart::getContent();
        $cartTotal = Cart::getTotal();
        
        if ($cartItems->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Your cart is empty']);
        }

        DB::beginTransaction();
        try {
            // Create order
            $order = ProductOrder::create([
                'user_id' => Auth::id(),
                'order_number' => 'ORD-' . time() . '-' . Auth::id(),
                'status' => 'pending',
                'total_amount' => $cartTotal,
                'shipping_address' => $request->shipping_address,
                'shipping_city' => $request->shipping_city,
                'shipping_state' => $request->shipping_state,
                'shipping_zip' => $request->shipping_zip,
                'shipping_country' => $request->shipping_country,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
            ]);

            // Add order items
            foreach ($cartItems as $item) {
                $product = Product::find($item->id);
                $order->items()->create([
                    'product_id' => $item->id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'total' => $item->price * $item->quantity,
                ]);

                // Update stock
                if ($product->stock !== null) {
                    $product->decrement('stock', $item->quantity);
                }
            }

            // Process payment based on method
            $paymentResult = $this->processPayment($order, $request->payment_method);
            
            if (!$paymentResult['success']) {
                throw new \Exception($paymentResult['message']);
            }

            // Add order timeline
            $order->timeline()->create([
                'status' => 'order_created',
                'description' => 'Order created successfully',
                'timestamp' => now(),
            ]);

            DB::commit();

            // Clear cart
            Cart::clear();

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'order_id' => $order->id,
                'redirect' => route('ecommerce.orderDetails', $order->id)
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Checkout error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during checkout. Please try again.'
            ]);
        }
    }

    // Process Payment
    private function processPayment($order, $paymentMethod)
    {
        switch ($paymentMethod) {
            case 'stripe':
                return $this->processStripePayment($order);
            case 'razorpay':
                return $this->processRazorpayPayment($order);
            case 'wallet':
                return $this->processWalletPayment($order);
            default:
                return ['success' => false, 'message' => 'Invalid payment method'];
        }
    }

    // Stripe Payment
    private function processStripePayment($order)
    {
        try {
            Stripe::setApiKey(config('services.stripe.secret'));
            
            $paymentIntent = PaymentIntent::create([
                'amount' => $order->total_amount * 100, // Convert to cents
                'currency' => 'usd',
                'metadata' => [
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                ],
            ]);

            $order->update([
                'payment_intent_id' => $paymentIntent->id,
                'payment_status' => 'processing'
            ]);

            return ['success' => true, 'payment_intent' => $paymentIntent];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // Razorpay Payment
    private function processRazorpayPayment($order)
    {
        try {
            $api = new \Razorpay\Api\Api(
                config('services.razorpay.key'),
                config('services.razorpay.secret')
            );

            $payment = $api->payment->fetch($order->id);
            
            if ($payment->status === 'captured') {
                $order->update(['payment_status' => 'completed']);
                return ['success' => true];
            }

            return ['success' => false, 'message' => 'Payment not completed'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // Wallet Payment
    private function processWalletPayment($order)
    {
        try {
            $user = Auth::user();
            $wallet = $user->wallet;

            if (!$wallet || $wallet->balance < $order->total_amount) {
                return ['success' => false, 'message' => 'Insufficient wallet balance'];
            }

            // Deduct from wallet
            $wallet->decrement('balance', $order->total_amount);
            
            // Create wallet transaction
            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'debit',
                'amount' => $order->total_amount,
                'description' => 'Payment for order #' . $order->order_number,
                'reference' => $order->id,
            ]);

            $order->update(['payment_status' => 'completed']);
            
            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // User Orders
    public function orders()
    {
        $orders = ProductOrder::where('user_id', Auth::id())
            ->with(['items.product'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('ecommerce.orders', compact('orders'));
    }

    // Order Details
    public function orderDetails($orderId)
    {
        $order = ProductOrder::with(['items.product', 'timeline'])
            ->where('user_id', Auth::id())
            ->findOrFail($orderId);

        return view('ecommerce.order-details', compact('order'));
    }

    // Product Reviews
    public function addReview(Request $request, $productId)
    {
        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|between:1,5',
            'comment' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        $product = Product::findOrFail($productId);
        
        // Check if user has purchased the product
        $hasPurchased = ProductOrder::where('user_id', Auth::id())
            ->whereHas('items', function($query) use ($productId) {
                $query->where('product_id', $productId);
            })
            ->where('payment_status', 'completed')
            ->exists();

        if (!$hasPurchased) {
            return response()->json(['success' => false, 'message' => 'You can only review products you have purchased']);
        }

        // Check if user already reviewed
        $existingReview = ProductReview::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->first();

        if ($existingReview) {
            $existingReview->update([
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]);
        } else {
            ProductReview::create([
                'user_id' => Auth::id(),
                'product_id' => $productId,
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Review submitted successfully']);
    }

    // Apply Coupon
    public function applyCoupon(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'coupon_code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        $coupon = ProductCouponCode::where('code', $request->coupon_code)
            ->where('status', 1)
            ->where('expires_at', '>', now())
            ->first();

        if (!$coupon) {
            return response()->json(['success' => false, 'message' => 'Invalid or expired coupon code']);
        }

        // Check usage limits
        $usageCount = ProductCouponHistory::where('coupon_id', $coupon->id)->count();
        if ($coupon->max_uses && $usageCount >= $coupon->max_uses) {
            return response()->json(['success' => false, 'message' => 'Coupon usage limit exceeded']);
        }

        // Check user usage
        $userUsageCount = ProductCouponHistory::where('coupon_id', $coupon->id)
            ->where('user_id', Auth::id())
            ->count();
        if ($coupon->max_uses_per_user && $userUsageCount >= $coupon->max_uses_per_user) {
            return response()->json(['success' => false, 'message' => 'You have already used this coupon']);
        }

        // Store coupon in session
        session(['applied_coupon' => $coupon]);

        return response()->json([
            'success' => true,
            'message' => 'Coupon applied successfully',
            'discount' => $coupon->discount_amount,
            'discount_type' => $coupon->discount_type
        ]);
    }

    // Remove Coupon
    public function removeCoupon()
    {
        session()->forget('applied_coupon');
        
        return response()->json([
            'success' => true,
            'message' => 'Coupon removed successfully'
        ]);
    }
}