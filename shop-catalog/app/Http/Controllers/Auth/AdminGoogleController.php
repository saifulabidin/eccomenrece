<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class AdminGoogleController extends Controller
{
    /**
     * Redirect the user to the Google authentication page for admin login.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->redirectUrl(config('app.url') . '/admin/auth/google/callback')
            ->scopes(['openid', 'profile', 'email'])
            ->redirect();
    }

    /**
     * Handle the Google authentication callback for admin login.
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')
                ->redirectUrl(config('app.url') . '/admin/auth/google/callback')
                ->user();

            // Check if user's email is authorized for admin access
            // Check both database and .env
            $isAdminInDb = AdminUser::isAuthorizedEmail($googleUser->getEmail());
            $adminEmails = explode(',', env('ADMIN_EMAILS', ''));
            $isAdminInEnv = in_array($googleUser->getEmail(), $adminEmails);
            
            if (!$isAdminInDb && !$isAdminInEnv) {
                return redirect()->route('admin.login')->with('error', 'Bro Berfikir Bisa Login? Tidak Semudah Itu Ferguso');
            }

            // Find or create user
            $user = User::findByGoogleId($googleUser->getId());

            if (!$user) {
                // Check if user exists with same email
                $user = User::where('email', $googleUser->getEmail())->first();

                if ($user) {
                    // Update existing user with Google data
                    $user->update([
                        'google_id' => $googleUser->getId(),
                        'google_name' => $googleUser->getName(),
                        'google_avatar' => $googleUser->getAvatar(),
                        'google_token' => $googleUser->token,
                        'google_refresh_token' => $googleUser->refreshToken,
                        'google_expires_in' => now()->addSeconds($googleUser->expiresIn),
                        'is_admin' => true, // Authorized admin
                    ]);
                } else {
                    // Create new user
                    $user = User::create([
                        'name' => $googleUser->getName(),
                        'email' => $googleUser->getEmail(),
                        'google_id' => $googleUser->getId(),
                        'google_name' => $googleUser->getName(),
                        'google_avatar' => $googleUser->getAvatar(),
                        'google_token' => $googleUser->token,
                        'google_refresh_token' => $googleUser->refreshToken,
                        'google_expires_in' => now()->addSeconds($googleUser->expiresIn),
                        'is_admin' => true,
                        'password' => bcrypt(\Illuminate\Support\Str::random(16)),
                    ]);
                }
            } else {
                // Update existing Google user
                $user->update([
                    'google_name' => $googleUser->getName(),
                    'google_avatar' => $googleUser->getAvatar(),
                    'google_token' => $googleUser->token,
                    'google_refresh_token' => $googleUser->refreshToken,
                    'google_expires_in' => now()->addSeconds($googleUser->expiresIn),
                ]);
            }

            // Login the user
            Auth::login($user, true);

            // Store admin session data
            session([
                'admin_google_user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->google_avatar,
                ],
                'admin_login_time' => now(),
            ]);

            return redirect()->route('filament.admin.pages.dashboard');

        } catch (\Exception $e) {
            \Log::error('Admin Google authentication error: ' . $e->getMessage());
            return redirect()->route('admin.login')->with('error', 'Tanya kan pada developer yang tamvan dan pemberani !');
        }
    }

    /**
     * Handle admin logout.
     */
    public function logout(Request $request)
    {
        // Clear admin session
        $request->session()->forget(['admin_google_user', 'admin_login_time']);

        // Logout user
        Auth::logout();

        // Clear all session data
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Anda telah berhasil logout hehe ');
    }

    /**
     * Get current admin user from session.
     */
    public static function getCurrentAdminUser(Request $request)
    {
        return $request->session()->get('admin_google_user');
    }

    /**
     * Check if current user is authenticated admin.
     */
    public static function isAuthenticatedAdmin(Request $request): bool
    {
        return $request->session()->has('admin_google_user') &&
               $request->session()->has('admin_login_time') &&
               Auth::check() &&
               Auth::user()->isAdmin();
    }
}