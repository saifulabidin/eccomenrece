<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdminUserResource\Pages;
use App\Filament\Resources\AdminUserResource\RelationManagers;
use App\Models\AdminUser;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AdminUserResource extends Resource
{
    protected static ?string $model = AdminUser::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'Pengguna Admin';
    protected static ?string $modelLabel = 'Pengguna Admin';
    protected static ?string $pluralModelLabel = 'Pengguna Admin';
    // protected static ?string $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pengguna Admin')
                    ->description('Mengelola akses pengguna admin ke panel')
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->label('Alamat Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->placeholder('Contoh : abidins799@gmail.com')
                            ->helperText('Email akun Google yang akan memiliki akses admin')
                            ->rules([
                                function () {
                                    return function (string $attribute, $value, \Closure $fail) {
                                        $superAdminEmails = explode(',', env('ADMIN_EMAILS', ''));
                                        $superAdminEmails = array_map('trim', $superAdminEmails);
                                        
                                        if (in_array($value, $superAdminEmails)) {
                                            $fail('Email ini adalah Super Admin dan tidak dapat ditambahkan secara manual.');
                                        }
                                    };
                                },
                            ]),

                        Forms\Components\TextInput::make('name')
                            ->label('Nama')
                            ->maxLength(255)
                            ->placeholder('Nama Admin (opsional)')
                            ->helperText('Opsional: Tampilkan nama untuk admin ini'),

                        Forms\Components\Select::make('role')
                            ->label('Role')
                            ->options([
                                'super_admin' => 'Super Admin',
                                'admin' => 'Admin',
                            ])
                            ->default('admin')
                            ->required()
                            ->helperText('Super Admin dapat mengelola pengguna admin, Admin memiliki akses terbatas')
                            ->disabled(fn ($record) => $record && $record->isSuperAdmin()),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Actif')
                            ->default(true)
                            ->helperText('Hanya admin aktif yang dapat mengakses panel')
                            ->inline(false),

                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3)
                            ->placeholder('Catatan tambahan tentang admin ini...')
                            ->helperText('Catatan internal (opsional)'),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                // Hide super admin emails from .env
                $superAdminEmails = explode(',', env('ADMIN_EMAILS', ''));
                $superAdminEmails = array_map('trim', $superAdminEmails);
                $query->whereNotIn('email', $superAdminEmails);
            })
            ->columns([
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->icon('heroicon-m-envelope')
                    ->iconColor('primary'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                Tables\Columns\BadgeColumn::make('role')
                    ->label('Role')
                    ->colors([
                        'danger' => 'super_admin',
                        'primary' => 'admin',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'super_admin' => 'Super Admin',
                        'admin' => 'Admin',
                        default => $state,
                    })
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Added')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->hidden(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->hidden(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('Role')
                    ->options([
                        'super_admin' => 'Super Admin',
                        'admin' => 'Admin',
                    ])
                    ->placeholder('Semua Role'),
                    
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('Semua Status')
                    ->trueLabel('Aktif')
                    ->falseLabel('Nonaktif'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->hidden(fn (AdminUser $record): bool => $record->isSuperAdmin()),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Pengguna Admin')
                    ->modalDescription('Apakah Anda yakin ingin menghapus pengguna admin ini? Mereka tidak akan bisa lagi mengakses panel.')
                    ->modalSubmitActionLabel('Ya, Hapus')
                    ->hidden(fn (AdminUser $record): bool => $record->isSuperAdmin()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Aktifkan')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Aktifkan Pengguna Admin')
                        ->modalDescription('Apakah Anda yakin ingin mengaktifkan pengguna admin yang dipilih?')
                        ->modalSubmitActionLabel('Ya, Aktifkan')
                        ->action(function ($records) {
                            $records->each->update(['is_active' => true]);
                        })
                        ->deselectRecordsAfterCompletion(),
                        
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Nonaktifkan')
                        ->icon('heroicon-o-x-circle')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Nonaktifkan Pengguna Admin')
                        ->modalDescription('Apakah Anda yakin ingin menonaktifkan pengguna admin yang dipilih? Mereka tidak akan bisa mengakses panel.')
                        ->modalSubmitActionLabel('Ya, Nonaktifkan')
                        ->action(function ($records) {
                            $records->each->update(['is_active' => false]);
                        })
                        ->deselectRecordsAfterCompletion(),
                        
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Pengguna Admin')
                        ->modalDescription('Apakah Anda yakin ingin menghapus pengguna admin yang dipilih?')
                        ->modalSubmitActionLabel('Ya, Hapus'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Belum ada pengguna admin')
            ->emptyStateDescription('Tambahkan pengguna admin yang dapat mengakses panel ini')
            ->emptyStateIcon('heroicon-o-shield-check');
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
            'index' => Pages\ListAdminUsers::route('/'),
            'create' => Pages\CreateAdminUser::route('/create'),
            'edit' => Pages\EditAdminUser::route('/{record}/edit'),
        ];
    }

    /**
     * Only super admins can access this resource
     */
    public static function canViewAny(): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }

        $adminUser = AdminUser::where('email', $user->email)->first();
        return $adminUser && $adminUser->isSuperAdmin();
    }
}