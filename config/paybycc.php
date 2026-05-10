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

];
