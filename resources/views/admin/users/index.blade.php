@extends('layouts.admin')

@section('title', 'Users — '.config('app.name'))

@section('content')
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Users</h1>
            <p class="mt-1 text-sm text-slate-600">Search, review status, and open a user to edit details.</p>
        </div>
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex w-full max-w-md gap-2">
            <label for="q" class="sr-only">Search</label>
            <input id="q" name="q" type="search" value="{{ $q }}" placeholder="Name, email, code, phone…"
                class="block min-w-0 flex-1 rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            <button type="submit" class="shrink-0 rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Search</button>
        </form>
    </div>

    <div class="w-full overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="w-full min-w-0 overflow-x-auto">
            <table class="w-full min-w-[56rem] border-collapse divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-xs font-medium uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3 sm:px-6">User</th>
                        <th class="whitespace-nowrap px-4 py-3 sm:px-6">Code</th>
                        <th class="whitespace-nowrap px-4 py-3 sm:px-6">Phone</th>
                        <th class="px-4 py-3 sm:px-6">Account</th>
                        <th class="whitespace-nowrap px-4 py-3 sm:px-6">KYC</th>
                        <th class="whitespace-nowrap px-4 py-3 text-right tabular-nums sm:px-6">Banks</th>
                        <th class="w-24 whitespace-nowrap px-4 py-3 text-right sm:px-6"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($users as $u)
                        <tr class="bg-white">
                            <td class="px-4 py-4 sm:px-6">
                                <p class="font-medium text-slate-900">{{ $u->name }}</p>
                                <p class="text-xs text-slate-600">{{ $u->email }}</p>
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 font-mono text-xs text-slate-700 sm:px-6">{{ $u->user_code }}</td>
                            <td class="whitespace-nowrap px-4 py-4 text-slate-700 sm:px-6">{{ $u->phone ?? '—' }}</td>
                            <td class="px-4 py-4 sm:px-6">
                                <span class="{{ $u->status === 'active' ? 'text-emerald-700' : 'text-slate-500' }} capitalize">{{ $u->status }}</span>
                                @if ($u->is_admin)
                                    <span class="ml-2 rounded bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-900">Admin</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 text-slate-700 sm:px-6">{{ $u->kyc_status_label }}</td>
                            <td class="whitespace-nowrap px-4 py-4 text-right tabular-nums text-slate-700 sm:px-6">{{ $u->banks_count }}</td>
                            <td class="whitespace-nowrap px-4 py-4 text-right sm:px-6">
                                <a href="{{ route('admin.users.edit', $u) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-sm text-slate-600">No users match your search.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($users->hasPages())
            <div class="border-t border-slate-100 px-4 py-4 sm:px-6">
                {{ $users->links() }}
            </div>
        @endif
    </div>
@endsection
