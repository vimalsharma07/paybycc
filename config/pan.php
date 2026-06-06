<?php

return [

    /*
    |--------------------------------------------------------------------------
    | PAN verification (apitxt.com)
    |--------------------------------------------------------------------------
    |
    | https://apitxt.com/api/panVerify — 1 credit per successful lookup.
    | Reuses SMS_AUTHKEY when PAN_VERIFY_AUTHKEY is not set.
    |
    */

    'apitxt' => [
        'url' => env('PAN_VERIFY_API_URL', 'https://apitxt.com/api/panVerify'),
        'authkey' => env('PAN_VERIFY_AUTHKEY', env('SMS_AUTHKEY')),
    ],

];
