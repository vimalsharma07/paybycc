@php
    $rates = $commerceRates ?? [];
@endphp
<div id="fee-estimate-panel" class="hidden rounded-2xl border border-slate-200 bg-slate-50/90 p-5 text-sm" aria-live="polite">
    <h3 class="text-xs font-bold uppercase tracking-wide text-slate-500">Settlement estimate</h3>
    <p class="mt-1 text-xs text-slate-600">You pay the full amount. Fees below are deducted from the seller’s settlement.</p>
    <dl class="mt-4 space-y-2">
        <div class="flex justify-between gap-4">
            <dt class="text-slate-600">You pay</dt>
            <dd class="font-mono font-bold text-slate-900" id="fee-order-amount">—</dd>
        </div>
        <div class="flex justify-between gap-4 border-t border-slate-200/80 pt-2" id="fee-processing-wrap">
            <dt class="text-slate-600">Processing fee <span class="text-xs text-slate-400" id="fee-processing-label"></span></dt>
            <dd class="font-mono text-slate-800" id="fee-processing">—</dd>
        </div>
        <div class="flex justify-between gap-4">
            <dt class="text-slate-600">GST on processing fee</dt>
            <dd class="font-mono text-slate-800" id="fee-gst">—</dd>
        </div>
        <div class="flex justify-between gap-4">
            <dt class="text-slate-600">Service fee ({{ $rates['flat_order_percent'] ?? 0.5 }}% every order)</dt>
            <dd class="font-mono text-slate-800" id="fee-flat">—</dd>
        </div>
        <div class="flex justify-between gap-4 hidden" id="fee-tcs-wrap">
            <dt class="text-slate-600">TCS <span class="text-xs text-emerald-700">(seller GSTIN)</span></dt>
            <dd class="font-mono text-slate-800" id="fee-tcs">—</dd>
        </div>
        <div class="flex justify-between gap-4 hidden" id="fee-tds-wrap">
            <dt class="text-slate-600">TDS <span class="text-xs text-slate-400">(FY net ≥ ₹{{ number_format($rates['tds_threshold'] ?? 500000, 0) }})</span></dt>
            <dd class="font-mono text-slate-800" id="fee-tds">—</dd>
        </div>
        <div class="flex justify-between gap-4 border-t border-emerald-200/80 pt-3">
            <dt class="font-semibold text-emerald-900">Seller receives (net)</dt>
            <dd class="font-mono font-bold text-emerald-800" id="fee-net">—</dd>
        </div>
    </dl>
    <p id="fee-estimate-error" class="mt-3 hidden text-xs font-medium text-red-700"></p>
    <p id="fee-estimate-hint" class="mt-3 text-xs text-slate-500">
        Processing {{ $rates['processing_percent'] ?? 3 }}% when amount &gt; ₹{{ number_format($rates['processing_threshold'] ?? 1000, 0) }}.
        @if (! ($selectedFreelancer?->hasGstRegistered() ?? false))
            Sellers without GSTIN: max ₹{{ number_format(($rates['no_gst_fy_cap'] ?? 2000000) / 100000, 0) }} lakh net per financial year.
        @endif
    </p>
</div>
