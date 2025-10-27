<?php

namespace App\Filament\Resources\AdminUserResource\Pages;

use App\Filament\Resources\AdminUserResource;
use App\Models\AdminUser;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms;
use Filament\Forms\Form;

class EditAdminUser extends EditRecord
{
    protected static string $resource = AdminUserResource::class;

    public function form(Form $form): Form
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
                                        $superAdminEmails = config('auth.admin_emails', []);

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

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
