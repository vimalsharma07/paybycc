@extends('layouts.app')

@section('title', 'Payments sent — '.config('app.name'))
@section('page_heading', 'Payments sent')
@section('page_subheading', 'UPI, card & gateway payments you made to freelancers')

@section('content')
    <div class="overflow-hidden rounded-2xl border border-slate-200/90 bg-white shadow-lg ring-1 ring-slate-900/5">
        @if ($payments->isEmpty())
            <div class="px-6 py-16 text-center">
                <p class="text-lg font-bold text-slate-900">No payments yet</p>
                <p class="mt-2 text-sm text-slate-600">Find a freelancer and pay with UPI, card, or net banking.</p>
                <a href="{{ route('payments.create') }}" class="mt-6 inline-flex rounded-2xl bg-indigo-600 px-6 py-3 text-sm font-bold text-white shadow-md hover:bg-indigo-500">Pay now</a>
            </div>
        @else
            <div class="divide-y divide-slate-100">
                @foreach ($payments as $payment)
                    <article class="flex flex-col gap-3 px-5 py-4 transition hover:bg-slate-50/80 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <x-payment-status-pill :status="$payment->status" />
                                <span class="font-mono text-xs text-slate-500">#{{ $payment->id }}</span>
                            </div>
                            <p class="mt-2 font-mono text-lg font-bold tabular-nums text-slate-900">₹{{ number_format((float) $payment->amount, 2) }}</p>
                            @if ($payment->order?->freelancer)
                                <p class="mt-1 text-sm text-slate-700">To <span class="font-semibold">{{ $payment->order->freelancer->name }}</span></p>
                            @endif
                            @if ($payment->remark)
                                <p class="mt-1 truncate text-xs text-slate-500">{{ $payment->remark }}</p>
                            @endif
                            <p class="mt-1 text-xs text-slate-500">{{ $payment->created_at->format('l, M j, Y · H:i') }} · {{ $payment->gateway?->name ?? 'Gateway' }}</p>
                        </div>
                        <div class="flex shrink-0 flex-wrap gap-2">
                            @if ($payment->status === 'completed')
                                <a href="{{ route('payments.success', $payment) }}" class="rounded-xl border border-slate-200 px-4 py-2 text-xs font-bold text-slate-800 hover:bg-white">Receipt</a>
                            @elseif ($payment->status === 'failed')
                                <a href="{{ route('payments.failed', $payment) }}" class="rounded-xl border border-slate-200 px-4 py-2 text-xs font-bold text-slate-800 hover:bg-white">Details</a>
                            @elseif ($payment->status === 'pending')
                                <a href="{{ route('payments.pending', $payment) }}" class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-2 text-xs font-bold text-amber-900">Status</a>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
            @if ($payments->hasPages())
                <div class="border-t border-slate-100 px-4 py-4">{{ $payments->links() }}</div>
            @endif
        @endif
    </div>
@endsection
