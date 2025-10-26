<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Auto-generate base SKU if not provided and variants are enabled
        if (isset($data['has_variants']) && $data['has_variants'] && empty($data['base_sku'])) {
            $data['base_sku'] = \Illuminate\Support\Str::upper(\Illuminate\Support\Str::slug($data['name']));
        }

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        $record = parent::handleRecordCreation($data);

        // Auto-generate variants if enabled and attributes are provided
        if (isset($data['has_variants']) && $data['has_variants'] && isset($data['variant_attributes']) && !empty($data['variant_attributes'])) {

            // Create variant attributes first
            foreach ($data['variant_attributes'] as $attribute) {
                $record->variantAttributes()->create([
                    'attribute_type' => $attribute['attribute_type'],
                    'attribute_name' => $attribute['attribute_name'],
                    'attribute_values' => $attribute['attribute_values'],
                    'is_active' => true,
                ]);
            }

            // Reload relationships to get the newly created attributes
            $record->load(['variantAttributes']);

            // Now generate all variant combinations
            try {
                $variants = $record->generateAllVariantCombinations();
            } catch (\Exception $e) {
                // Log error for debugging
                \Log::error('Failed to generate variants', [
                    'product_id' => $record->id,
                    'error' => $e->getMessage(),
                    'attributes' => $data['variant_attributes']
                ]);

                // Re-throw the exception so user knows something went wrong
                throw $e;
            }
        }

        return $record;
    }
}
