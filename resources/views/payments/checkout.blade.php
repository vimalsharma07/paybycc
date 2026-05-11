@extends('layouts.app')

@section('title', 'Secure checkout — '.config('app.name'))

@section('content')
    <div class="mx-auto max-w-lg rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
        <h1 class="text-xl font-semibold text-slate-900">Redirecting to secure payment</h1>
        <p class="mt-2 text-sm text-slate-600">
            You are paying <span class="font-mono font-semibold text-slate-900">₹{{ number_format((float) $payment->amount, 2) }}</span> with <strong>card only</strong> (credit or debit) via Cashfree.
        </p>
        <p class="mt-4 text-xs text-slate-500">If nothing happens, use the button below. Do not refresh while paying.</p>
        <button type="button" id="cf-pay-btn" class="mt-6 inline-flex w-full justify-center rounded-lg bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2">
            Open Cashfree checkout
        </button>
        <p class="mt-6 text-center text-xs text-slate-500">
            <a href="{{ route('payments.create') }}" class="font-medium text-indigo-600 hover:text-indigo-500">Cancel and return to Pay</a>
        </p>
    </div>

    <script src="https://sdk.cashfree.com/js/v3/cashfree.js"></script>
    <script>
        (function () {
            var mode = @json($cashfreeMode);
            var sessionId = @json($paymentSessionId);
            function openCheckout() {
                if (typeof Cashfree !== 'function') {
                    alert('Payment script could not load. Check your network or ad blocker.');
                    return;
                }
                var cashfree = Cashfree({ mode: mode });
                cashfree.checkout({
                    paymentSessionId: sessionId,
                    redirectTarget: '_self',
                });
            }
            document.getElementById('cf-pay-btn').addEventListener('click', openCheckout);
            setTimeout(openCheckout, 400);
        })();
    </script>
@endsection
