<?php

namespace App\Http\Controllers;

use App\Gateways\Contracts\HostedCheckoutGateway;
use App\Models\Payment;
use App\Services\Orders\OrderService;
use App\Services\Orders\SellerSearch;
use App\Services\Payments\GatewayManager;
use App\Services\Payments\PaymentCheckoutService;
use App\Services\Payments\PaymentReturnService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;

class PaymentController extends Controller
{
    public function __construct(
        protected OrderService $orders,
        protected SellerSearch $sellerSearch,
        protected PaymentReturnService $paymentReturns,
        protected PaymentCheckoutService $checkout,
        protected GatewayManager $gateways,
    ) {}

    public function create(Request $request, GatewayManager $gatewayManager): View
    {
        $primary = $gatewayManager->primaryGateway();

        $freelancerId = (int) $request->query('freelancer', 0);
        $selectedFreelancer = $freelancerId > 0 ? $this->sellerSearch->findSeller($freelancerId) : null;

        $searchQ = $request->string('q')->trim()->toString();
        $searchResults = $this->sellerSearch->suggest($searchQ !== '' ? $searchQ : null);

        return view('payments.create', [
            'gateway' => $primary,
            'gatewayConfigured' => $gatewayManager->primaryReady(),
            'selectedFreelancer' => $selectedFreelancer,
            'searchQ' => $searchQ,
            'searchResults' => $searchResults,
            'commerceRates' => $this->commerceRatesForView(),
        ]);
    }

    public function feeEstimate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'freelancer_id' => ['required', 'integer', 'exists:users,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
        ]);

        $freelancer = $this->sellerSearch->findSeller((int) $validated['freelancer_id']);
        if (! $freelancer) {
            return response()->json(['message' => 'Invalid freelancer.'], 422);
        }

        try {
            $breakdown = $this->orders->previewFees($freelancer, number_format((float) $validated['amount'], 2, '.', ''));
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'ok' => true,
            'fees' => $breakdown->toPublicArray(),
            'rates' => $this->commerceRatesForView(),
        ]);
    }

    /**
     * @return array<string, float|int|bool>
     */
    protected function commerceRatesForView(): array
    {
        return [
            'processing_threshold' => (float) config('commerce.processing_fee.threshold_inr'),
            'processing_percent' => (float) config('commerce.processing_fee.percent'),
            'gst_on_processing_percent' => (float) config('commerce.gst_on_processing_fee_percent'),
            'flat_order_percent' => (float) config('commerce.flat_order_fee_percent'),
            'tcs_percent' => (float) config('commerce.tcs_when_gst_registered_percent'),
            'tds_threshold' => (float) config('commerce.tds.cumulative_net_threshold_inr'),
            'tds_percent' => (float) config('commerce.tds.percent_after_threshold'),
            'no_gst_fy_cap' => (float) config('commerce.seller_no_gst_max_net_payout_per_fy_inr'),
        ];
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'freelancer_id' => ['required', 'integer', 'exists:users,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'remark' => ['nullable', 'string', 'max:160'],
            'checkout_meta' => ['nullable', 'string', 'max:2000'],
        ]);

        $freelancer = $this->sellerSearch->findSeller((int) $validated['freelancer_id']);
        if (! $freelancer) {
            return back()->withInput()->withErrors(['freelancer_id' => 'Select a valid freelancer or seller from search.']);
        }

        $remarkRaw = isset($validated['remark']) ? trim((string) $validated['remark']) : '';
        $remark = $remarkRaw === '' ? null : $remarkRaw;
        $amountDecimal = number_format((float) $validated['amount'], 2, '.', '');

        try {
            $result = $this->checkout->initiate(
                $request->user(),
                $freelancer,
                $amountDecimal,
                $remark,
            );
        } catch (InvalidArgumentException $e) {
            $field = str_contains(strtolower($e->getMessage()), 'freelancer') || str_contains(strtolower($e->getMessage()), 'yourself') || str_contains(strtolower($e->getMessage()), 'kyc')
                ? 'freelancer_id'
                : 'amount';

            return back()->withInput()->withErrors([$field => $e->getMessage()]);
        }

        return $this->redirectAfterCheckout($result['payment'], $result['hosted_checkout']);
    }

    public function checkout(Payment $payment): View|RedirectResponse
    {
        $this->authorizePayment($payment);

        if ($payment->status !== 'pending') {
            return $this->redirectToResult($payment);
        }

        $driver = $this->gateways->driverForPayment($payment);
        if (! $driver instanceof HostedCheckoutGateway) {
            return redirect()
                ->route('payments.create')
                ->with('status', 'Checkout is not available for this payment.');
        }

        $viewData = $driver->hostedCheckoutView($payment);
        if ($viewData === null) {
            return redirect()
                ->route('payments.create')
                ->with('status', 'Payment session missing. Please start a new payment.');
        }

        return view('payments.checkout', $viewData);
    }

    public function success(Payment $payment): View|RedirectResponse
    {
        $this->authorizePayment($payment);
        $payment->loadMissing(['gateway', 'order.freelancer']);

        if ($payment->status !== 'completed') {
            return $this->redirectToResult($payment);
        }

        return view('payments.success', $this->paymentReturns->resultContext($payment));
    }

    public function failed(Payment $payment): View|RedirectResponse
    {
        $this->authorizePayment($payment);
        $payment->loadMissing(['gateway', 'order.freelancer']);

        if ($payment->status === 'completed') {
            return redirect()->route('payments.success', $payment);
        }

        if ($payment->status === 'pending') {
            return redirect()->route('payments.pending', $payment);
        }

        return view('payments.failed', $this->paymentReturns->resultContext($payment));
    }

    public function pending(Payment $payment): View|RedirectResponse
    {
        $this->authorizePayment($payment);
        $payment = $this->paymentReturns->syncFromGateway($payment);

        if ($payment->status === 'completed') {
            return redirect()->route('payments.success', $payment);
        }

        if ($payment->status === 'failed') {
            return redirect()->route('payments.failed', $payment);
        }

        return view('payments.pending', $this->paymentReturns->resultContext($payment));
    }

    protected function authorizePayment(Payment $payment): void
    {
        abort_unless((int) $payment->user_id === (int) auth()->id(), 403);
    }

    protected function redirectAfterCheckout(Payment $payment, bool $hostedCheckout): RedirectResponse
    {
        if ($hostedCheckout) {
            return redirect()->route('payments.checkout', $payment);
        }

        return $this->redirectToResult($payment);
    }

    protected function redirectToResult(Payment $payment): RedirectResponse
    {
        $payment->refresh();

        return match ($this->paymentReturns->outcome($payment)) {
            'success' => redirect()->route('payments.success', $payment),
            'failed' => redirect()->route('payments.failed', $payment),
            default => redirect()->route('payments.pending', $payment),
        };
    }
}
