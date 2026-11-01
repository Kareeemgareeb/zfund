<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure the admin role exists
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            ['display_name' => 'مدير النظام']
        );

        // 2. Read admin credentials from environment or use default values
        $email = env('ADMIN_EMAIL', 'admin@zfund.ly');
        $username = env('ADMIN_USERNAME', 'admin');
        $nationalId = env('ADMIN_NATIONAL_ID', '119900000000');
        $phone = env('ADMIN_PHONE', '0910000000');
        $password = env('ADMIN_PASSWORD', 'Admin@123456');

        // 3. Create or update the admin account (idempotent)
        $admin = User::updateOrCreate(
            ['email' => $email],
            [
                'national_id' => $nationalId,
                'username' => $username,
                'phone_number' => $phone,
                'password' => Hash::make($password),
                'status' => 'ACTIVE',
            ]
        );

        // 4. Attach admin role
        $admin->roles()->syncWithoutDetaching([$adminRole->id]);

        if (isset($this->command)) {
            $this->command->info("Admin user created/updated successfully:");
            $this->command->line(" - Email:    {$email}");
            $this->command->line(" - Username: {$username}");
            $this->command->line(" - Password: {$password}");
        }
    }
}
