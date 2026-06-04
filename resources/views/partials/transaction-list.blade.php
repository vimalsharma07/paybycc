@props(['transactions'])

@if ($transactions->isEmpty())
    <div class="px-6 py-14 text-center sm:px-8">
        <p class="text-base font-semibold text-slate-900">No transactions yet</p>
        <p class="mx-auto mt-2 max-w-sm text-sm text-slate-600">Wallet movements and card payment settlements appear here.</p>
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
                    <p class="shrink-0 font-mono text-sm font-bold tabular-nums text-slate-900">₹{{ number_format((float) $tx->amount, 2) }}</p>
                </div>
                <div class="mt-3 flex flex-wrap gap-2 text-xs text-slate-600">
                    @if ($tx->type === \App\Models\Transaction::TYPE_CARD_PAYMENT && $tx->settlement_trigger_at)
                        <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 font-semibold text-amber-900 ring-1 ring-amber-400/35">Due {{ $tx->settlement_trigger_at->format('M j, Y') }}</span>
                    @elseif ($tx->settled_at)
                        <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 font-semibold text-emerald-900 ring-1 ring-emerald-400/35">Settled {{ $tx->settled_at->format('M j, Y') }}</span>
                    @endif
                    @if ($tx->bank)
                        <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 font-semibold text-slate-800">{{ $tx->bank->bank_name }}</span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
    <div class="hidden overflow-x-auto md:block">
        <table class="w-full min-w-[42rem] text-left text-sm">
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
                        <td class="whitespace-nowrap px-6 py-3.5 font-mono font-semibold tabular-nums">₹{{ number_format((float) $tx->amount, 2) }}</td>
                        <td class="px-6 py-3.5 text-xs text-slate-600">
                            @if ($tx->type === \App\Models\Transaction::TYPE_CARD_PAYMENT && $tx->settlement_trigger_at)
                                <span class="font-semibold text-amber-800">Due {{ $tx->settlement_trigger_at->format('M j, Y') }}</span>
                            @elseif ($tx->settled_at)
                                <span class="font-semibold text-emerald-800">{{ $tx->settled_at->format('M j, Y') }}</span>
                            @else — @endif
                        </td>
                        <td class="px-6 py-3.5">{{ $tx->bank?->bank_name ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if ($transactions->hasPages())
        <div class="border-t border-slate-100 bg-slate-50/50 px-4 py-4">{{ $transactions->links() }}</div>
    @endif
@endif
