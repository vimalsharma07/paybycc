<?php

return [

    'enabled' => env('APP_LOG_DB_ENABLED', true),

    /** Log plain OTP in DB context when true (local debugging only). */
    'log_otp_code' => env('APP_LOG_OTP_CODE', false),

    'channels' => [
        'otp',
        'sms',
        'auth',
        'payment',
        'gateway',
        'order',
        'transaction',
        'wallet',
        'kyc',
        'bank',
        'admin',
        'system',
    ],

];
