<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

    protected static ?string $recordTitleAttribute = 'name';

    public function fillForm(Form $form, ProductVariant $record): void
    {
        // Auto-generate SKU if empty
        if (empty($record->sku) && $record->product) {
            $sizeValue = $record->combinations()
                ->whereHas('attribute', function ($query) {
                    $query->where('attribute_type', 'size');
                })
                ->value('attribute_value');

            $colorValue = $record->combinations()
                ->whereHas('attribute', function ($query) {
                    $query->where('attribute_type', 'color');
                })
                ->value('attribute_value');

            $autoSku = $record->product->generateVariantSku($sizeValue, $colorValue);
            $record->sku = $autoSku;
        }

        $form->fill($record->toArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('sku')
                    ->disabled()
                    ->maxLength(255)
                    ->label('SKU (Auto-generated)')
                    ->helperText('SKU is automatically generated from Base SKU and variant attributes'),

                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Variant Name'),

                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('price')
                        ->required()
                        ->numeric()
                        ->prefix('Rp')
                        ->label('Price'),

                    Forms\Components\TextInput::make('discount_price')
                        ->numeric()
                        ->prefix('Rp')
                        ->label('Discount Price (Optional)'),
                ]),

                Forms\Components\TextInput::make('stock')
                    ->numeric()
                    ->default(0)
                    ->label('Stock'),

                Forms\Components\FileUpload::make('images')
                    ->multiple()
                    ->image()
                    ->directory('product-variants')
                    ->maxFiles(5)
                    ->label('Images'),

                Forms\Components\Toggle::make('is_active')
                    ->default(true)
                    ->label('Active'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
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
            ])
            ->filters([
                
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
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
                            ->title('Variants berhasil diaktifkan')
                            ->success()
                            ->send()),

                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Nonaktifkan yang Dipilih')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn ($records) => $records->each->update(['is_active' => false]))
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation()
                        ->after(fn () => \Filament\Notifications\Notification::make()
                            ->title('Variants berhasil dinonaktifkan')
                            ->success()
                            ->send()),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}