<?php

namespace App\Filament\Pages\Auth;

use App\Http\Controllers\Auth\AdminGoogleController;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Http\RedirectResponse;

class Logout
{
    public function __invoke(): RedirectResponse
    {
        $controller = new AdminGoogleController();
        return $controller->logout(request());
    }
}
