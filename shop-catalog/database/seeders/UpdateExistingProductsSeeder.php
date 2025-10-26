<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class UpdateExistingProductsSeeder extends Seeder
{
    public function run()
    {
        // Update existing products to set has_variants to false by default
        Product::whereNull('has_variants')->update(['has_variants' => false]);

        $this->command->info('Updated existing products to set has_variants = false');
    }
}