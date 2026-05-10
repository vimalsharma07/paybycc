@extends('layouts.guest')

@section('title', 'Complete KYC — '.config('app.name'))

@section('content')
    <h1 class="text-center text-2xl font-semibold tracking-tight text-slate-900">Complete KYC</h1>
    <p class="mt-2 text-center text-sm text-slate-600">Verify your identity using PAN details.</p>

    <div class="mt-8 rounded-xl border border-indigo-100 bg-indigo-50 px-4 py-3 text-sm text-indigo-900">
        <p class="font-medium">Document type</p>
        <p class="mt-1 text-indigo-800">PAN card — Permanent Account Number (India)</p>
    </div>

    <form method="POST" action="{{ route('kyc.pan') }}" class="mt-8 space-y-5">
        @csrf

        <div>
            <label for="pan" class="mb-1 block text-sm font-medium text-slate-700">PAN number</label>
            <input id="pan" type="text" name="pan" value="{{ old('pan') }}" required maxlength="10" autocomplete="off" placeholder="e.g. ABCDE1234F"
                class="block w-full rounded-lg border border-slate-300 px-3 py-2 font-mono text-sm uppercase shadow-sm placeholder:normal-case placeholder:text-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('pan') border-red-500 @enderror">
            @error('pan')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="pan_name" class="mb-1 block text-sm font-medium text-slate-700">Name as per PAN</label>
            <input id="pan_name" type="text" name="pan_name" value="{{ old('pan_name') }}" required autocomplete="name"
                class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('pan_name') border-red-500 @enderror">
            @error('pan_name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="aadhar" class="mb-1 block text-sm font-medium text-slate-700">Aadhaar <span class="font-normal text-slate-500">(optional)</span></label>
            <input id="aadhar" type="text" name="aadhar" value="{{ old('aadhar') }}" maxlength="12" inputmode="numeric" placeholder="12 digits"
                class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('aadhar') border-red-500 @enderror">
            @error('aadhar')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="inline-flex w-full justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2">
            Submit &amp; finish KYC
        </button>
    </form>
@endsection

@section('guest-footer')
    <p class="mt-6 text-center text-sm text-slate-600">
        Signed in as {{ auth()->user()->email }}
    </p>
    <form method="POST" action="{{ route('logout') }}" class="mt-3 text-center">
        @csrf
        <button type="submit" class="text-sm font-medium text-slate-700 underline decoration-slate-300 underline-offset-4 hover:text-indigo-600">Log out</button>
    </form>
@endsection
