<?php

namespace App\Filament\Widgets;

use App\Models\Review;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestReviews extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    
    protected static ?int $sort = 3;
    
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Review::query()
                    ->with(['product'])
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('user_name')
                    ->label('Pengguna')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Produk')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                    
                Tables\Columns\TextColumn::make('rating')
                    ->label('Rating')
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state >= 4 => 'success',
                        $state >= 3 => 'warning',
                        default => 'danger',
                    })
                    ->formatStateUsing(fn (int $state): string => $state . '/5 ⭐'),
                    
                Tables\Columns\TextColumn::make('review')
                    ->label('Ulasan')
                    ->limit(50)
                    ->wrap(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->heading('Ulasan Terbaru')
            ->description('5 ulasan produk terbaru dari pelanggan');
    }
}
