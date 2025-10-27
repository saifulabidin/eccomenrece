<?php

namespace App\Filament\Resources\ProductVariantResource\Pages;

use App\Filament\Resources\ProductVariantResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ListProductVariants extends ListRecords
{
    protected static string $resource = ProductVariantResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->searchable()
                    ->sortable()
                    ->label('Product'),

                Tables\Columns\TextColumn::make('sku')
                    ->searchable()
                    ->sortable()
                    ->label('SKU'),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label('Variant Name'),

                Tables\Columns\TextColumn::make('price')
                    ->money('IDR')
                    ->sortable()
                    ->label('Price'),

                Tables\Columns\TextColumn::make('discount_price')
                    ->money('IDR')
                    ->sortable()
                    ->label('Discount Price'),

                Tables\Columns\TextColumn::make('stock')
                    ->numeric()
                    ->sortable()
                    ->label('Stock'),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable()
                    ->label('Created At'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('product')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Product'),


                Tables\Filters\SelectFilter::make('attribute_type')
                    ->label('Attribute Type')
                    ->options([
                        'size' => 'Size',
                        'color' => 'Color',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['value'],
                                fn (Builder $query, string $value): Builder => $query->whereHas('combinations.variantAttribute',
                                    fn (Builder $query) => $query->where('attribute_type', $value)
                                )
                            );
                    }),

                Tables\Filters\SelectFilter::make('attribute_value')
                    ->label('Attribute Values')
                    ->options(function (): array {
                        try {
                            // Get all unique attribute values from existing variant combinations
                            $attributeValues = \App\Models\VariantCombination::query()
                                ->join('variant_attributes', 'variant_combinations.variant_attribute_id', '=', 'variant_attributes.id')
                                ->select('variant_combinations.attribute_value')
                                ->distinct()
                                ->whereNotNull('variant_combinations.attribute_value')
                                ->orderBy('variant_combinations.attribute_value')
                                ->pluck('attribute_value', 'attribute_value')
                                ->toArray();

                            return $attributeValues;
                        } catch (\Exception $e) {
                            // Return empty array on any error to prevent crashes
                            return [];
                        }
                    })
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['value'],
                                fn (Builder $query, string $value): Builder => $query->whereHas('combinations',
                                    fn (Builder $query) => $query->where('attribute_value', $value)
                                )
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Aktifkan yang Dipilih')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['is_active' => true]))
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation()
                        ->after(fn () => \Filament\Notifications\Notification::make()
                            ->title('Product variants berhasil diaktifkan')
                            ->success()
                            ->send())

                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Nonaktifkan yang Dipilih')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn ($records) => $records->each->update(['is_active' => false]))
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation()
                        ->after(fn () => \Filament\Notifications\Notification::make()
                            ->title('Product variants berhasil dinonaktifkan')
                            ->success()
                            ->send()),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
