@extends('layouts.marketing')

@section('title', 'About us — '.$siteSettings->displayName())
@section('meta_description', 'About '.$siteSettings->displayName().' — accept business payments online for freelancers, service providers, startups & small businesses via licensed gateways.')

@section('content')
    <article class="mx-auto max-w-3xl px-4 py-16 sm:px-6 lg:py-24">
        <p class="text-sm font-semibold uppercase tracking-wider text-indigo-400">About us</p>
        <h1 class="mt-3 text-4xl font-bold tracking-tight text-white">Online business payments — quick, attractive &amp; compliant.</h1>
        <p class="mt-6 text-lg leading-relaxed text-slate-400">{{ $siteSettings->displayName() }} helps <strong class="text-slate-300">business owners, freelancers, service providers, and startups</strong> accept payments online through licensed payment gateways — with identity verification, tax-ready records, and settlement to your bank account.</p>

        <div class="mt-8">
            <x-payment-methods variant="dark" size="sm" />
        </div>

        <div class="relative mt-12 overflow-hidden rounded-3xl border border-white/10">
            <img src="{{ asset('images/about-banner.png') }}" alt="Indian business team accepting online payments" class="h-56 w-full object-cover sm:h-72" loading="lazy" />
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent"></div>
            <p class="absolute bottom-6 left-6 right-6 text-sm text-slate-200">Licensed checkout · KYC &amp; PAN · GST-ready · Built for India</p>
        </div>

        <div class="mt-14 space-y-10 text-slate-400 leading-relaxed">
            <div>
                <h2 class="text-xl font-semibold text-white">Who we serve</h2>
                <ul class="mt-4 list-inside list-disc space-y-3">
                    <li><strong class="text-slate-300">Freelancers &amp; consultants</strong> — invoices, milestones, retainers.</li>
                    <li><strong class="text-slate-300">Service providers &amp; agencies</strong> — client projects with proper payment records.</li>
                    <li><strong class="text-slate-300">Startups &amp; small businesses</strong> — collect revenue without building payment infrastructure.</li>
                    <li><strong class="text-slate-300">Customers &amp; clients</strong> — pay via UPI, cards, net banking, or wallets at secure checkout.</li>
                </ul>
            </div>

            <div>
                <h2 class="text-xl font-semibold text-white">Approved way to accept payments</h2>
                <p class="mt-4">Collections run through <strong class="text-slate-300">RBI-authorised payment gateway partners</strong> — not personal UPI handles or informal bank transfers for platform orders. Sellers complete <strong class="text-slate-300">PAN/KYC verification</strong> before bank settlements. We maintain order, payment, and settlement status for transparency.</p>
            </div>

            <div>
                <h2 class="text-xl font-semibold text-white">Compliance &amp; records</h2>
                <p class="mt-4">Where applicable, the platform supports <strong class="text-slate-300">GST, TDS, and TCS</strong> fields on orders so you can reconcile with your accountant. Our <a href="{{ route('terms') }}" class="font-medium text-indigo-400 underline-offset-2 hover:underline">Terms &amp; Conditions</a> and <a href="{{ route('privacy') }}" class="font-medium text-indigo-400 underline-offset-2 hover:underline">Privacy Policy</a> explain roles, data use, and your rights in plain language.</p>
            </div>

            <div>
                <h2 class="text-xl font-semibold text-white">What we believe</h2>
                <ul class="mt-4 list-inside list-disc space-y-3">
                    <li><strong class="text-slate-300">Trust is earned.</strong> Verification, logging, and clear policies — not fine print surprises.</li>
                    <li><strong class="text-slate-300">Compliance matters.</strong> Settlements follow KYC and risk review, not shortcuts.</li>
                    <li><strong class="text-slate-300">Support should be human.</strong> <a href="{{ route('contact') }}" class="font-medium text-indigo-400 underline-offset-2 hover:underline">Contact us</a> when something does not look right.</li>
                </ul>
            </div>

            <div class="rounded-2xl border border-white/10 bg-white/5 p-8">
                <p class="text-sm font-medium text-white">Questions about onboarding or compliance?</p>
                <p class="mt-2 text-slate-400">Partnerships, seller verification, or policy — we read every message.</p>
                <a href="{{ route('contact') }}" class="mt-6 inline-flex rounded-xl bg-gradient-to-r from-indigo-500 to-violet-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-500/25">Contact us</a>
            </div>
        </div>
    </article>
@endsection
