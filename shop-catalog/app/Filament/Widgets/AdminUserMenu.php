<?php

namespace App\Filament\Widgets;

use App\Http\Controllers\Auth\AdminGoogleController;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class AdminUserMenu extends Widget
{
    protected static string $view = 'filament.widgets.admin-user-menu';

    protected function getViewData(): array
    {
        $user = Auth::user();
        $adminUser = AdminGoogleController::getCurrentAdminUser(request());

        return [
            'user' => $user,
            'adminUser' => $adminUser,
        ];
    }
}