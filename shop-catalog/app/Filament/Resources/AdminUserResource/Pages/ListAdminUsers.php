<?php

namespace App\Filament\Resources\AdminUserResource\Pages;

use App\Filament\Resources\AdminUserResource;
use App\Models\AdminUser;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;

class ListAdminUsers extends ListRecords
{
    protected static string $resource = AdminUserResource::class;

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
                            ->title('Already synced')
                            ->body('All .env admin emails are already in the database')
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
