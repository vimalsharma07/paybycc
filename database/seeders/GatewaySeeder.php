<?php

namespace Database\Seeders;

use App\Services\Payments\CashfreeGatewaySync;
use Illuminate\Database\Seeder;

class GatewaySeeder extends Seeder
{
    public function run(): void
    {
        if (! CashfreeGatewaySync::credentialsConfigured()) {
            $this->command?->warn('Skipping gateway seed: set CASHFREE_CLIENT_ID and CASHFREE_CLIENT_SECRET in .env');

            return;
        }

        CashfreeGatewaySync::sync();
        $this->command?->info('Cashfree gateway synced as primary.');
    }
}
