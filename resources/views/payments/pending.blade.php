@extends('layouts.payment-result')

@section('title', 'Confirming payment — '.config('app.name'))

@section('content')
    <article class="pay-result-card pay-result-pending overflow-hidden rounded-3xl border border-amber-200/80 bg-white shadow-xl shadow-amber-900/10 ring-1 ring-amber-500/10">
        <div class="bg-gradient-to-br from-amber-500 via-orange-500 to-amber-600 px-6 py-10 text-center text-white sm:px-10 sm:py-12">
            <div class="pay-result-icon-wrap mx-auto">
                <div class="pay-result-icon flex h-20 w-20 items-center justify-center rounded-full bg-white/20 ring-4 ring-white/30 backdrop-blur-sm">
                    <svg class="pay-pending-ring h-11 w-11 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/>
                        <circle cx="12" cy="12" r="9" stroke-opacity="0.35"/>
                    </svg>
                </div>
            </div>
            <h1 class="animate-fade-up animate-delay-100 mt-6 text-2xl font-extrabold tracking-tight sm:text-3xl">Confirming your payment</h1>
            <p class="animate-fade-up animate-delay-200 mx-auto mt-3 max-w-md text-sm leading-relaxed text-amber-50/95">
                We’re checking with {{ $gatewayName }}. This usually takes a few seconds — please don’t close this page yet.
            </p>
        </div>

        <div class="space-y-6 p-6 sm:p-8">
            @include('payments.partials.result-details')

            <div class="animate-fade-up animate-delay-300 flex flex-col gap-3 sm:flex-row sm:justify-center">
                <a href="{{ route('payments.return', ['pid' => $payment->id]) }}" class="inline-flex items-center justify-center rounded-2xl bg-gradient-to-r from-amber-500 to-orange-600 px-6 py-3.5 text-sm font-bold text-white shadow-lg transition hover:brightness-110">
                    Refresh status
                </a>
                <a href="{{ route('contact') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-6 py-3.5 text-sm font-bold text-slate-800 transition hover:bg-slate-50">
                    Contact support
                </a>
            </div>
            <p class="text-center text-xs text-slate-500">Works with any payment gateway · Reference {{ $platformReference }}</p>
        </div>
    </article>

    <script>
        setTimeout(function () {
            window.location.href = @json(route('payments.return', ['pid' => $payment->id]));
        }, 8000);
    </script>
@endsection
