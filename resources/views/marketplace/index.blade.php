@extends('layouts.app')

@section('title', 'Find freelancers — '.config('app.name'))

@section('content')
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900 sm:text-3xl">Find freelancers &amp; sellers</h1>
        <p class="mt-2 text-sm text-slate-600">Search by name, user code, company, city, or skill. Pay with card from their profile or the Pay page.</p>
    </div>

    <form method="GET" action="{{ route('marketplace.index') }}" class="mb-8 flex flex-col gap-3 sm:flex-row">
        <input type="search" name="q" value="{{ $query ?? '' }}" placeholder="Search freelancers…"
            class="min-w-0 flex-1 rounded-xl border border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/25">
        <button type="submit" class="rounded-xl bg-indigo-600 px-6 py-3 text-sm font-bold text-white shadow hover:bg-indigo-500">Search</button>
        <a href="{{ route('payments.create') }}" class="inline-flex items-center justify-center rounded-xl border border-indigo-200 bg-indigo-50 px-6 py-3 text-sm font-bold text-indigo-700 hover:bg-indigo-100">Pay now</a>
    </form>

    @if ($freelancers->isEmpty())
        <div class="rounded-2xl border border-slate-200 bg-white px-6 py-12 text-center text-sm text-slate-600">
            @if ($query)
                No freelancers match “{{ $query }}”.
            @else
                No sellers listed yet. Run <span class="font-mono text-xs">php artisan db:seed --class=PlatformSeeder</span> for a demo freelancer.
            @endif
        </div>
    @else
        <div class="grid gap-4 sm:grid-cols-2">
            @foreach ($freelancers as $f)
                <a href="{{ route('marketplace.show', $f) }}" class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm ring-1 ring-slate-900/5 transition hover:border-indigo-300 hover:shadow-md">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-bold text-slate-900 group-hover:text-indigo-700">{{ $f->name }}</p>
                            @if ($f->company_name)
                                <p class="mt-0.5 text-sm text-slate-600">{{ $f->company_name }}</p>
                            @endif
                            <p class="mt-2 font-mono text-xs text-slate-500">{{ $f->user_code }}</p>
                        </div>
                        <span class="shrink-0 rounded-full px-2.5 py-1 text-[10px] font-bold uppercase {{ $f->hasActiveKyc() ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-900' }}">
                            {{ $f->hasActiveKyc() ? 'Verified' : 'KYC pending' }}
                        </span>
                    </div>
                    @php
                        $skills = $f->sellerSubservices
                            ->map(fn ($fs) => $fs->subservice?->name)
                            ->filter()
                            ->unique()
                            ->take(4);
                    @endphp
                    @if ($skills->isNotEmpty())
                        <p class="mt-3 text-xs text-slate-600">{{ $skills->implode(' · ') }}</p>
                    @endif
                    @if ($f->city)
                        <p class="mt-2 text-xs text-slate-500">{{ $f->city }}@if ($f->state), {{ $f->state }}@endif</p>
                    @endif
                    <span class="mt-4 inline-flex text-xs font-bold text-indigo-600 group-hover:underline">View &amp; pay →</span>
                </a>
            @endforeach
        </div>
        <div class="mt-8">{{ $freelancers->links() }}</div>
    @endif
@endsection
