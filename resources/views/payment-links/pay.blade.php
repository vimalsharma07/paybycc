@extends('layouts.marketing')

@section('title', ($seller?->name ?? 'Payment').' — '.($paymentLink->isOpenAmount() ? 'Pay' : 'Pay ₹'.number_format((float) $paymentLink->amount, 0)).' — '.config('app.name'))
@section('meta_description', 'Pay '.$seller?->name.' via UPI, card, or net banking on '.config('app.name').'.')
@section('meta_robots', 'noindex, nofollow')

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
                    @if ($paymentLink->isOpenAmount())
                        <p class="mt-6 text-lg font-semibold text-white/90">Enter amount to pay</p>
                        <p class="mt-1 text-sm text-white/70">₹{{ number_format($minAmount, 0) }} – ₹{{ number_format($maxAmount, 0) }}</p>
                    @else
                        <p class="mt-6 font-mono text-5xl font-extrabold tracking-tight">₹{{ number_format((float) $paymentLink->amount, 2) }}</p>
                    @endif
                </div>

                <div class="space-y-5 p-6 sm:p-8">
                    @if ($paymentLink->description)
                        <div class="rounded-xl border border-white/10 bg-white/5 px-4 py-3">
                            <p class="text-xs font-bold uppercase text-slate-500">Note</p>
                            <p class="mt-1 text-sm text-slate-200">{{ $paymentLink->description }}</p>
                        </div>
                    @endif

                    <div class="flex flex-wrap gap-2 text-xs">
                        <span class="rounded-full border border-white/10 bg-white/5 px-2.5 py-1 text-slate-400">{{ $paymentLink->usageLimitLabel() }}</span>
                        @if ($paymentLink->expires_at && $payable)
                            <span class="rounded-full border border-white/10 bg-white/5 px-2.5 py-1 text-slate-400">Expires {{ $paymentLink->expires_at->format('M j, Y') }}</span>
                        @endif
                    </div>

                    <div>
                        <x-payment-methods variant="dark" size="sm" :show-label="false" />
                    </div>

                    @if (! $payable)
                        <div class="rounded-xl border border-amber-500/30 bg-amber-500/10 px-4 py-4 text-sm text-amber-100">
                            <p class="font-semibold">{{ $payError ?? 'This payment link is not available.' }}</p>
                        </div>
                    @elseif ($guestCheckout)
                        @if ($canPayNow && $pendingPayment && (int) $pendingPayment->user_id === (int) auth()->id())
                            <a href="{{ route('payments.checkout', $pendingPayment) }}"
                                class="pay-now-btn inline-flex w-full items-center justify-center rounded-2xl bg-gradient-to-r from-emerald-500 via-cyan-500 to-indigo-600 px-6 py-4 text-base font-bold text-white shadow-xl">
                                Resume checkout →
                            </a>
                        @elseif ($canPayNow)
                            <form method="POST" action="{{ route('payment-links.pay.store', $paymentLink->link_token) }}" class="mb-4 space-y-4">
                                @csrf
                                @include('partials.checkout-client-meta')
                                @include('payment-links.partials.pay-amount-field', ['paymentLink' => $paymentLink])
                                <button type="submit" class="pay-now-btn inline-flex w-full items-center justify-center rounded-2xl bg-gradient-to-r from-emerald-500 via-cyan-500 to-indigo-600 px-6 py-4 text-base font-bold text-white shadow-xl">
                                    Pay as logged-in user
                                </button>
                            </form>
                            <p class="text-center text-xs text-slate-500">— or pay without an account —</p>
                        @endif

                        <form method="POST" action="{{ route('payment-links.pay.guest', $paymentLink->link_token) }}" class="mt-4 space-y-4">
                            @csrf
                            @include('partials.checkout-client-meta')
                            <div>
                                <label for="guest-name" class="block text-sm font-semibold text-slate-300">Your name</label>
                                <input id="guest-name" name="name" type="text" value="{{ old('name') }}" required
                                    class="mt-2 block w-full rounded-xl border border-white/10 bg-slate-950/80 px-4 py-3 text-white focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                                @error('name')<p class="mt-1 text-sm text-rose-400">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="guest-email" class="block text-sm font-semibold text-slate-300">Email</label>
                                <input id="guest-email" name="email" type="email" value="{{ old('email') }}" required
                                    class="mt-2 block w-full rounded-xl border border-white/10 bg-slate-950/80 px-4 py-3 text-white focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                                @error('email')<p class="mt-1 text-sm text-rose-400">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="guest-phone" class="block text-sm font-semibold text-slate-300">Mobile</label>
                                <input id="guest-phone" name="phone" type="tel" inputmode="numeric" value="{{ old('phone') }}" required maxlength="10"
                                    class="mt-2 block w-full rounded-xl border border-white/10 bg-slate-950/80 px-4 py-3 text-white focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                                @error('phone')<p class="mt-1 text-sm text-rose-400">{{ $message }}</p>@enderror
                            </div>
                            @include('payment-links.partials.pay-amount-field', ['paymentLink' => $paymentLink])
                            <button type="submit" class="pay-now-btn inline-flex w-full items-center justify-center rounded-2xl bg-gradient-to-r from-cyan-500 via-indigo-500 to-violet-600 px-6 py-4 text-base font-bold text-white shadow-xl">
                                Continue to pay
                            </button>
                        </form>
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
                    @elseif ($needsKyc)
                        <div class="rounded-xl border border-indigo-500/30 bg-indigo-500/10 px-4 py-4 text-center text-sm text-indigo-100">
                            <p>This seller only accepts payers who have completed KYC.</p>
                            <a href="{{ route('kyc.index') }}" class="mt-3 inline-flex font-bold text-white underline">Complete KYC →</a>
                        </div>
                    @elseif (! $canPayNow)
                        <div class="rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-4 text-sm text-rose-100">
                            <p class="font-semibold">You cannot pay this link.</p>
                            <p class="mt-1 text-rose-200/90">This may be your own payment link.</p>
                        </div>
                    @else
                        @if ($pendingPayment && (int) $pendingPayment->user_id === (int) auth()->id())
                            <a href="{{ route('payments.checkout', $pendingPayment) }}"
                                class="pay-now-btn inline-flex w-full items-center justify-center rounded-2xl bg-gradient-to-r from-emerald-500 via-cyan-500 to-indigo-600 px-6 py-4 text-base font-bold text-white shadow-xl">
                                Resume checkout →
                            </a>
                        @else
                            <form method="POST" action="{{ route('payment-links.pay.store', $paymentLink->link_token) }}" class="space-y-4">
                                @csrf
                                @include('partials.checkout-client-meta')
                                @include('payment-links.partials.pay-amount-field', ['paymentLink' => $paymentLink])
                                <button type="submit"
                                    class="pay-now-btn inline-flex w-full items-center justify-center rounded-2xl bg-gradient-to-r from-emerald-500 via-cyan-500 to-indigo-600 px-6 py-4 text-base font-bold text-white shadow-xl hover:brightness-110">
                                    @if ($paymentLink->isOpenAmount())
                                        Continue to pay
                                    @else
                                        Pay ₹{{ number_format((float) $paymentLink->amount, 2) }} now
                                    @endif
                                </button>
                            </form>
                        @endif

                        <p class="text-center text-xs text-slate-500">Secure checkout · UPI, cards, net banking &amp; wallets</p>
                    @endif

                    @error('payment_link')
                        <p class="text-sm text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <p class="mt-8 text-center text-xs text-slate-600">
                Powered by <a href="{{ route('home') }}" class="font-semibold text-indigo-400 hover:text-white">{{ config('app.name') }}</a>
            </p>
        </div>
    </section>
@endsection
