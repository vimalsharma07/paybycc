<?php

namespace App\Contracts;

interface SmsSender
{
    /**
     * Send an OTP to a mobile number.
     *
     * @return array{ok: bool, error?: string, request_id?: string}
     */
    public function sendOtp(string $mobile, string $otp): array;
}
