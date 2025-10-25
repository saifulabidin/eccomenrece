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
        // Kategori yang sudah ada
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

        // Tambahan kategori baru
        Category::create([
            'name' => 'Makanan & Minuman',
            'slug' => 'makanan-minuman',
            'description' => 'Produk makanan dan minuman segar',
        ]);

        Category::create([
            'name' => 'Kesehatan & Kecantikan',
            'slug' => 'kesehatan-kecantikan',
            'description' => 'Produk perawatan kesehatan dan kecantikan',
        ]);

        Category::create([
            'name' => 'Olahraga & Outdoor',
            'slug' => 'olahraga-outdoor',
            'description' => 'Peralatan olahraga dan aktivitas outdoor',
        ]);

        Category::create([
            'name' => 'Otomotif',
            'slug' => 'otomotif',
            'description' => 'Aksesoris dan peralatan otomotif',
        ]);

        Category::create([
            'name' => 'Buku & Alat Tulis',
            'slug' => 'buku-alat-tulis',
            'description' => 'Buku, majalah, dan peralatan tulis menulis',
        ]);

        Category::create([
            'name' => 'Mainan & Hobi',
            'slug' => 'mainan-hobi',
            'description' => 'Mainan anak-anak dan perlengkapan hobi',
        ]);

        Category::create([
            'name' => 'Perlengkapan Bayi',
            'slug' => 'perlengkapan-bayi',
            'description' => 'Kebutuhan bayi dan anak-anak',
        ]);
    }
}