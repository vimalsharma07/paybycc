<?php

namespace App\Services\Payments;

use App\Constants\OrderStatuses;
use App\Constants\TransactionStatuses;
use App\Enums\LogLevel;
use App\Models\Gateway;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Logging\FlowLog;
use App\Services\Orders\OrderService;
use App\Services\PaymentLinks\PaymentLinkService;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class PaymentCheckoutService
{
    public function __construct(
        protected OrderService $orders,
        protected GatewayManager $gatewayManager,
        protected PaymentLinkService $paymentLinks,
        protected FlowLog $flow,
    ) {}

    /**
     * @return array{payment: Payment, hosted_checkout: bool}
     */
    public function initiate(
        User $customer,
        User $freelancer,
        string $amountDecimal,
        ?string $remark,
        ?int $paymentLinkId = null,
    ): array {
        $viaPaymentLink = $paymentLinkId !== null;

        $this->flow->order(
            'checkout.start',
            'Payment checkout initiated',
            $this->flow->userContext($customer, [
                'freelancer_id' => $freelancer->id,
                'amount' => (float) $amountDecimal,
                'via_payment_link' => $viaPaymentLink,
                'payment_link_id' => $paymentLinkId,
            ]),
            null,
            LogLevel::Debug,
        );

        try {
            return $this->runCheckout($customer, $freelancer, $amountDecimal, $remark, $paymentLinkId, $viaPaymentLink);
        } catch (InvalidArgumentException $e) {
            $this->flow->order(
                'checkout.blocked',
                $e->getMessage(),
                $this->flow->userContext($customer, [
                    'freelancer_id' => $freelancer->id,
                    'amount' => (float) $amountDecimal,
                    'via_payment_link' => $viaPaymentLink,
                ]),
                null,
                LogLevel::Warning,
            );

            throw $e;
        }
    }

    /**
     * @return array{payment: Payment, hosted_checkout: bool}
     */
    protected function runCheckout(
        User $customer,
        User $freelancer,
        string $amountDecimal,
        ?string $remark,
        ?int $paymentLinkId,
        bool $viaPaymentLink,
    ): array {
        $gateway = $this->gatewayManager->primaryGateway();

        if (! $gateway || ! $gateway->isActive()) {
            throw new InvalidArgumentException('Payments are unavailable: no active primary gateway.');
        }

        $this->orders->assertCanPay($customer, $freelancer, $viaPaymentLink);

        $order = $this->orders->createOrder($customer, $freelancer, $amountDecimal, $remark, $viaPaymentLink);

        $this->assertGatewayLimits($gateway, (float) $amountDecimal);

        try {
            $driver = $this->gatewayManager->resolveDriver($gateway);
        } catch (\Throwable $e) {
            report($e);

            throw new InvalidArgumentException('Payment gateway configuration error. Please try again later.');
        }

        $orderNote = $remark ?? ('Payment to '.$freelancer->name.' · '.$order->order_code);

        $payment = DB::transaction(function () use ($customer, $gateway, $amountDecimal, $driver, $orderNote, $order, $freelancer, $paymentLinkId) {
            $payment = Payment::create([
                'user_id' => $customer->id,
                'gateway_id' => $gateway->id,
                'order_id' => $order->id,
                'payment_link_id' => $paymentLinkId,
                'amount' => $amountDecimal,
                'currency' => 'INR',
                'remark' => $orderNote,
                'status' => 'pending',
            ]);

            $returnUrl = route('payments.return', ['pid' => $payment->id], true);

            $result = $driver->initiatePayment($amountDecimal, [
                'payment_id' => $payment->id,
                'user_id' => $customer->id,
                'currency' => 'INR',
                'customer_email' => $customer->email,
                'customer_name' => $customer->name,
                'customer_phone' => (string) $customer->phone,
                'return_url' => $returnUrl,
            ]);

            $hosted = (($result['mode'] ?? '') === 'cashfree_hosted') && ($result['success'] ?? false);
            $reference = $result['reference']
                ?? $result['gateway_reference']
                ?? $result['payment_reference']
                ?? $result['cashfree_order_id']
                ?? null;

            $success = (bool) ($result['success'] ?? false);

            if ($hosted) {
                $payment->update([
                    'gateway_reference' => is_string($reference) ? $reference : null,
                    'driver_payload' => $result,
                    'status' => 'pending',
                ]);
                $this->logCheckoutOutcome('checkout.hosted', 'Hosted checkout session created', $payment, $order, $hosted);
            } elseif ($success) {
                $payment->update([
                    'gateway_reference' => is_string($reference) ? $reference : null,
                    'driver_payload' => $result,
                    'status' => 'completed',
                ]);

                $payment = $payment->fresh();
                if ($payment) {
                    $this->createPayerTransaction($customer->id, $payment, $amountDecimal);
                    $this->orders->recordPaymentSuccess($payment);
                    $this->paymentLinks->markPaidFromPayment($payment);
                }
                $this->logCheckoutOutcome('checkout.completed', 'Payment completed immediately', $payment, $order, $hosted);
            } else {
                $payment->update([
                    'gateway_reference' => is_string($reference) ? $reference : null,
                    'driver_payload' => $result,
                    'status' => 'failed',
                ]);
                $order->update(['payment_status' => OrderStatuses::PAYMENT_FAILED]);
                $this->logCheckoutOutcome('checkout.failed', 'Gateway rejected payment initiation', $payment, $order, $hosted, LogLevel::Warning);
            }

            if ($paymentLinkId) {
                $link = \App\Models\PaymentLink::query()->find($paymentLinkId);
                if ($link) {
                    $this->paymentLinks->attachCheckout($link, $payment->fresh() ?? $payment);
                }
            }

            return $payment->fresh();
        });

        if (! $payment) {
            throw new InvalidArgumentException('Could not start payment.');
        }

        $payload = $payment->driver_payload ?? [];
        $hostedCheckout = is_array($payload)
            && (($payload['mode'] ?? '') === 'cashfree_hosted')
            && $payment->status === 'pending';

        return [
            'payment' => $payment,
            'hosted_checkout' => $hostedCheckout,
        ];
    }

    protected function assertGatewayLimits(Gateway $gateway, float $amount): void
    {
        $min = (float) $gateway->min_txn;
        $max = (float) $gateway->max_txn;

        if ($amount < $min) {
            throw new InvalidArgumentException('Amount is below the minimum for this gateway (₹'.$gateway->min_txn.').');
        }

        if ($amount > $max) {
            throw new InvalidArgumentException('Amount is above the maximum for this gateway (₹'.$gateway->max_txn.').');
        }

        $dailyCap = (float) $gateway->daily_limit;
        if ($dailyCap > 0) {
            $usedToday = (float) Payment::query()
                ->where('gateway_id', $gateway->id)
                ->whereDate('created_at', today())
                ->whereIn('status', ['pending', 'completed'])
                ->sum('amount');

            if ($usedToday + $amount > $dailyCap + 0.00001) {
                throw new InvalidArgumentException('Daily volume limit for this gateway would be exceeded. Try again tomorrow or use a smaller amount.');
            }
        }
    }

    protected function logCheckoutOutcome(
        string $event,
        string $message,
        ?Payment $payment,
        Order $order,
        bool $hosted,
        LogLevel $level = LogLevel::Info,
    ): void {
        if (! $payment) {
            return;
        }

        $this->flow->order(
            $event,
            $message,
            array_merge(
                $this->flow->paymentContext($payment),
                $this->flow->orderContext($order),
                ['hosted_checkout' => $hosted],
            ),
            $order,
            $level,
        );
    }

    protected function createPayerTransaction(int $userId, Payment $payment, string $amountDecimal): void
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
            'user_id' => $userId,
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
