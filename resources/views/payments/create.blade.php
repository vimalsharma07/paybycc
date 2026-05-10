@extends('layouts.app')

@section('title', 'Pay — '.config('app.name'))

@section('content')
    <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
        <h1 class="text-2xl font-semibold text-slate-900">Payment</h1>
        <p class="mt-2 text-sm text-slate-600">Pay securely using the gateway configured by your administrator.</p>

        @if ($gateway)
            <div class="mt-6 rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                <p class="font-medium">Active gateway: {{ $gateway->name }}</p>
                <p class="mt-1 text-xs text-emerald-800">Amount limits {{ $gateway->min_txn }} – {{ $gateway->max_txn }} INR @if ((float) $gateway->daily_limit > 0)· Daily cap {{ $gateway->daily_limit }} INR @endif</p>
            </div>

            <form method="POST" action="{{ route('payments.store') }}" class="mt-8 max-w-md space-y-5">
                @csrf
                <div>
                    <label for="amount" class="mb-1 block text-sm font-medium text-slate-700">Amount (INR)</label>
                    <input id="amount" name="amount" type="text" inputmode="decimal" value="{{ old('amount') }}" required placeholder="e.g. 100.00"
                        class="block w-full rounded-lg border border-slate-300 px-3 py-2 font-mono text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('amount') border-red-500 @enderror">
                    @error('amount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="inline-flex rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2">
                    Continue
                </button>
            </form>
        @else
            <div class="mt-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                No primary active gateway is configured yet. Please try again later or contact support.
            </div>
            <p class="mt-4 text-sm text-slate-600">Administrators can add gateways under <span class="font-mono text-xs">Admin → Gateways</span>.</p>
        @endif
    </div>
@endsection
