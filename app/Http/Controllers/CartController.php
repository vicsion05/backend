<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Cart $cart)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cart $cart)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cart $cart)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cart $cart)
    {
        //
    }

    /**
     * Add a product to the cart.
     */
    public function addToCart(Request $request) {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);
        
        $cartItem = Cart::updateOrCreate(
            ['user_id' => auth()->id(), 'product_id' => $validated['product_id']],
            ['quantity' => DB::raw('quantity + ' . $validated['quantity'])]
        );
        
        return response()->json($cartItem, 200);
    }

    /**
     * View the cart items of the authenticated user.
     */
    public function viewCart() {
        $cartItems = Cart::with('product')->where('user_id', auth()->id())->get();
        return response()->json($cartItems, 200);
    }

    /**
     * Remove an item from the cart.
     */
    public function removeFromCart($id) {
        $cartItem = Cart::where('user_id', auth()->id())->find($id);
        if (!$cartItem) return response()->json(['message' => 'Item not found'], 404);
        
        $cartItem->delete();
        return response()->json(['message' => 'Item removed'], 200);
    }
}
