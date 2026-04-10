<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Models\Review;
use Illuminate\Support\Facades\DB;

echo "Updating product ratings...\n";

$products = Product::all();

foreach ($products as $product) {
    $stats = Review::where('product_id', $product->id)
        ->where('is_active', 1)
        ->selectRaw('COUNT(*) as cnt, AVG(rating) as avg')
        ->first();
    
    $count = $stats->cnt ?? 0;
    $avg = round($stats->avg ?? 0, 1);
    
    DB::table('products')
        ->where('id', $product->id)
        ->update([
            'review_count' => $count,
            'average_rating' => $avg,
        ]);
    
    echo "Updated product {$product->id}: rating={$avg}, count={$count}\n";
}

echo "Done!\n";
