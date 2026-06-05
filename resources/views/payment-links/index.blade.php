@extends('layouts.app')

@section('title', 'Payment links — '.config('app.name'))
@section('page_heading', 'Payment links')
@section('page_subheading', 'Create shareable links — clients pay via UPI, card, or net banking')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-slate-600">Send a fixed-amount link to any client. They pay at secure checkout; you receive to your bank.</p>
        <a href="{{ route('payment-links.create') }}" class="inline-flex items-center justify-center rounded-2xl bg-gradient-to-r from-indigo-600 to-violet-600 px-5 py-2.5 text-sm font-bold text-white shadow-md hover:brightness-110">
            + New payment link
        </a>
    </div>

    @if ($paymentLinks->isEmpty())
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center">
            <p class="text-lg font-bold text-slate-900">No payment links yet</p>
            <p class="mt-2 text-sm text-slate-600">Create a link with an amount and share the URL on WhatsApp, email, or invoice.</p>
            <a href="{{ route('payment-links.create') }}" class="mt-6 inline-flex rounded-2xl bg-indigo-600 px-6 py-3 text-sm font-bold text-white shadow-md hover:bg-indigo-500">Create your first link</a>
        </div>
    @else
        <div class="overflow-hidden rounded-2xl border border-slate-200/90 bg-white shadow-lg ring-1 ring-slate-900/5">
            <ul class="divide-y divide-slate-100">
                @foreach ($paymentLinks as $link)
                    @php
                        $statusTone = match ($link->status) {
                            \App\Models\PaymentLink::STATUS_OPEN => 'bg-emerald-100 text-emerald-800',
                            \App\Models\PaymentLink::STATUS_PAID => 'bg-indigo-100 text-indigo-800',
                            \App\Models\PaymentLink::STATUS_CANCELLED => 'bg-slate-100 text-slate-600',
                            default => 'bg-amber-100 text-amber-900',
                        };
                    @endphp
                    <li class="flex flex-col gap-4 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <p class="font-mono text-xl font-bold text-slate-900">₹{{ number_format((float) $link->amount, 2) }}</p>
                                <span class="rounded-full px-2.5 py-0.5 text-[10px] font-bold uppercase {{ $statusTone }}">{{ $link->statusLabel() }}</span>
                            </div>
                            @if ($link->description)
                                <p class="mt-1 text-sm text-slate-700">{{ $link->description }}</p>
                            @endif
                            <p class="mt-1 text-xs text-slate-500">
                                Created {{ $link->created_at->diffForHumans() }}
                                @if ($link->expires_at)
                                    · Expires {{ $link->expires_at->format('M j, Y') }}
                                @endif
                            </p>
                        </div>
                        <div class="flex shrink-0 flex-wrap gap-2">
                            <a href="{{ route('payment-links.show', $link) }}" class="rounded-xl border border-slate-200 px-4 py-2 text-xs font-bold text-slate-800 hover:bg-slate-50">View &amp; copy</a>
                            @if ($link->status === \App\Models\PaymentLink::STATUS_OPEN)
                                <a href="{{ $link->publicUrl() }}" target="_blank" rel="noopener" class="rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-2 text-xs font-bold text-indigo-700 hover:bg-indigo-100">Open link</a>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="mt-6">{{ $paymentLinks->links() }}</div>
    @endif
@endsection
