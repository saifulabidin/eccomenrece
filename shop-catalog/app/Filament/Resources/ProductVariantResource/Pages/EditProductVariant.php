<?php

namespace App\Filament\Resources\ProductVariantResource\Pages;

use App\Filament\Resources\ProductVariantResource;
use App\Models\ProductVariant;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProductVariant extends EditRecord
{
    protected static string $resource = ProductVariantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();

        if ($record && $record->combinations()->count() > 0) {
            // Load existing variant combinations
            $existingCombinations = $record->combinations()->get()->map(function ($combination) {
                return [
                    'variant_attribute_id' => $combination->variant_attribute_id,
                    'attribute_value' => $combination->attribute_value,
                ];
            })->toArray();

            $data['combinations'] = $existingCombinations;
        }

        return $data;
    }
}
