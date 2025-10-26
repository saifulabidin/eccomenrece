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

    protected function afterCreate(): void
    {
        $record = $this->record;

        // Auto-generate variants if enabled
        if ($record->has_variants) {
            // Wait a bit for relationships to be saved
            sleep(1);
            
            // Reload relationships
            $record->load(['variantAttributes']);

            // Generate variant combinations
            try {
                $variants = $record->generateAllVariantCombinations();
                
            } catch (\Exception $e) {
            }
        }
    }
}
