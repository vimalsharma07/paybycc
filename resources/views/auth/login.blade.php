@extends('layouts.guest')

@section('title', 'Log in — '.config('app.name'))

@section('guest_hero')
    <p class="text-xs font-semibold uppercase tracking-wider text-indigo-400">Welcome back</p>
    <h1 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">Sign in and stay on top of bills</h1>
    <p class="max-w-md text-sm leading-relaxed text-slate-400">Access your wallet, payments, and settlement tools in one place.</p>
@endsection

@section('content')
    <h2 class="text-center text-xl font-semibold tracking-tight text-white">Log in</h2>
    <p class="mt-1.5 text-center text-sm text-slate-400">Use your email and password</p>

    <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
        @csrf

        <div>
            <label for="email" class="auth-label">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                class="auth-input @error('email') auth-input-error @enderror">
            @error('email')
                <p class="auth-error-text">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="auth-label">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                class="auth-input @error('password') auth-input-error @enderror">
            @error('password')
                <p class="auth-error-text">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3">
            <label class="flex cursor-pointer items-center gap-2 text-sm text-slate-400">
                <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }} class="rounded border-white/20 bg-white/5 text-indigo-500 focus:ring-2 focus:ring-indigo-500/40 focus:ring-offset-0">
                Remember me
            </label>
            <a href="{{ route('password.request') }}" class="text-sm font-medium text-indigo-400 transition hover:text-indigo-300">Forgot password?</a>
        </div>

        <button type="submit" class="auth-btn-primary">
            Log in
        </button>
    </form>

    <p class="mt-8 text-center text-sm text-slate-400">
        No account?
        <a href="{{ route('register') }}" class="auth-link">Create one</a>
    </p>
@endsection
