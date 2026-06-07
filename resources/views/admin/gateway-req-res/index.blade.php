@extends('layouts.admin')

@section('title', 'Gateway Req/Res — '.config('app.name'))

@section('content')
    <div class="mb-6 overflow-hidden rounded-2xl border border-indigo-200/70 bg-gradient-to-br from-white via-indigo-50/40 to-violet-50/50 p-5 shadow-sm sm:p-6">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Gateway Req/Res</h1>
            <p class="mt-1 max-w-2xl text-sm text-slate-600">Raw gateway API requests, responses, and webhooks mapped to transactions.</p>
        </div>

        <form method="GET" action="{{ route('admin.gateway-req-res.index') }}" class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-6">
            <div>
                <label for="status" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Status</label>
                <select id="status" name="status" class="block w-full rounded-xl border border-indigo-200/80 bg-white/90 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-400/30">
                    <option value="">All statuses</option>
                    @foreach ($statuses as $st)
                        <option value="{{ $st }}" @selected($status === $st)>{{ $st }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="transaction_id" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Transaction ID</label>
                <input id="transaction_id" name="transaction_id" type="number" min="1" value="{{ $transactionId }}" placeholder="e.g. 12"
                    class="block w-full rounded-xl border border-indigo-200/80 bg-white/90 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-400/30">
            </div>
            <div>
                <label for="date_from" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">From</label>
                <input id="date_from" name="date_from" type="date" value="{{ $dateFrom }}"
                    class="block w-full rounded-xl border border-indigo-200/80 bg-white/90 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-400/30">
            </div>
            <div>
                <label for="date_to" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">To</label>
                <input id="date_to" name="date_to" type="date" value="{{ $dateTo }}"
                    class="block w-full rounded-xl border border-indigo-200/80 bg-white/90 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-400/30">
            </div>
            <div class="sm:col-span-2">
                <label for="q" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Search JSON</label>
                <input id="q" name="q" type="search" value="{{ $q }}" placeholder="gateway, order_id, action…"
                    class="block w-full rounded-xl border border-indigo-200/80 bg-white/90 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-400/30">
            </div>
            <div class="flex items-end gap-2 sm:col-span-2 lg:col-span-6">
                <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Filter</button>
                <a href="{{ route('admin.gateway-req-res.index') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">Reset</a>
            </div>
        </form>
    </div>

    <div class="w-full overflow-hidden rounded-2xl border border-slate-200/90 bg-white shadow-md shadow-slate-200/50 ring-1 ring-slate-900/5">
        <div class="w-full min-w-0 overflow-x-auto">
            <table class="w-full min-w-[56rem] border-collapse divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-gradient-to-r from-slate-100 via-indigo-50/80 to-violet-50/90 text-xs font-semibold uppercase tracking-wide text-slate-600">
                    <tr>
                        <th class="whitespace-nowrap px-4 py-3 sm:px-6">Time</th>
                        <th class="whitespace-nowrap px-4 py-3 sm:px-6">Txn</th>
                        <th class="whitespace-nowrap px-4 py-3 sm:px-6">Gateway</th>
                        <th class="whitespace-nowrap px-4 py-3 sm:px-6">Action</th>
                        <th class="whitespace-nowrap px-4 py-3 sm:px-6">Status</th>
                        <th class="whitespace-nowrap px-4 py-3 sm:px-6">Has</th>
                        <th class="whitespace-nowrap px-4 py-3 sm:px-6"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($entries as $entry)
                        @php
                            $req = is_array($entry->req) ? $entry->req : [];
                        @endphp
                        <tr class="transition-colors hover:bg-slate-50/90">
                            <td class="whitespace-nowrap px-4 py-4 text-xs text-slate-600 sm:px-6">
                                {{ $entry->created_at?->format('M j, H:i:s') }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 font-mono text-xs sm:px-6">
                                @if ($entry->transaction_id)
                                    <a href="{{ route('admin.gateway-req-res.index', ['transaction_id' => $entry->transaction_id]) }}" class="text-indigo-600 hover:text-indigo-500">#{{ $entry->transaction_id }}</a>@if ($entry->transaction?->transaction_id) · {{ $entry->transaction->transaction_id }}@endif
                                @else
                                    <span class="text-slate-300">—</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 sm:px-6">
                                <span class="font-mono text-xs text-slate-800">{{ $req['gateway'] ?? '—' }}</span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 font-mono text-xs text-slate-700 sm:px-6">{{ $req['action'] ?? '—' }}</td>
                            <td class="whitespace-nowrap px-4 py-4 sm:px-6">
                                @if ($entry->status)
                                    <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-800 ring-1 ring-slate-200">{{ $entry->status }}</span>
                                @else
                                    <span class="text-slate-300">—</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 text-xs text-slate-600 sm:px-6">
                                @if ($entry->req)<span class="text-emerald-700">req</span>@endif
                                @if ($entry->response)<span class="mx-1 text-indigo-700">res</span>@endif
                                @if ($entry->webhook)<span class="text-amber-700">hook</span>@endif
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 text-right sm:px-6">
                                <a href="{{ route('admin.gateway-req-res.show', $entry) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">Details</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-sm text-slate-500">
                                No gateway request/response records yet. They appear after Cashfree checkout, return sync, or webhooks.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($entries->hasPages())
            <div class="border-t border-slate-200 px-4 py-4 sm:px-6">
                {{ $entries->links() }}
            </div>
        @endif
    </div>
@endsection
