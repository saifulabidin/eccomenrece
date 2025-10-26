<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\Product;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // The relationship() method in the Repeater will automatically load existing variant attributes
        // No manual data loading needed anymore
        return $data;
    }

    protected function afterSave(): void
    {
        $record = $this->record;
        $data = $this->data;

        // Auto-generate variants if enabled and attributes are provided
        if (isset($data['has_variants']) && $data['has_variants']) {
            // Reload relationships
            $record->load(['variantAttributes']);

            // Regenerate variant combinations
            try {
                $record->generateAllVariantCombinations();
            } catch (\Exception $e) {
                \Log::error('Failed to generate variants on update', [
                    'product_id' => $record->id,
                    'error' => $e->getMessage(),
                ]);
            }
        } elseif (!$data['has_variants']) {
            // If variants disabled, clean up
            $record->variantAttributes()->delete();
            $record->variants()->delete();
        }
    }
}
