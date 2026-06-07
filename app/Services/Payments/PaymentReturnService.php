<?php

namespace App\Services\Payments;

use App\Constants\OrderStatuses;
use App\Constants\TransactionStatuses;
use App\Enums\LogLevel;
use App\Gateways\Cashfree;
use App\Models\Gateway;
use App\Models\Payment;
use App\Models\Transaction;
use App\Services\Logging\FlowLog;
use App\Services\Orders\OrderService;
use App\Services\PaymentLinks\PaymentLinkService;
use Illuminate\Support\Facades\DB;

class PaymentReturnService
{
    public function __construct(
        protected CashfreeClient $cashfreeClient,
        protected OrderService $orders,
        protected PaymentLinkService $paymentLinks,
        protected FlowLog $flow,
    ) {}

    /**
     * Reconcile payment status with the gateway when the customer returns from hosted checkout.
     */
    public function syncFromGateway(Payment $payment): Payment
    {
        $payment->loadMissing(['gateway', 'order']);

        $payload = is_array($payment->driver_payload) ? $payment->driver_payload : [];
        $mode = (string) ($payload['mode'] ?? '');

        $this->flow->order(
            'return.sync',
            'Reconciling payment from gateway return',
            $this->flow->paymentContext($payment, ['mode' => $mode]),
            $payment->order,
            LogLevel::Debug,
        );

        if ($mode === 'cashfree_hosted') {
            $this->syncCashfreeHosted($payment);
        }

        $fresh = $payment->fresh(['gateway', 'order.freelancer', 'order.customer']);

        if ($fresh) {
            $this->flow->order(
                'return.synced',
                'Payment status after gateway sync',
                $this->flow->paymentContext($fresh),
                $fresh->order,
            );
        }

        return $fresh ?? $payment;
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
            $this->flow->order(
                'return.amount_mismatch',
                'Gateway amount mismatch — sync aborted',
                array_merge(
                    $this->flow->paymentContext($payment),
                    ['gateway_amount' => $orderAmount, 'gateway_status' => $orderStatus],
                ),
                $payment->order,
                LogLevel::Warning,
            );

            return;
        }

        if ($orderStatus === 'PAID') {
            $this->finalizePaid($payment, $data);

            return;
        }

        if (in_array($orderStatus, ['EXPIRED', 'TERMINATED'], true)) {
            $payment->update(['status' => 'failed']);
            $payment->order?->update(['payment_status' => OrderStatuses::PAYMENT_FAILED]);
            $this->flow->order(
                'return.failed',
                'Gateway reported payment '.$orderStatus,
                array_merge($this->flow->paymentContext($payment), ['gateway_status' => $orderStatus]),
                $payment->order,
                LogLevel::Warning,
            );
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
                $this->flow->order(
                    'return.finalize.skip',
                    'Finalize paid skipped — payment not pending',
                    $this->flow->paymentContext($payment),
                    $payment->order,
                    LogLevel::Debug,
                );

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
                $this->paymentLinks->markPaidFromPayment($fresh);
                $this->flow->order(
                    'return.finalized',
                    'Payment finalized from gateway return',
                    $this->flow->paymentContext($fresh),
                    $fresh->order,
                );
            }
        });
    }

    protected function createPayerTransaction(Payment $payment, string $amountDecimal): void
    {
        if ($payment->transactions()->exists()) {
            $this->flow->transaction(
                'transaction.skip',
                'Payer transaction already exists for payment',
                $this->flow->paymentContext($payment),
                null,
                LogLevel::Debug,
            );

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
            'status' => TransactionStatuses::COMPLETED,
            'settlement_trigger_at' => now()->addDays($bufferDays),
            'settled_at' => null,
            'note' => $note,
        ]);
    }
}
