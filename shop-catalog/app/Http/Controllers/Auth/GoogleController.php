<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->scopes(['openid', 'profile', 'email'])
            ->redirect();
    }

    /**
     * Obtain the user information from Google.
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Store user data in session for review system
            $request->session()->put('google_user', [
                'id' => $googleUser->getId(),
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'avatar' => $googleUser->getAvatar(),
            ]);

            // Redirect back to the product page
            $redirectTo = $request->session()->get('intended_url', route('home'));
            $request->session()->forget('intended_url');

            return redirect($redirectTo)->with('google_auth_success', 'Berhasil terhubung dengan Akun Google!');

        } catch (\Exception $e) {
            return redirect()->back()->with('google_auth_error', 'Otentikasi Google gagal. Silakan coba lagi.');
        }
    }

    /**
     * Logout from Google authentication.
     */
    public function logout(Request $request)
    {
        $request->session()->forget('google_user');
        return redirect()->back()->with('google_logout_success', 'Berhasil terputus dari  Akun Google.');
    }

    /**
     * Get the current Google user from session.
     */
    public static function getCurrentGoogleUser(Request $request)
    {
        return $request->session()->get('google_user');
    }

    /**
     * Check if user is authenticated with Google.
     */
    public static function isAuthenticated(Request $request)
    {
        return !empty($request->session()->get('google_user'));
    }
}
