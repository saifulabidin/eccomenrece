<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdminUserResource\Pages;
use App\Filament\Resources\AdminUserResource\RelationManagers;
use App\Models\AdminUser;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;

class AdminUserResource extends Resource
{
    protected static ?string $model = AdminUser::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'Pengguna Admin';
    protected static ?string $modelLabel = 'Pengguna Admin';
    protected static ?string $pluralModelLabel = 'Pengguna Admin';
    // protected static ?string $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 1;

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
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        $adminUser = AdminUser::where('email', $user->email)->first();
        return $adminUser && $adminUser->isSuperAdmin();
    }
}