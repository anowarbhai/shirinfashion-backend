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
        Schema::create('sms_settings', function (Blueprint $table) {
            $table->id();
            
            // API Configuration
            $table->string('api_key')->nullable();
            $table->string('sender_id')->nullable();
            $table->enum('environment', ['sandbox', 'live'])->default('sandbox');
            $table->boolean('is_active')->default(false);
            
            // Feature Toggles
            $table->boolean('order_status_sms')->default(false);
            $table->boolean('order_placement_sms')->default(false);
            $table->boolean('admin_login_otp')->default(false);
            $table->boolean('customer_login_otp')->default(false);
            
            // SMS Templates
            $table->text('order_placement_template')->nullable();
            $table->text('order_status_template')->nullable();
            $table->text('otp_template')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_settings');
    }
};
