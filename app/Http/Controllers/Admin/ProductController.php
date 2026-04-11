<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    private function generateUniqueSlug(string $name, ?int $excludeId = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (true) {
            $query = Product::where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
            if (! $query->exists()) {
                break;
            }
            $slug = $originalSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%")
                ->orWhere('sku', 'like', "%{$request->search}%");
        }

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->status) {
            $query->where('is_active', $request->status === 'active');
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $brands = Brand::where('is_active', true)->orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        $attributes = Attribute::with('values')->get();

        return view('admin.products.create', compact('categories', 'brands', 'tags', 'attributes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'sku' => 'nullable|string|unique:products,sku',
            'stock_quantity' => 'integer|min:0',
            'manage_stock' => 'boolean',
            'stock_status' => 'nullable|in:instock,outofstock,backorder',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'step_url_enabled' => 'boolean',
            'reviews_enabled' => 'boolean',
            'avg_rating_enabled' => 'boolean',
            'image' => 'nullable|string',
            'images' => 'nullable|string',
            'brand' => 'nullable|string|max:255',
            'origin' => 'nullable|string|max:255',
            'weight' => 'nullable|integer',
            'dimensions' => 'nullable|string|max:255',
        ]);

        if (! isset($validated['manage_stock'])) {
            $validated['manage_stock'] = false;
        }

        if (! isset($validated['stock_status'])) {
            $validated['stock_status'] = 'instock';
        }

        if (! isset($validated['is_active'])) {
            $validated['is_active'] = true;
        }

        if (! empty($validated['slug'])) {
            $validated['slug'] = $this->generateUniqueSlug($validated['slug'], $product->id);
        } elseif (isset($validated['name']) && $product->name !== $validated['name']) {
            $validated['slug'] = $this->generateUniqueSlug($validated['name'], $product->id);
        }

        if (isset($validated['images']) && is_string($validated['images'])) {
            $validated['images'] = json_decode($validated['images'], true) ?? [];
        }

        $categoryIds = $request->input('category_ids', []);
        $tagIds = $request->input('tag_ids', []);
        $attributeValueIds = $request->input('attribute_value_ids', []);

        // Force update description fields from raw request
        $product->short_description = $request->input('short_description', '');
        $product->description = $request->input('description', '');

        $product->update($validated);

        $allCategoryIds = array_unique(array_filter(array_merge([$validated['category_id'] ?? null], $categoryIds)));
        $product->categories()->sync(array_filter($allCategoryIds));

        $product->tags()->sync($tagIds);
        $product->attributeValues()->sync($attributeValueIds);

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully');
    }
}
