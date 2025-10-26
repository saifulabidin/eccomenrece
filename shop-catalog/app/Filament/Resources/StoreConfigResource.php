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
    protected static ?string $navigationLabel = 'Konfigurasi Toko';
    protected static ?string $modelLabel = 'Konfigurasi Toko';
    protected static ?string $pluralModelLabel = 'Konfigurasi Toko';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Umum')
                    ->schema([
                        Forms\Components\TextInput::make('store_name')
                            ->label('Nama Toko')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->nullable()
                            ->rows(3),
                        Forms\Components\Textarea::make('address')
                            ->label('Alamat')
                            ->nullable()
                            ->rows(2),
                    ])->columns(1),
                
                Forms\Components\Section::make('Kontak')
                    ->schema([
                        Forms\Components\TextInput::make('whatsapp_number')
                            ->label('Nomor WhatsApp')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., 6281234567890'),
                        Forms\Components\TextInput::make('phone')
                            ->label('Nomor Telepon')
                            ->tel()
                            ->maxLength(255)
                            ->placeholder('+62 812-3456-7890'),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255)
                            ->placeholder('info@katalogonline.com'),
                    ])->columns(3),
                
                Forms\Components\Section::make('Social Media')
                    ->schema([
                        Forms\Components\TextInput::make('facebook_url')
                            ->label('Facebook URL')
                            ->url()
                            ->maxLength(255)
                            ->placeholder('https://facebook.com/yourpage'),
                        Forms\Components\TextInput::make('instagram_url')
                            ->label('Instagram URL')
                            ->url()
                            ->maxLength(255)
                            ->placeholder('https://instagram.com/youraccount'),
                        Forms\Components\TextInput::make('twitter_url')
                            ->label('Twitter URL')
                            ->url()
                            ->maxLength(255)
                            ->placeholder('https://twitter.com/youraccount'),
                    ])->columns(3),
                
                Forms\Components\Section::make('Logo & Hero Images')
                    ->schema([
                        Forms\Components\FileUpload::make('logo')
                            ->label('Logo Toko')
                            ->image()
                            ->directory('logos')
                            ->maxSize(1024)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'])
                            ->helperText('Upload logo untuk navbar. Ukuran ideal: 200x60px atau SVG.'),
                        Forms\Components\FileUpload::make('hero_images')
                            ->label('Hero Images')
                            ->multiple()
                            ->image()
                            ->directory('hero-images')
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->helperText('Upload gambar untuk hero section. Ukuran ideal: 1920x1080px (16:9) atau 1600x900px. Multiple images akan menjadi slideshow otomatis.'),
                    ])->columns(2),
                
                Forms\Components\Section::make('Favicon & PWA Icons')
                    ->description('Upload icon untuk browser tab dan progressive web app')
                    ->schema([
                        Forms\Components\FileUpload::make('favicon')
                            ->label('Favicon')
                            ->image()
                            ->directory('icons')
                            ->maxSize(512)
                            ->acceptedFileTypes(['image/x-icon', 'image/png', 'image/jpeg'])
                            ->helperText('Upload favicon untuk browser tab. Format: .ico, .png (16x16 atau 32x32).'),
                        Forms\Components\FileUpload::make('pwa_icon_192')
                            ->label('PWA Icon 192x192')
                            ->image()
                            ->directory('icons')
                            ->maxSize(512)
                            ->acceptedFileTypes(['image/png', 'image/jpeg'])
                            ->helperText('Upload icon untuk PWA. Ukuran: 192x192px. Format: .png'),
                        Forms\Components\FileUpload::make('pwa_icon_512')
                            ->label('PWA Icon 512x512')
                            ->image()
                            ->directory('icons')
                            ->maxSize(1024)
                            ->acceptedFileTypes(['image/png', 'image/jpeg'])
                            ->helperText('Upload icon untuk PWA. Ukuran: 512x512px. Format: .png'),
                    ])->columns(3),
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
