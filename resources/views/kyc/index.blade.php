@extends('layouts.guest')

@section('title', 'Complete KYC — '.config('app.name'))

@section('guest_hero')
    <p class="text-xs font-semibold uppercase tracking-wider text-indigo-400">Verification</p>
    <h1 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">Complete your KYC</h1>
    <p class="max-w-md text-sm leading-relaxed text-slate-400">Verify with PAN to receive payouts. You can skip for now to explore and pay via UPI, card, or net banking.</p>
@endsection

@section('content')
    <h2 class="text-center text-xl font-semibold tracking-tight text-white">Identity check</h2>
    <p class="mt-1.5 text-center text-sm text-slate-400">Verify using PAN details (India)</p>

    @if ($user->hasSkippedKyc())
        <div class="mt-6 rounded-xl border border-amber-400/30 bg-amber-500/10 px-4 py-3 text-sm text-amber-100">
            <p class="font-medium text-white">You skipped KYC earlier</p>
            <p class="mt-1 text-amber-200/90">Submit PAN below to receive payments and add bank accounts.</p>
        </div>
    @else
        <div class="mt-6 rounded-xl border border-cyan-400/25 bg-cyan-500/10 px-4 py-3 text-sm text-cyan-100">
            <p class="font-medium text-white">Optional for now</p>
            <p class="mt-1 text-cyan-200/90">Skip to explore the platform and pay. Receiving money requires KYC.</p>
        </div>
    @endif

    <div class="mt-4 rounded-xl border border-indigo-400/25 bg-indigo-500/10 px-4 py-3 text-sm text-indigo-100">
        <p class="font-medium text-white">Document type</p>
        <p class="mt-1 text-indigo-200/90">PAN card — Permanent Account Number</p>
    </div>

    <form method="POST" action="{{ route('kyc.pan') }}" class="mt-8 space-y-5">
        @csrf

        <div>
            <label for="pan" class="auth-label">PAN number</label>
            <input id="pan" type="text" name="pan" value="{{ old('pan') }}" required maxlength="10" autocomplete="off" placeholder="e.g. ABCDE1234F"
                class="auth-input font-mono uppercase placeholder:normal-case @error('pan') auth-input-error @enderror">
            @error('pan')
                <p class="auth-error-text">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="pan_name" class="auth-label">Name as per PAN</label>
            <input id="pan_name" type="text" name="pan_name" value="{{ old('pan_name') }}" required autocomplete="name"
                class="auth-input @error('pan_name') auth-input-error @enderror">
            @error('pan_name')
                <p class="auth-error-text">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="aadhar" class="auth-label">Aadhaar <span class="font-normal text-slate-500">(optional)</span></label>
            <input id="aadhar" type="text" name="aadhar" value="{{ old('aadhar') }}" maxlength="12" inputmode="numeric" placeholder="12 digits"
                class="auth-input @error('aadhar') auth-input-error @enderror">
            @error('aadhar')
                <p class="auth-error-text">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="auth-btn-primary">
            Submit &amp; finish KYC
        </button>
    </form>

    @unless ($user->hasSkippedKyc())
        <form method="POST" action="{{ route('kyc.skip') }}" class="mt-4">
            @csrf
            <button type="submit" class="w-full rounded-xl border border-white/15 bg-white/5 px-4 py-3 text-sm font-semibold text-slate-200 transition hover:bg-white/10 hover:text-white">
                Skip for now — explore &amp; pay
            </button>
        </form>
        <p class="mt-3 text-center text-xs text-slate-500">You can complete KYC later from your profile.</p>
    @endunless
@endsection

@section('guest-footer')
    @if ($user->canUsePlatform())
        <p class="mt-6 text-center">
            <a href="{{ route('dashboard') }}" class="text-sm font-medium text-indigo-400 underline decoration-indigo-400/30 underline-offset-4 hover:text-indigo-300">Back to dashboard</a>
        </p>
    @endif
    <p class="mt-4 text-center text-sm text-slate-500">
        Signed in as <span class="text-slate-300">{{ auth()->user()->email }}</span>
    </p>
    <form method="POST" action="{{ route('logout') }}" class="mt-3 text-center">
        @csrf
        <button type="submit" class="text-sm font-medium text-slate-400 underline decoration-white/20 underline-offset-4 transition hover:text-indigo-400">Log out</button>
    </form>
@endsection
