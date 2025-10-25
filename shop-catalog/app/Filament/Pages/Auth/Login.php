<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Illuminate\Http\Request;

class Login extends BaseLogin
{
    public function mount(): void
    {
        // Redirect to our custom Google admin login
        $this->redirect(route('admin.login'));
    }

    public function authenticate(): ?LoginResponse
    {
        // This method should not be called as we redirect to Google auth
        return null;
    }
}