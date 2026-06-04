<?php

namespace App\Services\Orders;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Models\Wallet;
use InvalidArgumentException;

class OrderService
{
    public function assertCanPay(User $customer, User $freelancer): void
    {
        if ($customer->id === $freelancer->id) {
            throw new InvalidArgumentException('You cannot pay yourself.');
        }

        if (! $freelancer->isSeller() || $freelancer->status !== 'active' || $freelancer->is_admin) {
            throw new InvalidArgumentException('This freelancer is not available for payments.');
        }

        if (! $freelancer->acceptsCustomer($customer)) {
            throw new InvalidArgumentException('This seller only accepts customers who have completed KYC.');
        }
    }

    public function createOrder(User $customer, User $freelancer, string $amountDecimal, ?string $notes): Order
    {
        $this->assertCanPay($customer, $freelancer);

        $amount = round((float) $amountDecimal, 2);
        $min = (float) config('platform.marketplace.min_order_amount', 1);
        $max = (float) config('platform.marketplace.max_order_amount', 500000);

        if ($amount < $min || $amount > $max) {
            throw new InvalidArgumentException('Order amount is outside allowed limits.');
        }

        $feePercent = (float) config('platform.marketplace.platform_fee_percent', 0);
        $platformFee = round($amount * ($feePercent / 100), 2);
        $net = round($amount - $platformFee, 2);

        return Order::create([
            'order_code' => $this->uniqueOrderCode(),
            'customer_id' => $customer->id,
            'freelancer_id' => $freelancer->id,
            'service_id' => null,
            'subservice_id' => null,
            'order_amount' => number_format($amount, 2, '.', ''),
            'platform_fee' => number_format($platformFee, 2, '.', ''),
            'gst_amount' => '0.00',
            'tds_amount' => '0.00',
            'tcs_amount' => '0.00',
            'net_settlement_amount' => number_format($net, 2, '.', ''),
            'currency' => 'INR',
            'order_status' => 'created',
            'payment_status' => 'pending',
            'settlement_status' => 'pending',
            'safe_status' => 'pending_review',
            'notes' => $notes,
        ]);
    }

    public function recordPaymentSuccess(Payment $payment): void
    {
        $payment->loadMissing('order.freelancer');
        $order = $payment->order;
        if (! $order instanceof Order) {
            return;
        }

        if ($order->payment_status === 'paid') {
            return;
        }

        $freelancer = $order->freelancer;
        $canSettle = $freelancer && $freelancer->canReceivePayouts();

        $order->update([
            'payment_status' => 'paid',
            'order_status' => 'accepted',
            'settlement_status' => $canSettle ? 'eligible' : 'pending',
            'safe_status' => $canSettle ? 'safe' : 'hold',
        ]);

        if ($canSettle && (float) $order->net_settlement_amount > 0) {
            $wallet = Wallet::firstOrCreate(
                ['user_id' => $freelancer->id],
                [
                    'balance' => 0,
                    'auto_settle_to_bank' => true,
                    'default_bank_id' => null,
                ]
            );
            $wallet->increment('balance', (float) $order->net_settlement_amount);
        }
    }

    protected function uniqueOrderCode(): string
    {
        do {
            $code = 'ORD-'.strtoupper(\Illuminate\Support\Str::random(10));
        } while (Order::query()->where('order_code', $code)->exists());

        return $code;
    }
}
