@extends('layouts.guest')

@section('title', 'Register — '.config('app.name'))

@section('guest_hero')
    <p class="text-xs font-semibold uppercase tracking-wider text-indigo-400">Get started</p>
    <h1 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">Create your account in minutes</h1>
    <p class="max-w-md text-sm leading-relaxed text-slate-400">Pay tuition, utilities, rent, and more — with clear records and secure card flows.</p>
@endsection

@section('content')
    <h2 class="text-center text-xl font-semibold tracking-tight text-white">Register</h2>
    <p class="mt-1.5 text-center text-sm text-slate-400">We’ll guide you through KYC after signup</p>

    <form method="POST" action="{{ route('register') }}" class="mt-8 space-y-4">
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
            <label for="phone" class="auth-label">Phone</label>
            <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" required maxlength="10" inputmode="numeric" autocomplete="tel" placeholder="10-digit mobile"
                class="auth-input @error('phone') auth-input-error @enderror">
            @error('phone')
                <p class="auth-error-text">{{ $message }}</p>
            @enderror
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

        <button type="submit" class="auth-btn-primary mt-2">
            Create account
        </button>
    </form>

    <p class="mt-8 text-center text-sm text-slate-400">
        Already registered?
        <a href="{{ route('login') }}" class="auth-link">Log in</a>
    </p>
@endsection
