@extends('layouts.app')

@section('title', 'Wallet — '.config('app.name'))

@section('content')
    <div class="overflow-hidden rounded-3xl border border-indigo-200/80 bg-gradient-to-br from-indigo-600 via-violet-600 to-slate-900 shadow-xl shadow-indigo-900/20">
        <div class="px-6 py-8 text-white sm:px-8 sm:py-10">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div class="flex items-start gap-4">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-white/15 ring-1 ring-white/25" aria-hidden="true">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 00-2.25-2.25H12a2.25 2.25 0 00-2.25 2.25v6.75A2.25 2.25 0 009.75 21.75h-1.5A2.25 2.25 0 016 19.5V12a2.25 2.25 0 00-2.25-2.25H3"/><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 3.375H9.75A2.25 2.25 0 0112 5.625v.75m0 0h3.75m-3.75 0H9m0 0H5.625A2.25 2.25 0 003 8.25v.375c0 .621.504 1.125 1.125 1.125h16.5c.621 0 1.125-.504 1.125-1.125V8.25A2.25 2.25 0 0019.875 6H16.5"/></svg>
                    </span>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-white/70">Wallet</p>
                        <h1 class="mt-1 text-2xl font-bold tracking-tight sm:text-3xl">Your money hub</h1>
                        <p class="mt-2 max-w-xl text-sm leading-relaxed text-white/85">Balance, payout preferences, and a clear history of what moved and when.</p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('profile.show') }}" class="inline-flex items-center rounded-xl border border-white/20 bg-white/10 px-4 py-2.5 text-sm font-semibold text-white backdrop-blur transition hover:bg-white/15">Profile</a>
                    <a href="{{ route('banks.index') }}" class="inline-flex items-center rounded-xl border border-white/20 bg-white/10 px-4 py-2.5 text-sm font-semibold text-white backdrop-blur transition hover:bg-white/15">Banks</a>
                    <a href="{{ route('payments.create') }}" class="inline-flex items-center rounded-xl bg-white px-4 py-2.5 text-sm font-bold text-indigo-700 shadow-lg shadow-black/10 transition hover:bg-violet-50">Pay</a>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-2">
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

    <div class="mt-10 overflow-hidden rounded-3xl border border-slate-200/90 bg-white shadow-xl shadow-slate-200/60 ring-1 ring-slate-900/5">
        <div class="border-b border-slate-100 bg-gradient-to-r from-slate-50 via-indigo-50/50 to-violet-50/40 px-6 py-5 sm:px-8">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-indigo-600 shadow-sm ring-1 ring-slate-200/80" aria-hidden="true">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18"/><path stroke-linecap="round" stroke-linejoin="round" d="M7 14l4-4 3 3 6-6"/></svg>
                </span>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Transactions</h2>
                    <p class="mt-0.5 text-xs text-slate-600">Settlement dates for card loads; bank name when a payout settled.</p>
                </div>
            </div>
        </div>

        @if ($transactions->isEmpty())
            <div class="px-6 py-14 text-center sm:px-8">
                <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-500 ring-1 ring-slate-200" aria-hidden="true">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18"/><path stroke-linecap="round" stroke-linejoin="round" d="M7 14l4-4 3 3 6-6"/></svg>
                </span>
                <p class="mt-4 text-base font-semibold text-slate-900">No activity yet</p>
                <p class="mx-auto mt-2 max-w-sm text-sm text-slate-600">When you pay with your card, entries will appear here with amounts and settlement timing.</p>
                <a href="{{ route('payments.create') }}" class="mt-6 inline-flex items-center justify-center rounded-2xl bg-indigo-600 px-6 py-3 text-sm font-bold text-white shadow-md transition hover:bg-indigo-500">Make your first payment</a>
            </div>
        @else
            <div class="divide-y divide-slate-100 md:hidden">
                @foreach ($transactions as $tx)
                    <div class="px-5 py-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $tx->created_at->format('M j, Y · H:i') }}</p>
                                <p class="mt-1 font-semibold text-slate-900">{{ $tx->type_label }}</p>
                            </div>
                            <p class="shrink-0 font-mono text-sm font-bold tabular-nums text-slate-900">{{ $tx->amount }} {{ $tx->currency }}</p>
                        </div>
                        <div class="mt-3 flex flex-wrap gap-2 text-xs text-slate-600">
                            @if ($tx->type === \App\Models\Transaction::TYPE_CARD_PAYMENT && $tx->settlement_trigger_at)
                                <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-1 font-semibold text-amber-900 ring-1 ring-amber-400/35">Due {{ $tx->settlement_trigger_at->format('M j, Y') }}</span>
                            @elseif ($tx->settled_at)
                                <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-1 font-semibold text-emerald-900 ring-1 ring-emerald-400/35">Settled {{ $tx->settled_at->format('M j, Y') }}</span>
                            @else
                                <span class="text-slate-500">—</span>
                            @endif
                            @if ($tx->bank)
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 font-semibold text-slate-800 ring-1 ring-slate-300/50">{{ $tx->bank->bank_name }}</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="hidden overflow-x-auto md:block">
                <table class="w-full min-w-[42rem] border-collapse divide-y divide-slate-200 text-left text-sm">
                    <thead class="bg-slate-50/90 text-xs font-bold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-6 py-3">Date</th>
                            <th class="px-6 py-3">Type</th>
                            <th class="px-6 py-3">Amount</th>
                            <th class="px-6 py-3">Settlement</th>
                            <th class="px-6 py-3">Bank</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($transactions as $tx)
                            <tr class="bg-white transition hover:bg-indigo-50/30">
                                <td class="whitespace-nowrap px-6 py-3.5 text-slate-700">{{ $tx->created_at->format('M j, Y H:i') }}</td>
                                <td class="px-6 py-3.5 font-medium text-slate-900">{{ $tx->type_label }}</td>
                                <td class="whitespace-nowrap px-6 py-3.5 font-mono text-sm font-semibold tabular-nums text-slate-900">{{ $tx->amount }} {{ $tx->currency }}</td>
                                <td class="px-6 py-3.5 text-xs text-slate-600">
                                    @if ($tx->type === \App\Models\Transaction::TYPE_CARD_PAYMENT && $tx->settlement_trigger_at)
                                        <span class="font-semibold text-amber-800">Due {{ $tx->settlement_trigger_at->format('M j, Y') }}</span>
                                    @elseif ($tx->settled_at)
                                        <span class="font-semibold text-emerald-800">{{ $tx->settled_at->format('M j, Y H:i') }}</span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-6 py-3.5 text-slate-700">
                                    {{ $tx->bank?->bank_name ?? '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($transactions->hasPages())
                <div class="border-t border-slate-100 bg-slate-50/50 px-4 py-4 sm:px-6">
                    {{ $transactions->links() }}
                </div>
            @endif
        @endif
    </div>
@endsection
