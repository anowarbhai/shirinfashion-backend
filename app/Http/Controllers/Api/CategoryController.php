<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CategoryController extends BaseController
{
    public function index()
    {
        // Cache categories for 30 minutes
        $categories = Cache::remember('categories_list', 1800, function () {
            return Category::where('is_active', true)
                ->withCount('products')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
        });
            
        return $this->success($categories);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $category = Category::create($validated);
        
        // Clear categories cache
        Cache::forget('categories_list');
        
        return $this->success($category, 'Category created successfully', 201);
    }

    public function show($slug)
    {
        $category = Category::where('slug', $slug)->first();
        
        if (!$category) {
            return $this->error('Category not found', 404);
        }
        
        return $this->success($category);
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:categories,slug,' . $category->id,
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $category->update($validated);
        
        // Clear categories cache
        Cache::forget('categories_list');
        
        return $this->success($category, 'Category updated successfully');
    }

    public function destroy(Category $category)
    {
        // Clear cache before delete
        Cache::forget('categories_list');
        
        $category->delete();
        
        return $this->success(null, 'Category deleted successfully');
    }
}
