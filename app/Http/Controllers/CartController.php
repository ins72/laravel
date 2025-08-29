<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Darryldecode\Cart\Facades\CartFacade as Cart;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = Cart::getContent();
        $total = Cart::getTotal();
        
        return view('cart.index', compact('cartItems', 'total'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);
        
        // Check if product is in stock
        if ($product->stock !== null && $product->stock < $request->quantity) {
            return back()->with('error', 'Not enough stock available.');
        }

        // Check if product is already in cart
        $existingItem = Cart::get($product->id);
        if ($existingItem) {
            $newQuantity = $existingItem->quantity + $request->quantity;
            if ($product->stock !== null && $product->stock < $newQuantity) {
                return back()->with('error', 'Not enough stock available.');
            }
        }

        Cart::add([
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->isOnSale ? $product->salePrice : $product->price,
            'quantity' => $request->quantity,
            'attributes' => [
                'image' => $product->featured_img['url'] ?? null,
                'slug' => $product->slug,
                'original_price' => $product->price,
                'is_on_sale' => $product->isOnSale,
            ]
        ]);

        return back()->with('success', 'Product added to cart successfully!');
    }

    public function update(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);
        
        // Check stock
        if ($product->stock !== null && $product->stock < $request->quantity) {
            return back()->with('error', 'Not enough stock available.');
        }

        Cart::update($request->product_id, [
            'quantity' => [
                'relative' => false,
                'value' => $request->quantity
            ]
        ]);

        return back()->with('success', 'Cart updated successfully!');
    }

    public function remove($productId)
    {
        Cart::remove($productId);
        return back()->with('success', 'Product removed from cart!');
    }

    public function clear()
    {
        Cart::clear();
        return back()->with('success', 'Cart cleared successfully!');
    }

    public function getCartCount()
    {
        $count = Cart::getTotalQuantity();
        return response()->json(['count' => $count]);
    }
}