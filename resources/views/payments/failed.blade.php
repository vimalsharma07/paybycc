@extends('layouts.payment-result')

@section('title', 'Payment failed — '.config('app.name'))

@section('content')
    <article class="pay-result-card pay-result-failed overflow-hidden rounded-3xl border border-rose-200/80 bg-white shadow-xl shadow-rose-900/10 ring-1 ring-rose-500/10">
        <div class="bg-gradient-to-br from-rose-500 via-red-500 to-orange-600 px-6 py-10 text-center text-white sm:px-10 sm:py-12">
            <div class="pay-result-icon-wrap mx-auto">
                <div class="pay-result-icon flex h-20 w-20 items-center justify-center rounded-full bg-white/20 ring-4 ring-white/30 backdrop-blur-sm">
                    <svg class="h-10 w-10 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
            </div>
            <h1 class="animate-fade-up animate-delay-100 mt-6 text-2xl font-extrabold tracking-tight sm:text-3xl">Payment not completed</h1>
            <p class="animate-fade-up animate-delay-200 mx-auto mt-3 max-w-md text-sm leading-relaxed text-rose-50/95">
                No money was taken for this attempt, or the payment was cancelled. You can try again anytime.
            </p>
        </div>

        <div class="space-y-6 p-6 sm:p-8">
            @include('payments.partials.result-details')

            <div class="animate-fade-up animate-delay-300 rounded-xl border border-amber-100 bg-amber-50 px-4 py-3 text-sm text-amber-950">
                <p class="font-semibold">Need help?</p>
                <p class="mt-1 text-amber-900/90">If money was deducted from your account, contact support with reference <span class="font-mono font-bold">{{ $platformReference }}</span> — we’ll verify with {{ $gatewayName }}.</p>
            </div>

            <div class="animate-fade-up animate-delay-400 flex flex-col gap-3 sm:flex-row sm:justify-center">
                @if ($freelancerId)
                    <a href="{{ route('payments.create', ['freelancer' => $freelancerId]) }}" class="inline-flex items-center justify-center rounded-2xl bg-gradient-to-r from-rose-600 to-red-600 px-6 py-3.5 text-sm font-bold text-white shadow-lg transition hover:brightness-110">
                        Try payment again
                    </a>
                @endif
                <a href="{{ route('marketplace.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-6 py-3.5 text-sm font-bold text-slate-800 transition hover:border-rose-200 hover:bg-rose-50">
                    Find freelancers
                </a>
            </div>
        </div>
    </article>
@endsection
