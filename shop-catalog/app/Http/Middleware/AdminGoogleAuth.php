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
        // Check if user is authenticated via Laravel Auth
        if (!Auth::check()) {
            session()->put('admin_intended_url', $request->fullUrl());
            return redirect()->route('admin.login')
                ->with('error', 'Silakan login dengan akun Google Anda untuk mengakses admin panel.');
        }

        // Check if user has admin access
        $user = Auth::user();
        if (!$user->is_admin) {
            Auth::logout();
            session()->flush();
            return redirect()->route('admin.login')
                ->with('error', 'Akses admin Anda telah dicabut. Silakan hubungi administrator.');
        }

        return $next($request);
    }
}