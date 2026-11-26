<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Display customer cart
     */
    public function index()
    {
        $items = CartItem::with('product')
            ->where('user_id', auth()->id())
            ->where('status', 'in_cart')
            ->get();

        $total = $items->sum(fn($i) => $i->product->price * $i->quantity);

        return view('cart.index', compact('items', 'total'));
    }

    /**
     * Add item to customer cart (supports AJAX JSON response)
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'nullable|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->stock_quantity < 1) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => "{$product->name} is out of stock."], 422);
            }
            return back()->with('error', "{$product->name} is currently out of stock.");
        }

        $qty = $request->input('quantity', 1);

        $cartItem = CartItem::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->where('status', 'in_cart')
            ->first();

        if ($cartItem) {
            $cartItem->increment('quantity', $qty);
        } else {
            CartItem::create([
                'user_id'    => auth()->id(),
                'product_id' => $product->id,
                'quantity'   => $qty,
                'status'     => 'in_cart',
            ]);
        }

        // Total cart quantity for badge update
        $cartCount = CartItem::where('user_id', auth()->id())
            ->where('status', 'in_cart')
            ->sum('quantity');

        if ($request->expectsJson()) {
            return response()->json([
                'success'    => true,
                'message'    => "{$product->name} added to cart!",
                'cart_count' => $cartCount,
            ]);
        }

        return redirect()->route('cart.index')->with('success', "{$product->name} added to cart!");
    }

    /**
     * Remove item from cart
     */
    public function destroy(CartItem $cart)
    {
        if ($cart->user_id === auth()->id()) {
            $cart->delete();
        }

        return redirect()->route('cart.index')->with('success', 'Item removed from cart.');
    }

    /**
     * Customer Checkout - Submits items as orders to the Store / Staff POS register
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|string|in:Cash,GCash,Credit Card,Bank Transfer',
        ]);

        $items = CartItem::with('product')
            ->where('user_id', auth()->id())
            ->where('status', 'in_cart')
            ->get();

        if ($items->isEmpty()) {
            return back()->with('error', 'Your cart is empty.');
        }

        foreach ($items as $item) {
            $item->update([
                'status'         => 'ordered',
                'payment_method' => $request->payment_method,
            ]);
        }

        return redirect()->route('products.catalog')->with('success', 'Order submitted to the store counter! Staff will prepare your transaction for confirmation.');
    }
}
