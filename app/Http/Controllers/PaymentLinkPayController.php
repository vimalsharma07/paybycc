<?php

namespace App\Http\Controllers;

use App\Http\Requests\PayPaymentLinkRequest;
use App\Services\Orders\OrderService;
use App\Services\PaymentLinks\PaymentLinkService;
use App\Services\Payments\PaymentCheckoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use InvalidArgumentException;

class PaymentLinkPayController extends Controller
{
    public function __construct(
        protected PaymentLinkService $paymentLinks,
        protected PaymentCheckoutService $checkout,
        protected OrderService $orders,
    ) {}

    public function show(string $linkToken): View
    {
        $paymentLink = $this->paymentLinks->findByToken($linkToken);

        if (! $paymentLink) {
            abort(404, 'Payment link not found.');
        }

        $payable = true;
        $payError = null;

        try {
            $this->paymentLinks->resolveForPayment($paymentLink);
        } catch (InvalidArgumentException $e) {
            $payable = false;
            $payError = $e->getMessage();
        }

        $feeBreakdown = null;
        $seller = $paymentLink->seller;

        if ($payable && $seller && ! $paymentLink->isOpenAmount()) {
            try {
                $feeBreakdown = $this->orders->previewFees(
                    $seller,
                    number_format((float) $paymentLink->amount, 2, '.', '')
                );
            } catch (InvalidArgumentException) {
                // Fee preview optional on public page
            }
        }

        $user = auth()->user();
        $canPayNow = $payable
            && $user
            && $user->canUsePlatform()
            && $seller
            && (int) $user->id !== (int) $seller->id;

        $pendingPayment = $payable ? $this->paymentLinks->resumePendingPayment($paymentLink) : null;

        return view('payment-links.pay', [
            'paymentLink' => $paymentLink,
            'seller' => $seller,
            'payable' => $payable,
            'payError' => $payError,
            'feeBreakdown' => $feeBreakdown,
            'canPayNow' => $canPayNow,
            'pendingPayment' => $pendingPayment,
            'minAmount' => (float) config('platform.marketplace.min_order_amount', 1),
            'maxAmount' => (float) config('platform.marketplace.max_order_amount', 500000),
        ]);
    }

    public function pay(PayPaymentLinkRequest $request, string $linkToken): RedirectResponse
    {
        $paymentLink = $this->paymentLinks->findByToken($linkToken);

        if (! $paymentLink) {
            abort(404, 'Payment link not found.');
        }

        $customer = $request->user();
        if (! $customer->canUsePlatform()) {
            return redirect()->route('kyc.index');
        }

        try {
            $this->paymentLinks->resolveForPayment($paymentLink);
        } catch (InvalidArgumentException $e) {
            return redirect()
                ->route('payment-links.pay.show', $linkToken)
                ->withErrors(['payment_link' => $e->getMessage()]);
        }

        $pending = $this->paymentLinks->resumePendingPayment($paymentLink);
        if ($pending && (int) $pending->user_id === (int) $customer->id) {
            return redirect()->route('payments.checkout', $pending);
        }

        $seller = $paymentLink->seller;
        if (! $seller) {
            return redirect()
                ->route('payment-links.pay.show', $linkToken)
                ->withErrors(['payment_link' => 'Seller account is unavailable.']);
        }

        try {
            $amountDecimal = $this->paymentLinks->resolveAmountForPayment(
                $paymentLink,
                $request->input('amount'),
            );
        } catch (InvalidArgumentException $e) {
            return redirect()
                ->route('payment-links.pay.show', $linkToken)
                ->withInput()
                ->withErrors(['amount' => $e->getMessage()]);
        }

        $remark = $paymentLink->description
            ?? ('Payment link · '.$seller->name);

        try {
            $result = $this->checkout->initiate(
                $customer,
                $seller,
                $amountDecimal,
                $remark,
                $paymentLink->id,
            );
        } catch (InvalidArgumentException $e) {
            return redirect()
                ->route('payment-links.pay.show', $linkToken)
                ->withErrors(['payment_link' => $e->getMessage()]);
        }

        $payment = $result['payment'];

        if ($result['hosted_checkout']) {
            return redirect()->route('payments.checkout', $payment);
        }

        return match ($payment->status) {
            'completed' => redirect()->route('payments.success', $payment),
            'failed' => redirect()->route('payments.failed', $payment),
            default => redirect()->route('payments.pending', $payment),
        };
    }
}
