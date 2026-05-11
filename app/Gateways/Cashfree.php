<?php

namespace App\Gateways;

use App\Services\Payments\CashfreeClient;

/**
 * Cashfree Payment Gateway — hosted web checkout (see Cashfree docs).
 *
 * Admin credentials JSON:
 * - client_id: App ID from Cashfree dashboard
 * - client_secret: Secret key
 * - env: "sandbox" or "production"
 *
 * Orders restrict payment methods to credit & debit cards only (cc, dc).
 */
class Cashfree extends AbstractGateway
{
    protected CashfreeClient $client;

    public function __construct(
        array $credentials = [],
        ?CashfreeClient $client = null,
    ) {
        parent::__construct($credentials);
        $this->client = $client ?? new CashfreeClient;
    }

    public function label(): string
    {
        return 'Cashfree';
    }

    /**
     * Create a Cashfree order and return a payment_session_id for the JS checkout.
     *
     * Required $meta keys: payment_id, user_id, customer_email, customer_name, customer_phone, return_url
     *
     * @param  array<string, mixed>  $meta
     * @return array<string, mixed>
     */
    public function initiatePayment(string $amount, array $meta = []): array
    {
        $clientId = (string) $this->credential('client_id', '');
        $secret = (string) $this->credential('client_secret', '');

        if ($clientId === '' || $secret === '') {
            return [
                'success' => false,
                'error' => 'Cashfree credentials incomplete. Set client_id and client_secret in Admin → Gateways.',
            ];
        }

        $returnUrl = $meta['return_url'] ?? null;
        if (! is_string($returnUrl) || $returnUrl === '') {
            return [
                'success' => false,
                'error' => 'Missing return_url for Cashfree order.',
            ];
        }

        $env = strtolower((string) $this->credential('env', 'sandbox'));
        $sandbox = $env !== 'production';

        $userId = (int) ($meta['user_id'] ?? 0);
        $paymentId = (int) ($meta['payment_id'] ?? 0);
        $currency = (string) ($meta['currency'] ?? 'INR');
        $email = (string) ($meta['customer_email'] ?? 'customer@example.com');
        $name = $this->normalizeCustomerName((string) ($meta['customer_name'] ?? 'Customer'));
        $phone = $this->normalizeCustomerPhone((string) ($meta['customer_phone'] ?? ''));

        $amountFloat = round((float) $amount, 2);
        if ($amountFloat <= 0) {
            return ['success' => false, 'error' => 'Invalid order amount.'];
        }

        $customerId = 'paybycc_u'.$userId;
        $orderNote = 'PayByCC #'.$paymentId;

        $api = $this->client->createOrder(
            clientId: $clientId,
            clientSecret: $secret,
            sandbox: $sandbox,
            orderAmount: $amountFloat,
            currency: $currency,
            customerId: $customerId,
            customerPhone: $phone,
            customerEmail: $email,
            customerName: $name,
            returnUrl: $returnUrl,
            orderNote: $orderNote,
            paymentMethods: 'cc,dc',
        );

        if (! $api['ok'] || ! isset($api['data']) || ! is_array($api['data'])) {
            return [
                'success' => false,
                'error' => (string) ($api['error'] ?? 'Could not create Cashfree order.'),
                'cashfree_status' => $api['status'] ?? null,
                'cashfree_body' => $api['data'] ?? null,
            ];
        }

        $data = $api['data'];
        $orderId = $data['order_id'] ?? null;
        $sessionId = $data['payment_session_id'] ?? null;

        if (! is_string($orderId) || $orderId === '' || ! is_string($sessionId) || $sessionId === '') {
            return [
                'success' => false,
                'error' => 'Cashfree order response missing order_id or payment_session_id.',
                'cashfree_body' => $data,
            ];
        }

        return [
            'success' => true,
            'mode' => 'cashfree_hosted',
            'cashfree_order_id' => $orderId,
            'reference' => $orderId,
            'payment_session_id' => $sessionId,
            'environment' => $sandbox ? 'sandbox' : 'production',
            'order_amount' => $amountFloat,
            'currency' => $currency,
            'payment_id' => $paymentId,
        ];
    }

    /**
     * @param  array<string, mixed>  $credentials
     */
    public static function isSandboxCredentials(array $credentials): bool
    {
        return strtolower((string) ($credentials['env'] ?? 'sandbox')) !== 'production';
    }

    protected function normalizeCustomerPhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone) ?? '';
        if (strlen($digits) >= 10) {
            return substr($digits, -10);
        }

        return str_pad(substr($digits, 0, 10), 10, '0');
    }

    protected function normalizeCustomerName(string $name): string
    {
        $name = trim($name);

        return strlen($name) >= 3 ? $name : 'PayByCC user';
    }
}
