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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('google_id')->nullable()->unique(); // Google user ID for uniqueness
            $table->string('user_name')->nullable(); // Fallback if Google name not available
            $table->string('user_email')->nullable();
            $table->string('user_avatar')->nullable(); // Google profile picture URL
            $table->integer('rating')->unsigned()->default(5); // 1-5 star rating
            $table->text('review')->nullable(); // Review content
            $table->boolean('approved')->default(false); // Admin approval required
            $table->string('ip_address')->nullable(); // For security tracking
            $table->timestamps();

            // Indexes for performance
            $table->index(['product_id', 'approved']);
            $table->index('google_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
