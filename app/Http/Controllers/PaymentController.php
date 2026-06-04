<?php

namespace App\Http\Controllers;

use App\Gateways\Cashfree;
use App\Models\Gateway;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Services\Orders\OrderService;
use App\Services\Orders\SellerSearch;
use App\Services\Payments\CashfreeClient;
use App\Services\Payments\CashfreeGatewaySync;
use App\Services\Payments\GatewayManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use InvalidArgumentException;

class PaymentController extends Controller
{
    public function __construct(
        protected CashfreeClient $cashfreeClient,
        protected OrderService $orders,
        protected SellerSearch $sellerSearch,
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
        ]);
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

            $returnUrl = route('payments.cashfree.return', ['pid' => $payment->id], true);

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

        $message = is_array($payload)
            ? (string) ($payload['message'] ?? ($payload['error'] ?? ($payment->status === 'completed' ? 'Payment recorded successfully.' : 'Payment could not be started.')))
            : 'Done.';

        return redirect()
            ->route('payments.create', ['freelancer' => $freelancer->id])
            ->with('status', $message.' Reference: #'.$payment->id.($payment->gateway_reference ? ' · '.$payment->gateway_reference : ''));
    }

    public function checkout(Payment $payment): View|RedirectResponse
    {
        $this->authorizePayment($payment);

        if ($payment->status !== 'pending') {
            return redirect()
                ->route('payments.create')
                ->with('status', 'This payment is no longer pending.');
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

    public function cashfreeReturn(Request $request): RedirectResponse
    {
        $pid = (int) $request->query('pid', 0);
        if ($pid <= 0) {
            return redirect()->route('payments.create')->withErrors(['amount' => 'Invalid return link.']);
        }

        $payment = Payment::query()->whereKey($pid)->where('user_id', $request->user()->id)->first();
        if (! $payment) {
            abort(403);
        }

        $payment->load('gateway');
        $message = $this->syncCashfreeOrderAndFinalize($payment);

        $freelancerId = $payment->order?->freelancer_id;

        return redirect()
            ->route('payments.create', $freelancerId ? ['freelancer' => $freelancerId] : [])
            ->with('status', $message);
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
        $note = $userNote !== ''
            ? $userNote.' · Cashfree card'
            : 'Card payment via Cashfree';

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

    protected function syncCashfreeOrderAndFinalize(Payment $payment): string
    {
        if ($payment->status === 'completed') {
            return 'Payment already completed. Reference: #'.$payment->id.'.';
        }

        if ($payment->status === 'failed') {
            return 'This payment was not successful. Reference: #'.$payment->id.'.';
        }

        $payload = $payment->driver_payload ?? [];
        if (! is_array($payload) || ($payload['mode'] ?? '') !== 'cashfree_hosted') {
            return 'Unable to verify this payment.';
        }

        $gateway = $payment->gateway;
        if (! $gateway instanceof Gateway) {
            return 'Gateway configuration missing.';
        }

        $creds = is_array($gateway->credentials) ? $gateway->credentials : [];
        $clientId = (string) ($creds['client_id'] ?? '');
        $secret = (string) ($creds['client_secret'] ?? '');
        $orderId = $payment->gateway_reference;

        if ($clientId === '' || $secret === '' || ! is_string($orderId) || $orderId === '') {
            return 'Payment could not be verified (missing gateway or order reference).';
        }

        $sandbox = Cashfree::isSandboxCredentials($creds);
        $api = $this->cashfreeClient->fetchOrder($clientId, $secret, $sandbox, $orderId);

        if (! $api['ok'] || ! isset($api['data']) || ! is_array($api['data'])) {
            return 'Could not confirm payment with Cashfree yet. If you were charged, contact support with reference #'.$payment->id.'.';
        }

        $data = $api['data'];
        $orderStatus = strtoupper((string) ($data['order_status'] ?? ''));
        $orderAmount = isset($data['order_amount']) ? (float) $data['order_amount'] : null;

        if ($orderAmount !== null && abs($orderAmount - (float) $payment->amount) > 0.02) {
            report(new \RuntimeException('Cashfree order amount mismatch for payment '.$payment->id));

            return 'Payment verification failed (amount mismatch). Contact support with reference #'.$payment->id.'.';
        }

        if ($orderStatus === 'PAID') {
            return $this->finalizePaidCashfreePayment($payment, $data);
        }

        if (in_array($orderStatus, ['EXPIRED', 'TERMINATED'], true)) {
            $payment->update(['status' => 'failed']);
            $payment->order?->update(['payment_status' => 'failed']);

            return 'Payment window expired or was cancelled. Reference: #'.$payment->id.'.';
        }

        return 'Payment not completed yet (status: '.$orderStatus.'). Reference: #'.$payment->id.'. You can retry from Pay or contact support.';
    }

    protected function finalizePaidCashfreePayment(Payment $payment, array $cashfreeOrderData): string
    {
        $amountDecimal = number_format((float) $payment->amount, 2, '.', '');

        DB::transaction(function () use ($payment, $cashfreeOrderData, $amountDecimal) {
            $locked = Payment::query()->whereKey($payment->id)->lockForUpdate()->first();
            if (! $locked) {
                return;
            }

            if ($locked->status === 'completed') {
                return;
            }

            if ($locked->status !== 'pending') {
                return;
            }

            $basePayload = is_array($locked->driver_payload) ? $locked->driver_payload : [];

            $locked->update([
                'status' => 'completed',
                'driver_payload' => array_merge($basePayload, [
                    'cashfree_order_snapshot' => $cashfreeOrderData,
                ]),
            ]);

            $fresh = $locked->fresh();
            if ($fresh) {
                $this->createCardPaymentTransaction((int) $locked->user_id, $fresh, $amountDecimal);
                $this->orders->recordPaymentSuccess($fresh);
            }
        });

        return 'Payment successful. Reference: #'.$payment->id.' · '.$payment->gateway_reference;
    }
}
