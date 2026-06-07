<?php

/**
 * Marketplace platform structure (roles, statuses, page map).
 * See docs/MARKETPLACE_PLATFORM.md for full requirements.
 */

return [

    'roles' => [
        'customer' => 'customer',
        'seller' => 'seller',
        'admin' => 'admin',
    ],

    // orders.* status columns store int codes — see App\Constants\OrderStatuses
    'order_statuses' => [
        0 => 'created', 1 => 'pending', 2 => 'accepted', 3 => 'in_progress',
        4 => 'completed', 5 => 'cancelled', 6 => 'disputed',
    ],

    'payment_statuses' => [
        0 => 'pending', 1 => 'authorized', 2 => 'paid', 3 => 'failed',
        4 => 'refunded', 5 => 'partially_refunded',
    ],

    'settlement_statuses' => [
        0 => 'pending', 1 => 'eligible', 2 => 'settled', 3 => 'failed',
    ],

    'safe_statuses' => [
        0 => 'pending_review', 1 => 'safe', 2 => 'hold', 3 => 'rejected',
    ],

    'transaction_statuses' => [
        0 => 'pending', 1 => 'completed', 2 => 'failed', 3 => 'processing',
    ],

    'settlement_record_statuses' => [
        0 => 'pending', 1 => 'eligible', 2 => 'settled', 3 => 'failed',
    ],

    'marketplace' => [
        // Order fees: config/commerce.php + App\Constants\CommerceConstants
        'min_order_amount' => (float) env('MARKETPLACE_MIN_ORDER_AMOUNT', 1),
        'max_order_amount' => (float) env('MARKETPLACE_MAX_ORDER_AMOUNT', 500000),
    ],

    'payment_links' => [
        'default_expiry_days' => (int) env('PAYMENT_LINK_DEFAULT_EXPIRY_DAYS', 30),
        'max_expiry_days' => (int) env('PAYMENT_LINK_MAX_EXPIRY_DAYS', 90),
        'max_active_per_seller' => (int) env('PAYMENT_LINK_MAX_ACTIVE_PER_SELLER', 50),
        'default_daily_limit' => (float) env('PAYMENT_LINK_DEFAULT_DAILY_LIMIT', 100000),
        'default_monthly_limit' => (float) env('PAYMENT_LINK_DEFAULT_MONTHLY_LIMIT', 300000),
        'default_yearly_limit' => (float) env('PAYMENT_LINK_DEFAULT_YEARLY_LIMIT', 2000000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Page / route map by area (current + planned)
    |--------------------------------------------------------------------------
    */
    'pages' => [
        'public' => ['home', 'about', 'contact', 'privacy', 'terms', 'login', 'register'],
        'customer' => [
            'dashboard',
            'search_freelancers',
            'place_order',
            'profile',
            'kyc',
        ],
        'seller' => [
            'dashboard',
            'payment_links',
            'onboarding',         // planned
            'services',           // planned
            'orders',             // planned
            'settlements',        // planned
            'banks',
            'wallet',
            'kyc',
            'profile',
        ],
        'admin' => [
            'dashboard',
            'users',
            'banks',
            'transactions',
            'logs',
            'gateways',
            'website-settings',
            'orders',
            'services',           // planned
            'service_approvals',  // planned
            'kyc_reviews',        // planned
            'settlements',        // planned
            'compliance_reports', // planned
        ],
    ],

];
