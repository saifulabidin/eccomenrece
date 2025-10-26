<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductVariantResource\Pages;
use App\Filament\Resources\ProductVariantResource\RelationManagers;
use App\Models\ProductVariant;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductVariantResource extends Resource
{
    protected static ?string $model = ProductVariant::class;

    protected static ?string $navigationIcon = 'heroicon-o-swatch';

    protected static ?string $navigationGroup = 'Products';

    protected static ?string $label = 'Product Variant';

    protected static ?string $pluralLabel = 'Product Variants';

    public static function shouldRegisterNavigation(): bool
    {
        return false; // Hide this resource from navigation
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->relationship('product', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Product')
                    ->reactive()
                    ->afterStateUpdated(fn (Forms\Set $set, $state) => self::updateVariantFields($set, $state)),

                Forms\Components\TextInput::make('sku')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->label('SKU')
                    ->disabled()
                    ->helperText('Auto-generated based on Base SKU and variant attributes'),

                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Variant Name')
                    ->disabled()
                    ->helperText('Auto-generated based on product name and variant attributes'),

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

                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\Select::make('size_value')
                        ->label('Size')
                        ->options(function (Forms\Get $get) {
                            $productId = $get('product_id');
                            if (!$productId) return [];

                            $product = \App\Models\Product::find($productId);
                            if (!$product || !$product->has_variants) return [];

                            $sizeAttribute = $product->variantAttributes()->byType('size')->first();
                            return $sizeAttribute ?
                                array_combine(
                                    $sizeAttribute->attribute_values ?? [],
                                    $sizeAttribute->attribute_values ?? []
                                ) : [];
                        })
                        ->visible(fn (Forms\Get $get) => $get('../../product_id'))
                        ->reactive()
                        ->afterStateUpdated(fn (Forms\Set $set, $state, Forms\Get $get) => self::updateVariantFields($set, $get('product_id'), $state, $get('color_value'))),

                    Forms\Components\Select::make('color_value')
                        ->label('Color')
                        ->options(function (Forms\Get $get) {
                            $productId = $get('product_id');
                            if (!$productId) return [];

                            $product = \App\Models\Product::find($productId);
                            if (!$product || !$product->has_variants) return [];

                            $colorAttribute = $product->variantAttributes()->byType('color')->first();
                            return $colorAttribute ?
                                array_combine(
                                    $colorAttribute->attribute_values ?? [],
                                    $colorAttribute->attribute_values ?? []
                                ) : [];
                        })
                        ->visible(fn (Forms\Get $get) => $get('../../product_id'))
                        ->reactive()
                        ->afterStateUpdated(fn (Forms\Set $set, $state, Forms\Get $get) => self::updateVariantFields($set, $get('product_id'), $get('size_value'), $state)),
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
                    ->maxSize(2048) // 2MB max per image
                    ->imageResizeTargetWidth('800')
                    ->imageResizeTargetHeight('800')
                    ->imageCropAspectRatio('1:1')
                    ->helperText('Upload up to 5 images for this variant. If no images are uploaded, the product images will be used.')
                    ->label('Variant Images (Optional)')
                    ->columnSpanFull(),

                Forms\Components\Toggle::make('is_active')
                    ->default(true)
                    ->label('Active'),

                Forms\Components\Repeater::make('combinations')
                    ->relationship()
                    ->schema([
                        Forms\Components\Select::make('variant_attribute_id')
                            ->label('Attribute Type')
                            ->options(function (Forms\Get $get) {
                                $productId = $get('../../product_id');
                                if (!$productId) return [];

                                return \App\Models\VariantAttribute::where('product_id', $productId)
                                    ->active()
                                    ->get()
                                    ->pluck('attribute_name', 'id');
                            })
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn (Forms\Set $set, $state) => $set('attribute_value', null)),

                        Forms\Components\Select::make('attribute_value')
                            ->label('Attribute Value')
                            ->options(function (Forms\Get $get) {
                                $attributeId = $get('variant_attribute_id');
                                if (!$attributeId) return [];

                                $attribute = \App\Models\VariantAttribute::find($attributeId);
                                return $attribute ? array_combine($attribute->display_values, $attribute->display_values) : [];
                            })
                            ->required(),
                    ])
                    ->columns(2)
                    ->label('Variant Attributes'),
            ]);
    }

    public static function table(Table $table): Table
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
                            ->send()),

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

    /**
     * Auto-generate SKU and variant name based on selected attributes
     */
    public static function updateVariantFields(Forms\Set $set, ?int $productId = null, ?string $sizeValue = null, ?string $colorValue = null): void
    {
        if (!$productId) return;

        $product = \App\Models\Product::find($productId);
        if (!$product || !$product->has_variants) return;

        $baseSku = $product->base_sku;
        $productName = $product->name;

        // Generate SKU
        $sizeCode = $sizeValue ? strtoupper(str_replace([' ', '-'], '', $sizeValue)) : '';
        $colorCode = $colorValue ? strtoupper(str_replace([' ', '-'], '', $colorValue)) : '';

        $skuParts = array_filter([$baseSku, $sizeCode, $colorCode]);
        $generatedSku = implode('-', $skuParts);

        // Generate variant name
        $nameParts = array_filter([$productName, $sizeValue, $colorValue]);
        $generatedName = implode(' - ', $nameParts);

        $set('sku', $generatedSku);
        $set('name', $generatedName);
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
            'index' => Pages\ListProductVariants::route('/'),
            'create' => Pages\CreateProductVariant::route('/create'),
            'edit' => Pages\EditProductVariant::route('/{record}/edit'),
        ];
    }
}
