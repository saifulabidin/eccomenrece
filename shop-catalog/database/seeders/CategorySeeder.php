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
            'name' => 'Pengembangan Web Full Stack',
            'slug' => 'pengembangan-web-full-stack',
            'description' => 'Jasa pembuatan website company profile, landing page, dan aplikasi web kustom',
        ]);

        Category::create([
            'name' => 'Layanan Keamanan Siber',
            'slug' => 'layanan-keamanan-siber',
            'description' => 'Jasa penetration testing, security audit, dan hardening server',
        ]);

        Category::create([
            'name' => 'Pengembangan Aplikasi Mobile',
            'slug' => 'pengembangan-aplikasi-mobile',
            'description' => 'Jasa pembuatan aplikasi mobile Android & iOS dengan Flutter',
        ]);

        Category::create([
            'name' => 'Paket Maintenance & Retainer',
            'slug' => 'paket-maintenance-retainer',
            'description' => 'Paket maintenance website bulanan dan jasa developer retainer',
        ]);
    }
}