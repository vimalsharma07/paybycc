@extends('layouts.app')

@section('title', 'Payment settings — '.config('app.name'))
@section('page_heading', 'Payment settings')
@section('page_subheading', 'Who can pay you & your receive limits')

@section('content')
    <div class="mx-auto max-w-2xl">
        <form method="POST" action="{{ route('settings.payment.update') }}" class="space-y-6">
            @csrf
            @method('PATCH')

            <section class="overflow-hidden rounded-2xl border border-slate-200/90 bg-white shadow-lg ring-1 ring-slate-900/5">
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
            </section>

            <section class="overflow-hidden rounded-2xl border border-slate-200/90 bg-white shadow-lg ring-1 ring-slate-900/5">
                <div class="border-b border-slate-100 px-6 py-5">
                    <h2 class="text-lg font-bold text-slate-900">Receive limits</h2>
                    <p class="mt-1 text-sm text-slate-600">Maximum you can receive via payment links. Enter <span class="font-mono font-semibold">0</span> for unlimited — no check at payment time.</p>
                </div>

                <div class="grid gap-5 p-6 sm:grid-cols-3">
                    @foreach ([
                        'daily_limit' => ['label' => 'Daily', 'key' => 'daily'],
                        'monthly_limit' => ['label' => 'Monthly', 'key' => 'monthly'],
                        'yearly_limit' => ['label' => 'Yearly (FY)', 'key' => 'yearly'],
                    ] as $field => $meta)
                        @php $row = $limits[$meta['key']]; @endphp
                        <div>
                            <label for="{{ $field }}" class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">{{ $meta['label'] }}</label>
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-sm text-slate-400">₹</span>
                                <input
                                    id="{{ $field }}"
                                    name="{{ $field }}"
                                    type="text"
                                    inputmode="decimal"
                                    value="{{ old($field, (float) $user->{$field} == 0 ? '0' : $user->{$field}) }}"
                                    required
                                    class="block w-full rounded-lg border border-slate-300 py-2 pl-7 pr-3 font-mono text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error($field) border-rose-500 @enderror"
                                >
                            </div>
                            @error($field)
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                            @if ($row['limit'] > 0)
                                <p class="mt-2 text-xs text-slate-600">Used ₹{{ number_format($row['used'], 0) }} · Left ₹{{ number_format($row['remaining'] ?? 0, 0) }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>

            <div>
                <button type="submit" class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-bold text-white shadow hover:bg-indigo-500">
                    Save settings
                </button>
            </div>
        </form>
    </div>
@endsection
