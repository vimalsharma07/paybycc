<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Settlement buffer (days)
    |--------------------------------------------------------------------------
    |
    | After a card payment, we store this many days as the expected settlement
    | trigger time on the transaction record (simple T+N estimate).
    |
    */
    'settlement_buffer_days' => (int) env('SETTLEMENT_BUFFER_DAYS', 2),

    /*
    |--------------------------------------------------------------------------
    | Operations notifications (admin inbox)
    |--------------------------------------------------------------------------
    |
    | When set to a valid email, the app sends internal alerts (e.g. new user
    | signup, new wallet transaction) from your configured MAIL_FROM_* address.
    |
    */
    'ops_notification_email' => env('OPS_NOTIFICATION_EMAIL'),

];
