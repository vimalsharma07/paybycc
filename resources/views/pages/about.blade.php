@extends('layouts.marketing')

@section('title', 'About us — '.$siteSettings->displayName())
@section('meta_description', 'About '.$siteSettings->displayName().' — a freelancer marketplace where clients pay via UPI, cards, net banking & wallets, with compliant bank settlements.')

@section('content')
    <article class="mx-auto max-w-3xl px-4 py-16 sm:px-6 lg:py-24">
        <p class="text-sm font-semibold uppercase tracking-wider text-indigo-400">About us</p>
        <h1 class="mt-3 text-4xl font-bold tracking-tight text-white">Where freelancers get paid — however clients prefer to pay.</h1>
        <p class="mt-6 text-lg leading-relaxed text-slate-400">{{ $siteSettings->displayName() }} is a marketplace for discovering freelancers and service providers, placing orders, and paying securely — with settlements released only after verification and compliance checks.</p>

        <div class="mt-8">
            <x-payment-methods variant="dark" size="sm" />
        </div>

        <div class="relative mt-12 overflow-hidden rounded-3xl border border-white/10">
            <img src="https://images.unsplash.com/photo-1600880292203-757bb62b4baf?auto=format&amp;fit=crop&amp;w=1200&amp;q=80" alt="Professionals collaborating" class="h-56 w-full object-cover sm:h-72" loading="lazy" />
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent"></div>
            <p class="absolute bottom-6 left-6 right-6 text-sm text-slate-200">UPI · Cards · Net banking · Wallets · Built for India</p>
        </div>

        <div class="mt-14 space-y-10 text-slate-400 leading-relaxed">
            <div>
                <h2 class="text-xl font-semibold text-white">Our mission</h2>
                <p class="mt-4">Connect customers who need trusted work with freelancers who deliver it — on a platform that handles multi-method checkout, KYC, risk review, and bank settlements transparently.</p>
            </div>

            <div>
                <h2 class="text-xl font-semibold text-white">For customers</h2>
                <ul class="mt-4 list-inside list-disc space-y-3">
                    <li>Search providers by service, name, mobile, or PAN (as features roll out).</li>
                    <li>Pay on the platform with <strong class="text-slate-300">UPI, credit card, debit card, net banking, or wallets</strong> — whatever you use day to day.</li>
                    <li>Clear order and payment status from checkout to completion.</li>
                </ul>
            </div>

            <div>
                <h2 class="text-xl font-semibold text-white">For freelancers &amp; sellers</h2>
                <ul class="mt-4 list-inside list-disc space-y-3">
                    <li>Onboard with identity, bank details, and the services you offer.</li>
                    <li>Accept payments across methods without setting up separate UPI QR and card links.</li>
                    <li>Receive automatic bank settlements after KYC, risk (Safe Status), and compliance rules are satisfied.</li>
                    <li>Choose whether you only accept orders from KYC-verified customers.</li>
                </ul>
            </div>

            <div>
                <h2 class="text-xl font-semibold text-white">Payments</h2>
                <p class="mt-4">Checkout runs through licensed payment partners (Cashfree). Clients can pay via <strong class="text-slate-300">UPI, credit &amp; debit cards, net banking, and wallets</strong> where enabled — with records for reconciliation and tax where applicable (GST, TDS, TCS).</p>
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
                <p class="text-sm font-medium text-white">Questions?</p>
                <p class="mt-2 text-slate-400">Partnerships, onboarding, or policy — we read every message.</p>
                <a href="{{ route('contact') }}" class="mt-6 inline-flex rounded-xl bg-gradient-to-r from-indigo-500 to-violet-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-500/25">Contact us</a>
            </div>
        </div>
    </article>
@endsection
