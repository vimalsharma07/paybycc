@extends('layouts.app')

@section('title', 'Pay — '.config('app.name'))
@section('page_heading', 'Pay now')
@section('page_subheading', 'Pay via UPI, card, net banking, or wallet')

@php
    $remarkPresets = ['Project milestone', 'Freelance work', 'Consulting', 'Design', 'Development', 'Monthly retainer'];
@endphp

@section('content')
    <div class="overflow-hidden rounded-2xl border border-slate-200/90 bg-white shadow-lg shadow-slate-200/60 ring-1 ring-slate-900/5 sm:rounded-3xl">
        <div class="border-b border-indigo-100 bg-gradient-to-r from-indigo-600 via-violet-600 to-fuchsia-600 px-6 py-8 text-white sm:px-10 sm:py-10">
            <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">Pay a freelancer</h1>
            <p class="mt-2 max-w-xl text-sm leading-relaxed text-white/90">Search for a seller, enter the amount in INR, then choose UPI, credit card, debit card, net banking, or wallet at secure checkout.</p>
            <div class="mt-4">
                <x-payment-methods variant="dark" size="sm" :show-label="false" />
            </div>
            <p class="mt-3">
                <a href="{{ route('marketplace.index') }}" class="text-sm font-semibold text-white/90 underline decoration-white/40 underline-offset-4 hover:text-white">Browse all freelancers →</a>
            </p>
        </div>

        <div class="p-6 sm:p-8 lg:p-10">
        @if ($gateway)
            <div class="mx-auto max-w-xl space-y-3">
                <label for="freelancer-search" class="block text-sm font-semibold text-slate-800">Find freelancer / seller</label>
                <form method="GET" action="{{ route('payments.create') }}" class="flex gap-2">
                    @if ($selectedFreelancer)
                        <input type="hidden" name="freelancer" value="{{ $selectedFreelancer->id }}">
                    @endif
                    <input id="freelancer-search" name="q" type="search" value="{{ $searchQ }}" placeholder="Name, code, company, skill…" autocomplete="off"
                        class="block min-w-0 flex-1 rounded-xl border border-slate-200 bg-slate-50/80 px-4 py-3 text-sm shadow-inner focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-500/15">
                    <button type="submit" class="shrink-0 rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-3 text-sm font-bold text-indigo-700 hover:bg-indigo-100">Search</button>
                </form>
            </div>

            <form method="POST" action="{{ route('payments.store') }}" class="mx-auto mt-8 max-w-xl space-y-8" id="pay-form">
                @csrf

                <div class="space-y-3">

                    @if ($searchResults->isNotEmpty())
                        <ul class="divide-y divide-slate-100 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                            @foreach ($searchResults as $f)
                                <li>
                                    <a href="{{ route('payments.create', ['freelancer' => $f->id, 'q' => $searchQ]) }}"
                                        class="flex flex-col gap-1 px-4 py-3 transition hover:bg-indigo-50/80 sm:flex-row sm:items-center sm:justify-between {{ $selectedFreelancer && $selectedFreelancer->id === $f->id ? 'bg-indigo-50 ring-2 ring-inset ring-indigo-400/50' : '' }}">
                                        <span>
                                            <span class="font-bold text-slate-900">{{ $f->name }}</span>
                                            @if ($f->company_name)
                                                <span class="text-slate-600"> · {{ $f->company_name }}</span>
                                            @endif
                                            <span class="mt-0.5 block font-mono text-xs text-slate-500">{{ $f->user_code }}</span>
                                        </span>
                                        <span class="text-xs font-semibold {{ $f->hasActiveKyc() ? 'text-emerald-700' : 'text-amber-700' }}">
                                            {{ $f->hasActiveKyc() ? 'Can receive payouts' : 'KYC pending — payment held for seller' }}
                                        </span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @elseif ($searchQ !== '')
                        <p class="text-sm text-slate-600">No sellers found. Try another name or <a href="{{ route('marketplace.index') }}" class="font-semibold text-indigo-600 hover:underline">browse marketplace</a>.</p>
                    @endif

                    <input type="hidden" name="freelancer_id" value="{{ old('freelancer_id', $selectedFreelancer?->id) }}" required>
                    @error('freelancer_id')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    @if ($selectedFreelancer)
                        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-950">
                            <p class="font-semibold">Paying: {{ $selectedFreelancer->name }}</p>
                            <p class="mt-1 text-emerald-800/90">{{ $selectedFreelancer->user_code }}@if ($selectedFreelancer->city) · {{ $selectedFreelancer->city }}@endif</p>
                            @unless ($selectedFreelancer->hasActiveKyc())
                                <p class="mt-2 text-xs text-amber-900">This seller has not completed KYC — funds will be held until they verify.</p>
                            @endunless
                        </div>
                    @else
                        <p class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-950">Select a freelancer above before paying.</p>
                    @endif
                </div>

                <div class="space-y-2 {{ $selectedFreelancer ? '' : 'pointer-events-none opacity-50' }}">
                    <label for="amount" class="block text-sm font-semibold text-slate-800">Amount</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-lg font-semibold text-slate-400">₹</span>
                        <input id="amount" name="amount" type="text" inputmode="decimal" value="{{ old('amount') }}" required autocomplete="transaction-amount" placeholder="0.00" @disabled(! $selectedFreelancer)
                            class="block w-full rounded-2xl border border-slate-200 bg-slate-50/80 py-4 pl-10 pr-4 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 shadow-inner shadow-slate-200/50 transition placeholder:text-slate-400 focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-500/15 @error('amount') border-red-400 ring-2 ring-red-200 @enderror">
                    </div>
                    @error('amount')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    @include('payments.partials.fee-estimate', ['commerceRates' => $commerceRates ?? []])
                </div>

                <div class="rounded-2xl border border-indigo-100 bg-gradient-to-br from-white via-indigo-50/40 to-violet-50/50 p-5 shadow-inner shadow-indigo-950/5 sm:p-6 {{ $selectedFreelancer ? '' : 'pointer-events-none opacity-50' }}">
                    <div class="flex flex-wrap items-end justify-between gap-2">
                        <div>
                            <label for="remark" class="text-sm font-semibold text-slate-900">Payment note</label>
                            <p class="mt-0.5 text-xs text-slate-600">Optional — shown on your receipt and order.</p>
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

                    <textarea id="remark" name="remark" rows="3" maxlength="160" placeholder="Type your own note or tap a suggestion above…" @disabled(! $selectedFreelancer)
                        class="mt-4 block w-full resize-y rounded-xl border border-slate-200 bg-white/90 px-4 py-3 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/25 @error('remark') border-red-400 @enderror">{{ old('remark') }}</textarea>
                    @error('remark')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" @disabled(! $selectedFreelancer) class="inline-flex w-full items-center justify-center rounded-2xl bg-gradient-to-r from-indigo-600 to-violet-600 px-5 py-4 text-base font-bold text-white shadow-lg shadow-indigo-900/20 transition hover:brightness-110 focus:outline-none focus-visible:ring-4 focus-visible:ring-indigo-500/40 disabled:cursor-not-allowed disabled:opacity-50">
                    Continue to checkout
                </button>
            </form>

            <script>
                (function () {
                    var feePanel = document.getElementById('fee-estimate-panel');
                    var amountInput = document.getElementById('amount');
                    var freelancerInput = document.querySelector('input[name="freelancer_id"]');
                    var feeEstimateUrl = @json(route('payments.fee-estimate'));
                    var feeTimer = null;

                    function inr(n) {
                        if (n === null || n === undefined || isNaN(n)) return '—';
                        return '₹' + Number(n).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    }

                    function setFeeLine(id, value, hideZero) {
                        var el = document.getElementById(id);
                        var wrap = document.getElementById(id + '-wrap');
                        if (!el) return;
                        var num = Number(value) || 0;
                        el.textContent = inr(num);
                        if (wrap) {
                            wrap.classList.toggle('hidden', hideZero && num <= 0);
                        }
                    }

                    function fetchFeeEstimate() {
                        if (!feePanel || !amountInput || !freelancerInput) return;
                        var fid = freelancerInput.value;
                        var amt = parseFloat(String(amountInput.value).replace(/,/g, ''));
                        var errEl = document.getElementById('fee-estimate-error');
                        if (!fid || !amt || amt <= 0) {
                            feePanel.classList.add('hidden');
                            return;
                        }
                        var url = feeEstimateUrl + '?freelancer_id=' + encodeURIComponent(fid) + '&amount=' + encodeURIComponent(amt);
                        fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                            .then(function (r) { return r.json().then(function (j) { return { ok: r.ok, j: j }; }); })
                            .then(function (res) {
                                feePanel.classList.remove('hidden');
                                if (!res.ok) {
                                    if (errEl) {
                                        errEl.textContent = res.j.message || 'Could not estimate fees.';
                                        errEl.classList.remove('hidden');
                                    }
                                    document.getElementById('fee-net').textContent = '—';
                                    return;
                                }
                                if (errEl) errEl.classList.add('hidden');
                                var f = res.j.fees || {};
                                document.getElementById('fee-order-amount').textContent = inr(f.order_amount);
                                var procLabel = document.getElementById('fee-processing-label');
                                if (procLabel) {
                                    procLabel.textContent = f.processing_fee > 0 ? '' : '(not applied below threshold)';
                                }
                                setFeeLine('fee-processing', f.processing_fee, true);
                                document.getElementById('fee-gst').textContent = inr(f.gst_on_processing_fee);
                                document.getElementById('fee-flat').textContent = inr(f.flat_order_fee);
                                var tcsWrap = document.getElementById('fee-tcs-wrap');
                                if (tcsWrap) tcsWrap.classList.toggle('hidden', !f.tcs_applied);
                                document.getElementById('fee-tcs').textContent = inr(f.tcs_amount);
                                var tdsWrap = document.getElementById('fee-tds-wrap');
                                if (tdsWrap) tdsWrap.classList.toggle('hidden', !f.tds_applied);
                                document.getElementById('fee-tds').textContent = inr(f.tds_amount);
                                document.getElementById('fee-net').textContent = inr(f.net_settlement);
                            })
                            .catch(function () {
                                feePanel.classList.add('hidden');
                            });
                    }

                    if (amountInput && freelancerInput) {
                        amountInput.addEventListener('input', function () {
                            clearTimeout(feeTimer);
                            feeTimer = setTimeout(fetchFeeEstimate, 400);
                        });
                        fetchFeeEstimate();
                    }

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
            <div class="mx-auto max-w-xl space-y-4 rounded-2xl border border-amber-200 bg-amber-50 px-5 py-5 text-sm text-amber-950">
                <p class="font-semibold">Payments need a Cashfree gateway</p>
                <p class="leading-relaxed">No active payment gateway is configured. To enable UPI, card, and net banking checkout:</p>
                <ol class="list-decimal space-y-2 pl-5">
                    <li>Add your Cashfree sandbox keys to <span class="font-mono text-xs">.env</span> (see below).</li>
                    <li>Run <span class="font-mono text-xs">php artisan db:seed --class=GatewaySeeder</span> or reload this page (auto-sync when keys are set).</li>
                    <li>Or configure manually under <strong>Admin → Gateways</strong> if you are an admin.</li>
                </ol>
                <pre class="overflow-x-auto rounded-lg bg-amber-100/80 p-3 text-xs text-amber-950">CASHFREE_CLIENT_ID=your_app_id
CASHFREE_CLIENT_SECRET=your_secret
CASHFREE_ENV=sandbox</pre>
                @if ($gatewayConfigured ?? false)
                    <p class="text-emerald-800">Keys detected in .env — try refreshing this page or run the gateway seeder.</p>
                @endif
            </div>
        @endif
        </div>
    </div>
@endsection
