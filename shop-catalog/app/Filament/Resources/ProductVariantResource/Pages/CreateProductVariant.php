<?php

namespace App\Filament\Resources\ProductVariantResource\Pages;

use App\Filament\Resources\ProductVariantResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms;
use Filament\Forms\Form;

class CreateProductVariant extends CreateRecord
{
    protected static string $resource = ProductVariantResource::class;

    public function form(Form $form): Form
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
                    ->afterStateUpdated(fn (Forms\Set $set, $state) => ProductVariantResource::updateVariantFields($set, $state)),

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
                        ->afterStateUpdated(fn (Forms\Set $set, $state, Forms\Get $get) => ProductVariantResource::updateVariantFields($set, $get('product_id'), $state, $get('color_value'))),

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
                        ->afterStateUpdated(fn (Forms\Set $set, $state, Forms\Get $get) => ProductVariantResource::updateVariantFields($set, $get('product_id'), $get('size_value'), $state)),
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
}
