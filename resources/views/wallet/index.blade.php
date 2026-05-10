@extends('layouts.app')

@section('title', 'Wallet — '.config('app.name'))

@section('content')
    <div class="mb-8 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Wallet</h1>
            <p class="mt-1 text-sm text-slate-600">Balance, settlement preference, and payment history.</p>
        </div>
        <a href="{{ route('payments.create') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">Make a payment →</a>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Balance</h2>
            <p class="mt-3 text-3xl font-semibold tabular-nums text-slate-900">{{ number_format((float) $wallet->balance, 2) }} <span class="text-base font-normal text-slate-500">INR</span></p>
            <p class="mt-4 text-xs text-slate-500">Simple wallet ledger. Card payments create a transaction with an expected settlement date; bank settlements can be recorded separately when funds arrive.</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Settings</h2>
            <form method="POST" action="{{ route('wallet.update') }}" class="mt-4 space-y-5">
                @csrf
                @method('PATCH')
                <div class="rounded-lg border border-slate-100 bg-slate-50 px-4 py-3">
                    <input type="hidden" name="auto_settle_to_bank" value="0">
                    <div class="flex items-start gap-3">
                        <input id="wallet_auto_settle" name="auto_settle_to_bank" type="checkbox" value="1" class="mt-1 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                            @checked(old('auto_settle_to_bank', $wallet->auto_settle_to_bank))>
                        <label for="wallet_auto_settle" class="text-sm text-slate-700">
                            <span class="font-medium text-slate-900">Automatically send money to my bank when I receive settlement in my wallet</span>
                        </label>
                    </div>
                </div>
                <div>
                    <label for="default_bank_id" class="mb-1 block text-sm font-medium text-slate-700">Default bank for payouts</label>
                    <select id="default_bank_id" name="default_bank_id"
                        class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('default_bank_id') border-red-500 @enderror">
                        <option value="">— None —</option>
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
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-slate-500">Used when auto-settlement runs (future payout step).</p>
                </div>
                <button type="submit" class="inline-flex rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-500">Save settings</button>
            </form>
        </div>
    </div>

    <div class="mt-8 rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-4">
            <h2 class="text-lg font-semibold text-slate-900">Transactions</h2>
            <p class="mt-1 text-xs text-slate-500">Card payments show expected settlement date. Settlement rows include the bank that received funds.</p>
        </div>
        @if ($transactions->isEmpty())
            <p class="px-6 py-10 text-center text-sm text-slate-600">No transactions yet.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-[42rem] border-collapse divide-y divide-slate-200 text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-medium uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3 sm:px-6">Date</th>
                            <th class="px-4 py-3 sm:px-6">Type</th>
                            <th class="px-4 py-3 sm:px-6">Amount</th>
                            <th class="px-4 py-3 sm:px-6">Settlement</th>
                            <th class="px-4 py-3 sm:px-6">Bank</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($transactions as $tx)
                            <tr class="bg-white">
                                <td class="whitespace-nowrap px-4 py-3 text-slate-700 sm:px-6">{{ $tx->created_at->format('M j, Y H:i') }}</td>
                                <td class="px-4 py-3 text-slate-800 sm:px-6">{{ $tx->type_label }}</td>
                                <td class="whitespace-nowrap px-4 py-3 font-mono tabular-nums text-slate-900 sm:px-6">{{ $tx->amount }} {{ $tx->currency }}</td>
                                <td class="px-4 py-3 text-xs text-slate-600 sm:px-6">
                                    @if ($tx->type === \App\Models\Transaction::TYPE_CARD_PAYMENT && $tx->settlement_trigger_at)
                                        <span class="font-medium text-slate-800">Due {{ $tx->settlement_trigger_at->format('M j, Y') }}</span>
                                    @elseif ($tx->settled_at)
                                        {{ $tx->settled_at->format('M j, Y H:i') }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-700 sm:px-6">
                                    @if ($tx->bank)
                                        {{ $tx->bank->bank_name }}
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($transactions->hasPages())
                <div class="border-t border-slate-100 px-4 py-4">
                    {{ $transactions->links() }}
                </div>
            @endif
        @endif
    </div>
@endsection
