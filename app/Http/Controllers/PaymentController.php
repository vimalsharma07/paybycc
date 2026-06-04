<?php

namespace App\Http\Controllers;

use App\Models\Gateway;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Services\Orders\OrderService;
use App\Services\Orders\SellerSearch;
use App\Services\Payments\CashfreeGatewaySync;
use App\Services\Payments\GatewayManager;
use App\Services\Payments\PaymentReturnService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use InvalidArgumentException;

class PaymentController extends Controller
{
    public function __construct(
        protected OrderService $orders,
        protected SellerSearch $sellerSearch,
        protected PaymentReturnService $paymentReturns,
    ) {}

    public function create(Request $request, GatewayManager $gatewayManager): View
    {
        $primary = $gatewayManager->primaryGateway();

        if (! $primary && CashfreeGatewaySync::credentialsConfigured()) {
            CashfreeGatewaySync::sync();
            $primary = $gatewayManager->primaryGateway();
        }

        $wallet = Wallet::firstOrCreate(
            ['user_id' => auth()->id()],
            [
                'balance' => 0,
                'auto_settle_to_bank' => true,
                'default_bank_id' => null,
            ]
        );

        $freelancerId = (int) $request->query('freelancer', 0);
        $selectedFreelancer = $freelancerId > 0 ? $this->sellerSearch->findSeller($freelancerId) : null;

        $searchQ = $request->string('q')->trim()->toString();
        $searchResults = $this->sellerSearch->suggest($searchQ !== '' ? $searchQ : null);

        return view('payments.create', [
            'gateway' => $primary,
            'gatewayConfigured' => CashfreeGatewaySync::credentialsConfigured(),
            'wallet' => $wallet,
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

    public function store(Request $request, GatewayManager $gatewayManager): RedirectResponse
    {
        $gateway = $gatewayManager->primaryGateway();

        if (! $gateway || ! $gateway->isActive()) {
            return back()
                ->withInput()
                ->withErrors(['amount' => 'Payments are unavailable: no active primary gateway. Add Cashfree keys to .env or Admin → Gateways.']);
        }

        $validated = $request->validate([
            'freelancer_id' => ['required', 'integer', 'exists:users,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'remark' => ['nullable', 'string', 'max:160'],
        ]);

        $freelancer = $this->sellerSearch->findSeller((int) $validated['freelancer_id']);
        if (! $freelancer) {
            return back()->withInput()->withErrors(['freelancer_id' => 'Select a valid freelancer or seller from search.']);
        }

        try {
            $this->orders->assertCanPay($request->user(), $freelancer);
        } catch (InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['freelancer_id' => $e->getMessage()]);
        }

        $remarkRaw = isset($validated['remark']) ? trim((string) $validated['remark']) : '';
        $remark = $remarkRaw === '' ? null : $remarkRaw;

        $amountDecimal = number_format((float) $validated['amount'], 2, '.', '');

        try {
            $order = $this->orders->createOrder(
                $request->user(),
                $freelancer,
                $amountDecimal,
                $remark
            );
        } catch (InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['amount' => $e->getMessage()]);
        }

        $min = (float) $gateway->min_txn;
        $max = (float) $gateway->max_txn;
        $amt = (float) $amountDecimal;

        if ($amt < $min) {
            return back()->withInput()->withErrors(['amount' => 'Amount is below the minimum for this gateway ('.$gateway->min_txn.').']);
        }

        if ($amt > $max) {
            return back()->withInput()->withErrors(['amount' => 'Amount is above the maximum for this gateway ('.$gateway->max_txn.').']);
        }

        $dailyCap = (float) $gateway->daily_limit;
        if ($dailyCap > 0) {
            $usedToday = (float) Payment::query()
                ->where('gateway_id', $gateway->id)
                ->whereDate('created_at', today())
                ->whereIn('status', ['pending', 'completed'])
                ->sum('amount');

            if ($usedToday + $amt > $dailyCap + 0.00001) {
                return back()->withInput()->withErrors(['amount' => 'Daily volume limit for this gateway would be exceeded. Try again tomorrow or use a smaller amount.']);
            }
        }

        try {
            $driver = $gatewayManager->resolveDriver($gateway);
        } catch (\Throwable $e) {
            report($e);

            return back()->withInput()->withErrors(['amount' => 'Payment gateway configuration error. Please try again later.']);
        }

        $orderNote = $remark ?? ('Payment to '.$freelancer->name.' · '.$order->order_code);

        $payment = DB::transaction(function () use ($request, $gateway, $amountDecimal, $driver, $orderNote, $order, $freelancer) {
            $payment = Payment::create([
                'user_id' => $request->user()->id,
                'gateway_id' => $gateway->id,
                'order_id' => $order->id,
                'amount' => $amountDecimal,
                'currency' => 'INR',
                'remark' => $orderNote,
                'status' => 'pending',
            ]);

            $returnUrl = route('payments.return', ['pid' => $payment->id], true);

            $result = $driver->initiatePayment($amountDecimal, [
                'payment_id' => $payment->id,
                'user_id' => $request->user()->id,
                'currency' => 'INR',
                'customer_email' => $request->user()->email,
                'customer_name' => $request->user()->name,
                'customer_phone' => (string) $request->user()->phone,
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
            } elseif ($success) {
                $payment->update([
                    'gateway_reference' => is_string($reference) ? $reference : null,
                    'driver_payload' => $result,
                    'status' => 'completed',
                ]);

                $payment = $payment->fresh();
                if ($payment) {
                    $this->createCardPaymentTransaction($request->user()->id, $payment, $amountDecimal);
                    $this->orders->recordPaymentSuccess($payment);
                }
            } else {
                $payment->update([
                    'gateway_reference' => is_string($reference) ? $reference : null,
                    'driver_payload' => $result,
                    'status' => 'failed',
                ]);
                $order->update(['payment_status' => 'failed']);
            }

            return $payment->fresh();
        });

        if (! $payment) {
            return back()->withInput()->withErrors(['amount' => 'Could not start payment.']);
        }

        $payload = $payment->driver_payload ?? [];
        $hostedDone = is_array($payload) && (($payload['mode'] ?? '') === 'cashfree_hosted') && $payment->status === 'pending';

        if ($hostedDone) {
            return redirect()->route('payments.checkout', $payment);
        }

        return $this->redirectToResult($payment);
    }

    public function checkout(Payment $payment): View|RedirectResponse
    {
        $this->authorizePayment($payment);

        if ($payment->status !== 'pending') {
            return $this->redirectToResult($payment);
        }

        $payload = $payment->driver_payload ?? [];
        if (! is_array($payload) || ($payload['mode'] ?? '') !== 'cashfree_hosted') {
            return redirect()
                ->route('payments.create')
                ->with('status', 'Checkout is not available for this payment.');
        }

        $sessionId = $payload['payment_session_id'] ?? null;
        $environment = $payload['environment'] ?? 'sandbox';
        if (! is_string($sessionId) || $sessionId === '' || ! is_string($environment)) {
            return redirect()
                ->route('payments.create')
                ->with('status', 'Payment session missing. Please start a new payment.');
        }

        $payment->load('order.freelancer');

        return view('payments.checkout', [
            'payment' => $payment,
            'paymentSessionId' => $sessionId,
            'cashfreeMode' => $environment === 'production' ? 'production' : 'sandbox',
        ]);
    }

    /** Gateway return URL — works for Cashfree and future hosted checkouts. */
    public function returnFromGateway(Request $request): RedirectResponse
    {
        $payment = $this->resolveReturnPayment($request);

        if (! $payment) {
            return redirect()->route('payments.create')->withErrors(['amount' => 'Invalid payment return link.']);
        }

        $payment = $this->paymentReturns->syncFromGateway($payment);

        return $this->redirectToResult($payment);
    }

    public function cashfreeReturn(Request $request): RedirectResponse
    {
        return $this->returnFromGateway($request);
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

    protected function createCardPaymentTransaction(int $userId, Payment $payment, string $amountDecimal): void
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
            'user_id' => $userId,
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

    protected function resolveReturnPayment(Request $request): ?Payment
    {
        $pid = (int) $request->query('pid', 0);
        if ($pid <= 0) {
            return null;
        }

        return Payment::query()
            ->whereKey($pid)
            ->where('user_id', $request->user()->id)
            ->first();
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
