<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    public function form(Form $form): Form
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
