<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop existing index if any
        try {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique('users_phone_unique');
            });
        } catch (\Exception $e) {
            // Index doesn't exist, continue
        }

        // Clean up duplicates - keep the newest user
        DB::statement('DELETE u1 FROM users u1 
            INNER JOIN users u2 
            WHERE u1.id < u2.id 
            AND u1.phone = u2.phone 
            AND u1.phone IS NOT NULL');

        // Add unique constraint to phone column
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->change();
        });
    }
};
