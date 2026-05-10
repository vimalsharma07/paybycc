@extends('layouts.marketing')

@section('title', 'About us — '.config('app.name'))
@section('meta_description', 'Learn about '.config('app.name').' — our mission to simplify bill payments, tuition, and settlements for Indian households.')

@section('content')
    <article class="mx-auto max-w-3xl px-4 py-16 sm:px-6 lg:py-24">
        <p class="text-sm font-semibold uppercase tracking-wider text-indigo-400">About us</p>
        <h1 class="mt-3 text-4xl font-bold tracking-tight text-white">Making bill pay feel human again.</h1>
        <p class="mt-6 text-lg leading-relaxed text-slate-400">{{ config('app.name') }} exists because paying tuition, utilities, rent, and dozens of small recurring bills shouldn’t feel scattered or stressful.</p>

        <div class="relative mt-12 overflow-hidden rounded-3xl border border-white/10">
            <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&amp;fit=crop&amp;w=1200&amp;q=80" alt="Team collaboration" class="h-56 w-full object-cover sm:h-72" loading="lazy" />
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent"></div>
            <p class="absolute bottom-6 left-6 right-6 text-sm text-slate-200">We combine careful engineering with plain-language policies — so you always know what happens to your money.</p>
        </div>

        <div class="mt-14 space-y-10 text-slate-400 leading-relaxed">
            <div>
            <h2 class="text-xl font-semibold text-white">Our mission</h2>
            <p class="mt-4">Give individuals and families one dependable place to pay education fees, household bills, insurance, subscriptions, and other everyday expenses — with transparent settlement timing and wallet controls.</p>
            </div>

            <div>
            <h2 class="text-xl font-semibold text-white">What we believe</h2>
            <ul class="mt-4 list-inside list-disc space-y-3">
                <li><strong class="text-slate-300">Clarity beats complexity.</strong> You should see limits, timing, and history without digging.</li>
                <li><strong class="text-slate-300">Privacy is respect.</strong> We only ask for data we truly need — detailed in our privacy policy.</li>
                <li><strong class="text-slate-300">Support should be reachable.</strong> Use our contact page for real questions.</li>
            </ul>
            </div>

            <div>
            <h2 class="text-xl font-semibold text-white">Roadmap (directionally)</h2>
            <p class="mt-4">We continue to refine gateways, settlement tracking, and payout preferences — always aiming for fewer taps and clearer receipts.</p>
            </div>

            <div class="rounded-2xl border border-white/10 bg-white/5 p-8">
                <p class="text-sm font-medium text-white">Questions?</p>
                <p class="mt-2 text-slate-400">We’d love to hear from you.</p>
                <a href="{{ route('contact') }}" class="mt-6 inline-flex rounded-xl bg-gradient-to-r from-indigo-500 to-violet-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-500/25">Contact us</a>
            </div>
        </div>
    </article>
@endsection
