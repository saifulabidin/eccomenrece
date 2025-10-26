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
        $actions = [Actions\DeleteAction::make()];

        // Only show "Generate Variants" action if product has variants enabled but no variants yet
        if ($this->record->has_variants && $this->record->variants()->count() === 0) {
            $actions[] = Actions\Action::make('generate_variants')
                ->label('Generate Variants')
                ->icon('heroicon-o-cog-6-tooth')
                ->action(function () {
                    try {
                        $this->record->generateAllVariantCombinations();
                        \Filament\Notifications\Notification::make()
                            ->title('Variants berhasil dibuat')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        \Filament\Notifications\Notification::make()
                            ->title('Gagal membuat variants')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                });
        }

        return $actions;
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

            }
        } elseif (!$data['has_variants']) {
            // If variants disabled, clean up
            $record->variantAttributes()->delete();
            $record->variants()->delete();
        }
    }
}
