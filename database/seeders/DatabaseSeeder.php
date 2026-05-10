<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'user_code' => 'ADMIN00001',
            'name' => 'Administrator',
            'email' => 'admin@paybycc.test',
            'phone' => '9876543210',
            'is_admin' => true,
            'kyc_status' => User::KYC_ACTIVE,
        ]);

        User::factory()->create([
            'name' => 'Demo User',
            'email' => 'user@paybycc.test',
            'phone' => '9123456789',
            'is_admin' => false,
            'kyc_status' => User::KYC_INCOMPLETE,
        ]);
    }
}
