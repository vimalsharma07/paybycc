@extends('layouts.admin')

@section('title', 'Bank accounts — '.config('app.name'))

@section('content')
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Bank accounts</h1>
            <p class="mt-1 text-sm text-slate-600">All saved accounts with owner details. Search by bank, IFSC, holder, or user.</p>
        </div>
        <form method="GET" action="{{ route('admin.banks.index') }}" class="flex w-full max-w-md gap-2">
            <label for="q" class="sr-only">Search</label>
            <input id="q" name="q" type="search" value="{{ $q }}" placeholder="Bank, IFSC, user…"
                class="block min-w-0 flex-1 rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            <button type="submit" class="shrink-0 rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Search</button>
        </form>
    </div>

    <div class="w-full overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="w-full min-w-0 overflow-x-auto">
            <table class="w-full min-w-[64rem] border-collapse divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-xs font-medium uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="min-w-[12rem] px-4 py-3 sm:px-6">Owner</th>
                        <th class="px-4 py-3 sm:px-6">Bank</th>
                        <th class="min-w-[8rem] px-4 py-3 sm:px-6">Holder</th>
                        <th class="whitespace-nowrap px-4 py-3 sm:px-6">IFSC</th>
                        <th class="whitespace-nowrap px-4 py-3 sm:px-6">Account</th>
                        <th class="whitespace-nowrap px-4 py-3 sm:px-6">Status</th>
                        <th class="w-24 whitespace-nowrap px-4 py-3 text-right sm:px-6"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($banks as $bank)
                        @php
                            $len = strlen($bank->account_no);
                            $masked = $len > 4 ? str_repeat('•', max(4, $len - 4)).substr($bank->account_no, -4) : '••••';
                        @endphp
                        <tr class="bg-white">
                            <td class="px-4 py-4 sm:px-6">
                                <p class="font-medium text-slate-900">{{ $bank->user->name }}</p>
                                <p class="text-xs text-slate-600">{{ $bank->user->email }}</p>
                                <p class="mt-0.5 font-mono text-xs text-slate-500">{{ $bank->user->user_code }}</p>
                            </td>
                            <td class="px-4 py-4 sm:px-6">
                                <span class="font-medium text-slate-900">{{ $bank->bank_name }}</span>
                                @if ($bank->is_primary)
                                    <span class="ml-2 rounded bg-indigo-100 px-2 py-0.5 text-xs font-medium text-indigo-800">Primary</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-slate-700 sm:px-6">{{ $bank->account_holder_name }}</td>
                            <td class="whitespace-nowrap px-4 py-4 font-mono text-xs text-slate-800 sm:px-6">{{ $bank->ifsc }}</td>
                            <td class="whitespace-nowrap px-4 py-4 font-mono text-xs text-slate-800 sm:px-6">{{ $masked }}</td>
                            <td class="whitespace-nowrap px-4 py-4 capitalize sm:px-6">
                                <span class="{{ $bank->status === 'active' ? 'text-emerald-700' : 'text-slate-500' }}">{{ $bank->status }}</span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 text-right sm:px-6">
                                <a href="{{ route('admin.banks.edit', $bank) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-sm text-slate-600">No bank accounts match your search.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($banks->hasPages())
            <div class="border-t border-slate-100 px-4 py-4 sm:px-6">
                {{ $banks->links() }}
            </div>
        @endif
    </div>
@endsection
