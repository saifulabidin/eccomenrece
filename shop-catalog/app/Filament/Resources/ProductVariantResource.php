<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductVariantResource\Pages;
use App\Filament\Resources\ProductVariantResource\RelationManagers;
use App\Models\ProductVariant;
use App\Models\Product;
use Filament\Resources\Resource;
use Filament\Forms\Forms\Set;
use Illuminate\Database\Eloquent\Builder;

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