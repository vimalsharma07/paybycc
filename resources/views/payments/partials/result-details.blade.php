@php
    /** @var \App\Models\Payment $payment */
@endphp
<div class="pay-result-details animate-fade-up animate-delay-200">
    <dl class="grid gap-3 sm:grid-cols-2">
        <div class="rounded-xl border border-slate-200/80 bg-slate-50/80 px-4 py-3">
            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Amount paid</dt>
            <dd class="mt-1 font-mono text-lg font-bold text-slate-900">{{ $amountLabel }}</dd>
            @if (! empty($netSettlementLabel))
                <dd class="mt-1 text-xs text-slate-600">Seller net settlement {{ $netSettlementLabel }}</dd>
            @endif
        </div>
        <div class="rounded-xl border border-slate-200/80 bg-slate-50/80 px-4 py-3">
            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Payment via</dt>
            <dd class="mt-1 text-sm font-semibold text-slate-900">{{ $gatewayName }}</dd>
        </div>
        @if ($freelancerName)
            <div class="rounded-xl border border-slate-200/80 bg-slate-50/80 px-4 py-3 sm:col-span-2">
                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Paid to</dt>
                <dd class="mt-1 text-sm font-semibold text-slate-900">{{ $freelancerName }}</dd>
            </div>
        @endif
        @if ($orderCode)
            <div class="rounded-xl border border-slate-200/80 bg-slate-50/80 px-4 py-3">
                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Order</dt>
                <dd class="mt-1 font-mono text-sm font-semibold text-slate-900">{{ $orderCode }}</dd>
            </div>
        @endif
        <div class="rounded-xl border border-slate-200/80 bg-slate-50/80 px-4 py-3">
            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Reference</dt>
            <dd class="mt-1 font-mono text-sm text-slate-800">{{ $platformReference }}</dd>
            @if ($gatewayReference)
                <dd class="mt-0.5 truncate font-mono text-xs text-slate-500" title="{{ $gatewayReference }}">{{ $gatewayReference }}</dd>
            @endif
        </div>
        @if (filled($remark))
            <div class="rounded-xl border border-indigo-100 bg-indigo-50/60 px-4 py-3 sm:col-span-2">
                <dt class="text-xs font-semibold uppercase tracking-wide text-indigo-700">Note</dt>
                <dd class="mt-1 text-sm text-slate-800">{{ $remark }}</dd>
            </div>
        @endif
    </dl>
</div>
