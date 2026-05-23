<?php

return [

    /*
    |--------------------------------------------------------------------------
    | SMS driver
    |--------------------------------------------------------------------------
    |
    | apitxt — https://apitxt.com/api/sendMsg
    | log    — log message + OTP (local / staging)
    |
    */

    'driver' => env('SMS_DRIVER', 'apitxt'),

    'apitxt' => [
        'url' => env('SMS_API_URL', 'https://apitxt.com/api/sendMsg'),
        'authkey' => env('SMS_AUTHKEY'),
        'sender' => env('SMS_SENDER'),
        'route' => env('SMS_ROUTE', '4'),
        'template_id' => env('SMS_TEMPLATE_ID'),
        'pe_id' => env('SMS_PE_ID'),
    ],

];
