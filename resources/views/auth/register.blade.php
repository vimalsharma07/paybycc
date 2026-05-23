@extends('layouts.guest')

@section('title', 'Register — '.config('app.name'))

@section('guest_hero')
    <p class="text-xs font-semibold uppercase tracking-wider text-indigo-400">Get started</p>
    <h1 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">Create your account in minutes</h1>
    <p class="max-w-md text-sm leading-relaxed text-slate-400">Pay tuition, utilities, rent, and more — with clear records and secure card flows.</p>
@endsection

@section('content')
    <h2 class="text-center text-xl font-semibold tracking-tight text-white">Register</h2>
    <p class="mt-1.5 text-center text-sm text-slate-400">Verify your mobile, then complete signup</p>

    <form
        id="register-form"
        method="POST"
        action="{{ route('register') }}"
        class="mt-8 space-y-4"
        data-send-otp-url="{{ route('register.otp.send') }}"
        data-verify-otp-url="{{ route('register.otp.verify') }}"
        data-otp-length="{{ (int) config('otp.length', 6) }}"
        data-resend-seconds="{{ (int) config('otp.resend_seconds', 60) }}"
    >
        @csrf

        <div>
            <label for="name" class="auth-label">Full name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                class="auth-input @error('name') auth-input-error @enderror">
            @error('name')
                <p class="auth-error-text">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="auth-label">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                class="auth-input @error('email') auth-input-error @enderror">
            @error('email')
                <p class="auth-error-text">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="phone" class="auth-label">Mobile number</label>
            <div class="flex gap-2">
                <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" required maxlength="10" inputmode="numeric" autocomplete="tel" placeholder="10-digit mobile"
                    class="auth-input min-w-0 flex-1 @error('phone') auth-input-error @enderror">
                <button type="button" id="send-otp-btn" class="auth-btn-secondary shrink-0 px-4" disabled>
                    Send OTP
                </button>
            </div>
            @error('phone')
                <p class="auth-error-text">{{ $message }}</p>
            @enderror
            <p id="phone-hint" class="mt-1.5 text-xs text-slate-500">We’ll send a one-time code to verify this number.</p>
        </div>

        <div id="otp-panel" class="hidden space-y-3 rounded-xl border border-white/10 bg-white/[0.03] p-4">
            <div>
                <label for="otp" class="auth-label">Verification code</label>
                <input id="otp" type="text" inputmode="numeric" autocomplete="one-time-code" maxlength="{{ (int) config('otp.length', 6) }}"
                    placeholder="{{ str_repeat('·', (int) config('otp.length', 6)) }}"
                    class="auth-input text-center font-mono text-lg tracking-[0.35em]">
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <button type="button" id="verify-otp-btn" class="auth-btn-secondary px-4" disabled>
                    Verify
                </button>
                <button type="button" id="resend-otp-btn" class="text-sm font-medium text-indigo-400 transition hover:text-indigo-300 disabled:cursor-not-allowed disabled:opacity-40" disabled>
                    Resend code
                </button>
            </div>
            <p id="otp-status" class="text-sm" role="status" aria-live="polite"></p>
        </div>

        <div id="phone-verified-badge" class="hidden flex items-center gap-2 rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
            <svg class="h-5 w-5 shrink-0 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>Mobile number verified</span>
        </div>

        <div>
            <label for="password" class="auth-label">Password</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                class="auth-input @error('password') auth-input-error @enderror">
            @error('password')
                <p class="auth-error-text">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="auth-label">Confirm password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                class="auth-input">
        </div>

        <button type="submit" id="register-submit" class="auth-btn-primary mt-2" disabled>
            Create account
        </button>
        <p class="text-center text-xs text-slate-500">Verify your mobile to enable account creation.</p>
    </form>

    <p class="mt-8 text-center text-sm text-slate-400">
        Already registered?
        <a href="{{ route('login') }}" class="auth-link">Log in</a>
    </p>
@endsection

@push('scripts')
    <script src="{{ asset('js/register-otp.js') }}" defer></script>
@endpush
