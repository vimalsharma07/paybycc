@extends('layouts.app')

@section('title', 'Payment settings — '.config('app.name'))
@section('page_heading', 'Payment settings')
@section('page_subheading', 'Who can pay you & your receive limits')

@section('content')
    <div class="mx-auto max-w-2xl space-y-6">
        <form method="POST" action="{{ route('settings.payment.update') }}" class="overflow-hidden rounded-2xl border border-slate-200/90 bg-white shadow-lg ring-1 ring-slate-900/5">
            @csrf
            @method('PATCH')

            <div class="border-b border-indigo-100 bg-gradient-to-r from-indigo-600 to-violet-600 px-6 py-6 text-white">
                <h2 class="text-lg font-bold">Who can pay via your payment links?</h2>
                <p class="mt-1 text-sm text-white/85">Applies to all your payment links, including your default link.</p>
            </div>

            <div class="space-y-3 p-6">
                @php
                    $modes = [
                        'login' => ['title' => 'Login required', 'desc' => 'Payer must log in or register. KYC not required.'],
                        'kyc' => ['title' => 'KYC required', 'desc' => 'Payer must be logged in and have completed KYC.'],
                        'guest' => ['title' => 'No login required', 'desc' => 'Anyone can pay with name, email & mobile — no account needed.'],
                    ];
                @endphp

                @foreach ($modes as $value => $meta)
                    <label class="flex cursor-pointer gap-3 rounded-xl border px-4 py-4 transition {{ old('payer_mode', $payerMode) === $value ? 'border-indigo-500 bg-indigo-50' : 'border-slate-200 hover:bg-slate-50' }}">
                        <input type="radio" name="payer_mode" value="{{ $value }}" class="mt-1" @checked(old('payer_mode', $payerMode) === $value)>
                        <span>
                            <span class="block text-sm font-bold text-slate-900">{{ $meta['title'] }}</span>
                            <span class="mt-0.5 block text-xs text-slate-600">{{ $meta['desc'] }}</span>
                        </span>
                    </label>
                @endforeach

                @error('payer_mode')
                    <p class="text-sm text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="border-t border-slate-100 px-6 py-4">
                <button type="submit" class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-bold text-white shadow hover:bg-indigo-500">
                    Save settings
                </button>
            </div>
        </form>

        <section class="overflow-hidden rounded-2xl border border-slate-200/90 bg-white shadow-lg ring-1 ring-slate-900/5">
            <div class="border-b border-slate-100 px-6 py-5">
                <h2 class="text-lg font-bold text-slate-900">Receive limits</h2>
                <p class="mt-1 text-sm text-slate-600">Maximum amount you can receive via payment links. Set by platform — contact support to change.</p>
            </div>

            <dl class="grid gap-px bg-slate-100 sm:grid-cols-3">
                @foreach (['daily' => 'Daily', 'monthly' => 'Monthly', 'yearly' => 'Yearly (FY)'] as $key => $label)
                    @php $row = $limits[$key]; @endphp
                    <div class="bg-white p-5">
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ $label }}</dt>
                        <dd class="mt-2 font-mono text-lg font-bold text-slate-900">
                            @if ($row['limit'] <= 0)
                                Unlimited
                            @else
                                ₹{{ number_format($row['limit'], 0) }}
                            @endif
                        </dd>
                        @if ($row['limit'] > 0)
                            <dd class="mt-1 text-xs text-slate-600">Used ₹{{ number_format($row['used'], 0) }} · Left ₹{{ number_format($row['remaining'] ?? 0, 0) }}</dd>
                        @endif
                    </div>
                @endforeach
            </dl>
        </section>
    </div>
@endsection
