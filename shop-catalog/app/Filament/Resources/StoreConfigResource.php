<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StoreConfigResource\Pages;
use App\Filament\Resources\StoreConfigResource\RelationManagers;
use App\Models\StoreConfig;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StoreConfigResource extends Resource
{
    protected static ?string $model = StoreConfig::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('store_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('whatsapp_number')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g., 6281234567890'),
                Forms\Components\Textarea::make('address')
                    ->nullable(),
                Forms\Components\Textarea::make('description')
                    ->nullable(),
                Forms\Components\FileUpload::make('logo')
                    ->image()
                    ->directory('logos')
                    ->maxSize(1024)
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'])
                    ->helperText('Upload logo untuk navbar. Ukuran ideal: 200x60px atau SVG.'),
                Forms\Components\FileUpload::make('hero_images')
                    ->multiple()
                    ->image()
                    ->directory('hero-images')
                    ->maxSize(2048)
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->helperText('Upload gambar untuk hero section. Ukuran ideal: 1920x1080px (16:9) atau 1600x900px. Multiple images akan menjadi slideshow otomatis.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('store_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('whatsapp_number'),
                Tables\Columns\TextColumn::make('address')
                    ->limit(50),
                Tables\Columns\TextColumn::make('description')
                    ->limit(50),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

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
