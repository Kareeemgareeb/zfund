<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Beneficiary;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class BeneficiaryTestSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'test@zakat.ly'],
            [
                'username' => 'test_user',
                'password' => Hash::make('password'),
                'status' => 'ACTIVE'
            ]
        );

        if (!$user->beneficiary) {
            Beneficiary::create([
                'user_id' => $user->id,
                'file_number' => 'ZAK-2026-001',
                'full_name' => 'أحمد محمد علي',
                'gender' => 'MALE',
                'birth_date' => '1980-05-15',
                'marital_status' => 'MARRIED',
                'family_members_count' => 5,
                'city' => 'Sirte',
                'address' => 'حي الزعفران، سرت',
                'eligibility_status' => 'VERIFIED',
                'next_renewal_due' => Carbon::now()->subDays(10)->format('Y-m-d')
            ]);
        }
    }
}