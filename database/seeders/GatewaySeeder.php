<?php

namespace Database\Seeders;

use App\Gateways\Cashfree;
use App\Models\Gateway;
use Illuminate\Database\Seeder;

class GatewaySeeder extends Seeder
{
    public function run(): void
    {
        if (Gateway::query()->where('code', Cashfree::CODE)->exists()) {
            $this->command?->info('Cashfree gateway row already exists — skipped.');

            return;
        }

        $hasPrimary = Gateway::query()->where('is_primary', true)->exists();

        Gateway::query()->create([
            'name' => 'Cashfree',
            'code' => Cashfree::CODE,
            'filename' => 'Cashfree',
            'credentials' => Cashfree::defaultCredentials(),
            'status' => 'inactive',
            'is_primary' => ! $hasPrimary,
            'min_txn' => 1,
            'max_txn' => 500000,
            'daily_limit' => 0,
        ]);

        $this->command?->info('Cashfree gateway row created. Set credentials in Admin → Gateways and mark active + primary.');
    }
}
