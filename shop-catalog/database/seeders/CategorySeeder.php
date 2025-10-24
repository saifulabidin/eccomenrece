<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create([
            'name' => 'Elektronik',
            'slug' => 'elektronik',
            'description' => 'Produk elektronik dan gadget',
        ]);

        Category::create([
            'name' => 'Pakaian',
            'slug' => 'pakaian',
            'description' => 'Pakaian pria dan wanita',
        ]);

        Category::create([
            'name' => 'Rumah Tangga',
            'slug' => 'rumah-tangga',
            'description' => 'Barang rumah tangga',
        ]);
    }
}
