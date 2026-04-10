<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Updating product ratings from reviews...\n\n";

// Reset all products
DB::table('products')->update(['review_count' => 0, 'average_rating' => 0]);

// Get reviews grouped by product
$reviews = DB::table('reviews')
    ->select('product_id', DB::raw('COUNT(*) as cnt'), DB::raw('AVG(rating) as avg'))
    ->where('is_active', 1)
    ->groupBy('product_id')
    ->get();

foreach ($reviews as $r) {
    DB::table('products')
        ->where('id', $r->product_id)
        ->update([
            'review_count' => $r->cnt,
            'average_rating' => round($r->avg, 1)
        ]);
    echo "Product {$r->product_id}: {$r->cnt} reviews, avg rating: " . round($r->avg, 1) . "\n";
}

echo "\nDone!\n";
