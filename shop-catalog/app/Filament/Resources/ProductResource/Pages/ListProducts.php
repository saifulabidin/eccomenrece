<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Table;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Harga')
                    ->sortable()
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->has_variants) {
                            $variants = $record->variants()->active()->get();
                            if ($variants->count() > 0) {
                                $minPrice = $variants->min('final_price');
                                $maxPrice = $variants->max('final_price');

                                if ($minPrice > 0 && $maxPrice > 0) {
                                    return $minPrice == $maxPrice
                                        ? 'Rp ' . number_format($minPrice, 0, ',', '.')
                                        : 'Rp ' . number_format($minPrice, 0, ',', '.') . ' - Rp ' . number_format($maxPrice, 0, ',', '.');
                                }
                            }
                            return 'Rp 0';
                        }
                        $price = $record->price;
                        return $price && $price > 0 ? 'Rp ' . number_format($price, 0, ',', '.') : 'Rp 0';
                    }),
                Tables\Columns\TextColumn::make('discount_price')
                    ->label('Harga Diskon')
                    ->sortable()
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->has_variants) {
                            return '-';
                        }
                        $discountPrice = $record->discount_price;
                        return $discountPrice && $discountPrice > 0 ? 'Rp ' . number_format($discountPrice, 0, ',', '.') : '-';
                    }),
                Tables\Columns\TextColumn::make('stock')
                    ->label('Stok')
                    ->sortable()
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->has_variants) {
                            $totalStock = $record->variants()->sum('stock');
                            return $totalStock;
                        }
                        return $record->stock;
                    }),
                Tables\Columns\IconColumn::make('has_variants')
                    ->boolean()
                    ->label('Punya Varian'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Kategori')
                    ->relationship('category', 'name'),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Dipublikasikan',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('publish')
                        ->label('Publikasikan yang Dipilih')
                        ->icon('heroicon-o-globe-alt')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['status' => 'published']))
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation()
                        ->after(fn () => \Filament\Notifications\Notification::make()
                            ->title('Produk berhasil dipublikasikan')
                            ->success()
                            ->send()),

                    Tables\Actions\BulkAction::make('draft')
                        ->label('Jadikan Draft yang Dipilih')
                        ->icon('heroicon-o-document-text')
                        ->color('warning')
                        ->action(fn ($records) => $records->each->update(['status' => 'draft']))
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation()
                        ->after(fn () => \Filament\Notifications\Notification::make()
                            ->title('Produk berhasil dijadikan draft')
                            ->success()
                            ->send()),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
