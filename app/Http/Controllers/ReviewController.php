<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    // Lấy danh sách review theo sản phẩm
    public function index($productId)
    {
        $reviews = Review::where('product_id', $productId)->with('user:id,name')->get();
        return response()->json($reviews);
    }

    // Tạo review mới
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $review = Review::create([
            'user_id' => auth()->id(),
            'product_id' => $request->product_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return response()->json(['message' => 'Review added successfully', 'review' => $review]);
    }

    // Xóa review
    public function destroy($id)
    {
        $review = Review::where('id', $id)->where('user_id', auth()->id())->first();
        if (!$review) {
            return response()->json(['message' => 'Review not found or unauthorized'], 403);
        }

        $review->delete();
        return response()->json(['message' => 'Review deleted successfully']);
    }
}
