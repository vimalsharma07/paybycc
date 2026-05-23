<?php

namespace App\Contracts;

interface SmsSender
{
    /**
     * @return array{ok: bool, error?: string}
     */
    public function send(string $mobile, string $message): array;
}
