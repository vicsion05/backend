<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderStatusController extends Controller
{
    public function updateStatus(Request $request, $orderId)
    {
        $order = Order::find($orderId);
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $order->update([
            'status' => $request->status,
        ]);

        return response()->json(['message' => 'Order status updated successfully']);
    }
}