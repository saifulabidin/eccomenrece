<?php

namespace App\Filament\Resources\ReviewResource\Pages;

use App\Filament\Resources\ReviewResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReviews extends ListRecords
{
    protected static string $resource = ReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export')
                ->label('Export Data')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(route('reviews.export'))
                ->openUrlInNewTab(),
        ];
    }

    // Hidden: Stats widget removed from header
    // protected function getHeaderWidgets(): array
    // {
    //     return [
    //         ReviewResource\Widgets\ReviewStats::class,
    //     ];
    // }
}
