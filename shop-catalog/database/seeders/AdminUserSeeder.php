<?php

namespace Database\Seeders;

use App\Models\AdminUser;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin emails from config
        $adminEmails = config('auth.admin_emails', []);
        
        foreach ($adminEmails as $email) {
            $email = trim($email);
            
            if (!empty($email)) {
                AdminUser::updateOrCreate(
                    ['email' => $email],
                    [
                        'name' => 'Super Admin',
                        'role' => 'super_admin', // Set as super admin
                        'is_active' => true,
                        'notes' => 'Super admin from .env configuration',
                    ]
                );
            }
        }
        
        $this->command->info('Admin users seeded successfully!');
    }
}
