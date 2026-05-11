<?php

namespace App\Services\Payments;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CashfreeClient
{
    public function baseUrl(bool $sandbox): string
    {
        return $sandbox
            ? 'https://sandbox.cashfree.com/pg'
            : 'https://api.cashfree.com/pg';
    }

    /**
     * @return array{ok: bool, data?: array<string, mixed>, status?: int, error?: string}
     */
    public function createOrder(
        string $clientId,
        string $clientSecret,
        bool $sandbox,
        float $orderAmount,
        string $currency,
        string $customerId,
        string $customerPhone,
        string $customerEmail,
        string $customerName,
        string $returnUrl,
        string $orderNote,
        string $paymentMethods = 'cc,dc',
    ): array {
        $payload = [
            'order_amount' => $orderAmount,
            'order_currency' => $currency,
            'customer_details' => array_filter([
                'customer_id' => $customerId,
                'customer_phone' => $customerPhone,
                'customer_email' => $customerEmail,
                'customer_name' => $customerName,
            ]),
            'order_meta' => [
                'return_url' => $returnUrl,
                'payment_methods' => $paymentMethods,
            ],
            'order_note' => $orderNote,
        ];

        return $this->postJson($clientId, $clientSecret, $sandbox, '/orders', $payload);
    }

    /**
     * @return array{ok: bool, data?: array<string, mixed>, status?: int, error?: string}
     */
    public function fetchOrder(
        string $clientId,
        string $clientSecret,
        bool $sandbox,
        string $cashfreeOrderId,
    ): array {
        return $this->getJson($clientId, $clientSecret, $sandbox, '/orders/'.$cashfreeOrderId);
    }

    /**
     * @param  array<string, mixed>  $body
     * @return array{ok: bool, data?: array<string, mixed>, status?: int, error?: string}
     */
    protected function postJson(
        string $clientId,
        string $clientSecret,
        bool $sandbox,
        string $path,
        array $body,
    ): array {
        $response = Http::timeout(30)
            ->withHeaders($this->headers($clientId, $clientSecret))
            ->acceptJson()
            ->asJson()
            ->post($this->baseUrl($sandbox).$path, $body);

        return $this->normalizeResponse($response);
    }

    /**
     * @return array{ok: bool, data?: array<string, mixed>, status?: int, error?: string}
     */
    protected function getJson(
        string $clientId,
        string $clientSecret,
        bool $sandbox,
        string $path,
    ): array {
        $response = Http::timeout(30)
            ->withHeaders($this->headers($clientId, $clientSecret))
            ->acceptJson()
            ->get($this->baseUrl($sandbox).$path);

        return $this->normalizeResponse($response);
    }

    /**
     * @return array<string, string>
     */
    protected function headers(string $clientId, string $clientSecret): array
    {
        return [
            'x-client-id' => $clientId,
            'x-client-secret' => $clientSecret,
            'x-api-version' => (string) config('cashfree.api_version', '2023-08-01'),
        ];
    }

    /**
     * @return array{ok: bool, data?: array<string, mixed>, status?: int, error?: string}
     */
    protected function normalizeResponse(Response $response): array
    {
        $status = $response->status();
        $data = $response->json();

        if (! is_array($data)) {
            Log::warning('cashfree.unexpected_response', ['status' => $status, 'body' => $response->body()]);

            return [
                'ok' => false,
                'status' => $status,
                'error' => 'Invalid response from Cashfree.',
            ];
        }

        if ($status >= 200 && $status < 300) {
            return ['ok' => true, 'data' => $data, 'status' => $status];
        }

        $message = $data['message'] ?? $data['error'] ?? 'Cashfree API error';

        Log::warning('cashfree.api_error', ['status' => $status, 'payload' => $data]);

        return [
            'ok' => false,
            'status' => $status,
            'error' => is_string($message) ? $message : 'Cashfree API error',
            'data' => $data,
        ];
    }
}
