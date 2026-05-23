@extends('layouts.admin')

@section('title', 'Logs — '.config('app.name'))

@section('content')
    <div class="mb-6 overflow-hidden rounded-2xl border border-indigo-200/70 bg-gradient-to-br from-white via-indigo-50/40 to-violet-50/50 p-5 shadow-sm sm:p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Application logs</h1>
                <p class="mt-1 max-w-2xl text-sm text-slate-600">Database audit trail for OTP, SMS, and other channels. Use filters to trace failures.</p>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.logs.index') }}" class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-6">
            <div>
                <label for="channel" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Channel</label>
                <select id="channel" name="channel" class="block w-full rounded-xl border border-indigo-200/80 bg-white/90 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-400/30">
                    <option value="">All channels</option>
                    @foreach ($channels as $ch)
                        <option value="{{ $ch }}" @selected($channel === $ch)>{{ $ch }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="level" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Level</label>
                <select id="level" name="level" class="block w-full rounded-xl border border-indigo-200/80 bg-white/90 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-400/30">
                    <option value="">All levels</option>
                    @foreach (['debug', 'info', 'notice', 'warning', 'error', 'critical'] as $lvl)
                        <option value="{{ $lvl }}" @selected($level === $lvl)>{{ ucfirst($lvl) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="event" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Event</label>
                <input id="event" name="event" type="text" value="{{ $event }}" list="event-suggestions" placeholder="e.g. otp.sms.failed"
                    class="block w-full rounded-xl border border-indigo-200/80 bg-white/90 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-400/30">
                <datalist id="event-suggestions">
                    @foreach ($events as $ev)
                        <option value="{{ $ev }}"></option>
                    @endforeach
                </datalist>
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
            <div class="sm:col-span-2 lg:col-span-1">
                <label for="q" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Search</label>
                <input id="q" name="q" type="search" value="{{ $q }}" placeholder="Message, phone, IP…"
                    class="block w-full rounded-xl border border-indigo-200/80 bg-white/90 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-400/30">
            </div>
            <div class="flex items-end gap-2 sm:col-span-2 lg:col-span-6">
                <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Filter</button>
                <a href="{{ route('admin.logs.index') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">Reset</a>
            </div>
        </form>
    </div>

    <div class="w-full overflow-hidden rounded-2xl border border-slate-200/90 bg-white shadow-md shadow-slate-200/50 ring-1 ring-slate-900/5">
        <div class="w-full min-w-0 overflow-x-auto">
            <table class="w-full min-w-[52rem] border-collapse divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-gradient-to-r from-slate-100 via-indigo-50/80 to-violet-50/90 text-xs font-semibold uppercase tracking-wide text-slate-600">
                    <tr>
                        <th class="whitespace-nowrap px-4 py-3 sm:px-6">Time</th>
                        <th class="whitespace-nowrap px-4 py-3 sm:px-6">Level</th>
                        <th class="whitespace-nowrap px-4 py-3 sm:px-6">Channel</th>
                        <th class="px-4 py-3 sm:px-6">Event</th>
                        <th class="px-4 py-3 sm:px-6">Message</th>
                        <th class="whitespace-nowrap px-4 py-3 sm:px-6"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($logs as $entry)
                        @php $levelEnum = $entry->levelEnum(); @endphp
                        <tr class="transition-colors hover:bg-slate-50/90">
                            <td class="whitespace-nowrap px-4 py-4 text-xs text-slate-600 sm:px-6">
                                <span title="{{ $entry->created_at }}">{{ $entry->created_at?->format('M j, H:i:s') }}</span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 sm:px-6">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ring-1 {{ $levelEnum->badgeClass() }}">
                                    {{ $levelEnum->value }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 sm:px-6">
                                <span class="inline-flex rounded-full bg-violet-100 px-2.5 py-1 text-xs font-semibold text-violet-900 ring-1 ring-violet-300/40">{{ $entry->channel }}</span>
                            </td>
                            <td class="max-w-[10rem] truncate px-4 py-4 font-mono text-xs text-slate-700 sm:max-w-xs sm:px-6" title="{{ $entry->event }}">{{ $entry->event }}</td>
                            <td class="max-w-md truncate px-4 py-4 text-slate-800 sm:px-6" title="{{ $entry->message }}">{{ $entry->message }}</td>
                            <td class="whitespace-nowrap px-4 py-4 text-right sm:px-6">
                                <a href="{{ route('admin.logs.show', $entry) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">Details</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-sm text-slate-500">
                                No log entries yet. OTP activity will appear here after send/verify attempts.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($logs->hasPages())
            <div class="border-t border-slate-200 px-4 py-4 sm:px-6">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
@endsection
