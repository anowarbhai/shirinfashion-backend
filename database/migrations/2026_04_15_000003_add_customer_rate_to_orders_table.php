<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->tinyInteger('customer_success_rate')->nullable()->after('payment_status');
            $table->tinyInteger('customer_cancel_rate')->nullable()->after('customer_success_rate');
            $table->tinyInteger('customer_total_orders')->nullable()->after('customer_cancel_rate');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['customer_success_rate', 'customer_cancel_rate', 'customer_total_orders']);
        });
    }
};
