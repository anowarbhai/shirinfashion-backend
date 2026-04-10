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
        Schema::create('general_settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name')->default('Shirin Fashion');
            $table->string('currency_symbol')->default('৳');
            $table->string('currency_code')->default('BDT');
            $table->string('currency_position')->default('left'); // left or right
            $table->string('timezone')->default('Asia/Dhaka');
            $table->string('date_format')->default('M d, Y');
            $table->string('time_format')->default('h:i A'); // 12h or 24h
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('general_settings');
    }
};
