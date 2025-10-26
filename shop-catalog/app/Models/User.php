<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'google_name',
        'google_avatar',
        'google_token',
        'google_refresh_token',
        'is_admin',
        'google_expires_in',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'google_token',
        'google_refresh_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'google_expires_in' => 'datetime',
        ];
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    /**
     * Determine if user can access Filament admin panel
     * Required by FilamentUser interface
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // Allow if user is marked as admin AND email is authorized
        if (!$this->isAdmin()) {
            return false;
        }

        // Double check authorization (database or config)
        return $this->isAuthorizedAdmin();
    }

    /**
     * Check if user is super admin
     */
    public function isSuperAdmin(): bool
    {
        if (!$this->isAdmin()) {
            return false;
        }

        // Check if email is in super admin config
        $superAdminEmails = config('auth.admin_emails', []);
        return in_array($this->email, $superAdminEmails);
    }

    /**
     * Get user's admin role
     */
    public function getAdminRole(): ?string
    {
        if (!$this->isAdmin()) {
            return null;
        }

        // Check if super admin
        if ($this->isSuperAdmin()) {
            return 'super_admin';
        }

        // Check database for role
        $adminUser = AdminUser::where('email', $this->email)
            ->where('is_active', true)
            ->first();

        return $adminUser?->role ?? 'admin';
    }

    /**
     * Check if user's email is in admin emails list
     * Checks both config and database
     */
    public function isAuthorizedAdmin(): bool
    {
        // Check database first
        if (AdminUser::isAuthorizedEmail($this->email)) {
            return true;
        }

        // Fallback to config
        $adminEmails = config('auth.admin_emails', []);
        return in_array($this->email, $adminEmails);
    }

    /**
     * Find user by Google ID
     */
    public static function findByGoogleId(string $googleId): ?User
    {
        return static::where('google_id', $googleId)->first();
    }

    /**
     * Create user from Google data
     */
    public static function createFromGoogle(array $googleUser): User
    {
        // Check database first
        $isAdminInDb = AdminUser::isAuthorizedEmail($googleUser['email']);
        
        // Fallback to config
        $adminEmails = config('auth.admin_emails', []);
        $isAdminInEnv = in_array($googleUser['email'], $adminEmails);
        
        $isAdmin = $isAdminInDb || $isAdminInEnv;

        return static::create([
            'name' => $googleUser['name'],
            'email' => $googleUser['email'],
            'google_id' => $googleUser['id'],
            'google_name' => $googleUser['name'],
            'google_avatar' => $googleUser['avatar'] ?? null,
            'google_token' => $googleUser['token'] ?? null,
            'google_refresh_token' => $googleUser['refresh_token'] ?? null,
            'google_expires_in' => $googleUser['expires_in'] ?? null,
            'is_admin' => $isAdmin,
            'password' => bcrypt(\Illuminate\Support\Str::random(16)), // Random password for Google users
        ]);
    }
}
