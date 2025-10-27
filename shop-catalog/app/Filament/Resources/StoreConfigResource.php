<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StoreConfigResource\Pages;
use App\Filament\Resources\StoreConfigResource\RelationManagers;
use App\Models\StoreConfig;
use Filament\Resources\Resource;

class StoreConfigResource extends Resource
{
    protected static ?string $model = StoreConfig::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Konfigurasi Toko';
    protected static ?string $modelLabel = 'Konfigurasi Toko';
    protected static ?string $pluralModelLabel = 'Konfigurasi Toko';

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStoreConfigs::route('/'),
            'create' => Pages\CreateStoreConfig::route('/create'),
            'edit' => Pages\EditStoreConfig::route('/{record}/edit'),
        ];
    }
}