<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    // Lấy danh sách wishlist của người dùng
    public function index()
    {
        $wishlist = Wishlist::where('user_id', Auth::id())->with('product')->get();
        return response()->json($wishlist);
    }

    // Thêm sản phẩm vào wishlist
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $exists = Wishlist::where('user_id', Auth::id())->where('product_id', $request->product_id)->exists();

        if ($exists) {
            return response()->json(['message' => 'Product already in wishlist'], 400);
        }

        $wishlist = Wishlist::create([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
        ]);

        return response()->json(['message' => 'Product added to wishlist', 'wishlist' => $wishlist], 201);
    }

    // Xóa sản phẩm khỏi wishlist
    public function destroy($id)
    {
        $wishlist = Wishlist::where('user_id', Auth::id())->where('id', $id)->first();

        if (!$wishlist) {
            return response()->json(['message' => 'Product not found in wishlist'], 404);
        }

        $wishlist->delete();
        return response()->json(['message' => 'Product removed from wishlist']);
    }
}
