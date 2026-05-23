<?php

return [

    'length' => (int) env('OTP_LENGTH', 6),
    'ttl_seconds' => (int) env('OTP_TTL', 600),
    'resend_seconds' => (int) env('OTP_RESEND_SECONDS', 60),
    'max_verify_attempts' => (int) env('OTP_MAX_ATTEMPTS', 5),

    /** How long a verified OTP row can be used to complete registration (minutes). */
    'verification_valid_minutes' => (int) env('OTP_VERIFICATION_VALID_MINUTES', 30),

    /** Max OTP SMS per mobile per calendar day (all purposes combined). 0 = disabled. */
    'daily_sms_limit' => (int) env('OTP_DAILY_SMS_LIMIT', 10),

    /*
    |--------------------------------------------------------------------------
    | OTP purposes
    |--------------------------------------------------------------------------
    |
    | Stored in otp_verify table (phone, purpose, status, etc.).
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
