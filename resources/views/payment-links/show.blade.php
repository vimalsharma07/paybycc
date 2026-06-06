@extends('layouts.app')

@section('title', 'Payment link — '.config('app.name'))
@section('page_heading', 'Payment link')
@section('page_subheading', $paymentLink->isDefault() ? 'Your default link — share URL or QR' : 'Share this URL with your client')

@section('content')
    @php
        $publicUrl = $paymentLink->publicUrl();
        $statusTone = match ($paymentLink->status) {
            \App\Models\PaymentLink::STATUS_OPEN => 'bg-emerald-100 text-emerald-800 ring-emerald-300/50',
            \App\Models\PaymentLink::STATUS_PAID => 'bg-indigo-100 text-indigo-800 ring-indigo-300/50',
            \App\Models\PaymentLink::STATUS_EXHAUSTED => 'bg-amber-100 text-amber-900 ring-amber-300/50',
            default => 'bg-slate-100 text-slate-700 ring-slate-300/50',
        };
    @endphp

    <div class="mx-auto max-w-2xl space-y-6">
        <div class="overflow-hidden rounded-3xl border border-slate-200/90 bg-white shadow-xl ring-1 ring-slate-900/5">
            <div class="bg-gradient-to-r from-indigo-600 via-violet-600 to-fuchsia-600 px-6 py-8 text-white sm:px-8">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-white/70">Amount</p>
                        <p class="mt-1 font-mono text-4xl font-extrabold">{{ $paymentLink->amountLabel() }}</p>
                        <p class="mt-2 text-sm text-white/80">{{ $paymentLink->usageLimitLabel() }}@if ($paymentLink->uses_count > 0) · {{ $paymentLink->uses_count }} paid @endif</p>
                    </div>
                    <span class="rounded-full px-3 py-1 text-xs font-bold ring-1 {{ $statusTone }}">{{ $paymentLink->statusLabel() }}</span>
                </div>
                @if ($paymentLink->description)
                    <p class="mt-4 text-sm text-white/90">{{ $paymentLink->description }}</p>
                @endif
                @if ($paymentLink->expires_at)
                    <p class="mt-3 text-xs text-white/75">Expires {{ $paymentLink->expires_at->format('M j, Y \a\t H:i') }}</p>
                @endif
            </div>

            @if ($paymentLink->status === \App\Models\PaymentLink::STATUS_OPEN)
                <div class="border-b border-slate-100 p-6 sm:p-8">
                    <label for="payment-link-url" class="block text-xs font-bold uppercase tracking-wider text-slate-500">Shareable payment link</label>
                    <div class="mt-3 flex flex-col gap-2 sm:flex-row">
                        <input id="payment-link-url" type="text" readonly value="{{ $publicUrl }}"
                            class="min-w-0 flex-1 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 font-mono text-xs text-slate-800">
                        <button type="button" id="copy-payment-link-btn"
                            class="shrink-0 rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow hover:bg-indigo-500"
                            data-copy-target="payment-link-url">
                            Copy link
                        </button>
                    </div>
                    <p class="mt-3 text-xs text-slate-500">Send via WhatsApp, email, or invoice. Client pays with UPI, card, or net banking.</p>
                    @if ($paymentLink->isDefault())
                        <div class="mt-5 border-t border-slate-100 pt-5">
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">QR code</p>
                            <x-payment-link-qr :payment-link="$paymentLink" prefix="show" class="mt-3" />
                        </div>
                    @endif
                </div>
            @endif

            <div class="flex flex-wrap gap-3 p-6 sm:p-8">
                <a href="{{ route('payment-links.index') }}" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50">← All links</a>
                @if ($paymentLink->status === \App\Models\PaymentLink::STATUS_OPEN)
                    <a href="{{ $publicUrl }}" target="_blank" rel="noopener" class="rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-2.5 text-sm font-bold text-indigo-700 hover:bg-indigo-100">Preview as client</a>
                    @unless ($paymentLink->isDefault())
                        <form method="POST" action="{{ route('payment-links.cancel', $paymentLink) }}" class="inline" onsubmit="return confirm('Cancel this payment link?');">
                            @csrf
                            <button type="submit" class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-2.5 text-sm font-bold text-rose-700 hover:bg-rose-100">Cancel link</button>
                        </form>
                    @endunless
                @endif
            </div>
        </div>

        @if ($paymentLink->status === \App\Models\PaymentLink::STATUS_PAID && $paymentLink->order)
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-950">
                <p class="font-semibold">Paid {{ $paymentLink->paid_at?->diffForHumans() ?? '' }}</p>
                <p class="mt-1">Order <span class="font-mono font-bold">{{ $paymentLink->order->order_code }}</span></p>
                <a href="{{ route('account.settlements') }}" class="mt-2 inline-flex text-xs font-bold text-emerald-800 underline">View settlements →</a>
            </div>
        @endif
    </div>

    @if ($paymentLink->status === \App\Models\PaymentLink::STATUS_OPEN)
        @push('scripts')
        <script>
            document.getElementById('copy-payment-link-btn')?.addEventListener('click', function () {
                const input = document.getElementById(this.dataset.copyTarget);
                if (!input) return;
                input.select();
                input.setSelectionRange(0, 99999);
                navigator.clipboard?.writeText(input.value).then(() => {
                    this.textContent = 'Copied!';
                    setTimeout(() => { this.textContent = 'Copy link'; }, 2000);
                }).catch(() => {
                    document.execCommand('copy');
                    this.textContent = 'Copied!';
                    setTimeout(() => { this.textContent = 'Copy link'; }, 2000);
                });
            });
        </script>
        @endpush
    @endif
@endsection
