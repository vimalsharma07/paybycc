<?php

namespace Database\Seeders;

use App\Models\SellerSubservice;
use App\Models\Service;
use App\Models\Subservice;
use App\Models\User;
use Illuminate\Database\Seeder;

class PlatformSeeder extends Seeder
{
    public function run(): void
    {
        $web = Service::query()->updateOrCreate(
            ['slug' => 'web-development'],
            [
                'name' => 'Web Development',
                'description' => 'Websites, apps, and full-stack projects.',
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        $design = Service::query()->updateOrCreate(
            ['slug' => 'design'],
            [
                'name' => 'Design & Creative',
                'description' => 'UI/UX, branding, and graphics.',
                'is_active' => true,
                'sort_order' => 2,
            ]
        );

        $laravel = Subservice::query()->updateOrCreate(
            ['service_id' => $web->id, 'slug' => 'laravel'],
            ['name' => 'Laravel / PHP', 'is_active' => true, 'sort_order' => 1]
        );

        $wordpress = Subservice::query()->updateOrCreate(
            ['service_id' => $web->id, 'slug' => 'wordpress'],
            ['name' => 'WordPress', 'is_active' => true, 'sort_order' => 2]
        );

        $uiux = Subservice::query()->updateOrCreate(
            ['service_id' => $design->id, 'slug' => 'ui-ux'],
            ['name' => 'UI/UX Design', 'is_active' => true, 'sort_order' => 1]
        );

        $seller = User::query()->updateOrCreate(
            ['email' => 'freelancer@paybycc.test'],
            [
                'user_code' => 'SLR-DEMO001',
                'name' => 'Demo Freelancer',
                'phone' => '9988776655',
                'password' => bcrypt('password'),
                'phone_verified_at' => now(),
                'is_admin' => false,
                'role' => 'seller',
                'company_name' => 'Demo Freelancer Studio',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'kyc_status' => User::KYC_ACTIVE,
                'pan' => 'ABCDE1234F',
                'pan_name' => 'Demo Freelancer',
                'status' => 'active',
                'accept_only_kyc_customers' => false,
            ]
        );

        foreach ([$laravel, $wordpress, $uiux] as $sub) {
            SellerSubservice::query()->updateOrCreate(
                ['user_id' => $seller->id, 'subservice_id' => $sub->id],
                ['is_active' => true]
            );
        }

        User::query()
            ->where('email', 'user@paybycc.test')
            ->update(['role' => 'customer']);

        $this->command?->info('Demo seller: freelancer@paybycc.test / password (code SLR-DEMO001)');
    }
}
