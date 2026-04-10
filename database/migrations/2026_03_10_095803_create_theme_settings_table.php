<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('theme_settings', function (Blueprint $table) {
            $table->id();
            // Appearance
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->string('company_name')->default('Shirin Fashion');
            $table->string('tagline')->nullable();
            $table->text('company_details')->nullable();
            // Header
            $table->string('header_style')->default('style1');
            // Footer
            $table->string('footer_style')->default('style1');
            // Menu
            $table->string('primary_menu')->default('main');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('theme_settings');
    }
};
