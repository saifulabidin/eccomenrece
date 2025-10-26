<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewResource\Pages;
use App\Filament\Resources\ReviewResource\RelationManagers;
use App\Models\Review;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Session;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';
    protected static ?string $navigationLabel = 'Ulasan Produk';
    protected static ?string $modelLabel = 'Ulasan';
    protected static ?string $pluralModelLabel = 'Ulasan';
    protected static ?string $slug = 'reviews';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Produk')
                    ->schema([
                        Forms\Components\Select::make('product_id')
                            ->relationship('product', 'name')
                            ->required()
                            ->disabled(),
                    ]),

                Forms\Components\Section::make('Informasi Pengguna')
                    ->schema([
                        Forms\Components\TextInput::make('user_name')
                            ->label('Nama Pengguna')
                            ->disabled(),
                        Forms\Components\TextInput::make('user_email')
                            ->label('Email')
                            ->disabled(),
                        Forms\Components\TextInput::make('ip_address')
                            ->label('Alamat IP')
                            ->disabled(),
                    ])->columns(1),

                Forms\Components\Section::make('Konten Ulasan')
                    ->schema([
                        Forms\Components\Select::make('rating')
                            ->label('Rating')
                            ->options([
                                1 => '1 Bintang',
                                2 => '2 Bintang',
                                3 => '3 Bintang',
                                4 => '4 Bintang',
                                5 => '5 Bintang',
                            ])
                            ->required()
                            ->disabled(),
                        Forms\Components\Textarea::make('review')
                            ->label('Isi Ulasan')
                            ->rows(6)
                            ->disabled(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Produk')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user_name')
                    ->label('Pengguna')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rating')
                    ->label('Rating')
                    ->formatStateUsing(fn ($state) => str_repeat('⭐', $state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('review')
                    ->label('Ulasan')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\IconColumn::make('approved')
                    ->label('Disetujui')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('approved')
                    ->label('Status Approval')
                    ->options([
                        '0' => 'Menunggu Approval',
                        '1' => 'Disetujui',
                    ]),
                Tables\Filters\SelectFilter::make('rating')
                    ->label('Rating')
                    ->options([
                        1 => '1 Bintang',
                        2 => '2 Bintang',
                        3 => '3 Bintang',
                        4 => '4 Bintang',
                        5 => '5 Bintang',
                    ]),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Lihat')
                    ->modalHeading('Detail Ulasan')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->extraModalFooterActions(fn ($record): array => [
                        Tables\Actions\Action::make('approve_modal')
                            ->label('Setujui Ulasan')
                            ->icon('heroicon-o-check')
                            ->color('success')
                            ->visible(fn () => !$record->approved)
                            ->requiresConfirmation()
                            ->action(function () use ($record) {
                                $record->update(['approved' => true]);
                            })
                            ->after(fn () => redirect()->route('filament.admin.resources.reviews.index'))
                            ->successNotificationTitle('Ulasan berhasil disetujui'),
                        
                        Tables\Actions\Action::make('reject_modal')
                            ->label('Tolak & Hapus')
                            ->icon('heroicon-o-x-mark')
                            ->color('danger')
                            ->visible(fn () => !$record->approved)
                            ->requiresConfirmation()
                            ->modalHeading('Tolak Ulasan')
                            ->modalDescription('Apakah Anda yakin ingin menolak dan menghapus ulasan ini?')
                            ->action(function () use ($record) {
                                $record->delete();
                            })
                            ->after(fn () => redirect()->route('filament.admin.resources.reviews.index'))
                            ->successNotificationTitle('Ulasan berhasil ditolak dan dihapus'),
                    ]),
                    
                Tables\Actions\Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn ($record) => !$record->approved)
                    ->action(fn ($record) => $record->update(['approved' => true]))
                    ->successNotificationTitle('Ulasan berhasil disetujui'),

                Tables\Actions\Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn ($record) => !$record->approved)
                    ->action(fn ($record) => $record->delete())
                    ->requiresConfirmation()
                    ->successNotificationTitle('Ulasan berhasil ditolak dan dihapus'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve')
                        ->label('Setujui yang Dipilih')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['approved' => true]))
                        ->successNotificationTitle('Ulasan terpilih berhasil disetujui'),

                    Tables\Actions\BulkAction::make('reject')
                        ->label('Tolak yang Dipilih')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->action(fn ($records) => $records->each->delete())
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->successNotificationTitle('Ulasan terpilih berhasil ditolak dan dihapus'),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListReviews::route('/'),
        ];
    }
}
