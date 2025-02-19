<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;

class ReportController extends Controller
{
    // Báo cáo tổng doanh thu
    public function totalRevenue()
    {
        $totalRevenue = Order::where('status', 'completed')->sum('total_price');

        return response()->json([
            'total_revenue' => $totalRevenue
        ]);
    }

    // Báo cáo số lượng đơn hàng
    public function totalOrders()
    {
        $totalOrders = Order::count();

        return response()->json([
            'total_orders' => $totalOrders
        ]);
    }

    // Báo cáo sản phẩm bán chạy
    public function bestSellingProducts()
    {
        $products = Product::withCount('orderDetails')
            ->orderBy('order_details_count', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'best_selling_products' => $products
        ]);
    }

    // Báo cáo doanh thu theo tháng
    public function monthlyRevenue()
    {
        $monthlyRevenue = Order::where('status', 'completed')
            ->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->sum('total_price');

        return response()->json([
            'monthly_revenue' => $monthlyRevenue
        ]);
    }
}
