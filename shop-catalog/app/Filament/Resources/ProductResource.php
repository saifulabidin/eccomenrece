<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (string $context, $state, Forms\Set $set) {
                        if ($context === 'create') {
                            $set('slug', \Illuminate\Support\Str::slug($state));
                        }
                    }),
                Forms\Components\TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Forms\Components\RichEditor::make('description')
                    ->label('Deskripsi')
                    ->nullable(),
                Forms\Components\Select::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name')
                    ->required(),
                Forms\Components\FileUpload::make('images')
                    ->label('Gambar')
                    ->multiple()
                    ->image()
                    ->directory('products')
                    ->disk('public')
                    ->visibility('public')
                    ->acceptedFileTypes([
                        'image/jpeg',
                        'image/jpg',
                        'image/png',
                        'image/webp',
                    ])
                    ->maxSize(5120)
                    ->nullable(),
                Forms\Components\Toggle::make('has_variants')
                    ->label('Aktifkan Varian')
                    ->default(false)
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        // When variants are disabled, clean up variant-related fields
                        if (!$state) {
                            $set('variant_attributes', []);
                        }
                        // When variants are enabled, set default price if not set
                        else {
                            $set('price', null);
                            $set('discount_price', null);
                            $set('stock', null);
                        }
                    }),

                Forms\Components\Hidden::make('base_sku')
                    ->visible(fn (Forms\Get $get) => $get('has_variants'))
                    ->default(function (Forms\Get $get) {
                        $name = $get('name');
                        return $name ? \Illuminate\Support\Str::upper(\Illuminate\Support\Str::slug($name)) : null;
                    }),

                Forms\Components\Fieldset::make('Atribut Varian')
                    ->visible(fn (Forms\Get $get) => $get('has_variants'))
                    ->schema([
                        Forms\Components\Repeater::make('variant_attributes')
                            ->relationship('variantAttributes')
                            ->schema([
                                Forms\Components\Select::make('attribute_type')
                                    ->options([
                                        'size' => 'Ukuran',
                                        'color' => 'Warna',
                                    ])
                                    ->required()
                                    ->label('Tipe Atribut'),

                                Forms\Components\TextInput::make('attribute_name')
                                    ->required()
                                    ->label('Nama Atribut (contoh: "Pilihan Ukuran", "Pilihan Warna")')
                                    ->minLength(2)
                                    ->maxLength(50),

                                Forms\Components\TagsInput::make('attribute_values')
                                    ->required()
                                    ->separator(',')
                                    ->label('Nilai Atribut')
                                    ->helperText('Masukkan nilai dipisahkan dengan koma'),
                            ])
                            ->columns(3)
                            ->addActionLabel('Tambah Atribut Lain')
                            ->label('Atribut Varian')
                            ->reorderable(false)
                            ->itemLabel(fn (array $state): ?string => $state['attribute_name'] ?? null)
                            ->helperText('Tambahkan minimal atribut Ukuran dan Warna untuk membuat varian produk.')
                            ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                                $data['is_active'] = true;
                                return $data;
                            })
                            ->mutateRelationshipDataBeforeSaveUsing(function (array $data): array {
                                $data['is_active'] = true;
                                return $data;
                            })
                    ]),

                Forms\Components\Section::make('Harga & Stok (untuk produk tanpa varian)')
                    ->visible(fn (Forms\Get $get) => !$get('has_variants'))
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->required(fn (Forms\Get $get) => !$get('has_variants'))
                            ->numeric()
                            ->prefix('Rp')
                            ->label('Harga'),

                        Forms\Components\TextInput::make('discount_price')
                            ->numeric()
                            ->prefix('Rp')
                            ->nullable()
                            ->label('Harga Diskon'),

                        Forms\Components\TextInput::make('stock')
                            ->numeric()
                            ->nullable()
                            ->label('Stok'),
                    ]),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Dipublikasikan',
                    ])
                    ->default('draft')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Harga')
                    ->sortable()
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->has_variants) {
                            $variants = $record->variants()->active()->get();
                            if ($variants->count() > 0) {
                                $minPrice = $variants->min('final_price');
                                $maxPrice = $variants->max('final_price');

                                if ($minPrice > 0 && $maxPrice > 0) {
                                    return $minPrice == $maxPrice
                                        ? 'Rp ' . number_format($minPrice, 0, ',', '.')
                                        : 'Rp ' . number_format($minPrice, 0, ',', '.') . ' - Rp ' . number_format($maxPrice, 0, ',', '.');
                                }
                            }
                            return 'Rp 0';
                        }
                        $price = $record->price;
                        return $price && $price > 0 ? 'Rp ' . number_format($price, 0, ',', '.') : 'Rp 0';
                    }),
                Tables\Columns\TextColumn::make('discount_price')
                    ->label('Harga Diskon')
                    ->sortable()
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->has_variants) {
                            return '-';
                        }
                        $discountPrice = $record->discount_price;
                        return $discountPrice && $discountPrice > 0 ? 'Rp ' . number_format($discountPrice, 0, ',', '.') : '-';
                    }),
                Tables\Columns\TextColumn::make('stock')
                    ->label('Stok')
                    ->sortable()
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->has_variants) {
                            $totalStock = $record->variants()->sum('stock');
                            return $totalStock;
                        }
                        return $record->stock;
                    }),
                Tables\Columns\IconColumn::make('has_variants')
                    ->boolean()
                    ->label('Punya Varian'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Kategori')
                    ->relationship('category', 'name'),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Dipublikasikan',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('publish')
                        ->label('Publikasikan yang Dipilih')
                        ->icon('heroicon-o-globe-alt')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['status' => 'published']))
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation()
                        ->after(fn () => \Filament\Notifications\Notification::make()
                            ->title('Produk berhasil dipublikasikan')
                            ->success()
                            ->send()),

                    Tables\Actions\BulkAction::make('draft')
                        ->label('Jadikan Draft yang Dipilih')
                        ->icon('heroicon-o-document-text')
                        ->color('warning')
                        ->action(fn ($records) => $records->each->update(['status' => 'draft']))
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation()
                        ->after(fn () => \Filament\Notifications\Notification::make()
                            ->title('Produk berhasil dijadikan draft')
                            ->success()
                            ->send()),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
