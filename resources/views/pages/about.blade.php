@extends('layouts.marketing')

@section('title', 'About us — '.$siteSettings->displayName())
@section('meta_description', 'About '.$siteSettings->displayName().' — a freelancer & service marketplace with secure card payments and compliant settlements.')

@section('content')
    <article class="mx-auto max-w-3xl px-4 py-16 sm:px-6 lg:py-24">
        <p class="text-sm font-semibold uppercase tracking-wider text-indigo-400">About us</p>
        <h1 class="mt-3 text-4xl font-bold tracking-tight text-white">Where customers and freelancers meet — and get paid right.</h1>
        <p class="mt-6 text-lg leading-relaxed text-slate-400">{{ $siteSettings->displayName() }} is a marketplace for discovering freelancers and service providers, placing orders, and paying securely — with settlements released only after verification and compliance checks.</p>

        <div class="relative mt-12 overflow-hidden rounded-3xl border border-white/10">
            <img src="https://images.unsplash.com/photo-1600880292203-757bb62b4baf?auto=format&amp;fit=crop&amp;w=1200&amp;q=80" alt="Professionals collaborating" class="h-56 w-full object-cover sm:h-72" loading="lazy" />
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent"></div>
            <p class="absolute bottom-6 left-6 right-6 text-sm text-slate-200">Card-first checkout today · More payment methods on the roadmap · Built for India</p>
        </div>

        <div class="mt-14 space-y-10 text-slate-400 leading-relaxed">
            <div>
                <h2 class="text-xl font-semibold text-white">Our mission</h2>
                <p class="mt-4">Connect customers who need trusted work with freelancers who deliver it — on a platform that handles payments, KYC, risk review, and settlements transparently.</p>
            </div>

            <div>
                <h2 class="text-xl font-semibold text-white">For customers</h2>
                <ul class="mt-4 list-inside list-disc space-y-3">
                    <li>Search providers by service, name, mobile, or PAN (as features roll out).</li>
                    <li>Place orders and pay on the platform — <strong class="text-slate-300">credit &amp; debit cards</strong> are our focus today.</li>
                    <li>Clear order and payment status from checkout to completion.</li>
                </ul>
            </div>

            <div>
                <h2 class="text-xl font-semibold text-white">For freelancers &amp; sellers</h2>
                <ul class="mt-4 list-inside list-disc space-y-3">
                    <li>Onboard with identity, bank details, and the services you offer.</li>
                    <li>Receive settlements after KYC, risk (Safe Status), and compliance rules are satisfied.</li>
                    <li>Choose whether you only accept orders from KYC-verified customers.</li>
                </ul>
            </div>

            <div>
                <h2 class="text-xl font-semibold text-white">Payments</h2>
                <p class="mt-4">We support multiple payment methods over time; <strong class="text-slate-300">card payments (CC/DC)</strong> are the primary experience now — fast checkout through licensed payment partners, with records for reconciliation and tax where applicable (GST, TDS, TCS).</p>
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
