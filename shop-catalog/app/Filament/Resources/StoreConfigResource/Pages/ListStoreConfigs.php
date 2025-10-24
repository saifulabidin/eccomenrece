<?php

namespace App\Filament\Resources\StoreConfigResource\Pages;

use App\Filament\Resources\StoreConfigResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStoreConfigs extends ListRecords
{
    protected static string $resource = StoreConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
