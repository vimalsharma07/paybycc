@extends('layouts.marketing')

@section('title', config('app.name').' — Pay bills, tuition & more')
@section('meta_description', 'Pay electricity, water, tuition, rent, insurance & everyday bills securely with '.config('app.name').'. Wallet & settlement tracking.')

@section('content')
    {{-- Hero --}}
    <section class="relative overflow-hidden px-4 pb-20 pt-12 sm:px-6 sm:pt-16 lg:pb-28 lg:pt-20">
        <div class="mx-auto grid max-w-6xl gap-12 lg:grid-cols-2 lg:items-center lg:gap-16">
            <div>
                <p class="animate-fade-up inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-indigo-300">
                    <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-emerald-400"></span>
                    Bills · Tuition · Wallet · Settlement
                </p>
                <h1 class="animate-fade-up animate-delay-100 mt-6 text-4xl font-extrabold leading-[1.1] tracking-tight text-white sm:text-5xl lg:text-[3.25rem]">
                    Pay every bill that matters — <span class="bg-gradient-to-r from-indigo-400 to-cyan-400 bg-clip-text text-transparent">in one calm place.</span>
                </h1>
                <p class="animate-fade-up animate-delay-200 mt-6 text-lg leading-relaxed text-slate-400">
                    Tuition fees, utilities, rent, subscriptions, insurance & more. Use your card safely, track settlements, and optionally route payouts to your bank when funds land in your wallet.
                </p>
                <div class="animate-fade-up animate-delay-300 mt-10 flex flex-wrap gap-4">
                    @auth
                        <a href="{{ auth()->user()->hasActiveKyc() ? route('payments.create') : route('kyc.index') }}" class="inline-flex items-center justify-center rounded-2xl bg-gradient-to-r from-indigo-500 to-violet-600 px-8 py-3.5 text-base font-semibold text-white shadow-xl shadow-indigo-500/30 transition hover:brightness-110">
                            {{ auth()->user()->hasActiveKyc() ? 'Pay a bill' : 'Complete KYC to pay' }}
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-2xl bg-gradient-to-r from-indigo-500 to-violet-600 px-8 py-3.5 text-base font-semibold text-white shadow-xl shadow-indigo-500/30 transition hover:brightness-110">
                            Create free account
                        </a>
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-2xl border border-white/15 bg-white/5 px-8 py-3.5 text-base font-semibold text-white transition hover:bg-white/10">
                            Log in
                        </a>
                    @endauth
                    <a href="{{ route('about') }}" class="inline-flex items-center justify-center rounded-2xl px-4 py-3.5 text-base font-medium text-slate-400 transition hover:text-white">
                        How we help →
                    </a>
                </div>
                <dl class="animate-fade-up animate-delay-400 mt-14 grid grid-cols-3 gap-6 border-t border-white/10 pt-10">
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Designed for</dt>
                        <dd class="mt-1 text-sm font-semibold text-white">India-first flows</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Your data</dt>
                        <dd class="mt-1 text-sm font-semibold text-white">Encrypted &amp; minimal</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Support</dt>
                        <dd class="mt-1 text-sm font-semibold text-white">Human contact page</dd>
                    </div>
                </dl>
            </div>
            <div class="relative lg:pl-8">
                <div class="animate-float-soft relative mx-auto max-w-lg">
                    <div class="absolute -right-6 -top-6 h-40 w-40 rounded-full bg-gradient-to-br from-indigo-500/40 to-transparent blur-2xl"></div>
                    <img
                        src="https://images.unsplash.com/photo-1563013544-824ae1b704d3?auto=format&amp;fit=crop&amp;w=900&amp;q=80"
                        alt="Secure digital payments and finance"
                        class="relative z-10 rounded-3xl border border-white/10 shadow-2xl shadow-black/40"
                        width="900"
                        height="600"
                        loading="eager"
                    />
                    <div class="absolute -bottom-6 -left-4 z-20 max-w-[220px] rounded-2xl border border-white/10 bg-slate-900/95 p-4 shadow-xl backdrop-blur-md">
                        <p class="text-xs font-medium text-indigo-300">Settlement insight</p>
                        <p class="mt-1 text-sm text-slate-300">See when funds are expected — then confirm bank receipt on your terms.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Bill types --}}
    <section id="bills" class="scroll-mt-24 border-y border-white/5 bg-slate-900/40 px-4 py-20 sm:px-6">
        <div class="mx-auto max-w-6xl">
            <div class="max-w-2xl">
                <h2 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">Everyday bills — covered.</h2>
                <p class="mt-4 text-lg text-slate-400">From classroom to kitchen: pay tuition &amp; education fees, household utilities, rent, insurance premiums, subscriptions, and other recurring charges — with clarity at each step.</p>
            </div>
            <div class="mt-14 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @php
                    $tiles = [
                        ['title' => 'Tuition & schools', 'desc' => 'Education fees, coaching, courses — schedule-friendly.', 'icon' => '🎓', 'img' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=600&q=80'],
                        ['title' => 'Utilities & home', 'desc' => 'Electricity, water, gas, broadband — stay current.', 'icon' => '🏠', 'img' => 'https://images.unsplash.com/photo-1581578731548-c64695cc6952?auto=format&fit=crop&w=600&q=80'],
                        ['title' => 'Rent & housing', 'desc' => 'Landlord payouts with records you can trust.', 'icon' => '🔑', 'img' => 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=600&q=80'],
                        ['title' => 'Insurance & health', 'desc' => 'Premiums for life, health, vehicle — one flow.', 'icon' => '🛡️', 'img' => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&w=600&q=80'],
                        ['title' => 'Subscriptions', 'desc' => 'OTT, software, memberships — fewer missed cycles.', 'icon' => '📱', 'img' => 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?auto=format&fit=crop&w=600&q=80'],
                        ['title' => 'Everything else', 'desc' => 'Any biller or purpose we support through your gateway.', 'icon' => '✨', 'img' => 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?auto=format&fit=crop&w=600&q=80'],
                    ];
                @endphp
                @foreach ($tiles as $i => $tile)
                    <article class="group overflow-hidden rounded-2xl border border-white/10 bg-slate-900/60 transition duration-300 hover:-translate-y-1 hover:border-indigo-500/40 hover:shadow-lg hover:shadow-indigo-500/10">
                        <div class="relative h-36 overflow-hidden">
                            <img src="{{ $tile['img'] }}" alt="" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy" />
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent"></div>
                            <span class="absolute bottom-3 left-4 text-2xl drop-shadow">{{ $tile['icon'] }}</span>
                        </div>
                        <div class="p-5">
                            <h3 class="text-lg font-semibold text-white">{{ $tile['title'] }}</h3>
                            <p class="mt-2 text-sm leading-relaxed text-slate-400">{{ $tile['desc'] }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- How it works --}}
    <section class="px-4 py-20 sm:px-6">
        <div class="mx-auto max-w-6xl">
            <h2 class="text-center text-3xl font-bold text-white sm:text-4xl">How PayByCC keeps it simple</h2>
            <p class="mx-auto mt-4 max-w-2xl text-center text-lg text-slate-400">Three calm steps — pay, track, settle — without jargon.</p>
            <div class="mt-16 grid gap-10 lg:grid-cols-3">
                <div class="rounded-2xl border border-white/10 bg-gradient-to-b from-white/5 to-transparent p-8 text-center">
                    <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-500/20 text-xl font-bold text-indigo-300">1</span>
                    <h3 class="mt-6 text-xl font-semibold text-white">Pay with your card</h3>
                    <p class="mt-3 text-sm leading-relaxed text-slate-400">Complete quick KYC once, then pay bills through your configured gateway — amounts checked against sensible limits.</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-gradient-to-b from-white/5 to-transparent p-8 text-center">
                    <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-violet-500/20 text-xl font-bold text-violet-300">2</span>
                    <h3 class="mt-6 text-xl font-semibold text-white">Watch your wallet</h3>
                    <p class="mt-3 text-sm leading-relaxed text-slate-400">Transactions log card payments with an expected settlement window. You choose whether payouts prefer your bank automatically.</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-gradient-to-b from-white/5 to-transparent p-8 text-center">
                    <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-cyan-500/20 text-xl font-bold text-cyan-300">3</span>
                    <h3 class="mt-6 text-xl font-semibold text-white">Confirm settlement</h3>
                    <p class="mt-3 text-sm leading-relaxed text-slate-400">When money lands in your linked bank, your history stays transparent — privacy-preserving by design.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Split feature --}}
    <section class="border-y border-white/5 bg-slate-900/50 px-4 py-20 sm:px-6">
        <div class="mx-auto grid max-w-6xl items-center gap-12 lg:grid-cols-2">
            <div class="order-2 lg:order-1">
                <img
                    src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?auto=format&amp;fit=crop&amp;w=900&amp;q=80"
                    alt="Paying bills online"
                    class="rounded-3xl border border-white/10 shadow-xl"
                    loading="lazy"
                    width="900"
                    height="600"
                />
            </div>
            <div class="order-1 lg:order-2">
                <h2 class="text-3xl font-bold text-white sm:text-4xl">Privacy that matches the promise.</h2>
                <p class="mt-6 text-lg text-slate-400">We collect only what we need to run payments, meet regulations, and protect you from fraud. Credentials for gateways are encrypted; sessions are secured; and you can read the full story anytime.</p>
                <ul class="mt-8 space-y-4 text-slate-300">
                    <li class="flex gap-3"><span class="text-emerald-400">✓</span> Clear privacy policy &amp; terms</li>
                    <li class="flex gap-3"><span class="text-emerald-400">✓</span> Contact us for real questions</li>
                    <li class="flex gap-3"><span class="text-emerald-400">✓</span> Wallet controls you can change anytime</li>
                </ul>
                <div class="mt-10 flex flex-wrap gap-4">
                    <a href="{{ route('privacy') }}" class="rounded-xl bg-white px-6 py-3 text-sm font-semibold text-slate-900 transition hover:bg-slate-200">Read privacy policy</a>
                    <a href="{{ route('contact') }}" class="rounded-xl border border-white/20 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10">Talk to us</a>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="px-4 py-24 sm:px-6">
        <div class="mx-auto max-w-4xl rounded-3xl border border-indigo-500/30 bg-gradient-to-br from-indigo-600/40 via-violet-600/30 to-slate-900 p-10 text-center shadow-2xl shadow-indigo-500/20 sm:p-14">
            <h2 class="text-3xl font-bold text-white sm:text-4xl">Ready to tidy up bill pay?</h2>
            <p class="mx-auto mt-4 max-w-xl text-lg text-indigo-100/90">Join {{ config('app.name') }} — built for families, students, renters, and anyone who wants fewer surprises at month-end.</p>
            <div class="mt-10 flex flex-wrap justify-center gap-4">
                @guest
                    <a href="{{ route('register') }}" class="inline-flex rounded-2xl bg-white px-8 py-3.5 text-base font-semibold text-indigo-900 shadow-lg transition hover:bg-indigo-50">Sign up free</a>
                @else
                    <a href="{{ auth()->user()->hasActiveKyc() ? route('dashboard') : route('kyc.index') }}" class="inline-flex rounded-2xl bg-white px-8 py-3.5 text-base font-semibold text-indigo-900 shadow-lg transition hover:bg-indigo-50">Go to app</a>
                @endguest
                <a href="{{ route('terms') }}" class="inline-flex rounded-2xl border border-white/30 px-8 py-3.5 text-base font-semibold text-white transition hover:bg-white/10">Review terms</a>
            </div>
        </div>
    </section>
@endsection
