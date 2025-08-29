<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductOrder;
use App\Models\ProductOrderNote;
use App\Models\ProductOrderTimeline;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Darryldecode\Cart\Facades\CartFacade as Cart;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function checkout()
    {
        $cartItems = Cart::getContent();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $total = Cart::getTotal();
        
        return view('orders.checkout', compact('cartItems', 'total'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'billing_name' => 'required|string|max:255',
            'billing_email' => 'required|email',
            'billing_phone' => 'required|string|max:20',
            'billing_address' => 'required|string',
            'billing_city' => 'required|string|max:100',
            'billing_state' => 'required|string|max:100',
            'billing_zip' => 'required|string|max:20',
            'billing_country' => 'required|string|max:100',
            'shipping_name' => 'nullable|string|max:255',
            'shipping_email' => 'nullable|email',
            'shipping_phone' => 'nullable|string|max:20',
            'shipping_address' => 'nullable|string',
            'shipping_city' => 'nullable|string|max:100',
            'shipping_state' => 'nullable|string|max:100',
            'shipping_zip' => 'nullable|string|max:20',
            'shipping_country' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $cartItems = Cart::getContent();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        DB::beginTransaction();
        
        try {
            // Create order
            $order = ProductOrder::create([
                'user_id' => auth()->id(),
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'status' => 'pending',
                'billing_name' => $request->billing_name,
                'billing_email' => $request->billing_email,
                'billing_phone' => $request->billing_phone,
                'billing_address' => $request->billing_address,
                'billing_city' => $request->billing_city,
                'billing_state' => $request->billing_state,
                'billing_zip' => $request->billing_zip,
                'billing_country' => $request->billing_country,
                'shipping_name' => $request->shipping_name ?: $request->billing_name,
                'shipping_email' => $request->shipping_email ?: $request->billing_email,
                'shipping_phone' => $request->shipping_phone ?: $request->billing_phone,
                'shipping_address' => $request->shipping_address ?: $request->billing_address,
                'shipping_city' => $request->shipping_city ?: $request->billing_city,
                'shipping_state' => $request->shipping_state ?: $request->billing_state,
                'shipping_zip' => $request->shipping_zip ?: $request->billing_zip,
                'shipping_country' => $request->shipping_country ?: $request->billing_country,
                'subtotal' => Cart::getSubTotal(),
                'tax' => 0, // Calculate tax based on your requirements
                'shipping' => 0, // Calculate shipping based on your requirements
                'total' => Cart::getTotal(),
                'notes' => $request->notes,
            ]);

            // Add order items
            foreach ($cartItems as $item) {
                $product = Product::find($item->id);
                
                $order->items()->create([
                    'product_id' => $item->id,
                    'product_name' => $item->name,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'total' => $item->price * $item->quantity,
                ]);

                // Update stock if applicable
                if ($product->stock !== null) {
                    $product->decrement('stock', $item->quantity);
                }
            }

            // Add order note
            if ($request->notes) {
                ProductOrderNote::create([
                    'order_id' => $order->id,
                    'note' => $request->notes,
                    'user_id' => auth()->id(),
                ]);
            }

            // Add timeline entry
            ProductOrderTimeline::create([
                'order_id' => $order->id,
                'status' => 'pending',
                'note' => 'Order created',
                'user_id' => auth()->id(),
            ]);

            DB::commit();

            // Clear cart
            Cart::clear();

            return redirect()->route('orders.show', $order->id)
                           ->with('success', 'Order placed successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'An error occurred while placing your order. Please try again.');
        }
    }

    public function index()
    {
        $orders = ProductOrder::where('user_id', auth()->id())
                             ->latest()
                             ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = ProductOrder::where('user_id', auth()->id())
                            ->with(['items.product', 'notes', 'timeline'])
                            ->findOrFail($id);

        return view('orders.show', compact('order'));
    }

    public function cancel($id)
    {
        $order = ProductOrder::where('user_id', auth()->id())
                            ->where('status', 'pending')
                            ->findOrFail($id);

        DB::beginTransaction();
        
        try {
            // Restore stock
            foreach ($order->items as $item) {
                $product = Product::find($item->product_id);
                if ($product && $product->stock !== null) {
                    $product->increment('stock', $item->quantity);
                }
            }

            // Update order status
            $order->update(['status' => 'cancelled']);

            // Add timeline entry
            ProductOrderTimeline::create([
                'order_id' => $order->id,
                'status' => 'cancelled',
                'note' => 'Order cancelled by customer',
                'user_id' => auth()->id(),
            ]);

            DB::commit();

            return redirect()->route('orders.show', $order->id)
                           ->with('success', 'Order cancelled successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'An error occurred while cancelling your order.');
        }
    }
}