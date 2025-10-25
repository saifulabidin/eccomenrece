<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Auth\AdminGoogleController;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminGoogleAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user is authenticated as admin
        if (!AdminGoogleController::isAuthenticatedAdmin($request)) {
            // Store the intended URL
            session()->put('admin_intended_url', $request->fullUrl());

            // Redirect to admin login page
            return redirect()->route('admin.login')
                ->with('error', 'Silakan login dengan akun Google Anda untuk mengakses admin panel.');
        }

        // Check if user is still an admin (email authorization)
        if (!Auth::user()->isAuthorizedAdmin()) {
            // Logout user immediately
            Auth::logout();
            session()->forget(['admin_google_user', 'admin_login_time', 'admin_intended_url']);

            return redirect()->route('admin.login')
                ->with('error', 'Akses admin Anda telah dicabut. Silakan hubungi administrator.');
        }

        // Optional: Check session timeout (24 hours)
        $loginTime = session('admin_login_time');
        if ($loginTime && now()->diffInHours($loginTime) > 24) {
            // Force re-authentication
            session()->forget(['admin_google_user', 'admin_login_time']);
            Auth::logout();

            return redirect()->route('admin.login')
                ->with('error', 'Sesi admin telah kedaluwarsa. Silakan login kembali.');
        }

        return $next($request);
    }
}