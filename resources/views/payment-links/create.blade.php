@extends('layouts.app')

@section('title', 'New payment link — '.config('app.name'))
@section('page_heading', 'New payment link')
@section('page_subheading', 'Set amount, usage limit & expiry — share with clients')

@section('content')
    <div class="mx-auto max-w-xl overflow-hidden rounded-2xl border border-slate-200/90 bg-white shadow-lg ring-1 ring-slate-900/5">
        <div class="border-b border-indigo-100 bg-gradient-to-r from-indigo-600 to-violet-600 px-6 py-6 text-white">
            <h1 class="text-xl font-bold">Create payment link</h1>
            <p class="mt-1 text-sm text-white/90">Your client opens the link, logs in, and pays via UPI, card, or net banking.</p>
        </div>

        <form method="POST" action="{{ route('payment-links.store') }}" class="space-y-6 p-6 sm:p-8">
            @csrf

            <div>
                <p class="block text-sm font-semibold text-slate-800">Amount</p>
                <div class="mt-3 grid grid-cols-2 gap-2">
                    <label class="cursor-pointer rounded-xl border px-4 py-3 text-sm font-semibold transition {{ old('amount_type', 'fixed') === 'fixed' ? 'border-indigo-500 bg-indigo-50 text-indigo-800' : 'border-slate-200 text-slate-700 hover:bg-slate-50' }}">
                        <input type="radio" name="amount_type" value="fixed" class="sr-only" {{ old('amount_type', 'fixed') === 'fixed' ? 'checked' : '' }}>
                        Fixed amount
                    </label>
                    <label class="cursor-pointer rounded-xl border px-4 py-3 text-sm font-semibold transition {{ old('amount_type') === 'open' ? 'border-indigo-500 bg-indigo-50 text-indigo-800' : 'border-slate-200 text-slate-700 hover:bg-slate-50' }}">
                        <input type="radio" name="amount_type" value="open" class="sr-only" {{ old('amount_type') === 'open' ? 'checked' : '' }}>
                        Client enters amount
                    </label>
                </div>
                <div id="fixed-amount-wrap" class="relative mt-4 {{ old('amount_type') === 'open' ? 'hidden' : '' }}">
                    <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 font-bold text-slate-500">₹</span>
                    <input id="amount" name="amount" type="text" inputmode="decimal" value="{{ old('amount') }}"
                        placeholder="0.00"
                        class="block w-full rounded-xl border border-slate-200 bg-slate-50/80 py-3 pl-9 pr-4 text-lg font-semibold shadow-inner focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-500/15">
                    <p class="mt-1.5 text-xs text-slate-500">Between ₹{{ number_format($minAmount, 0) }} and ₹{{ number_format($maxAmount, 0) }}</p>
                </div>
                @error('amount')
                    <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="usage_limit" class="block text-sm font-semibold text-slate-800">How many times can this link be used?</label>
                <select id="usage_limit" name="usage_limit"
                    class="mt-2 block w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm shadow-inner focus:border-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-500/15">
                    <option value="once" @selected(old('usage_limit', 'once') === 'once')>One-time use</option>
                    <option value="ten" @selected(old('usage_limit') === 'ten')>10 times</option>
                    <option value="unlimited" @selected(old('usage_limit') === 'unlimited')>Unlimited</option>
                </select>
                @error('usage_limit')
                    <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-semibold text-slate-800">Note for client <span class="font-normal text-slate-500">(optional)</span></label>
                <input id="description" name="description" type="text" value="{{ old('description') }}" maxlength="500"
                    placeholder="e.g. Website design — milestone 2"
                    class="mt-2 block w-full rounded-xl border border-slate-200 px-4 py-3 text-sm shadow-inner focus:border-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-500/15">
                @error('description')
                    <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="expires_on" class="block text-sm font-semibold text-slate-800">Expiry date <span class="font-normal text-slate-500">(optional)</span></label>
                    <input id="expires_on" name="expires_on" type="date" value="{{ old('expires_on') }}" min="{{ now()->toDateString() }}"
                        class="mt-2 block w-full rounded-xl border border-slate-200 px-4 py-3 text-sm shadow-inner focus:border-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-500/15">
                    @error('expires_on')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="expires_in_days" class="block text-sm font-semibold text-slate-800">Or expires in (days)</label>
                    <input id="expires_in_days" name="expires_in_days" type="number" min="1" max="{{ $maxExpiryDays }}"
                        value="{{ old('expires_in_days', $defaultExpiryDays) }}"
                        class="mt-2 block w-full rounded-xl border border-slate-200 px-4 py-3 text-sm shadow-inner focus:border-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-500/15">
                    <p class="mt-1.5 text-xs text-slate-500">Used if no date above · default {{ $defaultExpiryDays }} days</p>
                    @error('expires_in_days')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3">
                <x-payment-methods variant="light" size="sm" :show-label="false" />
            </div>

            <div class="flex flex-wrap gap-3 pt-2">
                <button type="submit" class="rounded-2xl bg-gradient-to-r from-indigo-600 to-violet-600 px-6 py-3 text-sm font-bold text-white shadow-md hover:brightness-110">
                    Create link
                </button>
                <a href="{{ route('payment-links.index') }}" class="rounded-2xl border border-slate-200 px-6 py-3 text-sm font-bold text-slate-700 hover:bg-slate-50">Cancel</a>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        document.querySelectorAll('input[name="amount_type"]').forEach((radio) => {
            radio.addEventListener('change', () => {
                const open = document.querySelector('input[name="amount_type"]:checked')?.value === 'open';
                document.getElementById('fixed-amount-wrap')?.classList.toggle('hidden', open);
                document.getElementById('amount')?.toggleAttribute('required', !open);
            });
        });
        document.querySelector('input[name="amount_type"]:checked')?.dispatchEvent(new Event('change'));
    </script>
    @endpush
@endsection
