<?php

namespace App\Filament\Resources\ReviewResource\Pages;

use App\Filament\Resources\ReviewResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class EditReview extends EditRecord
{
    protected static string $resource = ReviewResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Produk')
                    ->schema([
                        Forms\Components\Select::make('product_id')
                            ->relationship('product', 'name')
                            ->required()
                            ->disabled(),
                    ]),

                Forms\Components\Section::make('Informasi Pengguna')
                    ->schema([
                        Forms\Components\TextInput::make('user_name')
                            ->label('Nama Pengguna')
                            ->disabled(),
                        Forms\Components\TextInput::make('user_email')
                            ->label('Email')
                            ->disabled(),
                        Forms\Components\TextInput::make('ip_address')
                            ->label('Alamat IP')
                            ->disabled(),
                    ])->columns(1),

                Forms\Components\Section::make('Konten Ulasan')
                    ->schema([
                        Forms\Components\Select::make('rating')
                            ->label('Rating')
                            ->options([
                                1 => '1 Bintang',
                                2 => '2 Bintang',
                                3 => '3 Bintang',
                                4 => '4 Bintang',
                                5 => '5 Bintang',
                            ])
                            ->required()
                            ->disabled(),
                        Forms\Components\Textarea::make('review')
                            ->label('Isi Ulasan')
                            ->rows(6)
                            ->disabled(),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
