@extends('layouts.marketing')

@section('title', $siteSettings->displayName().' — Accept business payments online')
@section('meta_description', 'Business owners, freelancers, service providers & startups on '.$siteSettings->displayName().' accept UPI, cards & net banking through licensed gateways — with KYC, GST-ready records & bank settlements.')

@section('content')
    {{-- Hero --}}
    <section class="relative overflow-hidden px-4 pb-20 pt-12 sm:px-6 sm:pt-16 lg:pb-28 lg:pt-20">
        <div class="mx-auto grid max-w-6xl gap-12 lg:grid-cols-2 lg:items-center lg:gap-16">
            <div>
                <p class="animate-fade-up inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-indigo-300">
                    <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-emerald-400"></span>
                    Business owners · Freelancers · Startups · Licensed checkout
                </p>
                <h1 class="animate-fade-up animate-delay-100 mt-6 text-4xl font-extrabold leading-[1.1] tracking-tight text-white sm:text-5xl lg:text-[3.25rem]">
                    Accept business payments online — <span class="bg-gradient-to-r from-emerald-400 via-cyan-400 to-indigo-400 bg-clip-text text-transparent">fast, attractive &amp; approved.</span>
                </h1>
                <p class="animate-fade-up animate-delay-200 mt-6 text-lg leading-relaxed text-slate-400">
                    Whether you run a <strong class="font-medium text-slate-300">startup, small business, freelance practice, or service company</strong>, {{ $siteSettings->displayName() }} lets you collect payments through <strong class="font-medium text-slate-300">licensed payment gateways</strong> — UPI, cards, net banking &amp; wallets — with KYC verification, clear records, and settlement straight to your bank.
                </p>
                <div class="animate-fade-up animate-delay-300 mt-8">
                    <x-payment-methods variant="dark" size="sm" :show-label="false" />
                </div>
                <div class="animate-fade-up animate-delay-300 mt-10 flex flex-wrap gap-3">
                    @auth
                        @php $payUrl = auth()->user()->canUsePlatform() ? route('payments.create') : route('kyc.index'); @endphp
                        <a href="{{ $payUrl }}" class="pay-now-btn inline-flex items-center justify-center rounded-2xl bg-gradient-to-r from-cyan-500 via-indigo-500 to-violet-600 px-8 py-3.5 text-base font-bold text-white shadow-xl transition hover:brightness-110">
                            {{ auth()->user()->canUsePlatform() ? 'Pay a freelancer' : 'Complete KYC to pay' }}
                        </a>
                        <a href="{{ auth()->user()->canUsePlatform() ? route('dashboard') : route('kyc.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-white/20 bg-white/5 px-6 py-3.5 text-base font-semibold text-white transition hover:bg-white/10">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="pay-now-btn inline-flex items-center justify-center rounded-2xl bg-gradient-to-r from-cyan-500 via-indigo-500 to-violet-600 px-8 py-3.5 text-base font-bold text-white shadow-xl transition hover:brightness-110">
                            Start accepting business payments
                        </a>
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-2xl border border-fuchsia-400/40 bg-fuchsia-500/10 px-6 py-3.5 text-base font-semibold text-fuchsia-200 transition hover:bg-fuchsia-500/20">
                            Log in
                        </a>
                    @endauth
                    <a href="{{ route('about') }}" class="inline-flex items-center justify-center rounded-2xl px-4 py-3.5 text-base font-medium text-slate-400 transition hover:text-white">
                        How it works →
                    </a>
                </div>
                @guest
                    <p class="animate-fade-up animate-delay-300 mt-4 text-xs text-slate-500">RBI-authorised partners · PAN/KYC · GST &amp; tax fields · <a href="{{ route('terms') }}" class="text-indigo-400 hover:text-white">Terms</a> &amp; <a href="{{ route('privacy') }}" class="text-indigo-400 hover:text-white">Privacy</a> · <x-brand-wordmark variant="dark" size="sm" class="inline-flex align-baseline" /></p>
                @endguest
                <dl class="animate-fade-up animate-delay-400 mt-14 grid grid-cols-3 gap-6 border-t border-white/10 pt-10">
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Checkout</dt>
                        <dd class="mt-1 text-sm font-semibold text-white">UPI · Cards · NB</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">For</dt>
                        <dd class="mt-1 text-sm font-semibold text-white">Business &amp; startups</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Compliant</dt>
                        <dd class="mt-1 text-sm font-semibold text-white">KYC · GST · TDS/TCS</dd>
                    </div>
                </dl>
            </div>
            <div class="relative lg:pl-8">
                <div class="animate-float-soft relative mx-auto max-w-lg">
                    <div class="absolute -right-6 -top-6 h-40 w-40 rounded-full bg-gradient-to-br from-emerald-500/40 to-transparent blur-2xl"></div>
                    <div class="absolute -left-4 top-1/3 h-32 w-32 rounded-full bg-gradient-to-br from-indigo-500/30 to-transparent blur-2xl"></div>
                    <img
                        src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?auto=format&amp;fit=crop&amp;w=900&amp;q=80"
                        alt="Freelancer accepting UPI and card payments"
                        class="relative z-10 rounded-3xl border border-white/10 shadow-2xl shadow-black/40"
                        width="900"
                        height="600"
                        loading="eager"
                    />
                    <div class="absolute -bottom-6 -left-4 z-20 max-w-[260px] rounded-2xl border border-emerald-500/20 bg-slate-900/95 p-4 shadow-xl backdrop-blur-md">
                        <p class="text-xs font-medium text-emerald-300">Client paid via UPI</p>
                        <p class="mt-1 font-mono text-lg font-bold text-white">₹25,000</p>
                        <p class="mt-1 text-xs text-slate-400">Settled to your bank · GST-ready records</p>
                    </div>
                    <div class="absolute -right-2 top-8 z-20 rounded-xl border border-indigo-500/25 bg-indigo-950/90 px-3 py-2 text-xs font-bold text-indigo-200 shadow-lg backdrop-blur-sm">
                        + Credit card
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Payment methods spotlight --}}
    <section class="border-y border-white/5 bg-gradient-to-b from-slate-900/60 to-slate-950/40 px-4 py-16 sm:px-6 sm:py-20">
        <div class="mx-auto max-w-6xl text-center">
            <p class="text-sm font-semibold uppercase tracking-wider text-emerald-400">Every way clients pay online</p>
            <h2 class="mt-3 text-3xl font-bold tracking-tight text-white sm:text-4xl">Not just cards — the full Indian checkout.</h2>
            <p class="mx-auto mt-4 max-w-2xl text-lg text-slate-400">Your clients choose what feels natural. You see one order, one settlement — whether they paid with PhonePe, a Visa card, or net banking.</p>
            <div class="pay-methods__grid pay-methods__grid--5 mx-auto mt-12 max-w-5xl text-left">
                @foreach ([
                    ['code' => 'UPI', 'label' => 'UPI', 'desc' => 'GPay, PhonePe, Paytm, BHIM — instant for most clients.', 'tone' => 'emerald'],
                    ['code' => 'CC', 'label' => 'Credit card', 'desc' => 'Visa, Mastercard, RuPay — ideal for retainers & milestones.', 'tone' => 'indigo'],
                    ['code' => 'DC', 'label' => 'Debit card', 'desc' => 'Every major bank — familiar checkout in seconds.', 'tone' => 'violet'],
                    ['code' => 'NB', 'label' => 'Net banking', 'desc' => 'Direct bank transfer at checkout — trusted by businesses.', 'tone' => 'cyan'],
                    ['code' => 'APP', 'label' => 'Wallets', 'desc' => 'Paytm and other wallets where enabled on gateway.', 'tone' => 'fuchsia'],
                ] as $card)
                    <article class="pay-method-pill pay-method-pill--{{ $card['tone'] }} pay-method-pill--dark flex-col !items-start !gap-3 !p-5">
                        <span class="pay-method-pill__code">{{ $card['code'] }}</span>
                        <div>
                            <p class="pay-method-pill__label text-base">{{ $card['label'] }}</p>
                            <p class="mt-2 text-sm leading-relaxed text-slate-400">{{ $card['desc'] }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Who it's for --}}
    <section id="marketplace" class="scroll-mt-24 border-b border-white/5 bg-slate-900/40 px-4 py-20 sm:px-6">
        <div class="mx-auto max-w-6xl">
            <div class="max-w-2xl">
                <h2 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">Built for every kind of business that sells services online.</h2>
                <p class="mt-4 text-lg text-slate-400">Freelancers, agencies, consultants, and early-stage startups — accept payments in a quick, professional checkout while we handle verification, records, and bank payouts the approved way.</p>
            </div>
            <div class="mt-14 grid gap-6 lg:grid-cols-2">
                <article class="rounded-2xl border border-cyan-500/20 bg-gradient-to-br from-cyan-500/10 to-transparent p-8">
                    <p class="text-sm font-semibold uppercase tracking-wider text-cyan-300">For your clients</p>
                    <h3 class="mt-3 text-xl font-semibold text-white">Pay the way they already do</h3>
                    <ul class="mt-4 space-y-2 text-sm text-slate-400">
                        <li class="flex gap-2"><span class="text-cyan-400">✓</span> UPI, cards, net banking &amp; wallets at secure checkout</li>
                        <li class="flex gap-2"><span class="text-cyan-400">✓</span> Search freelancers by name, skill, or code</li>
                        <li class="flex gap-2"><span class="text-cyan-400">✓</span> Clear payment status from checkout to receipt</li>
                    </ul>
                </article>
                <article class="rounded-2xl border border-violet-500/20 bg-gradient-to-br from-violet-500/10 to-transparent p-8">
                    <p class="text-sm font-semibold uppercase tracking-wider text-violet-300">For business owners &amp; providers</p>
                    <h3 class="mt-3 text-xl font-semibold text-white">Share a link. Get paid. Stay compliant.</h3>
                    <ul class="mt-4 space-y-2 text-sm text-slate-400">
                        <li class="flex gap-2"><span class="text-violet-400">✓</span> Payment links for invoices, retainers &amp; project milestones</li>
                        <li class="flex gap-2"><span class="text-violet-400">✓</span> Licensed checkout — UPI, cards, net banking &amp; wallets</li>
                        <li class="flex gap-2"><span class="text-violet-400">✓</span> PAN/KYC, GST/TDS/TCS fields &amp; automatic bank settlement</li>
                    </ul>
                </article>
            </div>
            <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @php
                    $tiles = [
                        ['title' => 'Freelancers', 'desc' => 'Designers, developers, writers & more.', 'icon' => '🎨'],
                        ['title' => 'Service providers', 'desc' => 'Agencies, studios & professional services.', 'icon' => '💼'],
                        ['title' => 'Startups', 'desc' => 'Collect early revenue with proper records.', 'icon' => '🚀'],
                        ['title' => 'Small business', 'desc' => 'Quick online pay — no custom gateway build.', 'icon' => '🏪'],
                    ];
                @endphp
                @foreach ($tiles as $tile)
                    <article class="rounded-2xl border border-white/10 bg-slate-900/60 p-5 transition hover:border-indigo-500/30 hover:shadow-lg hover:shadow-indigo-500/5">
                        <span class="text-2xl">{{ $tile['icon'] }}</span>
                        <h3 class="mt-3 font-semibold text-white">{{ $tile['title'] }}</h3>
                        <p class="mt-2 text-sm text-slate-400">{{ $tile['desc'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- How it works --}}
    <section class="px-4 py-20 sm:px-6">
        <div class="mx-auto max-w-6xl">
            <h2 class="text-center text-3xl font-bold text-white sm:text-4xl">How it works</h2>
            <p class="mx-auto mt-4 max-w-2xl text-center text-lg text-slate-400">From first hello to money in your bank — with every major payment method in between.</p>
            <div class="mt-16 grid gap-10 lg:grid-cols-3">
                <div class="rounded-2xl border border-white/10 bg-gradient-to-b from-white/5 to-transparent p-8 text-center">
                    <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-500/20 text-xl font-bold text-indigo-300">1</span>
                    <h3 class="mt-6 text-xl font-semibold text-white">List &amp; share</h3>
                    <p class="mt-3 text-sm leading-relaxed text-slate-400">Complete KYC, add your bank, and share your profile or payment link with clients.</p>
                </div>
                <div class="rounded-2xl border border-emerald-500/20 bg-gradient-to-b from-emerald-500/10 to-transparent p-8 text-center">
                    <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-500/20 text-xl font-bold text-emerald-300">2</span>
                    <h3 class="mt-6 text-xl font-semibold text-white">Client pays their way</h3>
                    <p class="mt-3 text-sm leading-relaxed text-slate-400">UPI, credit card, debit card, net banking, or wallet — one secure Cashfree checkout.</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-gradient-to-b from-white/5 to-transparent p-8 text-center">
                    <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-cyan-500/20 text-xl font-bold text-cyan-300">3</span>
                    <h3 class="mt-6 text-xl font-semibold text-white">Settle to your bank</h3>
                    <p class="mt-3 text-sm leading-relaxed text-slate-400">Net amount lands in your bank after compliance checks — with full transaction history.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Payments focus --}}
    <section class="border-y border-white/5 bg-slate-900/50 px-4 py-20 sm:px-6">
        <div class="mx-auto grid max-w-6xl items-center gap-12 lg:grid-cols-2">
            <div class="order-2 lg:order-1">
                <img
                    src="https://images.unsplash.com/photo-1563013544-824ae1b704d3?auto=format&amp;fit=crop&amp;w=900&amp;q=80"
                    alt="Secure multi-method checkout for freelancers"
                    class="rounded-3xl border border-white/10 shadow-xl"
                    loading="lazy"
                    width="900"
                    height="600"
                />
            </div>
            <div class="order-1 lg:order-2">
                <h2 class="text-3xl font-bold text-white sm:text-4xl">Approved online payments, explained clearly.</h2>
                <p class="mt-6 text-lg text-slate-400">We route collections through <strong class="text-slate-300">RBI-authorised payment partners</strong> — not informal transfers. Every order carries fee breakdowns, tax fields where applicable, and audit-friendly status from checkout to bank credit.</p>
                <ul class="mt-8 space-y-4 text-slate-300">
                    <li class="flex gap-3"><span class="text-emerald-400">✓</span> Licensed gateway checkout (UPI, cards, net banking &amp; wallets)</li>
                    <li class="flex gap-3"><span class="text-emerald-400">✓</span> Identity verification (PAN/KYC) before seller bank payouts</li>
                    <li class="flex gap-3"><span class="text-emerald-400">✓</span> GST, TDS &amp; TCS fields aligned with Indian tax norms</li>
                    <li class="flex gap-3"><span class="text-emerald-400">✓</span> Clear <a href="{{ route('terms') }}" class="font-semibold text-indigo-300 underline-offset-2 hover:text-white">Terms</a> &amp; <a href="{{ route('privacy') }}" class="font-semibold text-indigo-300 underline-offset-2 hover:text-white">Privacy Policy</a> you can share with clients</li>
                </ul>
                <div class="mt-8">
                    <x-payment-methods variant="dark" size="sm" />
                </div>
                <div class="mt-10 flex flex-wrap gap-4">
                    <a href="{{ route('privacy') }}" class="rounded-xl bg-white px-6 py-3 text-sm font-semibold text-slate-900 transition hover:bg-slate-200">Privacy policy</a>
                    <a href="{{ route('terms') }}" class="rounded-xl border border-white/20 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10">Terms &amp; conditions</a>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="px-4 py-24 sm:px-6">
        <div class="mx-auto max-w-4xl rounded-3xl border border-indigo-500/30 bg-gradient-to-br from-indigo-600/40 via-violet-600/30 to-slate-900 p-10 text-center shadow-2xl shadow-indigo-500/20 sm:p-14">
            <h2 class="text-3xl font-bold text-white sm:text-4xl">Ready to accept business payments the right way?</h2>
            <p class="mx-auto mt-4 max-w-xl text-lg text-indigo-100/90">For business owners, freelancers, service providers &amp; startups — start in minutes, share payment links, and collect online through approved channels with settlements to your bank.</p>
            <div class="mt-8 flex justify-center">
                <x-payment-methods variant="dark" size="sm" :show-label="false" class="justify-center" />
            </div>
            <div class="mt-10 flex flex-wrap justify-center gap-3 sm:gap-4">
                @guest
                    <a href="{{ route('register') }}" class="pay-now-btn inline-flex rounded-2xl bg-gradient-to-r from-cyan-400 via-white to-amber-200 px-8 py-3.5 text-base font-bold text-slate-900 shadow-lg transition hover:brightness-105">Create free account</a>
                    <a href="{{ route('login') }}" class="inline-flex rounded-2xl border-2 border-fuchsia-300/50 bg-fuchsia-500/20 px-6 py-3.5 text-base font-semibold text-white transition hover:bg-fuchsia-500/30">Log in</a>
                @else
                    <a href="{{ auth()->user()->canUsePlatform() ? route('payments.create') : route('kyc.index') }}" class="pay-now-btn inline-flex rounded-2xl bg-gradient-to-r from-cyan-400 via-white to-amber-200 px-8 py-3.5 text-base font-bold text-slate-900 shadow-lg transition hover:brightness-105">Pay now</a>
                    <a href="{{ route('dashboard') }}" class="inline-flex rounded-2xl border-2 border-white/40 px-6 py-3.5 text-base font-semibold text-white transition hover:bg-white/10">Dashboard</a>
                @endguest
                <a href="{{ route('contact') }}" class="inline-flex rounded-2xl border border-white/30 px-6 py-3.5 text-base font-semibold text-white/90 transition hover:bg-white/10">Contact us</a>
            </div>
        </div>
    </section>
@endsection
