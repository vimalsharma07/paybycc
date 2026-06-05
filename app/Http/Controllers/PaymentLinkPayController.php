<?php

namespace App\Http\Controllers;

use App\Services\Orders\OrderService;
use App\Services\PaymentLinks\PaymentLinkService;
use App\Services\Payments\PaymentCheckoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        if ($payable && $seller) {
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
        ]);
    }

    public function pay(Request $request, string $linkToken): RedirectResponse
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

        $amountDecimal = number_format((float) $paymentLink->amount, 2, '.', '');
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
