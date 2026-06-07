@extends('layouts.admin')

@section('title', 'Gateway Req/Res #'.$entry->id.' — '.config('app.name'))

@section('content')
    @php
        $req = is_array($entry->req) ? $entry->req : [];
    @endphp

    <div class="mb-6">
        <a href="{{ route('admin.gateway-req-res.index', request()->only(['status', 'transaction_id', 'date_from', 'date_to', 'q'])) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">← Back to Gateway Req/Res</a>
        <h1 class="mt-3 text-2xl font-semibold tracking-tight text-slate-900">Record #{{ $entry->id }}</h1>
        <p class="mt-1 text-sm text-slate-600">{{ $entry->created_at?->format('l, F j, Y \a\t H:i:s') }}</p>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Summary</h2>
            <dl class="mt-4 space-y-4 text-sm">
                <div class="flex flex-wrap justify-between gap-2 border-b border-slate-100 pb-3">
                    <dt class="font-medium text-slate-500">Transaction</dt>
                    <dd class="font-mono text-slate-900">
                        @if ($entry->transaction_id)
                            #{{ $entry->transaction_id }}
                            @if ($entry->transaction)
                                <span class="text-slate-500">· ₹{{ number_format((float) $entry->transaction->amount, 2) }}</span>
                            @endif
                            · <a href="{{ route('admin.gateway-req-res.index', ['transaction_id' => $entry->transaction_id]) }}" class="text-xs text-indigo-600 hover:text-indigo-500">All records</a>
                        @else
                            <span class="text-slate-400">—</span>
                        @endif
                    </dd>
                </div>
                <div class="flex flex-wrap justify-between gap-2 border-b border-slate-100 pb-3">
                    <dt class="font-medium text-slate-500">Gateway</dt>
                    <dd class="font-mono text-slate-900">{{ $req['gateway'] ?? '—' }}</dd>
                </div>
                <div class="flex flex-wrap justify-between gap-2 border-b border-slate-100 pb-3">
                    <dt class="font-medium text-slate-500">Action</dt>
                    <dd class="font-mono text-slate-900">{{ $req['action'] ?? '—' }}</dd>
                </div>
                <div class="flex flex-wrap justify-between gap-2 border-b border-slate-100 pb-3">
                    <dt class="font-medium text-slate-500">Status</dt>
                    <dd class="font-mono text-slate-900">{{ $entry->status ?? '—' }}</dd>
                </div>
                @if (! empty($req['payment_id']))
                    <div class="flex flex-wrap justify-between gap-2 border-b border-slate-100 pb-3">
                        <dt class="font-medium text-slate-500">Payment ID</dt>
                        <dd class="font-mono text-slate-900">#{{ $req['payment_id'] }}</dd>
                    </div>
                @endif
                @if (! empty($req['order_id']))
                    <div class="flex flex-wrap justify-between gap-2">
                        <dt class="font-medium text-slate-500">Gateway order</dt>
                        <dd class="max-w-xs break-all font-mono text-xs text-slate-900">{{ $req['order_id'] }}</dd>
                    </div>
                @endif
            </dl>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Timestamps</h2>
            <dl class="mt-4 space-y-3 text-sm">
                <div class="flex justify-between gap-2">
                    <dt class="text-slate-500">Created</dt>
                    <dd class="font-mono text-slate-900">{{ $entry->created_at?->toDateTimeString() ?? '—' }}</dd>
                </div>
                <div class="flex justify-between gap-2">
                    <dt class="text-slate-500">Updated</dt>
                    <dd class="font-mono text-slate-900">{{ $entry->updated_at?->toDateTimeString() ?? '—' }}</dd>
                </div>
            </dl>
        </div>
    </div>

    @if ($entry->req)
        <div class="mt-6 rounded-2xl border border-slate-200 bg-slate-900 p-6 shadow-sm">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-400">Request (JSON)</h2>
            <pre class="mt-4 max-h-[24rem] overflow-auto text-xs leading-relaxed text-emerald-100/90">{{ json_encode($entry->req, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
        </div>
    @endif

    @if ($entry->response)
        <div class="mt-6 rounded-2xl border border-slate-200 bg-slate-900 p-6 shadow-sm">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-400">Response (JSON)</h2>
            <pre class="mt-4 max-h-[24rem] overflow-auto text-xs leading-relaxed text-sky-100/90">{{ json_encode($entry->response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
        </div>
    @endif

    @if ($entry->webhook)
        <div class="mt-6 rounded-2xl border border-slate-200 bg-slate-900 p-6 shadow-sm">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-400">Webhook (JSON)</h2>
            <pre class="mt-4 max-h-[24rem] overflow-auto text-xs leading-relaxed text-amber-100/90">{{ json_encode($entry->webhook, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
        </div>
    @endif
@endsection
