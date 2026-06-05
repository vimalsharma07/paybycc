<?php

namespace App\Services\PaymentLinks;

use App\Models\Payment;
use App\Models\PaymentLink;
use App\Models\User;
use Illuminate\Support\Str;
use InvalidArgumentException;

class PaymentLinkService
{
    public function assertSellerCanCreateLinks(User $seller): void
    {
        if (! $seller->canCreatePaymentLinks()) {
            throw new InvalidArgumentException('Only active freelancer accounts can create payment links.');
        }
    }

    public function create(User $seller, string $amountDecimal, ?string $description, ?\DateTimeInterface $expiresAt = null): PaymentLink
    {
        $this->assertSellerCanCreateLinks($seller);

        $amount = round((float) $amountDecimal, 2);
        $min = (float) config('platform.marketplace.min_order_amount', 1);
        $max = (float) config('platform.marketplace.max_order_amount', 500000);

        if ($amount < $min || $amount > $max) {
            throw new InvalidArgumentException('Amount is outside allowed limits (₹'.number_format($min, 0).' – ₹'.number_format($max, 0).').');
        }

        $maxActive = (int) config('platform.payment_links.max_active_per_seller', 50);
        $activeCount = PaymentLink::query()
            ->where('seller_id', $seller->id)
            ->where('status', PaymentLink::STATUS_OPEN)
            ->count();

        if ($activeCount >= $maxActive) {
            throw new InvalidArgumentException('You have too many open payment links. Cancel old links or wait for them to be paid.');
        }

        $description = $description !== null ? trim($description) : null;
        if ($description === '') {
            $description = null;
        }

        return PaymentLink::create([
            'link_token' => $this->uniqueLinkToken(),
            'seller_id' => $seller->id,
            'amount' => number_format($amount, 2, '.', ''),
            'currency' => 'INR',
            'description' => $description,
            'status' => PaymentLink::STATUS_OPEN,
            'expires_at' => $expiresAt,
        ]);
    }

    public function findByToken(string $linkToken): ?PaymentLink
    {
        $token = trim($linkToken);
        if ($token === '') {
            return null;
        }

        return PaymentLink::query()
            ->with(['seller', 'latestPayment', 'order'])
            ->where('link_token', $token)
            ->first();
    }

    public function resolveForPayment(PaymentLink $paymentLink): PaymentLink
    {
        if ($paymentLink->status === PaymentLink::STATUS_PAID) {
            throw new InvalidArgumentException('This payment link has already been paid.');
        }

        if ($paymentLink->status === PaymentLink::STATUS_CANCELLED) {
            throw new InvalidArgumentException('This payment link was cancelled by the seller.');
        }

        if ($paymentLink->expires_at && $paymentLink->expires_at->isPast()) {
            if ($paymentLink->status === PaymentLink::STATUS_OPEN) {
                $paymentLink->update(['status' => PaymentLink::STATUS_EXPIRED]);
            }

            throw new InvalidArgumentException('This payment link has expired.');
        }

        if ($paymentLink->status === PaymentLink::STATUS_EXPIRED) {
            throw new InvalidArgumentException('This payment link has expired.');
        }

        return $paymentLink;
    }

    public function attachCheckout(PaymentLink $paymentLink, Payment $payment): void
    {
        $paymentLink->update([
            'order_id' => $payment->order_id,
        ]);
    }

    public function markPaidFromPayment(Payment $payment): void
    {
        if (! $payment->payment_link_id) {
            return;
        }

        $link = PaymentLink::query()->whereKey($payment->payment_link_id)->first();
        if (! $link || $link->status === PaymentLink::STATUS_PAID) {
            return;
        }

        $link->update([
            'status' => PaymentLink::STATUS_PAID,
            'paid_at' => now(),
            'order_id' => $payment->order_id,
        ]);
    }

    public function resumePendingPayment(PaymentLink $paymentLink): ?Payment
    {
        $payment = $paymentLink->latestPayment;
        if (! $payment || $payment->status !== 'pending') {
            return null;
        }

        $payload = $payment->driver_payload ?? [];
        if (! is_array($payload) || ($payload['mode'] ?? '') !== 'cashfree_hosted') {
            return null;
        }

        return $payment;
    }

    public function cancel(PaymentLink $paymentLink, User $seller): void
    {
        if ((int) $paymentLink->seller_id !== (int) $seller->id) {
            throw new InvalidArgumentException('You cannot cancel this payment link.');
        }

        if ($paymentLink->status !== PaymentLink::STATUS_OPEN) {
            throw new InvalidArgumentException('Only open payment links can be cancelled.');
        }

        $paymentLink->update(['status' => PaymentLink::STATUS_CANCELLED]);
    }

    protected function uniqueLinkToken(): string
    {
        do {
            $token = Str::lower(Str::random(32));
        } while (PaymentLink::query()->where('link_token', $token)->exists());

        return $token;
    }
}
