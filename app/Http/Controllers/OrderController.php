<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    // Lấy danh sách đơn hàng của người dùng
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())->with('orderItems.product')->get();
        return response()->json($orders);
    }

    // Tạo đơn hàng mới từ giỏ hàng
    public function store(Request $request)
    {
        $request->validate([
            'shipping_address' => 'required|string',
            'payment_method' => 'required|in:cod,bank,momo,vnpay',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $total_price = 0;
        foreach ($request->items as $item) {
            $product = \App\Models\Product::find($item['product_id']);
            $total_price += $product->price * $item['quantity'];
        }

        $order = Order::create([
            'user_id' => Auth::id(),
            'shipping_address' => $request->shipping_address,
            'status' => 'pending',
            'payment_method' => $request->payment_method,
            'total_price' => $total_price,
        ]);

        foreach ($request->items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => \App\Models\Product::find($item['product_id'])->price,
            ]);
        }

        return response()->json(['message' => 'Order placed successfully!', 'order' => $order], 201);
    }

    // Xem chi tiết đơn hàng
    public function show($id)
    {
        $order = Order::where('user_id', Auth::id())->with('orderItems.product')->find($id);
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }
        return response()->json($order);
    }

    // Hủy đơn hàng
    public function cancel($id)
    {
        $order = Order::where('user_id', Auth::id())->find($id);
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        if ($order->status !== 'pending') {
            return response()->json(['message' => 'Only pending orders can be cancelled'], 400);
        }

        $order->update(['status' => 'cancelled']);
        return response()->json(['message' => 'Order cancelled successfully']);
    }
}
