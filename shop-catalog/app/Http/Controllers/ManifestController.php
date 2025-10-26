<?php

namespace App\Http\Controllers;

use App\Models\StoreConfig;
use Illuminate\Http\Request;

class ManifestController extends Controller
{
    public function index()
    {
        $config = StoreConfig::first();
        
        $manifest = [
            'name' => $config->store_name ?? 'Katalog Online',
            'short_name' => $config->store_name ?? 'Katalog',
            'description' => $config->description ?? 'Toko online terpercaya dengan produk berkualitas',
            'start_url' => '/',
            'display' => 'standalone',
            'background_color' => '#0f0f1e',
            'theme_color' => '#3b82f6',
            'orientation' => 'portrait-primary',
            'icons' => []
        ];
        
        // Add 192x192 icon if exists
        if ($config && $config->pwa_icon_192) {
            $manifest['icons'][] = [
                'src' => asset('storage/' . $config->pwa_icon_192),
                'sizes' => '192x192',
                'type' => 'image/png',
                'purpose' => 'any maskable'
            ];
        }
        
        // Add 512x512 icon if exists
        if ($config && $config->pwa_icon_512) {
            $manifest['icons'][] = [
                'src' => asset('storage/' . $config->pwa_icon_512),
                'sizes' => '512x512',
                'type' => 'image/png',
                'purpose' => 'any maskable'
            ];
        }
        
        return response()->json($manifest)
            ->header('Content-Type', 'application/manifest+json');
    }
}
