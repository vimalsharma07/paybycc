@extends('layouts.app')

@section('title', 'Pay — '.config('app.name'))

@php
    $remarkPresets = ['Education', 'Fees', 'Rent', 'Services', 'Healthcare', 'Shopping', 'Travel', 'Bills', 'Subscription'];
@endphp

@section('content')
    <div class="overflow-hidden rounded-2xl border border-slate-200/90 bg-white shadow-lg shadow-slate-200/60 ring-1 ring-slate-900/5 sm:rounded-3xl">
        <div class="border-b border-indigo-100 bg-gradient-to-r from-indigo-600 via-violet-600 to-fuchsia-600 px-6 py-8 text-white sm:px-10 sm:py-10">
            <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">Pay</h1>
            <p class="mt-2 max-w-xl text-sm leading-relaxed text-white/90">Enter amount in INR and add a short note so you can recognise this payment later. You’ll finish with your card on our secure Cashfree checkout.</p>
        </div>

        <div class="p-6 sm:p-8 lg:p-10">
        @if ($gateway)
            <form method="POST" action="{{ route('payments.store') }}" class="mx-auto max-w-xl space-y-8" id="pay-form">
                @csrf

                <div class="space-y-2">
                    <label for="amount" class="block text-sm font-semibold text-slate-800">Amount</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-lg font-semibold text-slate-400">₹</span>
                        <input id="amount" name="amount" type="text" inputmode="decimal" value="{{ old('amount') }}" required autocomplete="transaction-amount" placeholder="0.00"
                            class="block w-full rounded-2xl border border-slate-200 bg-slate-50/80 py-4 pl-10 pr-4 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 shadow-inner shadow-slate-200/50 transition placeholder:text-slate-400 focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-500/15 @error('amount') border-red-400 ring-2 ring-red-200 @enderror">
                    </div>
                    @error('amount')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="rounded-2xl border border-indigo-100 bg-gradient-to-br from-white via-indigo-50/40 to-violet-50/50 p-5 shadow-inner shadow-indigo-950/5 sm:p-6">
                    <div class="flex flex-wrap items-end justify-between gap-2">
                        <div>
                            <label for="remark" class="text-sm font-semibold text-slate-900">Payment note</label>
                            <p class="mt-0.5 text-xs text-slate-600">Optional — e.g. school fees, monthly rent, or a client invoice.</p>
                        </div>
                        <span class="text-xs font-medium tabular-nums text-slate-500"><span id="remark-count">0</span> / 160</span>
                    </div>

                    <div class="mt-4 flex flex-wrap gap-2" role="group" aria-label="Quick note suggestions">
                        @foreach ($remarkPresets as $preset)
                            <button type="button" data-remark-preset="{{ $preset }}"
                                class="remark-chip inline-flex items-center rounded-full border border-slate-200/90 bg-white px-3.5 py-1.5 text-xs font-semibold text-slate-700 shadow-sm transition hover:border-indigo-300 hover:bg-indigo-50 hover:text-indigo-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 {{ old('remark') === $preset ? 'border-indigo-500 bg-indigo-50 text-indigo-950 ring-2 ring-indigo-400/40' : '' }}">
                                {{ $preset }}
                            </button>
                        @endforeach
                    </div>

                    <textarea id="remark" name="remark" rows="3" maxlength="160" placeholder="Type your own note or tap a suggestion above…"
                        class="mt-4 block w-full resize-y rounded-xl border border-slate-200 bg-white/90 px-4 py-3 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/25 @error('remark') border-red-400 @enderror">{{ old('remark') }}</textarea>
                    @error('remark')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50/90 px-5 py-5">
                    <input type="hidden" name="auto_settle_to_bank" value="0">
                    <div class="flex items-start gap-4">
                        <input id="auto_settle_to_bank" name="auto_settle_to_bank" type="checkbox" value="1" class="mt-1 h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                            @checked(old('auto_settle_to_bank', $wallet->auto_settle_to_bank))>
                        <label for="auto_settle_to_bank" class="text-sm text-slate-700">
                            <span class="font-semibold text-slate-900">Send received funds to my bank automatically</span>
                            <span class="mt-1 block text-xs text-slate-500">You can change this anytime under Wallet.</span>
                        </label>
                    </div>
                </div>

                <button type="submit" class="inline-flex w-full items-center justify-center rounded-2xl bg-gradient-to-r from-indigo-600 to-violet-600 px-5 py-4 text-base font-bold text-white shadow-lg shadow-indigo-900/20 transition hover:brightness-110 focus:outline-none focus-visible:ring-4 focus-visible:ring-indigo-500/40">
                    Continue to pay
                </button>
            </form>

            <script>
                (function () {
                    var ta = document.getElementById('remark');
                    var countEl = document.getElementById('remark-count');
                    var chips = document.querySelectorAll('[data-remark-preset]');
                    if (!ta || !countEl) return;

                    function updateCount() {
                        countEl.textContent = String(ta.value.length);
                        var nearLimit = ta.value.length >= 140;
                        countEl.parentElement.classList.toggle('text-amber-700', nearLimit);
                        countEl.parentElement.classList.toggle('font-semibold', nearLimit);
                    }

                    function clearChipStyles() {
                        chips.forEach(function (btn) {
                            btn.classList.remove('border-indigo-500', 'bg-indigo-50', 'text-indigo-950', 'ring-2', 'ring-indigo-400/40');
                            btn.classList.add('border-slate-200/90', 'bg-white', 'text-slate-700');
                        });
                    }

                    function highlightMatchingChip() {
                        var v = ta.value.trim();
                        clearChipStyles();
                        chips.forEach(function (btn) {
                            if (btn.getAttribute('data-remark-preset') === v) {
                                btn.classList.remove('border-slate-200/90', 'bg-white', 'text-slate-700');
                                btn.classList.add('border-indigo-500', 'bg-indigo-50', 'text-indigo-950', 'ring-2', 'ring-indigo-400/40');
                            }
                        });
                    }

                    ta.addEventListener('input', function () {
                        updateCount();
                        highlightMatchingChip();
                    });

                    chips.forEach(function (btn) {
                        btn.addEventListener('click', function () {
                            ta.value = btn.getAttribute('data-remark-preset') || '';
                            ta.dispatchEvent(new Event('input'));
                            ta.focus();
                        });
                    });

                    updateCount();
                    highlightMatchingChip();
                })();
            </script>
        @else
            <div class="rounded-2xl border border-amber-200 bg-amber-50 px-5 py-5 text-sm text-amber-950">
                Payments are temporarily unavailable. Please try again later or contact support.
            </div>
        @endif
        </div>
    </div>
@endsection
