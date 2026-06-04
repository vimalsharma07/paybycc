@extends('layouts.app')

@section('title', $seller->name.' — '.config('app.name'))

@section('content')
    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-lg ring-1 ring-slate-900/5">
        <div class="bg-gradient-to-r from-indigo-600 to-violet-600 px-6 py-8 text-white sm:px-10">
            <a href="{{ route('marketplace.index') }}" class="text-sm font-medium text-white/80 hover:text-white">← All freelancers</a>
            <h1 class="mt-4 text-2xl font-bold sm:text-3xl">{{ $seller->name }}</h1>
            @if ($seller->company_name)
                <p class="mt-1 text-white/90">{{ $seller->company_name }}</p>
            @endif
            <p class="mt-3 font-mono text-sm text-white/70">{{ $seller->user_code }}</p>
            <div class="mt-4 flex flex-wrap gap-2">
                <span class="rounded-full bg-white/15 px-3 py-1 text-xs font-bold">{{ $seller->hasActiveKyc() ? 'KYC verified' : 'KYC not complete' }}</span>
                @if ($seller->city)
                    <span class="rounded-full bg-white/15 px-3 py-1 text-xs font-bold">{{ $seller->city }}@if ($seller->state), {{ $seller->state }}@endif</span>
                @endif
            </div>
        </div>
        <div class="p-6 sm:p-10">
            @php
                $offerings = $seller->sellerSubservices->loadMissing('subservice.service');
            @endphp
            @if ($offerings->isNotEmpty())
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-500">Services</h2>
                <ul class="mt-3 space-y-2">
                    @foreach ($offerings as $fs)
                        @if ($fs->subservice)
                            <li class="rounded-xl border border-slate-100 bg-slate-50 px-4 py-3 text-sm">
                                <span class="font-semibold text-slate-900">{{ $fs->subservice->name }}</span>
                                @if ($fs->subservice->service)
                                    <span class="text-slate-500"> · {{ $fs->subservice->service->name }}</span>
                                @endif
                            </li>
                        @endif
                    @endforeach
                </ul>
            @endif

            @unless ($seller->hasActiveKyc())
                <p class="mt-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-950">
                    This seller has not finished KYC. You can still pay; settlement to their wallet is held until they verify.
                </p>
            @endunless

            <a href="{{ route('payments.create', ['freelancer' => $seller->id]) }}"
                class="mt-8 inline-flex w-full items-center justify-center rounded-2xl bg-gradient-to-r from-indigo-600 to-violet-600 px-6 py-4 text-base font-bold text-white shadow-lg transition hover:brightness-110 sm:w-auto">
                Pay {{ $seller->name }}
            </a>
        </div>
    </div>
@endsection
