<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ReviewController extends BaseController
{
    public function index(Request $request)
    {
        $query = Review::with('product')
            ->where('is_active', true);

        if ($request->product_id) {
            $query->where('product_id', $request->product_id);
        }

        $reviews = $query->orderBy('created_at', 'desc')->paginate(10);

        return $this->success($reviews);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();

        if ($user) {
            $validated['user_id'] = $user->id;
            $validated['customer_name'] = $user->name;
            $validated['customer_phone'] = $request->customer_phone ?: ($user->phone ?? null);
            $validated['is_verified'] = true;
            $validated['is_active'] = true; // Auto-approve logged in user reviews
        } else {
            $validated['user_id'] = null;
            $validated['customer_name'] = $request->customer_name ?: 'Guest';
            $validated['customer_phone'] = $request->customer_phone;
            $validated['is_verified'] = false;
            $validated['is_active'] = false; // Guest reviews need admin approval
        }

        $review = Review::create($validated);

        $message = $user ? 'Review submitted successfully!' : 'Review submitted! It will be visible after admin approval.';

        return $this->success($review, $message, 201);
    }

    public function productReviews(Product $product)
    {
        $cacheKey = 'product_reviews_'.$product->id;

        $data = Cache::remember($cacheKey, 600, function () use ($product) {
            $reviews = $product->reviews()
                ->with('user:id,name,email')
                ->where('is_active', true)
                ->select(['id', 'product_id', 'user_id', 'customer_name', 'customer_phone', 'rating', 'comment', 'created_at'])
                ->orderBy('created_at', 'desc')
                ->get();

            $avgRating = $reviews->avg('rating');
            $ratingDistribution = [
                5 => $reviews->where('rating', 5)->count(),
                4 => $reviews->where('rating', 4)->count(),
                3 => $reviews->where('rating', 3)->count(),
                2 => $reviews->where('rating', 2)->count(),
                1 => $reviews->where('rating', 1)->count(),
            ];

            // Map reviews to ensure proper formatting
            $formattedReviews = $reviews->map(function ($review) {
                return [
                    'id' => $review->id,
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'customer_name' => $review->customer_name ?: ($review->user?->name ?? 'Guest'),
                    'customer_phone' => $review->customer_phone,
                    'created_at' => $review->created_at,
                    'user' => $review->user ? ['name' => $review->user->name] : null,
                ];
            })->toArray();

            return [
                'reviews' => $formattedReviews,
                'average_rating' => round($avgRating, 1),
                'total_reviews' => $reviews->count(),
                'rating_distribution' => $ratingDistribution,
            ];
        });

        return $this->success($data);
    }
}
