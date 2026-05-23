<?php

namespace App\Services\Sms;

use App\Contracts\SmsSender;
use Illuminate\Support\Facades\Log;

class LogSmsSender implements SmsSender
{
    /**
     * @return array{ok: bool, error?: string}
     */
    public function send(string $mobile, string $message): array
    {
        Log::info('SMS (log driver)', ['mobile' => $mobile, 'message' => $message]);

        return ['ok' => true];
    }
}
