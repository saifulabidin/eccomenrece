<?php

namespace App\Filament\Pages;

use App\Filament\Resources\ReviewResource\Widgets\ReviewStats;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\LatestReviews;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'Dasbor';
    
    protected static string $view = 'filament.pages.dashboard';
    
    public function getTitle(): string
    {
        return 'Dasbor';
    }
    
    public function getHeading(): string
    {
        return 'Dasbor';
    }
    
    public function getWidgets(): array
    {
        return [
            StatsOverview::class,
            ReviewStats::class,
            LatestReviews::class,
        ];
    }
}
