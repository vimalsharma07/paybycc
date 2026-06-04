@extends('layouts.payment-result')

@section('title', 'Payment successful — '.config('app.name'))

@section('content')
    <article class="pay-result-card pay-result-success overflow-hidden rounded-3xl border border-emerald-200/80 bg-white shadow-xl shadow-emerald-900/10 ring-1 ring-emerald-500/10">
        <div class="pay-confetti" aria-hidden="true">
            <span></span><span></span><span></span><span></span><span></span>
        </div>
        <div class="bg-gradient-to-br from-emerald-500 via-teal-500 to-cyan-600 px-6 py-10 text-center text-white sm:px-10 sm:py-12">
            <div class="pay-result-icon-wrap mx-auto">
                <div class="pay-result-icon flex h-20 w-20 items-center justify-center rounded-full bg-white/20 ring-4 ring-white/30 backdrop-blur-sm">
                    <svg class="h-10 w-10 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                        <path class="pay-check-path" stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
            <h1 class="animate-fade-up animate-delay-100 mt-6 text-2xl font-extrabold tracking-tight sm:text-3xl">Payment successful</h1>
            <p class="animate-fade-up animate-delay-200 mx-auto mt-3 max-w-md text-sm leading-relaxed text-emerald-50/95">
                Your payment of <span class="font-mono font-bold text-white">{{ $amountLabel }}</span> was received. Thank you — we’ve recorded everything securely.
            </p>
        </div>

        <div class="space-y-6 p-6 sm:p-8">
            @include('payments.partials.result-details')

            <div class="animate-fade-up animate-delay-300 flex flex-col gap-3 sm:flex-row sm:justify-center">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-3.5 text-sm font-bold text-white shadow-lg transition hover:brightness-110">
                    Go to dashboard
                </a>
                @if ($freelancerId)
                    <a href="{{ route('payments.create', ['freelancer' => $freelancerId]) }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-6 py-3.5 text-sm font-bold text-slate-800 transition hover:border-emerald-300 hover:bg-emerald-50">
                        Pay again
                    </a>
                @endif
            </div>
            <p class="text-center text-xs text-slate-500">Processed via {{ $gatewayName }} · {{ $completedAt?->format('d M Y, H:i') }}</p>
        </div>
    </article>
@endsection
