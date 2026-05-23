<?php

return [

    'length' => (int) env('OTP_LENGTH', 6),
    'ttl_seconds' => (int) env('OTP_TTL', 600),
    'resend_seconds' => (int) env('OTP_RESEND_SECONDS', 60),
    'max_verify_attempts' => (int) env('OTP_MAX_ATTEMPTS', 5),

    /** Max OTP SMS per mobile per calendar day (all purposes combined). 0 = disabled. */
    'daily_sms_limit' => (int) env('OTP_DAILY_SMS_LIMIT', 10),

    /*
    | Rate limits (summary)
    | - Resend: otp.resend_seconds between sends to the same number
    | - Daily: otp.daily_sms_limit SMS per mobile per calendar day (app timezone)
    | - Verify: otp.max_verify_attempts wrong guesses per active code
    | - HTTP: throttle:otp-send / throttle:otp-verify on routes (per IP, see AppServiceProvider)
    |
    |--------------------------------------------------------------------------
    | OTP purposes
    |--------------------------------------------------------------------------
    |
    | Each purpose gets its own cache key and session verification slot.
    | Add new purposes here and use App\Enums\OtpPurpose in code.
    |
    | Message placeholders: {otp}, {app}
    |
    */

    'purposes' => [
        'registration' => [
            'message' => env(
                'OTP_REGISTRATION_MESSAGE',
                'Your verification code for {app} is {otp}. Valid for 10 minutes. Do not share with anyone.'
            ),
        ],
    ],

];
