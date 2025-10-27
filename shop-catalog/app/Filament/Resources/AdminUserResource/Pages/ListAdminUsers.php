<?php

namespace App\Filament\Resources\AdminUserResource\Pages;

use App\Filament\Resources\AdminUserResource;
use App\Models\AdminUser;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ListAdminUsers extends ListRecords
{
    protected static string $resource = AdminUserResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                // Hide super admin emails from config
                $superAdminEmails = config('auth.admin_emails', []);
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

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Admin user created')
                        ->body('The admin user has been added successfully.')
                ),

            Actions\Action::make('sync_from_env')
                ->label('Sinkronkan Pengguna Admin')
                ->icon('heroicon-o-arrow-path')
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading('Sinkronkan Pengguna Admin')
                ->modalDescription('Ini akan menambahkan email admin yang belum ada di database.')
                ->modalSubmitActionLabel('Ya, sinkronkan sekarang')
                ->action(function () {
                    $adminEmails = config('auth.admin_emails', []);
                    $synced = 0;

                    foreach ($adminEmails as $email) {
                        $email = trim($email);                        if (!empty($email)) {
                            $existed = AdminUser::where('email', $email)->exists();

                            if (!$existed) {
                                AdminUser::create([
                                    'email' => $email,
                                    'name' => 'Admin',
                                    'is_active' => true,
                                    'notes' => 'Synced from .env configuration',
                                ]);
                                $synced++;
                            }
                        }
                    }

                    if ($synced > 0) {
                        Notification::make()
                            ->success()
                            ->title('Sync completed')
                            ->body("{$synced} admin user(s) synced from .env")
                            ->send();
                    } else {
                        Notification::make()
                            ->info()
                            ->title('Sudah disinkronkan')
                            ->send();
                    }
                }),
        ];
    }

    public function getHeaderWidgetsColumns(): int | array
    {
        return 1;
    }
}
