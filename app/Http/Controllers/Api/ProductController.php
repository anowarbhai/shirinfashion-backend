<?php

namespace App\Http\Controllers\Api;

use App\Models\GeneralSetting;
use App\Models\Product;
use App\Models\VolumeDiscount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ProductController extends BaseController
{
    public function index(Request $request)
    {
        // Create cache key based on request parameters
        $cacheKey = 'products_index_'.md5(json_encode($request->all()));

        // Cache for 5 minutes for better performance
        $products = Cache::remember($cacheKey, 300, function () use ($request) {
            $query = Product::with(['category', 'brandModel'])->where('is_active', true);

            if ($request->category_id) {
                $query->where('category_id', $request->category_id);
            }

            if ($request->featured) {
                $query->where('is_featured', true);
            }

            if ($request->search) {
                $query->where(function ($q) use ($request) {
                    $q->where('name', 'like', "%{$request->search}%")
                        ->orWhere('description', 'like', "%{$request->search}%")
                        ->orWhere('brand', 'like', "%{$request->search}%");
                });
            }

            if ($request->min_price) {
                $query->where(function ($q) use ($request) {
                    $q->where('sale_price', '>=', $request->min_price)
                        ->orWhere(function ($q2) use ($request) {
                            $q2->whereNull('sale_price')
                                ->where('price', '>=', $request->min_price);
                        });
                });
            }

            if ($request->max_price) {
                $query->where(function ($q) use ($request) {
                    $q->where(function ($q2) use ($request) {
                        $q2->whereNotNull('sale_price')
                            ->where('sale_price', '<=', $request->max_price);
                    })->orWhere(function ($q2) use ($request) {
                        $q2->whereNull('sale_price')
                            ->where('price', '<=', $request->max_price);
                    });
                });
            }

            // In stock filter
            if ($request->in_stock) {
                $query->where('stock_quantity', '>', 0);
            }

            // On sale filter (has sale_price less than regular price)
            if ($request->on_sale) {
                $query->whereNotNull('sale_price')
                    ->whereColumn('sale_price', '<', 'price');
            }

            // Minimum rating filter - handle null values
            if ($request->min_rating) {
                $query->whereNotNull('average_rating')
                    ->where('average_rating', '>=', $request->min_rating);
            }

            // Sort by price_high (high to low) - handle special case
            $sortBy = $request->sort_by ?? 'created_at';
            $sortOrder = $request->sort_order ?? 'desc';

            if ($sortBy === 'price_high') {
                $query->orderBy('price', 'desc');
            } elseif ($sortBy === 'price') {
                $query->orderBy('price', 'asc');
            } else {
                $allowedSorts = ['created_at', 'name', 'sales'];
                if (in_array($sortBy, $allowedSorts)) {
                    $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
                } else {
                    $query->orderBy('created_at', 'desc');
                }
            }

            $perPage = $request->per_page ?? 12;

            return $query->paginate($perPage);
        });

        return $this->success($products);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'sku' => 'nullable|string|unique:products,sku',
            'stock_quantity' => 'integer|min:0',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'image' => 'nullable|string',
            'images' => 'nullable|array',
            'brand' => 'nullable|string|max:255',
            'origin' => 'nullable|string|max:255',
            'weight' => 'nullable|integer',
            'dimensions' => 'nullable|string|max:255',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $product = Product::create($validated);

        // Clear products list cache
        Cache::flush();

        return $this->success($product->load('category'), 'Product created successfully', 201);
    }

    public function show(string $slug)
    {
        $cacheKey = 'product_'.$slug;

        $product = Cache::remember($cacheKey, 600, function () use ($slug) {
            return Product::with(['category', 'brandModel', 'attributeValues.attribute'])->where('slug', $slug)->first();
        });

        if (! $product) {
            return $this->error('Product not found', 404);
        }

        $volumeDiscounts = VolumeDiscount::with(['freeProduct:id,name,slug,image'])
            ->where('product_id', $product->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('quantity')
            ->get();

        return $this->success([
            ...$product->toArray(),
            'step_url_enabled' => $product->step_url_enabled,
            'volume_discounts' => $volumeDiscounts,
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'sometimes|exists:categories,id',
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:products,slug,'.$product->id,
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'price' => 'sometimes|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'sku' => 'nullable|string|unique:products,sku,'.$product->id,
            'stock_quantity' => 'integer|min:0',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'image' => 'nullable|string',
            'images' => 'nullable|array',
            'brand' => 'nullable|string|max:255',
            'origin' => 'nullable|string|max:255',
            'weight' => 'nullable|integer',
            'dimensions' => 'nullable|string|max:255',
        ]);

        $product->update($validated);

        // Clear product cache
        Cache::forget('product_'.$product->slug);
        Cache::flush();

        return $this->success($product->load('category'), 'Product updated successfully');
    }

    public function destroy(Product $product)
    {
        // Clear cache before delete
        Cache::forget('product_'.$product->slug);

        $product->delete();

        return $this->success(null, 'Product deleted successfully');
    }

    public function featured()
    {
        $products = Cache::remember('products_featured', 600, function () {
            return Product::with(['category', 'brandModel'])
                ->where('is_active', true)
                ->where('is_featured', true)
                ->orderBy('created_at', 'desc')
                ->limit(8)
                ->get();
        });

        return $this->success($products);
    }

    public function latest()
    {
        $products = Cache::remember('products_latest', 600, function () {
            return Product::with(['category', 'brandModel'])
                ->where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->limit(12)
                ->get();
        });

        return $this->success($products);
    }

    public function related(Request $request, string $slug)
    {
        $cacheKey = 'product_related_'.$slug;

        $products = Cache::remember($cacheKey, 600, function () use ($slug) {
            $product = Product::where('slug', $slug)->select(['id', 'category_id', 'brand'])->first();

            if (! $product) {
                return null;
            }

            return Product::with(['category', 'brandModel'])
                ->where('is_active', true)
                ->where('id', '!=', $product->id)
                ->where(function ($query) use ($product) {
                    // Same category
                    $query->where('category_id', $product->category_id)
                        // Or same brand
                        ->orWhere('brand', $product->brand);
                })
                ->orderBy('created_at', 'desc')
                ->limit(8)
                ->get();
        });

        if (! $products) {
            return $this->error('Product not found', 404);
        }

        return $this->success($products);
    }

    public function settings()
    {
        $generalSettings = GeneralSetting::getSettings();

        $settings = [
            'global_reviews_enabled' => filter_var(config('app.global_reviews_enabled', true), FILTER_VALIDATE_BOOLEAN),
            'global_avg_rating_enabled' => filter_var(config('app.global_avg_rating_enabled', true), FILTER_VALIDATE_BOOLEAN),
            'guest_reviews_enabled' => filter_var(config('app.guest_reviews_enabled', true), FILTER_VALIDATE_BOOLEAN),
            'contact_buttons_enabled' => filter_var(config('app.contact_buttons_enabled') ?? env('CONTACT_BUTTONS_ENABLED', false), FILTER_VALIDATE_BOOLEAN),
            'whatsapp_number' => config('app.whatsapp_number') ?? env('WHATSAPP_NUMBER', ''),
            'call_number' => config('app.call_number') ?? env('CALL_NUMBER', ''),
            'whatsapp_message' => config('app.whatsapp_message') ?? env('WHATSAPP_MESSAGE', 'Hi, I\'m interested in this product: {product_name}. Please provide more details.'),
            'currency_symbol' => $generalSettings->currency_symbol,
            'currency_code' => $generalSettings->currency_code,
            'currency_position' => $generalSettings->currency_position,
        ];

        return $this->success($settings);
    }
}
