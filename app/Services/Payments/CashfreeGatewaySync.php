<?php

namespace App\Services\Payments;

use App\Models\Gateway;
use Illuminate\Support\Facades\Schema;

class CashfreeGatewaySync
{
    public static function credentialsConfigured(): bool
    {
        $clientId = (string) config('cashfree.client_id', '');
        $secret = (string) config('cashfree.client_secret', '');

        return $clientId !== '' && $secret !== '';
    }

    /**
     * Upsert primary Cashfree gateway from config/cashfree.php (.env).
     */
    public static function sync(): ?Gateway
    {
        if (! Schema::hasTable('gateways') || ! self::credentialsConfigured()) {
            return null;
        }

        $env = strtolower((string) config('cashfree.env', 'sandbox'));
        if (! in_array($env, ['sandbox', 'production'], true)) {
            $env = 'sandbox';
        }

        Gateway::query()->where('is_primary', true)->update(['is_primary' => false]);

        return Gateway::query()->updateOrCreate(
            ['code' => 'cashfree'],
            [
                'name' => 'Cashfree',
                'filename' => 'Cashfree',
                'credentials' => array_filter([
                    'client_id' => (string) config('cashfree.client_id'),
                    'client_secret' => (string) config('cashfree.client_secret'),
                    'env' => $env,
                    'payment_methods' => config('cashfree.payment_methods'),
                ], fn ($v) => $v !== null && $v !== ''),
                'status' => 'active',
                'is_primary' => true,
                'min_txn' => 1,
                'max_txn' => 500000,
                'daily_limit' => 0,
            ]
        );
    }
}
