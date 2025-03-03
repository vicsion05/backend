<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class AdminController extends Controller
{
    // Lấy số liệu thống kê
    public function getStats()
    {
        $orders = Order::count();
        $revenue = Order::where('status', 'completed')->sum('total_price');
        $products = Product::count();
        $users = User::count();

        return response()->json([
            'orders' => $orders,
            'revenue' => $revenue,
            'products' => $products,
            'users' => $users,
        ]);
    }
}
