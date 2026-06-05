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

    'order_statuses' => [
        'created', 'pending', 'accepted', 'in_progress', 'completed', 'cancelled', 'disputed',
    ],

    'payment_statuses' => [
        'pending', 'authorized', 'paid', 'failed', 'refunded', 'partially_refunded',
    ],

    'settlement_statuses' => [
        'pending', 'eligible', 'settled', 'failed',
    ],

    'safe_statuses' => [
        'pending_review', 'safe', 'hold', 'rejected',
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
