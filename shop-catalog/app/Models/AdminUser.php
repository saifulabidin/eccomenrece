<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminUser extends Model
{
    protected $fillable = [
        'email',
        'name',
        'role',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Check if user is super admin (email in .env)
     */
    public function isSuperAdmin(): bool
    {
        $superAdminEmails = explode(',', env('ADMIN_EMAILS', ''));
        $superAdminEmails = array_map('trim', $superAdminEmails);
        return in_array($this->email, $superAdminEmails) && $this->role === 'super_admin';
    }

    /**
     * Check if email is authorized admin
     */
    public static function isAuthorizedEmail(string $email): bool
    {
        return self::where('email', $email)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Get all active admin emails
     */
    public static function getActiveEmails(): array
    {
        return self::where('is_active', true)
            ->pluck('email')
            ->toArray();
    }
}
