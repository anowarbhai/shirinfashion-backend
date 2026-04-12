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

        if (empty($validated['slug'])) {
            $validated['slug'] = $this->generateUniqueSlug($validated['name']);
        } else {
            $validated['slug'] = $this->generateUniqueSlug($validated['slug']);
        }

        if (isset($validated['images']) && is_string($validated['images'])) {
            $validated['images'] = json_decode($validated['images'], true) ?? [];
        }

        $categoryIds = $request->input('category_ids', []);
        $tagIds = $request->input('tag_ids', []);
        $attributeValueIds = $request->input('attribute_value_ids', []);

        $product = Product::create($validated);

        // Force set description fields
        $product->short_description = $request->input('short_description', '');
        $product->description = $request->input('description', '');
        $product->save();

        $allCategoryIds = array_unique(array_filter(array_merge([$validated['category_id']], $categoryIds)));
        if (! empty($allCategoryIds)) {
            $product->categories()->sync($allCategoryIds);
        }

        if (! empty($tagIds)) {
            $product->tags()->sync($tagIds);
        }

        if (! empty($attributeValueIds)) {
            $product->attributeValues()->sync($attributeValueIds);
        }

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully');
    }

    public function show(Product $product)
    {
        $product->load('category', 'reviews');

        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $brands = Brand::where('is_active', true)->orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        $attributes = Attribute::with('values')->get();
        $product->load('tags', 'attributeValues', 'brandModel', 'categories');

        return view('admin.products.edit', compact('product', 'categories', 'brands', 'tags', 'attributes'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'sometimes|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:products,slug,'.$product->id,
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'sku' => 'nullable|string|unique:products,sku,'.$product->id,
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
            $validated['is_active'] = false;
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

    public function export(Request $request)
    {
        if ($request->has('sample')) {
            $headers = ['Name', 'Slug', 'Category', 'Price', 'Sale Price', 'SKU', 'Stock', 'Status', 'Active', 'Image URL', 'Description', 'Short Description', 'Brand', 'Tags (comma separated)'];
            $sampleData = [
                ['Sample Product Name', 'sample-product', 'Category Name', '500', '450', 'SKU001', '10', 'instock', 'Yes', 'https://example.com/image.jpg', 'Description here', 'Short desc', 'Brand Name', 'Tag1, Tag2'],
            ];

            $csvContent = implode(',', $headers)."\n";
            foreach ($sampleData as $row) {
                $csvContent .= implode(',', array_map(fn ($v) => '"'.str_replace('"', '""', $v).'"', $row))."\n";
            }

            return response($csvContent)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', 'attachment; filename="products_sample.csv"');
        }

        $products = Product::with('category', 'attributeValues.attribute', 'tags')->get();
        $assetUrl = config('app.url').'/storage/';

        $headers = ['ID', 'Name', 'Slug', 'Category', 'Price', 'Sale Price', 'SKU', 'Stock', 'Status', 'Active', 'Image', 'Description', 'Short Description', 'Brand', 'Tags', 'Attributes'];
        $rows = $products->map(function ($p) use ($assetUrl) {
            $tags = $p->tags->pluck('name')->join(', ');
            $attrs = $p->attributeValues->map(fn ($av) => $av->attribute?->name.': '.$av->value)->join(', ');

            $imageUrl = '';
            if ($p->image) {
                if (str_starts_with($p->image, 'http')) {
                    $imageUrl = $p->image;
                } else {
                    $imageUrl = $assetUrl.$p->image;
                }
            }

            return [
                $p->id,
                $p->name,
                $p->slug,
                $p->category?->name ?? '',
                $p->price,
                $p->sale_price ?? '',
                $p->sku ?? '',
                $p->stock_quantity ?? 0,
                $p->stock_status,
                $p->is_active ? 'Yes' : 'No',
                $imageUrl,
                strip_tags($p->description ?? '') ?? '',
                strip_tags($p->short_description ?? '') ?? '',
                $p->brand ?? '',
                $tags,
                $attrs,
            ];
        });

        $csvContent = implode(',', $headers)."\n";
        foreach ($rows as $row) {
            $csvContent .= implode(',', array_map(fn ($v) => '"'.str_replace('"', '""', $v).'"', $row))."\n";
        }

        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="products_'.date('Y-m-d').'.csv"');
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getPathname(), 'r');
        fgetcsv($handle);

        $imported = 0;
        while (($row = fgetcsv($handle)) !== false) {
            if (empty($row[1])) {
                continue;
            }

            $category = \App\Models\Category::where('name', $row[3] ?? '')->first();

            $product = Product::updateOrCreate(
                ['sku' => $row[6] ?? null],
                [
                    'name' => $row[1],
                    'slug' => $row[2] ?: \Illuminate\Support\Str::slug($row[1]),
                    'category_id' => $category?->id,
                    'price' => (float) ($row[4] ?? 0),
                    'sale_price' => ! empty($row[5]) ? (float) $row[5] : null,
                    'stock_quantity' => (int) ($row[7] ?? 0),
                    'stock_status' => $row[8] ?? 'instock',
                    'is_active' => ($row[9] ?? 'Yes') === 'Yes',
                    'image' => $row[10] ?? null,
                    'description' => $row[11] ?? '',
                    'short_description' => $row[12] ?? '',
                    'brand' => $row[13] ?? null,
                ]
            );

            // Handle tags
            if (! empty($row[14])) {
                $tagNames = explode(',', $row[14]);
                $tagIds = [];
                foreach ($tagNames as $tagName) {
                    $tag = \App\Models\Tag::firstOrCreate(['name' => trim($tagName)]);
                    $tagIds[] = $tag->id;
                }
                $product->tags()->sync($tagIds);
            }

            $imported++;
        }
        fclose($handle);

        return redirect()->route('admin.products.index')->with('success', "Imported {$imported} products successfully");
    }
}
