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
        Schema::table('theme_settings', function (Blueprint $table) {
            // Contact fields
            $table->string('email')->nullable()->after('company_details');
            $table->string('phone')->nullable()->after('email');
            $table->text('address')->nullable()->after('phone');
            
            // Social media fields
            $table->string('facebook')->nullable()->after('address');
            $table->string('instagram')->nullable()->after('facebook');
            $table->string('twitter')->nullable()->after('instagram');
            $table->string('youtube')->nullable()->after('twitter');
            $table->string('linkedin')->nullable()->after('youtube');
            $table->string('tiktok')->nullable()->after('linkedin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('theme_settings', function (Blueprint $table) {
            $table->dropColumn(['email', 'phone', 'address', 'facebook', 'instagram', 'twitter', 'youtube', 'linkedin', 'tiktok']);
        });
    }
};
