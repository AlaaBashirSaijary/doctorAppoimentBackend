<?php

namespace App\Http\Controllers;
use App\Models\ProductReview;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductReviewController extends Controller
{
    public function index($productId)
    {
        $reviews = ProductReview::where('product_id', $productId)->with('user')->get();
        return response()->json(['reviews' => $reviews]);
    }
    public function store(Request $request, $productId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        // التحقق مما إذا كان المستخدم قد اشترى المنتج
        $userId = Auth::id();
        $hasPurchased = Order::where('user_id', $userId)
            ->whereHas('orderItems', function ($query) use ($productId) {
                $query->where('product_id', $productId);
            })
            ->exists();

        if (!$hasPurchased) {
            return response()->json(['message' => 'You can only review products you have purchased.'], 403);
        }

        // إنشاء التقييم إذا كان المستخدم قد اشترى المنتج
        $review = ProductReview::create([
            'user_id' => $userId,
            'product_id' => $productId,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return response()->json(['message' => 'Review added successfully', 'review' => $review]);
    }
}
