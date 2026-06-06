@if ($paymentLink->isOpenAmount())
    <div>
        <label for="pay-amount" class="block text-sm font-semibold text-slate-300">Amount (INR)</label>
        <div class="relative mt-2">
            <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 font-bold text-slate-500">₹</span>
            <input id="pay-amount" name="amount" type="text" inputmode="decimal" value="{{ old('amount') }}" required
                placeholder="0.00"
                class="block w-full rounded-xl border border-white/10 bg-slate-950/80 py-3 pl-9 pr-4 text-lg font-semibold text-white focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
        </div>
        @error('amount')
            <p class="mt-1 text-sm text-rose-400">{{ $message }}</p>
        @enderror
    </div>
@endif
