<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Payments\GatewayManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function create(GatewayManager $gatewayManager): View
    {
        $primary = $gatewayManager->primaryGateway();

        $wallet = Wallet::firstOrCreate(
            ['user_id' => auth()->id()],
            [
                'balance' => 0,
                'auto_settle_to_bank' => true,
                'default_bank_id' => null,
            ]
        );

        return view('payments.create', [
            'gateway' => $primary,
            'wallet' => $wallet,
        ]);
    }

    public function store(Request $request, GatewayManager $gatewayManager): RedirectResponse
    {
        $gateway = $gatewayManager->primaryGateway();

        if (! $gateway || ! $gateway->isActive()) {
            return back()
                ->withInput()
                ->withErrors(['amount' => 'Payments are unavailable: no active primary gateway. Contact support.']);
        }

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
        ]);

        $wallet = Wallet::firstOrCreate(
            ['user_id' => $request->user()->id],
            [
                'balance' => 0,
                'auto_settle_to_bank' => true,
                'default_bank_id' => null,
            ]
        );
        $wallet->update([
            'auto_settle_to_bank' => $request->boolean('auto_settle_to_bank'),
        ]);

        $amountDecimal = number_format((float) $validated['amount'], 2, '.', '');
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

        $payment = DB::transaction(function () use ($request, $gateway, $amountDecimal, $driver) {
            $payment = Payment::create([
                'user_id' => $request->user()->id,
                'gateway_id' => $gateway->id,
                'amount' => $amountDecimal,
                'currency' => 'INR',
                'status' => 'pending',
            ]);

            $result = $driver->initiatePayment($amountDecimal, [
                'payment_id' => $payment->id,
                'user_id' => $request->user()->id,
                'currency' => 'INR',
            ]);

            $reference = $result['reference']
                ?? $result['gateway_reference']
                ?? $result['payment_reference']
                ?? null;

            $success = (bool) ($result['success'] ?? false);

            $payment->update([
                'gateway_reference' => is_string($reference) ? $reference : null,
                'driver_payload' => $result,
                'status' => $success ? 'completed' : 'failed',
            ]);

            $payment = $payment->fresh();

            if ($success && $payment) {
                $bufferDays = max(0, (int) config('paybycc.settlement_buffer_days', 2));

                Transaction::create([
                    'user_id' => $request->user()->id,
                    'bank_id' => null,
                    'payment_id' => $payment->id,
                    'parent_transaction_id' => null,
                    'type' => Transaction::TYPE_CARD_PAYMENT,
                    'amount' => $amountDecimal,
                    'currency' => 'INR',
                    'status' => 'completed',
                    'settlement_trigger_at' => now()->addDays($bufferDays),
                    'settled_at' => null,
                    'note' => 'Card / online payment via PayByCC',
                ]);
            }

            return $payment;
        });

        $payload = $payment->driver_payload ?? [];
        $message = is_array($payload)
            ? (string) ($payload['message'] ?? ($payment->status === 'completed' ? 'Payment initiated successfully.' : 'Payment could not be completed.'))
            : 'Done.';

        return redirect()
            ->route('payments.create')
            ->with('status', $message.' Reference: #'.$payment->id.($payment->gateway_reference ? ' · '.$payment->gateway_reference : ''));
    }
}
