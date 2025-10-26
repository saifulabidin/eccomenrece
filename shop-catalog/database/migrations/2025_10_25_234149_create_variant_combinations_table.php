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
        Schema::create('variant_combinations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->constrained()->onDelete('cascade');
            $table->foreignId('variant_attribute_id')->constrained()->onDelete('cascade');
            $table->string('attribute_value');
            $table->timestamps();

            $table->unique(['product_variant_id', 'variant_attribute_id'], 'variant_attribute_unique');
            $table->index('product_variant_id');
            $table->index('variant_attribute_id');
            $table->index('attribute_value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variant_combinations');
    }
};
