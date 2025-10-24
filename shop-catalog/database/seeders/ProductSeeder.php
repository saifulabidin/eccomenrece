<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'name' => 'Smartphone Samsung Galaxy',
            'slug' => 'smartphone-samsung-galaxy',
            'description' => 'Smartphone terbaru dari Samsung',
            'category_id' => 1,
            'price' => 5000000,
            'stock' => 10,
            'status' => 'published',
        ]);

        Product::create([
            'name' => 'Kaos Polos Hitam',
            'slug' => 'kaos-polos-hitam',
            'description' => 'Kaos polos berkualitas tinggi',
            'category_id' => 2,
            'price' => 50000,
            'stock' => 50,
            'status' => 'published',
        ]);

        Product::create([
            'name' => 'Blender Philips',
            'slug' => 'blender-philips',
            'description' => 'Blender untuk rumah tangga',
            'category_id' => 3,
            'price' => 300000,
            'stock' => 20,
            'status' => 'published',
        ]);
    }
}
