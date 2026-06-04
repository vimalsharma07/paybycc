@extends('layouts.app')

@section('title', 'Wallet — '.config('app.name'))
@section('page_heading', 'Wallet')
@section('page_subheading', 'Balance, auto-settlement, and payout preferences')

@section('content')
    <div class="grid gap-6 lg:grid-cols-2">
        <div class="relative overflow-hidden rounded-3xl border border-emerald-200/70 bg-gradient-to-br from-white via-emerald-50/50 to-teal-50/40 p-6 shadow-lg ring-1 ring-emerald-900/5 sm:p-8">
            <div class="pointer-events-none absolute -right-10 -top-10 h-40 w-40 rounded-full bg-emerald-400/20 blur-3xl" aria-hidden="true"></div>
            <div class="relative flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-sm font-bold uppercase tracking-wider text-emerald-800/80">Balance</h2>
                    <p class="mt-3 text-4xl font-bold tabular-nums tracking-tight text-slate-900">{{ number_format((float) $wallet->balance, 2) }} <span class="text-lg font-semibold text-emerald-800/80">INR</span></p>
                </div>
                <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-500/15 text-emerald-700 ring-1 ring-emerald-500/25" aria-hidden="true">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <p class="relative mt-5 text-sm leading-relaxed text-slate-600">Card payments add to your wallet with a settlement date. You’ll see each step here as it happens.</p>
        </div>

        <div class="relative overflow-hidden rounded-3xl border border-indigo-200/70 bg-gradient-to-br from-white via-indigo-50/40 to-violet-50/30 p-6 shadow-lg ring-1 ring-indigo-900/5 sm:p-8">
            <div class="pointer-events-none absolute -left-8 -bottom-12 h-36 w-36 rounded-full bg-violet-400/20 blur-3xl" aria-hidden="true"></div>
            <div class="relative flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-sm font-bold uppercase tracking-wider text-indigo-800/80">Settings</h2>
                    <p class="mt-1 text-sm text-slate-600">Auto-settlement and default payout bank.</p>
                </div>
                <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-500/15 text-indigo-700 ring-1 ring-indigo-500/25" aria-hidden="true">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 12h9.75M10.5 18h9.75M3.75 6h.008v.008H3.75V6zm0 6h.008v.008H3.75V12zm0 6h.008v.008H3.75V18z"/></svg>
                </span>
            </div>
            <form method="POST" action="{{ route('wallet.update') }}" class="relative mt-6 space-y-5">
                @csrf
                @method('PATCH')
                <div class="rounded-2xl border border-indigo-100 bg-white/70 px-4 py-4 shadow-inner">
                    <input type="hidden" name="auto_settle_to_bank" value="0">
                    <div class="flex items-start gap-3">
                        <input id="wallet_auto_settle" name="auto_settle_to_bank" type="checkbox" value="1" class="mt-1 h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                            @checked(old('auto_settle_to_bank', $wallet->auto_settle_to_bank))>
                        <label for="wallet_auto_settle" class="text-sm text-slate-800">
                            <span class="font-semibold">Automatically send money to my bank</span>
                            <span class="mt-1 block text-xs text-slate-600">When settlement hits your wallet, we’ll route it using your default bank.</span>
                        </label>
                    </div>
                </div>
                <div>
                    <label for="default_bank_id" class="mb-1.5 block text-sm font-semibold text-slate-800">Default bank for payouts</label>
                    <select id="default_bank_id" name="default_bank_id"
                        class="block w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm shadow-inner focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 @error('default_bank_id') border-red-400 @enderror">
                        <option value="">— Choose a bank —</option>
                        @foreach ($banks as $bank)
                            <option value="{{ $bank->id }}" @selected(old('default_bank_id', $wallet->default_bank_id) == $bank->id)>
                                {{ $bank->bank_name }} · {{ $bank->account_holder_name }}
                                @if ($bank->is_primary)
                                    (primary)
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('default_bank_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @if ($banks->isEmpty())
                        <p class="mt-2 text-xs font-medium text-amber-800">Add a bank account first — open <a href="{{ route('banks.index') }}" class="underline decoration-amber-800/40 hover:text-amber-950">Banks</a>.</p>
                    @else
                        <p class="mt-2 text-xs text-slate-500">Change anytime. This is used for automated payouts.</p>
                    @endif
                </div>
                <button type="submit" class="inline-flex w-full items-center justify-center rounded-2xl bg-gradient-to-r from-indigo-600 to-violet-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-900/20 transition hover:brightness-110 sm:w-auto">
                    Save settings
                </button>
            </form>
        </div>
    </div>

    <div class="mt-8 overflow-hidden rounded-2xl border border-slate-200/90 bg-white shadow-lg ring-1 ring-slate-900/5 lg:col-span-2">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 sm:px-6">
            <h2 class="text-sm font-bold text-slate-900">Recent transactions</h2>
            <a href="{{ route('account.transactions') }}" class="text-xs font-bold text-indigo-600 hover:underline">Full history →</a>
        </div>
        @include('partials.transaction-list', ['transactions' => $transactions])
    </div>
@endsection
