@extends('layouts.admin')

@section('title', 'Log #'.$log->id.' — '.config('app.name'))

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.logs.index', request()->only(['channel', 'level', 'event', 'q', 'date_from', 'date_to'])) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">← Back to logs</a>
        <h1 class="mt-3 text-2xl font-semibold tracking-tight text-slate-900">Log entry #{{ $log->id }}</h1>
        <p class="mt-1 text-sm text-slate-600">{{ $log->created_at?->format('l, F j, Y \a\t H:i:s') }}</p>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Summary</h2>
            <dl class="mt-4 space-y-4 text-sm">
                <div class="flex flex-wrap justify-between gap-2 border-b border-slate-100 pb-3">
                    <dt class="font-medium text-slate-500">Level</dt>
                    <dd>
                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ring-1 {{ $log->levelEnum()->badgeClass() }}">{{ $log->level }}</span>
                    </dd>
                </div>
                <div class="flex flex-wrap justify-between gap-2 border-b border-slate-100 pb-3">
                    <dt class="font-medium text-slate-500">Channel</dt>
                    <dd class="font-mono text-slate-900">{{ $log->channel }}</dd>
                </div>
                <div class="flex flex-wrap justify-between gap-2 border-b border-slate-100 pb-3">
                    <dt class="font-medium text-slate-500">Event</dt>
                    <dd class="font-mono text-xs text-slate-900 sm:text-sm">{{ $log->event }}</dd>
                </div>
                <div class="flex flex-wrap justify-between gap-2 border-b border-slate-100 pb-3">
                    <dt class="font-medium text-slate-500">IP address</dt>
                    <dd class="font-mono text-slate-900">{{ $log->ip_address ?? '—' }}</dd>
                </div>
                <div class="flex flex-wrap justify-between gap-2 border-b border-slate-100 pb-3">
                    <dt class="font-medium text-slate-500">Request ID</dt>
                    <dd class="max-w-xs break-all font-mono text-xs text-slate-700">{{ $log->request_id ?? '—' }}</dd>
                </div>
                <div class="flex flex-wrap justify-between gap-2 border-b border-slate-100 pb-3">
                    <dt class="font-medium text-slate-500">User</dt>
                    <dd class="text-slate-900">
                        @if ($log->user)
                            {{ $log->user->name }} <span class="text-slate-500">({{ $log->user->email }})</span>
                        @else
                            <span class="text-slate-400">Guest / system</span>
                        @endif
                    </dd>
                </div>
                <div class="flex flex-wrap justify-between gap-2">
                    <dt class="font-medium text-slate-500">Subject</dt>
                    <dd class="font-mono text-slate-900">{{ $log->subjectLabel() ?? '—' }}</dd>
                </div>
            </dl>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Message</h2>
            <p class="mt-4 text-sm leading-relaxed text-slate-800">{{ $log->message }}</p>
        </div>
    </div>

    <div class="mt-6 rounded-2xl border border-slate-200 bg-slate-900 p-6 shadow-sm">
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-400">Context (JSON)</h2>
            @if ($log->context)
                <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('log-context').textContent)" class="rounded-lg bg-slate-800 px-3 py-1.5 text-xs font-medium text-slate-200 hover:bg-slate-700">Copy</button>
            @endif
        </div>
        @if ($log->context)
            <pre id="log-context" class="mt-4 max-h-[28rem] overflow-auto text-xs leading-relaxed text-emerald-100/90">{{ json_encode($log->context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
        @else
            <p class="mt-4 text-sm text-slate-500">No additional context recorded.</p>
        @endif
    </div>
@endsection
