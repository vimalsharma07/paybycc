<?php

namespace App\Http\Controllers;

use App\Http\Requests\GuestPayPaymentLinkRequest;
use App\Http\Requests\PayPaymentLinkRequest;
use App\Models\User;
use App\Services\PaymentLinks\PaymentLinkService;
use App\Services\Payments\GuestPayerService;
use App\Services\Payments\PaymentCheckoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use InvalidArgumentException;

class PaymentLinkPayController extends Controller
{
    public function __construct(
        protected PaymentLinkService $paymentLinks,
        protected PaymentCheckoutService $checkout,
        protected GuestPayerService $guestPayers,
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

        $seller = $paymentLink->seller;
        $user = auth()->user();
        $guestCheckout = $payable && $seller && $seller->allowsGuestPaymentLinks();
        $payerMode = $seller?->paymentLinkPayerMode() ?? 'login';

        $canPayNow = $payable
            && $seller
            && $user
            && (int) $user->id !== (int) $seller->id
            && $seller->acceptsPaymentLinkPayer($user);

        $needsKyc = $payable
            && $seller
            && $user
            && $seller->payerMustHaveKycForPaymentLinks()
            && ! $user->hasActiveKyc();

        $pendingPayment = $payable ? $this->paymentLinks->resumePendingPayment($paymentLink) : null;

        return view('payment-links.pay', [
            'paymentLink' => $paymentLink,
            'seller' => $seller,
            'payable' => $payable,
            'payError' => $payError,
            'canPayNow' => $canPayNow,
            'guestCheckout' => $guestCheckout,
            'payerMode' => $payerMode,
            'needsKyc' => $needsKyc,
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

        return $this->processPayment(
            $request->user(),
            $paymentLink,
            $linkToken,
            $request->input('amount'),
        );
    }

    public function guestPay(GuestPayPaymentLinkRequest $request, string $linkToken): RedirectResponse
    {
        $paymentLink = $this->paymentLinks->findByToken($linkToken);

        if (! $paymentLink) {
            abort(404, 'Payment link not found.');
        }

        $seller = $paymentLink->seller;
        if (! $seller || ! $seller->allowsGuestPaymentLinks()) {
            return redirect()
                ->route('payment-links.pay.show', $linkToken)
                ->withErrors(['payment_link' => 'This link requires you to log in before paying.']);
        }

        $customer = $this->guestPayers->resolve(
            $request->input('name'),
            $request->input('email'),
            $request->input('phone'),
        );

        Auth::login($customer);
        $request->session()->regenerate();

        return $this->processPayment(
            $customer,
            $paymentLink,
            $linkToken,
            $request->input('amount'),
        );
    }

    protected function processPayment(User $customer, $paymentLink, string $linkToken, ?string $submittedAmount): RedirectResponse
    {
        $seller = $paymentLink->seller;

        if (! $seller) {
            return redirect()
                ->route('payment-links.pay.show', $linkToken)
                ->withErrors(['payment_link' => 'Seller account is unavailable.']);
        }

        if ((int) $customer->id === (int) $seller->id) {
            return redirect()
                ->route('payment-links.pay.show', $linkToken)
                ->withErrors(['payment_link' => 'You cannot pay your own payment link.']);
        }

        if ($seller->payerMustHaveKycForPaymentLinks() && ! $customer->hasActiveKyc()) {
            return redirect()
                ->route('kyc.index')
                ->with('status', 'This seller requires KYC before you can pay.');
        }

        if (! $seller->acceptsPaymentLinkPayer($customer)) {
            return redirect()
                ->route('payment-links.pay.show', $linkToken)
                ->withErrors(['payment_link' => 'You are not allowed to pay this link.']);
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

        try {
            $amountDecimal = $this->paymentLinks->resolveAmountForPayment(
                $paymentLink,
                $submittedAmount,
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
