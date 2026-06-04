<?php

return [

    'api_version' => env('CASHFREE_API_VERSION', '2023-08-01'),

    /*
    |--------------------------------------------------------------------------
    | Default gateway credentials (synced to Admin → Gateways on boot/seed)
    |--------------------------------------------------------------------------
    |
    | Get sandbox keys from https://merchant.cashfree.com/merchants/pg/developers
    |
    */

    'client_id' => env('CASHFREE_CLIENT_ID'),

    'client_secret' => env('CASHFREE_CLIENT_SECRET'),

    'env' => env('CASHFREE_ENV', 'sandbox'),

    'auto_sync_gateway' => env('CASHFREE_AUTO_SYNC_GATEWAY', true),

    /*
    |--------------------------------------------------------------------------
    | Order payment methods (optional)
    |--------------------------------------------------------------------------
    |
    | Comma-separated Cashfree codes, e.g. cc,dc,upi,nb,paylater,app
    | Leave empty to show every method enabled on your Cashfree merchant dashboard.
    |
    */

    'payment_methods' => env('CASHFREE_PAYMENT_METHODS'),

];
