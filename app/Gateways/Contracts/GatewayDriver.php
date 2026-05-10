<?php

namespace App\Gateways\Contracts;

interface GatewayDriver
{
    /**
     * Human-readable label for logs / UI.
     */
    public function label(): string;

    /**
     * Start a payment: redirect URL, client token, or inline instructions.
     *
     * @param  array<string, mixed>  $meta  Must include payment id, user id, amount.
     * @return array<string, mixed>
     */
    public function initiatePayment(string $amount, array $meta = []): array;
}
