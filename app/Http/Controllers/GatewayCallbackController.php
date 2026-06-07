<?php

namespace App\Http\Controllers;

use App\Enums\LogLevel;
use App\Gateways\Contracts\HandlesPaymentReturn;
use App\Gateways\Contracts\HandlesPaymentWebhook;
use App\Models\Payment;
use App\Services\Logging\FlowLog;
use App\Services\Payments\GatewayManager;
use App\Services\Payments\PaymentReturnService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GatewayCallbackController extends Controller
{
    public function __construct(
        protected GatewayManager $gateways,
        protected PaymentReturnService $paymentReturns,
        protected FlowLog $flow,
    ) {}

    public function return(Request $request, string $gateway): RedirectResponse
    {
        $payment = $this->resolveReturnPayment($request);
        if (! $payment) {
            $this->flow->gateway('gateway.return.invalid', 'Invalid payment return link', [
                'gateway_code' => $gateway,
                'pid' => (int) $request->query('pid', 0),
            ], null, LogLevel::Warning);

            return redirect()->route('payments.create')->withErrors(['amount' => 'Invalid payment return link.']);
        }

        $this->flow->gateway('gateway.return.received', 'Customer returned from gateway', array_merge(
            $this->flow->gatewayContext($payment),
            ['gateway_code' => $gateway],
        ), $payment->order);

        $driver = $this->gateways->resolveDriverByCode($gateway);
        if (! $driver instanceof HandlesPaymentReturn) {
            abort(404, 'This gateway does not support return callbacks.');
        }

        $payment = $driver->handleReturn($request, $payment);
        $payment->loadMissing('order');

        $this->flow->gateway('gateway.return.processed', 'Gateway return handled', array_merge(
            $this->flow->gatewayContext($payment),
            ['gateway_code' => $gateway, 'outcome' => $this->paymentReturns->outcome($payment)],
        ), $payment->order);

        return $this->redirectToResult($payment);
    }

    public function webhook(Request $request, string $gateway): Response
    {
        $this->flow->gateway('gateway.webhook.received', 'Gateway webhook endpoint hit', [
            'gateway_code' => $gateway,
        ], null, LogLevel::Debug);

        $driver = $this->gateways->resolveDriverByCode($gateway);
        if (! $driver instanceof HandlesPaymentWebhook) {
            abort(404, 'This gateway does not support webhooks.');
        }

        return $driver->handleWebhook($request);
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
