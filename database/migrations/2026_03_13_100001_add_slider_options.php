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
        Schema::table('sliders', function (Blueprint $table) {
            // 2nd button fields
            $table->string('button_2_text')->nullable()->after('button_link');
            $table->string('button_2_link')->nullable()->after('button_2_text');
            $table->enum('button_2_color', ['rose', 'blue', 'green', 'purple', 'orange', 'dark', 'white', 'outline'])->default('outline')->after('button_2_link');
            
            // Text alignment
            $table->enum('text_align', ['left', 'center', 'right'])->default('left')->after('button_2_color');
            
            // Content position (vertical)
            $table->enum('content_position', ['top', 'center', 'bottom'])->default('center')->after('text_align');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sliders', function (Blueprint $table) {
            $table->dropColumn(['button_2_text', 'button_2_link', 'button_2_color', 'text_align', 'content_position']);
        });
    }
};
