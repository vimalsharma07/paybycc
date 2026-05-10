<?php

namespace App\Gateways;

/**
 * Wire this class to the Cashfree PHP SDK / REST API.
 * Store keys in the gateway credentials JSON (e.g. client_id, client_secret, env).
 */
class Cashfree extends AbstractGateway
{
    public function label(): string
    {
        return 'Cashfree';
    }

    public function initiatePayment(string $amount, array $meta = []): array
    {
        $clientId = $this->credential('client_id');
        $secret = $this->credential('client_secret');

        if (! $clientId || ! $secret) {
            return [
                'success' => false,
                'error' => 'Cashfree credentials incomplete. Set client_id and client_secret in Admin → Gateways.',
            ];
        }

        // Replace with real order/session creation using Cashfree SDK.
        return [
            'success' => true,
            'mode' => 'stub',
            'message' => 'Cashfree driver is connected. Replace initiatePayment() with SDK calls (create order / payment session).',
            'amount' => $amount,
            'currency' => $meta['currency'] ?? 'INR',
            'payment_id' => $meta['payment_id'] ?? null,
            'reference' => 'CF-STUB-'.($meta['payment_id'] ?? uniqid()),
        ];
    }
}
