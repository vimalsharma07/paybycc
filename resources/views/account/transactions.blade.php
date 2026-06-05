@extends('layouts.app')

@section('title', 'Transaction history — '.config('app.name'))
@section('page_heading', 'Transaction history')
@section('page_subheading', 'Payments, settlement timing, and bank payouts')

@section('content')
    <div class="mb-6 flex flex-wrap gap-3">
        <a href="{{ route('banks.index') }}" class="inline-flex items-center rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-2 text-sm font-bold text-indigo-800 hover:bg-indigo-100">Bank accounts</a>
        <a href="{{ route('account.payments') }}" class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-bold text-slate-800 hover:bg-slate-50">Payments sent</a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200/90 bg-white shadow-lg ring-1 ring-slate-900/5">
        @include('partials.transaction-list', ['transactions' => $transactions])
    </div>
@endsection
