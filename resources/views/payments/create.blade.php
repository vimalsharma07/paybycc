@extends('layouts.app')

@section('title', 'Pay — '.config('app.name'))

@section('content')
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Pay</h1>
        <p class="mt-2 text-sm leading-relaxed text-slate-600">Enter the amount in INR. You’ll complete payment on our secure hosted checkout with your card.</p>

        @if ($gateway)
            <form method="POST" action="{{ route('payments.store') }}" class="mt-8 max-w-md space-y-6">
                @csrf
                <div>
                    <label for="amount" class="mb-1.5 block text-sm font-medium text-slate-700">Amount (INR)</label>
                    <input id="amount" name="amount" type="text" inputmode="decimal" value="{{ old('amount') }}" required placeholder="e.g. 500.00"
                        class="block w-full rounded-xl border border-slate-300 px-4 py-3 text-lg font-medium tabular-nums tracking-tight shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 @error('amount') border-red-500 @enderror">
                    @error('amount')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50/80 px-4 py-4">
                    <input type="hidden" name="auto_settle_to_bank" value="0">
                    <div class="flex items-start gap-3">
                        <input id="auto_settle_to_bank" name="auto_settle_to_bank" type="checkbox" value="1" class="mt-1 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                            @checked(old('auto_settle_to_bank', $wallet->auto_settle_to_bank))>
                        <label for="auto_settle_to_bank" class="text-sm text-slate-700">
                            <span class="font-medium text-slate-900">Send received funds to my bank automatically</span>
                            <span class="mt-1 block text-xs text-slate-500">You can change this anytime under Wallet.</span>
                        </label>
                    </div>
                </div>
                <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-indigo-600 px-5 py-3.5 text-sm font-semibold text-white shadow-md transition hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2">
                    Continue to pay
                </button>
            </form>
        @else
            <div class="mt-8 rounded-xl border border-amber-200 bg-amber-50 px-4 py-4 text-sm text-amber-900">
                Payments are temporarily unavailable. Please try again later or contact support.
            </div>
        @endif
    </div>
@endsection
