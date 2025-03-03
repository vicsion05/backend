<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\OrderStatusController;

// Lấy thông tin người dùng đã xác thực
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json($request->user());
});

// Đăng ký & Đăng nhập
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Đăng xuất & Hồ sơ cá nhân
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [UserController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

// Quản lý sản phẩm
Route::middleware('auth:sanctum')->prefix('products')->group(function () {
    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/', [ProductController::class, 'store']);
    Route::get('/{id}', [ProductController::class, 'show']);
    Route::put('/{id}', [ProductController::class, 'update']);
    Route::delete('/{id}', [ProductController::class, 'destroy']);

    // Review cho sản phẩm
    Route::post('/reviews', [ReviewController::class, 'store']);
});

// Quản lý review
Route::middleware('auth:sanctum')->prefix('reviews')->group(function () {
    Route::post('/', [ReviewController::class, 'store']);
    Route::delete('/{id}', [ReviewController::class, 'destroy']);
});

// Quản lý thương hiệu (Brand)
Route::middleware('auth:sanctum')->prefix('brands')->group(function () {
    Route::get('/', [BrandController::class, 'index']);
    Route::post('/', [BrandController::class, 'store']);
    Route::get('/{id}', [BrandController::class, 'show']);
    Route::put('/{id}', [BrandController::class, 'update']);
    Route::delete('/{id}', [BrandController::class, 'destroy']);
});

// Quản lý giỏ hàng
Route::middleware('auth:sanctum')->prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index']);
    Route::post('/', [CartController::class, 'store']);
    Route::put('/{id}', [CartController::class, 'update']);
    Route::delete('/{id}', [CartController::class, 'destroy']);
});

// Quản lý đơn hàng
Route::middleware('auth:sanctum')->prefix('orders')->group(function () {
    Route::get('/', [OrderController::class, 'index']);
    Route::post('/', [OrderController::class, 'store']);
    Route::get('/{id}', [OrderController::class, 'show']);
    Route::put('/{id}', [OrderController::class, 'update']);
    Route::delete('/{id}', [OrderController::class, 'destroy']);
});

// Quản lý danh sách yêu thích
Route::middleware('auth:sanctum')->prefix('wishlist')->group(function () {
    Route::get('/', [WishlistController::class, 'index']);
    Route::post('/', [WishlistController::class, 'store']);
    Route::delete('/{id}', [WishlistController::class, 'destroy']);
});

// Quản lý thông báo
Route::middleware('auth:sanctum')->prefix('notifications')->group(function () {
    Route::get('/', [NotificationController::class, 'index']);
    Route::post('/', [NotificationController::class, 'store']);
    Route::put('/{id}/mark-as-read', [NotificationController::class, 'markAsRead']);
});

// Quản lý báo cáo & phân tích
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/reports/revenue', [ReportController::class, 'totalRevenue']);
    Route::get('/reports/orders', [ReportController::class, 'totalOrders']);
    Route::get('/reports/best-selling-products', [ReportController::class, 'bestSellingProducts']);
    Route::get('/reports/monthly-revenue', [ReportController::class, 'monthlyRevenue']);
});

// Upload file
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/upload', [UploadController::class, 'uploadFile']);
    Route::get('/uploads', [UploadController::class, 'listFiles']);
    Route::delete('/uploads/{id}', [UploadController::class, 'deleteFile']);
});

Route::get('/test', function () {
    return response()->json(['message' => 'API is working!']);
});

Route::post('/payment', [PaymentController::class, 'createPayment']);
Route::post('/payment/notify', [PaymentController::class, 'notify']);

Route::put('/order/{id}/status', [OrderStatusController::class, 'updateStatus']);

Route::middleware(['auth:sanctum', 'admin'])->get('/admin/stats', [AdminController::class, 'getStats']);