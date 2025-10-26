<?php

namespace App\Filament\Resources\ReviewResource\Widgets;

use App\Models\Review;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ReviewStats extends BaseWidget
{
    protected static ?int $sort = 2;
    
    protected function getStats(): array
    {
        $totalReviews = Review::count();
        $approvedReviews = Review::where('approved', true)->count();
        $pendingReviews = Review::where('approved', false)->count();
        $averageRating = Review::where('approved', true)->avg('rating') ?? 0;

        return [
            Stat::make('Total Ulasan', $totalReviews)
                ->description('Semua ulasan')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('gray'),

            Stat::make('Disetujui', $approvedReviews)
                ->description('Ulasan yang disetujui')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->chart([7, 12, 10, 14, 15, 18, 20]),

            Stat::make('Menunggu', $pendingReviews)
                ->description('Perlu approval')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->extraAttributes([
                    'class' => $pendingReviews > 0 ? 'animate-pulse' : '',
                ]),

            Stat::make('Rating Rata-rata', number_format($averageRating, 1))
                ->description('Dari ulasan yang disetujui')
                ->descriptionIcon('heroicon-m-star')
                ->color('primary'),
        ];
    }
}