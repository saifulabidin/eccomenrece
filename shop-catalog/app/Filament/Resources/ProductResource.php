<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Resources\Resource;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Produk';
    protected static ?string $modelLabel = 'Produk';
    protected static ?string $pluralModelLabel = 'Produk';


    public static function afterCreate(Product $record, array $data): void
    {
        // Auto-generate base SKU for variant products
        if ($data['has_variants'] && empty($record->base_sku)) {
            $record->base_sku = \Illuminate\Support\Str::upper(\Illuminate\Support\Str::slug($record->name));
            $record->save();
        }

        if ($data['has_variants'] && isset($data['variant_attributes'])) {
            foreach ($data['variant_attributes'] as $attribute) {
                $record->variantAttributes()->create([
                    'attribute_type' => $attribute['attribute_type'],
                    'attribute_name' => $attribute['attribute_name'],
                    'attribute_values' => $attribute['attribute_values'],
                    'is_active' => true,
                ]);
            }

            // Auto-generate variant combinations
            $record->generateAllVariantCombinations();
        }
    }

    public static function afterUpdate(Product $record, array $data): void
    {
        // Auto-generate base SKU for variant products
        if ($data['has_variants'] && empty($record->base_sku)) {
            $record->base_sku = \Illuminate\Support\Str::upper(\Illuminate\Support\Str::slug($record->name));
            $record->save();
        }

        if ($data['has_variants'] && isset($data['variant_attributes'])) {
            // Only update if attributes actually changed
            $existingAttributes = $record->variantAttributes()->get()->keyBy('attribute_type');

            // Update or create attributes
            foreach ($data['variant_attributes'] as $attribute) {
                if (isset($existingAttributes[$attribute['attribute_type']])) {
                    // Update existing attribute
                    $existingAttr = $existingAttributes[$attribute['attribute_type']];
                    $existingAttr->update([
                        'attribute_name' => $attribute['attribute_name'],
                        'attribute_values' => $attribute['attribute_values'],
                        'is_active' => true,
                    ]);
                } else {
                    // Create new attribute
                    $record->variantAttributes()->create([
                        'attribute_type' => $attribute['attribute_type'],
                        'attribute_name' => $attribute['attribute_name'],
                        'attribute_values' => $attribute['attribute_values'],
                        'is_active' => true,
                    ]);
                }
            }

            // Only regenerate variants if attributes changed
            $record->generateAllVariantCombinations();
        } elseif (!$data['has_variants']) {
            // If variants disabled, clean up
            $record->variantAttributes()->delete();
            $record->variants()->delete();
        }
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\VariantsRelationManager::class,
        ];
    }

    public static function validateVariantAttributes(array $data): array
    {
        $errors = [];

        if (isset($data['has_variants']) && $data['has_variants'] && isset($data['variant_attributes'])) {
            $variantAttributes = $data['variant_attributes'];

            // Check if at least one variant attribute is provided
            if (empty($variantAttributes) || (is_array($variantAttributes) && count($variantAttributes) === 0)) {
                $errors['variant_attributes'] = 'At least one variant attribute is required when variants are enabled.';
            }

            // Check for duplicate attribute types
            if (is_array($variantAttributes) && count($variantAttributes) > 1) {
                $attributeTypes = [];
                foreach ($variantAttributes as $index => $attribute) {
                    if (isset($attribute['attribute_type'])) {
                        if (in_array($attribute['attribute_type'], $attributeTypes)) {
                            $errors['variant_attributes.' . $index . '.attribute_type'] = 'Duplicate attribute type: ' . $attribute['attribute_type'] . '. Each attribute type must be unique.';
                        } else {
                            $attributeTypes[] = $attribute['attribute_type'];
                        }
                    }
                }
            }
        }

        return $errors;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
