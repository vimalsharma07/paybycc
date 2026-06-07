<?php

namespace App\Services\Payments;

use App\Constants\OrderStatuses;
use App\Constants\TransactionStatuses;
use App\Enums\LogLevel;
use App\Models\Payment;
use App\Models\Transaction;
use App\Services\Logging\FlowLog;
use App\Services\Orders\OrderService;
use App\Services\PaymentLinks\PaymentLinkService;
use Illuminate\Support\Facades\DB;

class PaymentCompletionService
{
    public function __construct(
        protected OrderService $orders,
        protected PaymentLinkService $paymentLinks,
        protected FlowLog $flow,
    ) {}

    /**
     * @param  array<string, mixed>|null  $gatewaySnapshot
     */
    public function complete(Payment $payment, ?array $gatewaySnapshot = null): Payment
    {
        $amountDecimal = number_format((float) $payment->amount, 2, '.', '');

        DB::transaction(function () use ($payment, $gatewaySnapshot, $amountDecimal) {
            $locked = Payment::query()->whereKey($payment->id)->lockForUpdate()->first();
            if (! $locked || $locked->status === 'completed' || $locked->status !== 'pending') {
                $this->flow->order(
                    'payment.complete.skip',
                    'Payment completion skipped — not pending',
                    $this->flow->paymentContext($payment),
                    $payment->order,
                    LogLevel::Debug,
                );

                $this->flow->gateway(
                    'payment.complete.skip',
                    'Payment completion skipped — not pending',
                    $this->flow->gatewayContext($payment),
                    $payment->order,
                    LogLevel::Debug,
                );

                return;
            }

            $basePayload = is_array($locked->driver_payload) ? $locked->driver_payload : [];
            $payload = $gatewaySnapshot !== null
                ? array_merge($basePayload, ['gateway_order_snapshot' => $gatewaySnapshot])
                : $basePayload;

            $locked->update([
                'status' => 'completed',
                'driver_payload' => $payload,
            ]);

            $fresh = $locked->fresh();
            if ($fresh) {
                $this->createPayerTransaction($fresh, $amountDecimal);
                $this->orders->recordPaymentSuccess($fresh);
                $this->paymentLinks->markPaidFromPayment($fresh);

                $fresh->loadMissing('order');

                $this->flow->payment(
                    'payment.completed',
                    'Payment marked completed',
                    $this->flow->gatewayContext($fresh),
                    $fresh,
                );

                $this->flow->gateway(
                    'payment.completed',
                    'Payment finalized from gateway',
                    $this->flow->gatewayContext($fresh),
                    $fresh->order,
                );

                $this->flow->order(
                    'payment.completed',
                    'Payment marked completed',
                    $this->flow->paymentContext($fresh),
                    $fresh->order,
                );
            }
        });

        return $payment->fresh(['gateway', 'order.freelancer', 'order.customer']) ?? $payment;
    }

    public function fail(Payment $payment): Payment
    {
        $payment->update(['status' => 'failed']);
        $payment->order?->update(['payment_status' => OrderStatuses::PAYMENT_FAILED]);

        $payment->loadMissing('order');

        $this->flow->payment(
            'payment.failed',
            'Payment marked failed',
            $this->flow->gatewayContext($payment),
            $payment,
            LogLevel::Warning,
        );

        $this->flow->gateway(
            'payment.failed',
            'Payment marked failed from gateway',
            $this->flow->gatewayContext($payment),
            $payment->order ?? $payment,
            LogLevel::Warning,
        );

        if ($payment->order) {
            $this->flow->order(
                'payment.failed',
                'Payment marked failed',
                array_merge(
                    $this->flow->paymentContext($payment),
                    $this->flow->orderContext($payment->order),
                ),
                $payment->order,
                LogLevel::Warning,
            );
        }

        return $payment->fresh(['gateway', 'order.freelancer', 'order.customer']) ?? $payment;
    }

    public function createPayerTransaction(Payment $payment, string $amountDecimal): void
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

        $transaction = Transaction::create([
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

        $this->flow->gateway(
            'transaction.created',
            'Payer transaction created after gateway payment',
            array_merge(
                $this->flow->gatewayContext($payment),
                $this->flow->transactionContext($transaction),
            ),
            $transaction,
        );
    }
}
