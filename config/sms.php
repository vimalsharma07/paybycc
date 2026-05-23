<?php

return [

    /*
    |--------------------------------------------------------------------------
    | SMS driver
    |--------------------------------------------------------------------------
    |
    | apitxt — https://apitxt.com/api/sendOTP (Unified OTP API)
    | log    — no real send; writes to application logs
    |
    */

    'driver' => env('SMS_DRIVER', 'apitxt'),

    'apitxt' => [
        'url' => env('SMS_API_URL', 'https://apitxt.com/api/sendOTP'),
        'authkey' => env('SMS_AUTHKEY'),
        /** sms (default), whatsapp, voice */
        'channel' => env('SMS_CHANNEL', 'sms'),
        'country' => env('SMS_COUNTRY', '91'),
        /**
         * Optional. Dashboard → SMS Templates internal ID.
         * If empty, apitxt uses the system default SMS OTP config.
         */
        'template_id' => env('SMS_TEMPLATE_ID'),
    ],

];
