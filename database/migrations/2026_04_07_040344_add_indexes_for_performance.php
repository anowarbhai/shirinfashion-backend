<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Products table indexes
        Schema::table('products', function (Blueprint $table) {
            // Indexes for filtering and sorting
            $table->index('is_active');
            $table->index('is_featured');
            $table->index('category_id');
            $table->index('created_at');
            $table->index(['is_active', 'created_at']);
            $table->index(['category_id', 'is_active']);
            $table->index(['average_rating', 'is_active']);
        });

        // Reviews table indexes
        Schema::table('reviews', function (Blueprint $table) {
            $table->index('product_id');
            $table->index('is_active');
            $table->index(['product_id', 'is_active']);
            $table->index('created_at');
        });

        // Categories table indexes
        Schema::table('categories', function (Blueprint $table) {
            $table->index('is_active');
            $table->index('slug');
        });

        // Orders table indexes
        Schema::table('orders', function (Blueprint $table) {
            $table->index('status');
            $table->index('payment_status');
            $table->index('created_at');
        });

        // Carts table indexes
        Schema::table('carts', function (Blueprint $table) {
            $table->index('session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
            $table->dropIndex(['is_featured']);
            $table->dropIndex(['category_id']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['is_active', 'created_at']);
            $table->dropIndex(['category_id', 'is_active']);
            $table->dropIndex(['average_rating', 'is_active']);
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex(['product_id']);
            $table->dropIndex(['is_active']);
            $table->dropIndex(['product_id', 'is_active']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
            $table->dropIndex(['slug']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['payment_status']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->dropIndex(['session_id']);
        });
    }
};
