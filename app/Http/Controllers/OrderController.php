<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        // Tính tổng giá trị đơn hàng và kiểm tra tồn kho
        $total_price = 0;
        foreach ($request->items as $item) {
            $product = Product::find($item['product_id']);
            if ($product->stock < $item['quantity']) {
                return response()->json([
                    'message' => "Not enough stock for
    {$product->name}"
                ], 400);
            }
            $total_price += $product->price * $item['quantity'];
        }

        DB::beginTransaction();

        try {
            // Tạo đơn hàng mới
            $order = Order::create([
                'user_id' => Auth::id(),
                'shipping_address' => $request->shipping_address,
                'status' => 'pending',
                'payment_method' => $request->payment_method,
                'total_price' => $total_price,
            ]);

            // Tạo các mục đơn hàng
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                ]);

                // Giảm số lượng tồn kho
                $product->decrement('stock', $item['quantity']);
            }

            DB::commit();

            return response()->json(['message' => 'Order placed successfully!', 'order' => $order], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to place order', 'error' => $e->getMessage()], 500);
        }
    }

    // Xem chi tiết đơn hàng
    public function show($id)
    {
        // Fetch order by ID and include related order items and product data
        $order = Order::where('user_id', Auth::id())
            ->where('id', $id)
            ->with('orderItems.product')  // Include order items and related product
            ->first();

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

        // Trả lại số lượng sản phẩm vào kho khi đơn hàng bị hủy
        foreach ($order->orderItems as $item) {
            $product = $item->product;
            $product->increment('stock', $item->quantity);
        }

        return response()->json(['message' => 'Order cancelled successfully']);
    }
}