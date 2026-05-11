@extends('layouts.admin')

@section('title', 'Add gateway — '.config('app.name'))

@section('content')
    <div class="mb-8">
        <a href="{{ route('admin.gateways.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">← Back to gateways</a>
        <h1 class="mt-4 text-2xl font-semibold text-slate-900">Add gateway</h1>
        <p class="mt-1 text-sm text-slate-600">Class name must match a file in <span class="font-mono text-xs">app/Gateways/</span> (e.g. <span class="font-mono">Cashfree</span> → <span class="font-mono">Cashfree.php</span>).</p>
    </div>

    <div class="max-w-3xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <form method="POST" action="{{ route('admin.gateways.store') }}" class="space-y-6">
            @csrf

            <div class="grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="name" class="mb-1 block text-sm font-medium text-slate-700">Display name</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required maxlength="120"
                        class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="code" class="mb-1 block text-sm font-medium text-slate-700">Code <span class="font-normal text-slate-500">(unique slug)</span></label>
                    <input id="code" name="code" type="text" value="{{ old('code') }}" required maxlength="64" placeholder="cashfree"
                        class="font-mono block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('code') border-red-500 @enderror">
                    @error('code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="filename" class="mb-1 block text-sm font-medium text-slate-700">Class / filename</label>
                    <input id="filename" name="filename" type="text" value="{{ old('filename', 'Cashfree') }}" required maxlength="120" placeholder="Cashfree or Cashfree.php"
                        class="font-mono block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('filename') border-red-500 @enderror">
                    @error('filename')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="status" class="mb-1 block text-sm font-medium text-slate-700">Status</label>
                    <select id="status" name="status" class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        <option value="active" @selected(old('status', 'active') === 'active')>Active</option>
                        <option value="inactive" @selected(old('status') === 'inactive')>Inactive</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex items-start gap-3 rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 sm:col-span-2">
                    <input type="hidden" name="is_primary" value="0">
                    <input id="is_primary" name="is_primary" type="checkbox" value="1" class="mt-1 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" @checked(old('is_primary'))>
                    <div>
                        <label for="is_primary" class="text-sm font-medium text-slate-800">Primary gateway</label>
                        <p class="mt-1 text-xs text-slate-600">Only one primary at a time; users pay through this gateway when it is active.</p>
                    </div>
                </div>
            </div>

            <div class="grid gap-5 sm:grid-cols-3">
                <div>
                    <label for="min_txn" class="mb-1 block text-sm font-medium text-slate-700">Min txn</label>
                    <input id="min_txn" name="min_txn" type="text" inputmode="decimal" value="{{ old('min_txn', '1.00') }}" required
                        class="block w-full rounded-lg border border-slate-300 px-3 py-2 font-mono text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('min_txn') border-red-500 @enderror">
                    @error('min_txn')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="max_txn" class="mb-1 block text-sm font-medium text-slate-700">Max txn</label>
                    <input id="max_txn" name="max_txn" type="text" inputmode="decimal" value="{{ old('max_txn', '999999.99') }}" required
                        class="block w-full rounded-lg border border-slate-300 px-3 py-2 font-mono text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('max_txn') border-red-500 @enderror">
                    @error('max_txn')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="daily_limit" class="mb-1 block text-sm font-medium text-slate-700">Daily limit</label>
                    <input id="daily_limit" name="daily_limit" type="text" inputmode="decimal" value="{{ old('daily_limit', '0') }}" required
                        class="block w-full rounded-lg border border-slate-300 px-3 py-2 font-mono text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('daily_limit') border-red-500 @enderror">
                    <p class="mt-1 text-xs text-slate-500">0 = no daily cap (sum of pending + completed today).</p>
                    @error('daily_limit')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="credentials_json" class="mb-1 block text-sm font-medium text-slate-700">Credentials (JSON)</label>
                <textarea id="credentials_json" name="credentials_json" rows="8" placeholder='{\n  "client_id": "Cashfree App ID",\n  "client_secret": "Secret key",\n  "env": "sandbox"\n}'
                    class="block w-full rounded-lg border border-slate-300 px-3 py-2 font-mono text-xs shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('credentials_json') border-red-500 @enderror">{{ old('credentials_json') }}</textarea>
                @error('credentials_json')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-wrap items-center gap-4 border-t border-slate-100 pt-6">
                <button type="submit" class="inline-flex rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2">
                    Save gateway
                </button>
                <a href="{{ route('admin.gateways.index') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">Cancel</a>
            </div>
        </form>
    </div>
@endsection
