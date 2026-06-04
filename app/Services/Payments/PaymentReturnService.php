<?php

namespace App\Services\Payments;

use App\Gateways\Cashfree;
use App\Models\Gateway;
use App\Models\Payment;
use App\Models\Transaction;
use App\Services\Orders\OrderService;
use Illuminate\Support\Facades\DB;

class PaymentReturnService
{
    public function __construct(
        protected CashfreeClient $cashfreeClient,
        protected OrderService $orders,
    ) {}

    /**
     * Reconcile payment status with the gateway when the customer returns from hosted checkout.
     */
    public function syncFromGateway(Payment $payment): Payment
    {
        $payment->loadMissing(['gateway', 'order']);

        $payload = is_array($payment->driver_payload) ? $payment->driver_payload : [];
        $mode = (string) ($payload['mode'] ?? '');

        if ($mode === 'cashfree_hosted') {
            $this->syncCashfreeHosted($payment);
        }

        return $payment->fresh(['gateway', 'order.freelancer', 'order.customer']);
    }

    public function outcome(Payment $payment): string
    {
        return match ($payment->status) {
            'completed' => 'success',
            'failed' => 'failed',
            default => 'pending',
        };
    }

    /**
     * @return array<string, mixed>
     */
    public function resultContext(Payment $payment): array
    {
        $payment->loadMissing([
            'gateway:id,name,code',
            'order.freelancer:id,name,company_name,user_code',
            'order.customer:id,name',
        ]);

        $order = $payment->order;
        $freelancer = $order?->freelancer;

        return [
            'payment' => $payment,
            'order' => $order,
            'freelancer' => $freelancer,
            'gatewayName' => $payment->gateway?->name ?? 'Payment gateway',
            'amountLabel' => '₹'.number_format((float) $payment->amount, 2),
            'netSettlementLabel' => $order
                ? '₹'.number_format((float) $order->net_settlement_amount, 2)
                : null,
            'currency' => $payment->currency,
            'platformReference' => '#'.$payment->id,
            'gatewayReference' => $payment->gateway_reference,
            'orderCode' => $order?->order_code,
            'freelancerName' => $freelancer?->name,
            'freelancerId' => $freelancer?->id,
            'remark' => $payment->remark,
            'completedAt' => $payment->updated_at,
        ];
    }

    protected function syncCashfreeHosted(Payment $payment): void
    {
        if ($payment->status === 'completed' || $payment->status === 'failed') {
            return;
        }

        $payload = $payment->driver_payload ?? [];
        if (! is_array($payload) || ($payload['mode'] ?? '') !== 'cashfree_hosted') {
            return;
        }

        $gateway = $payment->gateway;
        if (! $gateway instanceof Gateway) {
            return;
        }

        $creds = is_array($gateway->credentials) ? $gateway->credentials : [];
        $clientId = (string) ($creds['client_id'] ?? '');
        $secret = (string) ($creds['client_secret'] ?? '');
        $orderId = $payment->gateway_reference;

        if ($clientId === '' || $secret === '' || ! is_string($orderId) || $orderId === '') {
            return;
        }

        $sandbox = Cashfree::isSandboxCredentials($creds);
        $api = $this->cashfreeClient->fetchOrder($clientId, $secret, $sandbox, $orderId);

        if (! $api['ok'] || ! isset($api['data']) || ! is_array($api['data'])) {
            return;
        }

        $data = $api['data'];
        $orderStatus = strtoupper((string) ($data['order_status'] ?? ''));
        $orderAmount = isset($data['order_amount']) ? (float) $data['order_amount'] : null;

        if ($orderAmount !== null && abs($orderAmount - (float) $payment->amount) > 0.02) {
            report(new \RuntimeException('Cashfree order amount mismatch for payment '.$payment->id));

            return;
        }

        if ($orderStatus === 'PAID') {
            $this->finalizePaid($payment, $data);

            return;
        }

        if (in_array($orderStatus, ['EXPIRED', 'TERMINATED'], true)) {
            $payment->update(['status' => 'failed']);
            $payment->order?->update(['payment_status' => 'failed']);
        }
    }

    /**
     * @param  array<string, mixed>  $gatewaySnapshot
     */
    protected function finalizePaid(Payment $payment, array $gatewaySnapshot): void
    {
        $amountDecimal = number_format((float) $payment->amount, 2, '.', '');

        DB::transaction(function () use ($payment, $gatewaySnapshot, $amountDecimal) {
            $locked = Payment::query()->whereKey($payment->id)->lockForUpdate()->first();
            if (! $locked || $locked->status === 'completed' || $locked->status !== 'pending') {
                return;
            }

            $basePayload = is_array($locked->driver_payload) ? $locked->driver_payload : [];

            $locked->update([
                'status' => 'completed',
                'driver_payload' => array_merge($basePayload, [
                    'gateway_order_snapshot' => $gatewaySnapshot,
                ]),
            ]);

            $fresh = $locked->fresh();
            if ($fresh) {
                $this->createPayerTransaction($fresh, $amountDecimal);
                $this->orders->recordPaymentSuccess($fresh);
            }
        });
    }

    protected function createPayerTransaction(Payment $payment, string $amountDecimal): void
    {
        if ($payment->transactions()->exists()) {
            return;
        }

        $bufferDays = max(0, (int) config('paybycc.settlement_buffer_days', 2));
        $userNote = trim((string) ($payment->remark ?? ''));
        $gatewayLabel = $payment->gateway?->name ?? 'Gateway';
        $note = $userNote !== ''
            ? $userNote.' · '.$gatewayLabel
            : 'Payment via '.$gatewayLabel;

        Transaction::create([
            'user_id' => (int) $payment->user_id,
            'bank_id' => null,
            'payment_id' => $payment->id,
            'parent_transaction_id' => null,
            'type' => Transaction::TYPE_CARD_PAYMENT,
            'amount' => $amountDecimal,
            'currency' => 'INR',
            'status' => 'completed',
            'settlement_trigger_at' => now()->addDays($bufferDays),
            'settled_at' => null,
            'note' => $note,
        ]);
    }
}
