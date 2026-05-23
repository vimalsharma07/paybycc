<?php

namespace App\Services\Otp;

readonly class OtpResult
{
    public function __construct(
        public bool $success,
        public string $message,
        public ?int $retryAfter = null,
    ) {}

    public static function ok(string $message): self
    {
        return new self(true, $message);
    }

    public static function fail(string $message, ?int $retryAfter = null): self
    {
        return new self(false, $message, $retryAfter);
    }
}
