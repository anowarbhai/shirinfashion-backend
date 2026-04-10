<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['product', 'user']);

        if ($request->status) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->rating) {
            $query->where('rating', $request->rating);
        }

        $reviews = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.reviews.index', compact('reviews'));
    }

    public function create()
    {
        return redirect()->route('admin.reviews.index');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.reviews.index');
    }

    public function show(Review $review)
    {
        return view('admin.reviews.index', compact('review'));
    }

    public function edit(Review $review)
    {
        return redirect()->route('admin.reviews.index');
    }

    public function update(Request $request, Review $review)
    {
        $validated = $request->validate([
            'is_active' => 'boolean',
            'is_verified' => 'boolean',
        ]);

        $review->update($validated);

        return redirect()->route('admin.reviews.index')->with('success', 'Review updated successfully');
    }

    public function destroy(Review $review)
    {
        $review->delete();

        return redirect()->route('admin.reviews.index')->with('success', 'Review deleted successfully');
    }
}
