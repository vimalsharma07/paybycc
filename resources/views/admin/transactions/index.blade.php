@extends('layouts.admin')

@section('title', 'Transactions — '.config('app.name'))

@section('content')
    <div class="mb-6 overflow-hidden rounded-2xl border border-indigo-200/70 bg-gradient-to-br from-white via-indigo-50/40 to-violet-50/50 p-5 shadow-sm sm:p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Transactions</h1>
                <p class="mt-1 max-w-xl text-sm text-slate-600">Ledger entries, newest first. <span class="font-medium text-emerald-800">Green</span> = success, <span class="font-medium text-rose-800">red</span> = failed, <span class="font-medium text-amber-800">amber</span> = pending.</p>
            </div>
            <form method="GET" action="{{ route('admin.transactions.index') }}" class="flex w-full max-w-md flex-col gap-2 sm:flex-row sm:items-center">
                <label for="q" class="sr-only">Search by user</label>
                <input id="q" name="q" type="search" value="{{ $q }}" placeholder="User name or email…"
                    class="block min-w-0 flex-1 rounded-xl border border-indigo-200/80 bg-white/90 px-3 py-2.5 text-sm shadow-inner shadow-indigo-950/5 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-400/30">
                <button type="submit" class="shrink-0 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Search</button>
            </form>
        </div>
    </div>

    <div class="w-full overflow-hidden rounded-2xl border border-slate-200/90 bg-white shadow-md shadow-slate-200/50 ring-1 ring-slate-900/5">
        <div class="w-full min-w-0 overflow-x-auto">
            <table class="w-full min-w-[58rem] border-collapse divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-gradient-to-r from-slate-100 via-indigo-50/80 to-violet-50/90 text-xs font-semibold uppercase tracking-wide text-slate-600">
                    <tr>
                        <th class="whitespace-nowrap px-4 py-3 sm:px-6">ID</th>
                        <th class="px-4 py-3 sm:px-6">User</th>
                        <th class="whitespace-nowrap px-4 py-3 sm:px-6">Type</th>
                        <th class="whitespace-nowrap px-4 py-3 text-right tabular-nums sm:px-6">Amount</th>
                        <th class="whitespace-nowrap px-4 py-3 sm:px-6">Ledger</th>
                        <th class="whitespace-nowrap px-4 py-3 sm:px-6">Payment</th>
                        <th class="whitespace-nowrap px-4 py-3 sm:px-6">Settlement</th>
                        <th class="px-4 py-3 sm:px-6">Note</th>
                        <th class="whitespace-nowrap px-4 py-3 sm:px-6">Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($transactions as $tx)
                        @php
                            $st = strtolower((string) $tx->status);
                            $success = in_array($st, ['completed', 'success', 'succeeded', 'paid'], true);
                            $failed = in_array($st, ['failed', 'error', 'declined', 'rejected', 'denied', 'expired', 'terminated', 'cancelled', 'canceled'], true);
                            $pending = in_array($st, ['pending', 'processing', 'awaiting'], true);
                        @endphp
                        <tr @class([
                            'transition-colors hover:bg-slate-50/90',
                            'border-l-[5px] border-l-emerald-500 bg-emerald-50/25' => $success,
                            'border-l-[5px] border-l-rose-500 bg-rose-50/25' => $failed,
                            'border-l-[5px] border-l-amber-500 bg-amber-50/20' => $pending,
                            'border-l-[5px] border-l-slate-300 bg-white' => ! $success && ! $failed && ! $pending,
                        ])>
                            <td class="whitespace-nowrap px-4 py-4 font-mono text-xs text-slate-600 sm:px-6">#{{ $tx->id }}</td>
                            <td class="px-4 py-4 sm:px-6">
                                @if ($tx->user)
                                    <p class="font-medium text-slate-900">{{ $tx->user->name }}</p>
                                    <p class="text-xs text-slate-600">{{ $tx->user->email }}</p>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 sm:px-6">
                                @if ($tx->type === \App\Models\Transaction::TYPE_CARD_PAYMENT)
                                    <span class="inline-flex rounded-full bg-sky-100 px-2.5 py-1 text-xs font-semibold text-sky-900 ring-1 ring-sky-400/40">Card</span>
                                @elseif ($tx->type === \App\Models\Transaction::TYPE_SETTLEMENT)
                                    <span class="inline-flex rounded-full bg-violet-100 px-2.5 py-1 text-xs font-semibold text-violet-900 ring-1 ring-violet-400/40">Settlement</span>
                                @else
                                    <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-800 ring-1 ring-slate-400/35">{{ $tx->type_label }}</span>
                                @endif
                            </td>
                            <td @class([
                                'whitespace-nowrap px-4 py-4 text-right font-mono text-sm tabular-nums sm:px-6',
                                'font-semibold text-emerald-800' => $success,
                                'font-semibold text-rose-800' => $failed,
                                'font-semibold text-amber-800' => $pending,
                                'text-slate-900' => ! $success && ! $failed && ! $pending,
                            ])>{{ $tx->currency }} {{ number_format((float) $tx->amount, 2) }}</td>
                            <td class="whitespace-nowrap px-4 py-4 sm:px-6">
                                <x-admin-status-pill :status="$tx->status" />
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 sm:px-6">
                                @if ($tx->payment)
                                    <x-admin-status-pill :status="$tx->payment->status" size="xs" />
                                    @if ($tx->payment->gateway_reference)
                                        <p class="mt-1 max-w-[10rem] truncate font-mono text-[10px] text-slate-500" title="{{ $tx->payment->gateway_reference }}">{{ $tx->payment->gateway_reference }}</p>
                                    @endif
                                @else
                                    <span class="text-xs text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 sm:px-6">
                                @if ($tx->settled_at)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-900 ring-1 ring-emerald-400/40">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-emerald-500" aria-hidden="true"></span>
                                        Settled {{ $tx->settled_at->format('M j') }}
                                    </span>
                                @elseif ($tx->settlement_trigger_at)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-900 ring-1 ring-amber-400/40">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-amber-500" aria-hidden="true"></span>
                                        Due {{ $tx->settlement_trigger_at->format('M j') }}
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="max-w-[14rem] truncate px-4 py-4 text-xs text-slate-600 sm:px-6" title="{{ $tx->note }}">{{ $tx->note ?? '—' }}</td>
                            <td class="whitespace-nowrap px-4 py-4 text-xs text-slate-600 sm:px-6">{{ $tx->created_at?->format('M j, Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-12 text-center text-sm text-slate-500 sm:px-6">No transactions yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($transactions->hasPages())
            <div class="border-t border-slate-200 bg-slate-50/50 px-4 py-4 sm:px-6">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
@endsection
