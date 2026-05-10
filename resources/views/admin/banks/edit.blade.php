@extends('layouts.admin')

@section('title', 'Edit bank — '.config('app.name'))

@section('content')
    <div class="mb-8">
        <a href="{{ route('admin.banks.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">← Back to bank accounts</a>
        <h1 class="mt-4 text-2xl font-semibold text-slate-900">Edit bank account</h1>
        <p class="mt-1 text-sm text-slate-600">{{ $bank->bank_name }} · {{ $bank->ifsc }}</p>
    </div>

    <div class="mb-8 rounded-2xl border border-indigo-100 bg-indigo-50/80 p-5 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-wide text-indigo-800">Linked user</p>
        <div class="mt-3 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="font-semibold text-slate-900">{{ $bank->user->name }}</p>
                <p class="text-sm text-slate-600">{{ $bank->user->email }}</p>
                <p class="mt-1 font-mono text-xs text-slate-500">{{ $bank->user->user_code }}</p>
            </div>
            <a href="{{ route('admin.users.edit', $bank->user) }}" class="inline-flex shrink-0 items-center justify-center rounded-lg border border-indigo-200 bg-white px-4 py-2 text-sm font-medium text-indigo-700 shadow-sm hover:bg-indigo-50">
                Open user profile
            </a>
        </div>
    </div>

    <div class="max-w-3xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <form method="POST" action="{{ route('admin.banks.update', $bank) }}" class="space-y-6">
            @csrf
            @method('PATCH')

            <div class="grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="bank_name" class="mb-1 block text-sm font-medium text-slate-700">Bank name</label>
                    <input id="bank_name" name="bank_name" type="text" value="{{ old('bank_name', $bank->bank_name) }}" required maxlength="120"
                        class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('bank_name') border-red-500 @enderror">
                    @error('bank_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="sm:col-span-2">
                    <label for="account_holder_name" class="mb-1 block text-sm font-medium text-slate-700">Account holder name</label>
                    <input id="account_holder_name" name="account_holder_name" type="text" value="{{ old('account_holder_name', $bank->account_holder_name) }}" required maxlength="120"
                        class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('account_holder_name') border-red-500 @enderror">
                    @error('account_holder_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="ifsc" class="mb-1 block text-sm font-medium text-slate-700">IFSC</label>
                    <input id="ifsc" name="ifsc" type="text" value="{{ old('ifsc', $bank->ifsc) }}" required maxlength="11" autocomplete="off"
                        class="font-mono uppercase block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('ifsc') border-red-500 @enderror">
                    @error('ifsc')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="account_no" class="mb-1 block text-sm font-medium text-slate-700">Account number</label>
                    <input id="account_no" name="account_no" type="text" value="{{ old('account_no', $bank->account_no) }}" required maxlength="32" autocomplete="off"
                        class="font-mono block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('account_no') border-red-500 @enderror">
                    @error('account_no')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="status" class="mb-1 block text-sm font-medium text-slate-700">Status</label>
                    <select id="status" name="status"
                        class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('status') border-red-500 @enderror">
                        <option value="active" @selected(old('status', $bank->status) === 'active')>Active</option>
                        <option value="inactive" @selected(old('status', $bank->status) === 'inactive')>Inactive</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex items-center gap-2 pt-6 sm:pt-8">
                    <input type="hidden" name="is_primary" value="0">
                    <input id="is_primary" name="is_primary" type="checkbox" value="1" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                        @checked(old('is_primary', $bank->is_primary))>
                    <label for="is_primary" class="text-sm text-slate-700">Primary account for this user</label>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-4 border-t border-slate-100 pt-6">
                <button type="submit" class="inline-flex rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2">
                    Save changes
                </button>
                <a href="{{ route('admin.banks.index') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">Cancel</a>
            </div>
        </form>
    </div>
@endsection
