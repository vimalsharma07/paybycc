@extends('layouts.marketing')

@section('title', ($seller?->name ?? 'Payment').' — Pay ₹'.number_format((float) $paymentLink->amount, 0).' — '.config('app.name'))
@section('meta_description', 'Pay '.$seller?->name.' ₹'.number_format((float) $paymentLink->amount, 2).' via UPI, card, or net banking on '.config('app.name').'.')

@section('content')
    <section class="px-4 py-12 sm:px-6 sm:py-20">
        <div class="mx-auto max-w-lg">
            <div class="overflow-hidden rounded-3xl border border-white/10 bg-slate-900/80 shadow-2xl backdrop-blur-sm">
                <div class="border-b border-white/10 bg-gradient-to-r from-indigo-600/80 to-violet-600/80 px-6 py-8 text-center text-white">
                    <p class="text-xs font-bold uppercase tracking-widest text-white/70">Payment request</p>
                    @if ($seller)
                        <h1 class="mt-2 text-2xl font-bold">{{ $seller->name }}</h1>
                        @if ($seller->company_name)
                            <p class="mt-1 text-sm text-white/85">{{ $seller->company_name }}</p>
                        @endif
                    @endif
                    <p class="mt-6 font-mono text-5xl font-extrabold tracking-tight">₹{{ number_format((float) $paymentLink->amount, 2) }}</p>
                </div>

                <div class="space-y-5 p-6 sm:p-8">
                    @if ($paymentLink->description)
                        <div class="rounded-xl border border-white/10 bg-white/5 px-4 py-3">
                            <p class="text-xs font-bold uppercase text-slate-500">Note</p>
                            <p class="mt-1 text-sm text-slate-200">{{ $paymentLink->description }}</p>
                        </div>
                    @endif

                    <div>
                        <x-payment-methods variant="dark" size="sm" :show-label="false" />
                    </div>

                    @if (! $payable)
                        <div class="rounded-xl border border-amber-500/30 bg-amber-500/10 px-4 py-4 text-sm text-amber-100">
                            <p class="font-semibold">{{ $payError ?? 'This payment link is not available.' }}</p>
                        </div>
                    @elseif (auth()->guest())
                        <div class="space-y-3 text-center">
                            <p class="text-sm text-slate-400">Log in or create a free account to pay securely.</p>
                            <a href="{{ route('login', ['redirect' => '/pay/'.$paymentLink->link_token]) }}"
                                class="pay-now-btn inline-flex w-full items-center justify-center rounded-2xl bg-gradient-to-r from-cyan-500 via-indigo-500 to-violet-600 px-6 py-3.5 text-base font-bold text-white shadow-xl">
                                Log in to pay
                            </a>
                            <a href="{{ route('register') }}" class="inline-flex w-full items-center justify-center rounded-2xl border border-white/20 px-6 py-3 py-3.5 text-sm font-semibold text-white hover:bg-white/10">
                                Create account
                            </a>
                        </div>
                    @elseif (! auth()->user()->canUsePlatform())
                        <div class="rounded-xl border border-indigo-500/30 bg-indigo-500/10 px-4 py-4 text-center text-sm text-indigo-100">
                            <p>Complete KYC to pay on the platform.</p>
                            <a href="{{ route('kyc.index') }}" class="mt-3 inline-flex font-bold text-white underline">Complete KYC →</a>
                        </div>
                    @elseif (! $canPayNow)
                        <div class="rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-4 text-sm text-rose-100">
                            <p class="font-semibold">You cannot pay this link.</p>
                            <p class="mt-1 text-rose-200/90">This may be your own payment link, or the seller restricts who can pay.</p>
                        </div>
                    @else
                        @if ($pendingPayment && (int) $pendingPayment->user_id === (int) auth()->id())
                            <a href="{{ route('payments.checkout', $pendingPayment) }}"
                                class="pay-now-btn inline-flex w-full items-center justify-center rounded-2xl bg-gradient-to-r from-emerald-500 via-cyan-500 to-indigo-600 px-6 py-4 text-base font-bold text-white shadow-xl">
                                Resume checkout →
                            </a>
                        @else
                            <form method="POST" action="{{ route('payment-links.pay.store', $paymentLink->link_token) }}">
                                @csrf
                                <button type="submit"
                                    class="pay-now-btn inline-flex w-full items-center justify-center rounded-2xl bg-gradient-to-r from-emerald-500 via-cyan-500 to-indigo-600 px-6 py-4 text-base font-bold text-white shadow-xl hover:brightness-110">
                                    Pay ₹{{ number_format((float) $paymentLink->amount, 2) }} now
                                </button>
                            </form>
                        @endif

                        <p class="text-center text-xs text-slate-500">Secure checkout · UPI, cards, net banking &amp; wallets</p>
                    @endif

                    @error('payment_link')
                        <p class="text-sm text-rose-400">{{ $message }}</p>
                    @enderror

                    @if ($paymentLink->expires_at && $payable)
                        <p class="text-center text-xs text-slate-600">Link expires {{ $paymentLink->expires_at->format('M j, Y') }}</p>
                    @endif
                </div>
            </div>

            <p class="mt-8 text-center text-xs text-slate-600">
                Powered by <a href="{{ route('home') }}" class="font-semibold text-indigo-400 hover:text-white">{{ config('app.name') }}</a>
            </p>
        </div>
    </section>
@endsection
