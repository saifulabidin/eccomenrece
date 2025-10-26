<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use App\Models\Category;
use App\Models\Review;
use App\Models\AdminUser;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    
    protected function getStats(): array
    {
        $totalProducts = Product::count();
        $publishedProducts = Product::where('status', 'published')->count();
        $totalCategories = Category::count();
        $totalReviews = Review::count();
        $averageRating = Review::avg('rating');
        $adminUsers = AdminUser::where('is_active', true)->count();
        
        return [
            Stat::make('Total Produk', $totalProducts)
                ->description($publishedProducts . ' produk aktif')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3]),
                
            Stat::make('Kategori', $totalCategories)
                ->description('Total kategori produk')
                ->descriptionIcon('heroicon-m-squares-2x2')
                ->color('info'),
                
            Stat::make('Ulasan Produk', $totalReviews)
                ->description('Rating: ' . number_format($averageRating ?? 0, 1) . '/5.0 ⭐')
                ->descriptionIcon('heroicon-m-star')
                ->color('warning'),
                
            Stat::make('Admin Aktif', $adminUsers)
                ->description('Pengguna dengan akses admin')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('primary'),
        ];
    }
}
