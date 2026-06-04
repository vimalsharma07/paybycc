@extends('layouts.marketing')

@section('title', $siteSettings->displayName().' — Freelancer & service marketplace')
@section('meta_description', 'Find freelancers, place orders, and pay securely on '.$siteSettings->displayName().'. Card-first payments with KYC, compliance, and transparent settlements.')

@section('content')
    {{-- Hero --}}
    <section class="relative overflow-hidden px-4 pb-20 pt-12 sm:px-6 sm:pt-16 lg:pb-28 lg:pt-20">
        <div class="mx-auto grid max-w-6xl gap-12 lg:grid-cols-2 lg:items-center lg:gap-16">
            <div>
                <p class="animate-fade-up inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-indigo-300">
                    <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-emerald-400"></span>
                    Marketplace · Card payments · Settlements
                </p>
                <h1 class="animate-fade-up animate-delay-100 mt-6 text-4xl font-extrabold leading-[1.1] tracking-tight text-white sm:text-5xl lg:text-[3.25rem]">
                    Hire talent. Get paid. <span class="bg-gradient-to-r from-indigo-400 to-cyan-400 bg-clip-text text-transparent">One trusted platform.</span>
                </h1>
                <p class="animate-fade-up animate-delay-200 mt-6 text-lg leading-relaxed text-slate-400">
                    {{ $siteSettings->displayName() }} connects customers with freelancers and service providers. Discover skills, place orders, and pay securely — with <strong class="font-medium text-slate-300">credit &amp; debit cards</strong> as our primary checkout today and more payment methods coming soon.
                </p>
                <div class="animate-fade-up animate-delay-300 mt-10 flex flex-wrap gap-3">
                    @auth
                        @php $payUrl = auth()->user()->hasActiveKyc() ? route('payments.create') : route('kyc.index'); @endphp
                        <a href="{{ $payUrl }}" class="pay-now-btn inline-flex items-center justify-center rounded-2xl bg-gradient-to-r from-cyan-500 via-indigo-500 to-violet-600 px-8 py-3.5 text-base font-bold text-white shadow-xl transition hover:brightness-110">
                            {{ auth()->user()->hasActiveKyc() ? 'Pay with card' : 'Complete KYC to pay' }}
                        </a>
                        <a href="{{ auth()->user()->hasActiveKyc() ? route('dashboard') : route('kyc.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-white/20 bg-white/5 px-6 py-3.5 text-base font-semibold text-white transition hover:bg-white/10">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="pay-now-btn inline-flex items-center justify-center rounded-2xl bg-gradient-to-r from-cyan-500 via-indigo-500 to-violet-600 px-8 py-3.5 text-base font-bold text-white shadow-xl transition hover:brightness-110">
                            Join free
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
                    <p class="animate-fade-up animate-delay-300 mt-4 text-xs text-slate-500">India-first · KYC &amp; compliance built in · <x-brand-wordmark variant="dark" size="sm" class="inline-flex align-baseline" /></p>
                @endguest
                <dl class="animate-fade-up animate-delay-400 mt-14 grid grid-cols-3 gap-6 border-t border-white/10 pt-10">
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Payments</dt>
                        <dd class="mt-1 text-sm font-semibold text-white">Cards first (CC/DC)</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Sellers</dt>
                        <dd class="mt-1 text-sm font-semibold text-white">KYC before payout</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Trust</dt>
                        <dd class="mt-1 text-sm font-semibold text-white">Risk &amp; Safe Status</dd>
                    </div>
                </dl>
            </div>
            <div class="relative lg:pl-8">
                <div class="animate-float-soft relative mx-auto max-w-lg">
                    <div class="absolute -right-6 -top-6 h-40 w-40 rounded-full bg-gradient-to-br from-indigo-500/40 to-transparent blur-2xl"></div>
                    <img
                        src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?auto=format&amp;fit=crop&amp;w=900&amp;q=80"
                        alt="Secure card payment on marketplace"
                        class="relative z-10 rounded-3xl border border-white/10 shadow-2xl shadow-black/40"
                        width="900"
                        height="600"
                        loading="eager"
                    />
                    <div class="absolute -bottom-6 -left-4 z-20 max-w-[240px] rounded-2xl border border-white/10 bg-slate-900/95 p-4 shadow-xl backdrop-blur-md">
                        <p class="text-xs font-medium text-indigo-300">Settlement engine</p>
                        <p class="mt-1 text-sm text-slate-300">Funds release to freelancers only after payment, KYC, and risk checks pass.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Who it's for --}}
    <section id="marketplace" class="scroll-mt-24 border-y border-white/5 bg-slate-900/40 px-4 py-20 sm:px-6">
        <div class="mx-auto max-w-6xl">
            <div class="max-w-2xl">
                <h2 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">Built for both sides of the deal.</h2>
                <p class="mt-4 text-lg text-slate-400">Customers find verified talent. Freelancers get paid through a compliant settlement path — not informal transfers.</p>
            </div>
            <div class="mt-14 grid gap-6 lg:grid-cols-2">
                <article class="rounded-2xl border border-cyan-500/20 bg-gradient-to-br from-cyan-500/10 to-transparent p-8">
                    <p class="text-sm font-semibold uppercase tracking-wider text-cyan-300">Customers</p>
                    <h3 class="mt-3 text-xl font-semibold text-white">Find &amp; pay with confidence</h3>
                    <ul class="mt-4 space-y-2 text-sm text-slate-400">
                        <li class="flex gap-2"><span class="text-cyan-400">✓</span> Search by service, provider, or identity (as search rolls out)</li>
                        <li class="flex gap-2"><span class="text-cyan-400">✓</span> Place orders with clear status tracking</li>
                        <li class="flex gap-2"><span class="text-cyan-400">✓</span> Pay with card — primary method today</li>
                    </ul>
                </article>
                <article class="rounded-2xl border border-violet-500/20 bg-gradient-to-br from-violet-500/10 to-transparent p-8">
                    <p class="text-sm font-semibold uppercase tracking-wider text-violet-300">Freelancers &amp; sellers</p>
                    <h3 class="mt-3 text-xl font-semibold text-white">Onboard once, get settled right</h3>
                    <ul class="mt-4 space-y-2 text-sm text-slate-400">
                        <li class="flex gap-2"><span class="text-violet-400">✓</span> List services &amp; sub-services you offer</li>
                        <li class="flex gap-2"><span class="text-violet-400">✓</span> Mandatory KYC before bank settlement</li>
                        <li class="flex gap-2"><span class="text-violet-400">✓</span> Optional: only accept KYC-verified customers</li>
                    </ul>
                </article>
            </div>
            <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @php
                    $tiles = [
                        ['title' => 'Design & creative', 'desc' => 'Branding, UI, content, and more.', 'icon' => '🎨'],
                        ['title' => 'Tech & IT', 'desc' => 'Development, support, DevOps.', 'icon' => '💻'],
                        ['title' => 'Business services', 'desc' => 'Consulting, accounting, legal.', 'icon' => '📊'],
                        ['title' => 'Custom skills', 'desc' => 'Request new categories — admin approves.', 'icon' => '✨'],
                    ];
                @endphp
                @foreach ($tiles as $tile)
                    <article class="rounded-2xl border border-white/10 bg-slate-900/60 p-5 transition hover:border-indigo-500/30">
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
            <p class="mx-auto mt-4 max-w-2xl text-center text-lg text-slate-400">From discovery to settlement — with card payments at the centre.</p>
            <div class="mt-16 grid gap-10 lg:grid-cols-3">
                <div class="rounded-2xl border border-white/10 bg-gradient-to-b from-white/5 to-transparent p-8 text-center">
                    <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-500/20 text-xl font-bold text-indigo-300">1</span>
                    <h3 class="mt-6 text-xl font-semibold text-white">Discover &amp; order</h3>
                    <p class="mt-3 text-sm leading-relaxed text-slate-400">Customers find freelancers by service. Sellers complete onboarding and KYC.</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-gradient-to-b from-white/5 to-transparent p-8 text-center">
                    <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-violet-500/20 text-xl font-bold text-violet-300">2</span>
                    <h3 class="mt-6 text-xl font-semibold text-white">Pay by card</h3>
                    <p class="mt-3 text-sm leading-relaxed text-slate-400">Checkout via secure payment partners. Credit &amp; debit cards supported now; more methods planned.</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-gradient-to-b from-white/5 to-transparent p-8 text-center">
                    <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-cyan-500/20 text-xl font-bold text-cyan-300">3</span>
                    <h3 class="mt-6 text-xl font-semibold text-white">Settle safely</h3>
                    <p class="mt-3 text-sm leading-relaxed text-slate-400">Payouts after risk review (Safe Status), tax fields, and compliance checks — not before.</p>
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
                    alt="Secure card checkout"
                    class="rounded-3xl border border-white/10 shadow-xl"
                    loading="lazy"
                    width="900"
                    height="600"
                />
            </div>
            <div class="order-1 lg:order-2">
                <h2 class="text-3xl font-bold text-white sm:text-4xl">Payments you can explain to finance.</h2>
                <p class="mt-6 text-lg text-slate-400">We are building support for multiple payment rails. Today, <strong class="text-slate-300">card payments (CC/DC)</strong> are the main checkout path — fast for customers and familiar for reconciliation.</p>
                <ul class="mt-8 space-y-4 text-slate-300">
                    <li class="flex gap-3"><span class="text-emerald-400">✓</span> Licensed payment gateways</li>
                    <li class="flex gap-3"><span class="text-emerald-400">✓</span> Order, payment &amp; settlement status</li>
                    <li class="flex gap-3"><span class="text-emerald-400">✓</span> GST, TDS &amp; TCS fields for compliance</li>
                    <li class="flex gap-3"><span class="text-emerald-400">✓</span> Wallet &amp; bank payout tools for sellers</li>
                </ul>
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
            <h2 class="text-3xl font-bold text-white sm:text-4xl">Ready to join the marketplace?</h2>
            <p class="mx-auto mt-4 max-w-xl text-lg text-indigo-100/90">Whether you hire freelancers or offer services — start with a free account, verify when required, and pay or get paid with clarity.</p>
            <div class="mt-10 flex flex-wrap justify-center gap-3 sm:gap-4">
                @guest
                    <a href="{{ route('register') }}" class="pay-now-btn inline-flex rounded-2xl bg-gradient-to-r from-cyan-400 via-white to-amber-200 px-8 py-3.5 text-base font-bold text-slate-900 shadow-lg transition hover:brightness-105">Create account</a>
                    <a href="{{ route('login') }}" class="inline-flex rounded-2xl border-2 border-fuchsia-300/50 bg-fuchsia-500/20 px-6 py-3.5 text-base font-semibold text-white transition hover:bg-fuchsia-500/30">Log in</a>
                @else
                    <a href="{{ auth()->user()->hasActiveKyc() ? route('payments.create') : route('kyc.index') }}" class="pay-now-btn inline-flex rounded-2xl bg-gradient-to-r from-cyan-400 via-white to-amber-200 px-8 py-3.5 text-base font-bold text-slate-900 shadow-lg transition hover:brightness-105">Pay with card</a>
                    <a href="{{ route('dashboard') }}" class="inline-flex rounded-2xl border-2 border-white/40 px-6 py-3.5 text-base font-semibold text-white transition hover:bg-white/10">Dashboard</a>
                @endguest
                <a href="{{ route('contact') }}" class="inline-flex rounded-2xl border border-white/30 px-6 py-3.5 text-base font-semibold text-white/90 transition hover:bg-white/10">Contact us</a>
            </div>
        </div>
    </section>
@endsection
