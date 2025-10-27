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
        // Drop the existing unique constraint on google_id
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropUnique(['google_id']);
        });

        // Add a composite unique constraint on google_id and product_id
        Schema::table('reviews', function (Blueprint $table) {
            $table->unique(['google_id', 'product_id'], 'reviews_google_id_product_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the composite unique constraint
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropUnique(['google_id', 'product_id']);
        });

        // Add back the original unique constraint on google_id
        Schema::table('reviews', function (Blueprint $table) {
            $table->unique('google_id');
        });
    }
};