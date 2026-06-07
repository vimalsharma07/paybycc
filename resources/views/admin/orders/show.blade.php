@extends('layouts.admin')

@section('title', $order->order_code.' — Orders — '.config('app.name'))

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.orders.index', request()->only(['q', 'payment_status', 'order_status'])) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">← Back to orders</a>
        <h1 class="mt-3 font-mono text-2xl font-semibold tracking-tight text-slate-900">{{ $order->order_code }}</h1>
        <p class="mt-1 text-sm text-slate-600">Order #{{ $order->id }} · {{ $order->created_at?->format('l, F j, Y H:i') }}</p>
    </div>

    <div class="mb-6 flex flex-wrap gap-2">
        <x-admin-status-pill :status="$order->order_status" type="order" />
        <x-admin-status-pill :status="$order->payment_status" type="payment" />
        <x-admin-status-pill :status="$order->settlement_status" type="settlement" />
        <x-admin-status-pill :status="$order->safe_status" type="safe" />
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Amounts (INR)</h2>
            <dl class="mt-4 space-y-3 text-sm">
                <div class="flex justify-between border-b border-slate-100 pb-2">
                    <dt class="text-slate-600">Order amount</dt>
                    <dd class="font-mono font-semibold text-slate-900">{{ number_format((float) $order->order_amount, 2) }}</dd>
                </div>
                <div class="flex justify-between border-b border-slate-100 pb-2">
                    <dt class="text-slate-600">Platform fees <span class="block text-xs font-normal text-slate-400">processing + {{ config('commerce.flat_order_fee_percent') }}% service</span></dt>
                    <dd class="font-mono text-slate-900">{{ number_format((float) $order->platform_fee, 2) }}</dd>
                </div>
                <div class="flex justify-between border-b border-slate-100 pb-2">
                    <dt class="text-slate-600">GST on processing</dt>
                    <dd class="font-mono text-slate-900">{{ number_format((float) $order->gst_amount, 2) }}</dd>
                </div>
                <div class="flex justify-between border-b border-slate-100 pb-2">
                    <dt class="text-slate-600">TCS @if($order->freelancer?->hasGstRegistered())<span class="text-xs text-emerald-700">(GSTIN)</span>@else<span class="text-xs text-slate-400">(n/a)</span>@endif</dt>
                    <dd class="font-mono text-slate-900">{{ number_format((float) $order->tcs_amount, 2) }}</dd>
                </div>
                <div class="flex justify-between border-b border-slate-100 pb-2">
                    <dt class="text-slate-600">TDS <span class="text-xs text-slate-400">(FY net ≥ ₹{{ number_format(config('commerce.tds.cumulative_net_threshold_inr'), 0) }})</span></dt>
                    <dd class="font-mono text-slate-900">{{ number_format((float) $order->tds_amount, 2) }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="font-medium text-slate-800">Net settlement</dt>
                    <dd class="font-mono font-bold text-emerald-800">{{ number_format((float) $order->net_settlement_amount, 2) }}</dd>
                </div>
            </dl>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Service</h2>
            <dl class="mt-4 space-y-3 text-sm">
                <div>
                    <dt class="text-slate-500">Category</dt>
                    <dd class="mt-0.5 text-slate-900">{{ $order->service?->name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Subservice</dt>
                    <dd class="mt-0.5 text-slate-900">{{ $order->subservice?->name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Notes</dt>
                    <dd class="mt-0.5 whitespace-pre-wrap text-slate-800">{{ $order->notes ?: '—' }}</dd>
                </div>
                @if ($order->completed_at)
                    <div>
                        <dt class="text-slate-500">Completed at</dt>
                        <dd class="mt-0.5 text-slate-900">{{ $order->completed_at->format('d M Y H:i') }}</dd>
                    </div>
                @endif
            </dl>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Customer (payer)</h2>
            @if ($order->customer)
                <dl class="mt-4 space-y-2 text-sm">
                    <div><dt class="sr-only">Name</dt><dd class="font-semibold text-slate-900">{{ $order->customer->name }}</dd></div>
                    <div><dd class="text-slate-600">{{ $order->customer->email }}</dd></div>
                    <div><dd class="font-mono text-xs text-slate-500">{{ $order->customer->user_code }}</dd></div>
                    <div class="pt-2">
                        <a href="{{ route('admin.users.edit', $order->customer) }}" class="text-sm font-semibold text-indigo-600 hover:underline">Edit user →</a>
                    </div>
                </dl>
            @else
                <p class="mt-4 text-sm text-slate-400">—</p>
            @endif
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Freelancer (seller)</h2>
            @if ($order->freelancer)
                <dl class="mt-4 space-y-2 text-sm">
                    <div><dd class="font-semibold text-slate-900">{{ $order->freelancer->name }}</dd></div>
                    @if ($order->freelancer->company_name)
                        <div><dd class="text-slate-600">{{ $order->freelancer->company_name }}</dd></div>
                    @endif
                    <div><dd class="text-slate-600">{{ $order->freelancer->email }}</dd></div>
                    <div><dd class="font-mono text-xs text-slate-500">{{ $order->freelancer->user_code }} · KYC {{ $order->freelancer->kyc_status_label }}</dd></div>
                    <div class="pt-2">
                        <a href="{{ route('admin.users.edit', $order->freelancer) }}" class="text-sm font-semibold text-indigo-600 hover:underline">Edit user →</a>
                    </div>
                </dl>
            @else
                <p class="mt-4 text-sm text-slate-400">—</p>
            @endif
        </div>
    </div>

    <div class="mt-8 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 bg-slate-50 px-6 py-4">
            <h2 class="text-lg font-semibold text-slate-900">Payments</h2>
            <p class="text-xs text-slate-600">Card checkouts linked to this order</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[40rem] divide-y divide-slate-100 text-left text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-600">
                    <tr>
                        <th class="px-6 py-3">ID</th>
                        <th class="px-6 py-3">Gateway</th>
                        <th class="px-6 py-3 text-right">Amount</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Reference</th>
                        <th class="px-6 py-3">Remark</th>
                        <th class="px-6 py-3">Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($order->payments as $payment)
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-6 py-4 font-mono text-xs">#{{ $payment->id }}</td>
                            <td class="px-6 py-4">{{ $payment->gateway?->name ?? '—' }}</td>
                            <td class="px-6 py-4 text-right font-mono tabular-nums">{{ $payment->currency }} {{ number_format((float) $payment->amount, 2) }}</td>
                            <td class="px-6 py-4"><x-admin-status-pill :status="$payment->status" /></td>
                            <td class="max-w-[10rem] truncate px-6 py-4 font-mono text-xs text-slate-600" title="{{ $payment->gateway_reference }}">{{ $payment->gateway_reference ?? '—' }}</td>
                            <td class="max-w-[12rem] truncate px-6 py-4 text-slate-700" title="{{ $payment->remark }}">{{ $payment->remark ?? '—' }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-xs text-slate-600">{{ $payment->created_at?->format('d M Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-6 py-8 text-center text-slate-500">No payments recorded.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($order->payments->isNotEmpty())
        <div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 bg-slate-50 px-6 py-4">
                <h2 class="text-lg font-semibold text-slate-900">Ledger transactions</h2>
                <p class="text-xs text-slate-600">From linked payments</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[36rem] divide-y divide-slate-100 text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-600">
                        <tr>
                            <th class="px-6 py-3">ID</th>
                            <th class="px-6 py-3">Payment</th>
                            <th class="px-6 py-3">Type</th>
                            <th class="px-6 py-3 text-right">Amount</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Note</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @php $hasTx = false; @endphp
                        @foreach ($order->payments as $payment)
                            @foreach ($payment->transactions as $tx)
                                @php $hasTx = true; @endphp
                                <tr class="hover:bg-slate-50/80">
                                    <td class="px-6 py-4 font-mono text-xs">#{{ $tx->id }}</td>
                                    <td class="px-6 py-4 font-mono text-xs">#{{ $payment->id }}</td>
                                    <td class="px-6 py-4">{{ $tx->type_label }}</td>
                                    <td class="px-6 py-4 text-right font-mono tabular-nums">{{ number_format((float) $tx->amount, 2) }}</td>
                                    <td class="px-6 py-4"><x-admin-status-pill :status="$tx->status" type="transaction" /></td>
                                    <td class="max-w-[14rem] truncate px-6 py-4 text-slate-700" title="{{ $tx->note }}">{{ $tx->note ?? '—' }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                        @unless ($hasTx)
                            <tr><td colspan="6" class="px-6 py-8 text-center text-slate-500">No transactions yet.</td></tr>
                        @endunless
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 bg-slate-50 px-6 py-4">
            <h2 class="text-lg font-semibold text-slate-900">Settlements</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[32rem] divide-y divide-slate-100 text-left text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-600">
                    <tr>
                        <th class="px-6 py-3">ID</th>
                        <th class="px-6 py-3 text-right">Amount</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Bank</th>
                        <th class="px-6 py-3">Reference</th>
                        <th class="px-6 py-3">Settled at</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($order->settlements as $settlement)
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-6 py-4 font-mono text-xs">#{{ $settlement->id }}</td>
                            <td class="px-6 py-4 text-right font-mono tabular-nums">{{ number_format((float) $settlement->amount, 2) }}</td>
                            <td class="px-6 py-4"><x-admin-status-pill :status="$settlement->status" type="settlement" /></td>
                            <td class="px-6 py-4">{{ $settlement->bank?->bank_name ?? '—' }}</td>
                            <td class="px-6 py-4 font-mono text-xs">{{ $settlement->reference ?? '—' }}</td>
                            <td class="px-6 py-4 text-xs text-slate-600">{{ $settlement->settled_at?->format('d M Y H:i') ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-8 text-center text-slate-500">No settlement rows yet (wallet credit may still be pending).</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
