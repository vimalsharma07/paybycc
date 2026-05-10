@extends('layouts.admin')

@section('title', 'Payment gateways — '.config('app.name'))

@section('content')
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Payment gateways</h1>
            <p class="mt-1 text-sm text-slate-600">Add drivers under <span class="font-mono text-xs">app/Gateways</span>, then register them here. The primary active gateway is used for user payments.</p>
        </div>
        <a href="{{ route('admin.gateways.create') }}" class="inline-flex shrink-0 items-center justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-indigo-500">
            Add gateway
        </a>
    </div>

    <div class="w-full overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="w-full min-w-0 overflow-x-auto">
            <table class="w-full min-w-[56rem] border-collapse divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-xs font-medium uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3 sm:px-6">Name</th>
                        <th class="whitespace-nowrap px-4 py-3 sm:px-6">Code</th>
                        <th class="whitespace-nowrap px-4 py-3 sm:px-6">Class file</th>
                        <th class="whitespace-nowrap px-4 py-3 sm:px-6">Status</th>
                        <th class="whitespace-nowrap px-4 py-3 sm:px-6">Primary</th>
                        <th class="whitespace-nowrap px-4 py-3 sm:px-6">Txn range</th>
                        <th class="whitespace-nowrap px-4 py-3 sm:px-6">Daily cap</th>
                        <th class="w-28 whitespace-nowrap px-4 py-3 text-right sm:px-6"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($gateways as $g)
                        <tr class="bg-white">
                            <td class="px-4 py-4 font-medium text-slate-900 sm:px-6">{{ $g->name }}</td>
                            <td class="whitespace-nowrap px-4 py-4 font-mono text-xs text-slate-700 sm:px-6">{{ $g->code }}</td>
                            <td class="whitespace-nowrap px-4 py-4 font-mono text-xs text-slate-700 sm:px-6">{{ $g->filename }}.php</td>
                            <td class="whitespace-nowrap px-4 py-4 capitalize sm:px-6">
                                <span class="{{ $g->status === 'active' ? 'text-emerald-700' : 'text-slate-500' }}">{{ $g->status }}</span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 sm:px-6">
                                @if ($g->is_primary)
                                    <span class="rounded bg-indigo-100 px-2 py-0.5 text-xs font-medium text-indigo-900">Primary</span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 tabular-nums text-slate-700 sm:px-6">{{ $g->min_txn }} – {{ $g->max_txn }}</td>
                            <td class="whitespace-nowrap px-4 py-4 tabular-nums text-slate-700 sm:px-6">
                                @if ((float) $g->daily_limit <= 0)
                                    <span class="text-slate-500">Unlimited</span>
                                @else
                                    {{ $g->daily_limit }}
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 text-right sm:px-6">
                                <a href="{{ route('admin.gateways.edit', $g) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">Edit</a>
                                <form method="POST" action="{{ route('admin.gateways.destroy', $g) }}" class="mt-2 inline" onsubmit="return confirm('Remove this gateway?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-500">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-sm text-slate-600">No gateways yet. Create a class in <span class="font-mono">app/Gateways</span>, then add it here.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($gateways->hasPages())
            <div class="border-t border-slate-100 px-4 py-4 sm:px-6">
                {{ $gateways->links() }}
            </div>
        @endif
    </div>
@endsection
