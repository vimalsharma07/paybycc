<?php

namespace App\Gateways;

use App\Gateways\Contracts\DefinesGatewayCredentials;
use App\Gateways\Contracts\GatewayDriver;
use App\Gateways\Contracts\HandlesPaymentReturn;
use App\Gateways\Contracts\HandlesPaymentWebhook;
use App\Gateways\Contracts\HostedCheckoutGateway;
use App\Enums\LogLevel;
use App\Models\Payment;
use App\Models\Transaction;
use App\Services\Payments\CashfreeClient;
use App\Services\Payments\GatewayReqResService;
use App\Services\Payments\PaymentCompletionService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Cashfree Payment Gateway — hosted web checkout.
 *
 * Credentials (Admin → Gateways JSON):
 * - client_id (required), client_secret (required)
 * - env: sandbox|production (default sandbox)
 * - api_version (default 2023-08-01)
 * - payment_methods (optional, e.g. cc,dc,upi,nb)
 */
class Cashfree extends AbstractGateway implements DefinesGatewayCredentials, GatewayDriver, HandlesPaymentReturn, HandlesPaymentWebhook, HostedCheckoutGateway
{
    public const CODE = 'cashfree';

    public const HOSTED_MODE = 'cashfree_hosted';

    public const DEFAULT_API_VERSION = '2023-08-01';

    public const DEFAULT_ENV = 'sandbox';

    protected CashfreeClient $client;

    public function __construct(
        array $credentials = [],
        ?CashfreeClient $client = null,
        protected ?PaymentCompletionService $completion = null,
        protected ?GatewayReqResService $reqRes = null,
    ) {
        parent::__construct($credentials);
        $this->client = $client ?? new CashfreeClient;
        $this->completion ??= app(PaymentCompletionService::class);
        $this->reqRes ??= app(GatewayReqResService::class);
    }

    public static function gatewayCode(): string
    {
        return self::CODE;
    }

    /**
     * @return array<string, string>
     */
    public static function defaultCredentials(): array
    {
        return [
            'client_id' => '',
            'client_secret' => '',
            'env' => self::DEFAULT_ENV,
            'api_version' => self::DEFAULT_API_VERSION,
            'payment_methods' => '',
        ];
    }

    /**
     * @param  array<string, mixed>  $credentials
     */
    public static function credentialsComplete(array $credentials): bool
    {
        $merged = array_merge(self::defaultCredentials(), $credentials);

        return trim((string) ($merged['client_id'] ?? '')) !== ''
            && trim((string) ($merged['client_secret'] ?? '')) !== '';
    }

    public function label(): string
    {
        return 'Cashfree';
    }

    public function returnUrl(Payment $payment): string
    {
        return route('gateways.return', [
            'gateway' => self::CODE,
            'pid' => $payment->id,
        ], true);
    }

    /**
     * @param  array<string, mixed>  $meta
     * @return array<string, mixed>
     */
    public function initiatePayment(string $amount, array $meta = []): array
    {
        $txnRowId = (int) ($meta['transaction_row_id'] ?? 0);
        $txnRef = (string) ($meta['transaction_id'] ?? '');
        $paymentId = (int) ($meta['payment_id'] ?? 0);
        $userId = (int) ($meta['user_id'] ?? 0);
        $orderNote = 'PayByCC #'.($txnRef !== '' ? $txnRef : $txnRowId);

        $this->logGateway('cashfree.initiate.start', 'Creating Cashfree hosted order', [
            'gateway_code' => self::CODE,
            'payment_id' => $paymentId,
            'user_id' => $userId,
            'amount' => (float) $amount,
            'environment' => self::isSandboxCredentials($this->credentials) ? 'sandbox' : 'production',
        ], null, LogLevel::Debug);

        $clientId = (string) $this->credential('client_id', '');
        $secret = (string) $this->credential('client_secret', '');

        if ($clientId === '' || $secret === '') {
            $this->logGateway('cashfree.initiate.failed', 'Cashfree credentials incomplete', [
                'gateway_code' => self::CODE,
                'payment_id' => $paymentId,
                'reason' => 'missing_credentials',
            ], null, LogLevel::Warning);

            return [
                'success' => false,
                'error' => 'Cashfree credentials incomplete. Set client_id and client_secret in Admin → Gateways.',
            ];
        }

        $returnUrl = $meta['return_url'] ?? null;
        if (! is_string($returnUrl) || $returnUrl === '') {
            $this->logGateway('cashfree.initiate.failed', 'Missing return URL for Cashfree order', [
                'gateway_code' => self::CODE,
                'payment_id' => $paymentId,
                'reason' => 'missing_return_url',
            ], null, LogLevel::Warning);

            return [
                'success' => false,
                'error' => 'Missing return_url for Cashfree order.',
            ];
        }

        $sandbox = self::isSandboxCredentials($this->credentials);
        $currency = (string) ($meta['currency'] ?? 'INR');
        $email = (string) ($meta['customer_email'] ?? 'customer@example.com');
        $name = $this->normalizeCustomerName((string) ($meta['customer_name'] ?? 'Customer'));
        $phone = $this->normalizeCustomerPhone((string) ($meta['customer_phone'] ?? ''));

        $amountFloat = round((float) $amount, 2);
        if ($amountFloat <= 0) {
            $this->logGateway('cashfree.initiate.failed', 'Invalid order amount for Cashfree', [
                'gateway_code' => self::CODE,
                'payment_id' => $paymentId,
                'amount' => $amountFloat,
                'reason' => 'invalid_amount',
            ], null, LogLevel::Warning);

            return ['success' => false, 'error' => 'Invalid order amount.'];
        }

        $paymentMethods = trim((string) $this->credential('payment_methods', ''));

        $reqPayload = [
            'gateway' => self::CODE,
            'action' => 'create_order',
            'transaction_id' => $txnRef !== '' ? $txnRef : $txnRowId,
            'order_amount' => $amountFloat,
            'order_currency' => $currency,
            'customer_id' => 'paybycc_u'.$userId,
            'customer_phone' => $phone,
            'customer_email' => $email,
            'customer_name' => $name,
            'return_url' => $returnUrl,
            'order_note' => $orderNote,
            'payment_methods' => $paymentMethods !== '' ? $paymentMethods : null,
            'environment' => $sandbox ? 'sandbox' : 'production',
        ];

        $api = $this->client->createOrder(
            clientId: $clientId,
            clientSecret: $secret,
            sandbox: $sandbox,
            apiVersion: $this->apiVersion(),
            orderAmount: $amountFloat,
            currency: $currency,
            customerId: 'paybycc_u'.$userId,
            customerPhone: $phone,
            customerEmail: $email,
            customerName: $name,
            returnUrl: $returnUrl,
            orderNote: $orderNote,
            paymentMethods: $paymentMethods !== '' ? $paymentMethods : null,
        );

        if (! $api['ok'] || ! isset($api['data']) || ! is_array($api['data'])) {
            $this->logGateway('cashfree.initiate.failed', 'Cashfree create order API error', array_merge(
                $this->flow()->gatewayApiContext($api),
                ['gateway_code' => self::CODE, 'payment_id' => $paymentId],
            ), null, LogLevel::Warning);

            $this->reqRes->store($txnRowId ?: null, $reqPayload, $this->reqRes->normalizeApiResponse($api), null, 'failed');

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
            $this->logGateway('cashfree.initiate.failed', 'Cashfree response missing order_id or payment_session_id', [
                'gateway_code' => self::CODE,
                'payment_id' => $paymentId,
                'reason' => 'invalid_api_response',
            ], null, LogLevel::Warning);

            return [
                'success' => false,
                'error' => 'Cashfree order response missing order_id or payment_session_id.',
                'cashfree_body' => $data,
            ];
        }

        $this->logGateway('cashfree.initiate.success', 'Cashfree hosted session created', [
            'gateway_code' => self::CODE,
            'payment_id' => $paymentId,
            'gateway_reference' => $orderId,
            'driver_mode' => self::HOSTED_MODE,
            'environment' => $sandbox ? 'sandbox' : 'production',
        ]);

        if ($txnRowId > 0 && isset($data['cf_order_id'])) {
            Transaction::query()->whereKey($txnRowId)->update([
                'gateway_id' => (string) $data['cf_order_id'],
            ]);
        }

        $this->reqRes->store($txnRowId ?: null, $reqPayload, $this->reqRes->normalizeApiResponse($api), null, 'success');

        return [
            'success' => true,
            'mode' => self::HOSTED_MODE,
            'cashfree_order_id' => $orderId,
            'cf_order_id' => $data['cf_order_id'] ?? null,
            'reference' => $orderId,
            'payment_session_id' => $sessionId,
            'environment' => $sandbox ? 'sandbox' : 'production',
            'order_amount' => $amountFloat,
            'currency' => $currency,
            'payment_id' => $paymentId,
        ];
    }

    public function handleReturn(Request $request, Payment $payment): Payment
    {
        $payment->loadMissing('order');

        if ($payment->status === 'completed' || $payment->status === 'failed') {
            $this->logGatewayForPayment($payment, 'cashfree.return.skip', 'Return sync skipped — payment already finalized', [
                'reason' => 'already_finalized',
            ], LogLevel::Debug);

            return $payment->fresh(['gateway', 'order']) ?? $payment;
        }

        $payload = $payment->driver_payload ?? [];
        if (! is_array($payload) || ($payload['mode'] ?? '') !== self::HOSTED_MODE) {
            $this->logGatewayForPayment($payment, 'cashfree.return.skip', 'Return sync skipped — not a Cashfree hosted payment', [
                'reason' => 'not_hosted_mode',
            ], LogLevel::Debug);

            return $payment;
        }

        $clientId = (string) $this->credential('client_id', '');
        $secret = (string) $this->credential('client_secret', '');
        $orderId = $payment->gateway_reference;

        if ($clientId === '' || $secret === '' || ! is_string($orderId) || $orderId === '') {
            $this->logGatewayForPayment($payment, 'cashfree.return.skip', 'Return sync skipped — missing credentials or gateway reference', [
                'reason' => 'missing_credentials_or_reference',
            ], LogLevel::Warning);

            return $payment;
        }

        $this->logGatewayForPayment($payment, 'cashfree.return.sync', 'Fetching Cashfree order status', [], LogLevel::Debug);

        $reqPayload = [
            'gateway' => self::CODE,
            'action' => 'fetch_order',
            'payment_id' => $payment->id,
            'order_id' => $orderId,
        ];

        $api = $this->client->fetchOrder(
            $clientId,
            $secret,
            self::isSandboxCredentials($this->credentials),
            $this->apiVersion(),
            $orderId,
        );

        if (! $api['ok'] || ! isset($api['data']) || ! is_array($api['data'])) {
            $this->logGatewayForPayment($payment, 'cashfree.return.api_failed', 'Cashfree fetch order API error', $this->flow()->gatewayApiContext($api), LogLevel::Warning);
            $this->reqRes->store(
                $this->reqRes->transactionIdForPayment($payment),
                $reqPayload,
                $this->reqRes->normalizeApiResponse($api),
                null,
                'api_failed',
            );

            return $payment->fresh(['gateway', 'order']) ?? $payment;
        }

        $data = $api['data'];
        $txn = $payment->transactions()->orderBy('id')->first();
        if ($txn && ($txn->gateway_id === null || $txn->gateway_id === '') && isset($data['cf_order_id'])) {
            $txn->update(['gateway_id' => (string) $data['cf_order_id']]);
        }

        $orderStatus = strtoupper((string) ($data['order_status'] ?? ''));
        $orderAmount = isset($data['order_amount']) ? (float) $data['order_amount'] : null;

        if ($orderAmount !== null && abs($orderAmount - (float) $payment->amount) > 0.02) {
            report(new \RuntimeException('Cashfree order amount mismatch for payment '.$payment->id));
            $this->logGatewayForPayment($payment, 'cashfree.return.amount_mismatch', 'Gateway amount mismatch — sync aborted', array_merge(
                $this->flow()->gatewayApiContext($api),
                ['expected_amount' => (float) $payment->amount],
            ), LogLevel::Warning);

            return $payment;
        }

        if ($orderStatus === 'PAID') {
            $this->logGatewayForPayment($payment, 'cashfree.return.paid', 'Cashfree reported order PAID — finalizing payment', $this->flow()->gatewayApiContext($api));

            $payment = $this->completion->complete($payment, $data);
            $this->reqRes->store(
                $this->reqRes->transactionIdForPayment($payment),
                $reqPayload,
                $this->reqRes->normalizeApiResponse($api),
                null,
                $orderStatus,
            );

            return $payment;
        }

        if (in_array($orderStatus, ['EXPIRED', 'TERMINATED'], true)) {
            $this->logGatewayForPayment($payment, 'cashfree.return.failed', 'Cashfree reported payment '.$orderStatus, $this->flow()->gatewayApiContext($api), LogLevel::Warning);

            $payment = $this->completion->fail($payment);
            $this->reqRes->store(
                $this->reqRes->transactionIdForPayment($payment),
                $reqPayload,
                $this->reqRes->normalizeApiResponse($api),
                null,
                $orderStatus,
            );

            return $payment;
        }

        $this->logGatewayForPayment($payment, 'cashfree.return.pending', 'Cashfree order still pending', $this->flow()->gatewayApiContext($api), LogLevel::Debug);
        $this->reqRes->store(
            $this->reqRes->transactionIdForPayment($payment),
            $reqPayload,
            $this->reqRes->normalizeApiResponse($api),
            null,
            $orderStatus !== '' ? $orderStatus : 'pending',
        );

        return $payment->fresh(['gateway', 'order']) ?? $payment;
    }

    public function handleWebhook(Request $request): Response
    {
        $payload = $request->all();

        $gatewayOrderId = $payload['data']['order']['order_id'] ?? $payload['order_id'] ?? null;
        $webhookStatus = $payload['data']['order']['order_status'] ?? $payload['order_status'] ?? null;

        $this->logGateway('cashfree.webhook.received', 'Cashfree webhook received', [
            'gateway_code' => self::CODE,
            'event_type' => $payload['type'] ?? $payload['event'] ?? null,
            'order_id' => $gatewayOrderId,
            'order_status' => $webhookStatus,
        ]);

        $this->reqRes->store(
            $this->reqRes->transactionIdFromGatewayReference(is_string($gatewayOrderId) ? $gatewayOrderId : null),
            ['gateway' => self::CODE, 'action' => 'webhook'],
            null,
            $payload,
            is_string($webhookStatus) ? strtoupper($webhookStatus) : 'webhook',
        );

        return response('OK', 200);
    }

    public function isHostedInitResult(array $initResult): bool
    {
        return ($initResult['mode'] ?? '') === self::HOSTED_MODE && ($initResult['success'] ?? false);
    }

    public function isHostedPayment(Payment $payment): bool
    {
        $payload = $payment->driver_payload ?? [];

        return is_array($payload) && ($payload['mode'] ?? '') === self::HOSTED_MODE;
    }

    public function hostedCheckoutView(Payment $payment): ?array
    {
        if (! $this->isHostedPayment($payment)) {
            return null;
        }

        $payload = $payment->driver_payload ?? [];
        if (! is_array($payload)) {
            return null;
        }

        $sessionId = $payload['payment_session_id'] ?? null;
        $environment = $payload['environment'] ?? 'sandbox';
        if (! is_string($sessionId) || $sessionId === '') {
            return null;
        }

        return [
            'payment' => $payment,
            'paymentSessionId' => $sessionId,
            'cashfreeMode' => $environment === 'production' ? 'production' : 'sandbox',
            'gatewayLabel' => $this->label(),
        ];
    }

    /**
     * @param  array<string, mixed>  $credentials
     */
    public static function isSandboxCredentials(array $credentials): bool
    {
        return strtolower((string) ($credentials['env'] ?? 'sandbox')) !== 'production';
    }

    protected function apiVersion(): string
    {
        $version = trim((string) $this->credential('api_version', self::DEFAULT_API_VERSION));

        return $version !== '' ? $version : self::DEFAULT_API_VERSION;
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
