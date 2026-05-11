@extends('layouts.admin')

@section('title', 'Transactions — '.config('app.name'))

@section('content')
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Transactions</h1>
            <p class="mt-1 text-sm text-slate-600">All wallet and payment ledger rows, newest first.</p>
        </div>
        <form method="GET" action="{{ route('admin.transactions.index') }}" class="flex w-full max-w-md gap-2">
            <label for="q" class="sr-only">Search by user</label>
            <input id="q" name="q" type="search" value="{{ $q }}" placeholder="User name or email…"
                class="block min-w-0 flex-1 rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            <button type="submit" class="shrink-0 rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Search</button>
        </form>
    </div>

    <div class="w-full overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="w-full min-w-0 overflow-x-auto">
            <table class="w-full min-w-[52rem] border-collapse divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-xs font-medium uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="whitespace-nowrap px-4 py-3 sm:px-6">ID</th>
                        <th class="px-4 py-3 sm:px-6">User</th>
                        <th class="whitespace-nowrap px-4 py-3 sm:px-6">Type</th>
                        <th class="whitespace-nowrap px-4 py-3 text-right tabular-nums sm:px-6">Amount</th>
                        <th class="whitespace-nowrap px-4 py-3 sm:px-6">Status</th>
                        <th class="whitespace-nowrap px-4 py-3 sm:px-6">Settlement</th>
                        <th class="px-4 py-3 sm:px-6">Note</th>
                        <th class="whitespace-nowrap px-4 py-3 sm:px-6">Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($transactions as $tx)
                        <tr class="bg-white">
                            <td class="whitespace-nowrap px-4 py-4 font-mono text-xs text-slate-600 sm:px-6">#{{ $tx->id }}</td>
                            <td class="px-4 py-4 sm:px-6">
                                @if ($tx->user)
                                    <p class="font-medium text-slate-900">{{ $tx->user->name }}</p>
                                    <p class="text-xs text-slate-600">{{ $tx->user->email }}</p>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 text-slate-700 sm:px-6">{{ $tx->type_label }}</td>
                            <td class="whitespace-nowrap px-4 py-4 text-right font-mono tabular-nums text-slate-900 sm:px-6">{{ $tx->currency }} {{ number_format((float) $tx->amount, 2) }}</td>
                            <td class="whitespace-nowrap px-4 py-4 capitalize text-slate-700 sm:px-6">{{ $tx->status }}</td>
                            <td class="whitespace-nowrap px-4 py-4 text-xs text-slate-600 sm:px-6">
                                @if ($tx->settled_at)
                                    <span class="text-emerald-700">Settled {{ $tx->settled_at->format('M j, Y') }}</span>
                                @elseif ($tx->settlement_trigger_at)
                                    <span>Due {{ $tx->settlement_trigger_at->format('M j, Y') }}</span>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="max-w-[14rem] truncate px-4 py-4 text-xs text-slate-600 sm:px-6" title="{{ $tx->note }}">{{ $tx->note ?? '—' }}</td>
                            <td class="whitespace-nowrap px-4 py-4 text-xs text-slate-600 sm:px-6">{{ $tx->created_at?->format('M j, Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center text-sm text-slate-500 sm:px-6">No transactions yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($transactions->hasPages())
            <div class="border-t border-slate-200 px-4 py-4 sm:px-6">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
@endsection
