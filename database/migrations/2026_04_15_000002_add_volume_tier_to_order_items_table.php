<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('volume_tier_id')->nullable()->after('subtotal');
            $table->string('volume_tier_label')->nullable()->after('volume_tier_id');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['volume_tier_id', 'volume_tier_label']);
        });
    }
};
