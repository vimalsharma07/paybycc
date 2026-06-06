<?php

namespace App\Services\Kyc;

readonly class PanVerificationResult
{
    /**
     * @param  array<string, mixed>  $raw
     * @param  array<string, mixed>  $data
     */
    public function __construct(
        public bool $ok,
        public string $message,
        public array $raw = [],
        public array $data = [],
        public bool $fromCache = false,
        public ?string $userMessage = null,
    ) {}

    public function userMessage(): string
    {
        return $this->userMessage ?? $this->message;
    }

    /**
     * @param  array<string, mixed>  $raw
     */
    public static function success(array $raw, array $data, bool $fromCache = false): self
    {
        return new self(
            ok: true,
            message: 'success',
            raw: $raw,
            data: $data,
            fromCache: $fromCache,
        );
    }

    public static function failure(string $message, array $raw = [], ?string $userMessage = null): self
    {
        return new self(
            ok: false,
            message: $message,
            raw: $raw,
            userMessage: $userMessage,
        );
    }
}
