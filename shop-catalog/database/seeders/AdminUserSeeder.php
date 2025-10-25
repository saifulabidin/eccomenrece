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
        // Get admin emails from .env
        $adminEmails = explode(',', env('ADMIN_EMAILS', ''));
        
        foreach ($adminEmails as $email) {
            $email = trim($email);
            
            if (!empty($email)) {
                AdminUser::firstOrCreate(
                    ['email' => $email],
                    [
                        'name' => 'Admin',
                        'is_active' => true,
                        'notes' => 'Initial admin from .env configuration',
                    ]
                );
            }
        }
        
        $this->command->info('Admin users seeded successfully!');
    }
}
