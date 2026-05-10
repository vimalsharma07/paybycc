@extends('layouts.app')

@section('title', 'Bank accounts — '.config('app.name'))

@section('content')
    <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Bank accounts</h1>
            <p class="mt-1 text-sm text-slate-600">Add accounts you use for payouts. Shown only after KYC is verified.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">← Back to dashboard</a>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-900">Add bank account</h2>
        <form method="POST" action="{{ route('banks.store') }}" class="mt-6 space-y-5">
            @csrf
            <div class="grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="bank_name" class="mb-1 block text-sm font-medium text-slate-700">Bank name</label>
                    <input id="bank_name" name="bank_name" type="text" value="{{ old('bank_name') }}" required maxlength="120"
                        class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('bank_name') border-red-500 @enderror">
                    @error('bank_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="sm:col-span-2">
                    <label for="account_holder_name" class="mb-1 block text-sm font-medium text-slate-700">Account holder name</label>
                    <input id="account_holder_name" name="account_holder_name" type="text" value="{{ old('account_holder_name') }}" required maxlength="120"
                        class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('account_holder_name') border-red-500 @enderror">
                    @error('account_holder_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="ifsc" class="mb-1 block text-sm font-medium text-slate-700">IFSC</label>
                    <input id="ifsc" name="ifsc" type="text" value="{{ old('ifsc') }}" required maxlength="11" autocomplete="off" placeholder="e.g. HDFC0001234"
                        class="font-mono uppercase block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('ifsc') border-red-500 @enderror">
                    @error('ifsc')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="account_no" class="mb-1 block text-sm font-medium text-slate-700">Account number</label>
                    <input id="account_no" name="account_no" type="text" value="{{ old('account_no') }}" required maxlength="32" autocomplete="off"
                        class="font-mono block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('account_no') border-red-500 @enderror">
                    @error('account_no')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex items-center gap-2 sm:col-span-2">
                    <input id="is_primary_new" name="is_primary" type="checkbox" value="1" {{ old('is_primary') ? 'checked' : '' }}
                        class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="is_primary_new" class="text-sm text-slate-700">Set as primary account</label>
                </div>
            </div>
            <button type="submit" class="inline-flex rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2">
                Save bank account
            </button>
        </form>
    </div>

    <div class="mt-8 rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="border-b border-slate-100 px-6 py-4">
            <h2 class="text-lg font-semibold text-slate-900">Your accounts</h2>
        </div>
        @if ($banks->isEmpty())
            <p class="px-6 py-10 text-center text-sm text-slate-600">No bank accounts yet. Add one above.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-medium uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-6 py-3">Bank</th>
                            <th class="px-6 py-3">Holder</th>
                            <th class="px-6 py-3">IFSC</th>
                            <th class="px-6 py-3">Account</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($banks as $bank)
                            <tr class="bg-white">
                                <td class="px-6 py-4">
                                    <span class="font-medium text-slate-900">{{ $bank->bank_name }}</span>
                                    @if ($bank->is_primary)
                                        <span class="ml-2 rounded bg-indigo-100 px-2 py-0.5 text-xs font-medium text-indigo-800">Primary</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-slate-700">{{ $bank->account_holder_name }}</td>
                                <td class="px-6 py-4 font-mono text-slate-800">{{ $bank->ifsc }}</td>
                                <td class="px-6 py-4 font-mono text-slate-800">
                                    @php $len = strlen($bank->account_no); @endphp
                                    @if ($len > 4)
                                        {{ str_repeat('•', max(4, $len - 4)) }}{{ substr($bank->account_no, -4) }}
                                    @else
                                        ••••
                                    @endif
                                </td>
                                <td class="px-6 py-4 capitalize">
                                    <span class="{{ $bank->status === 'active' ? 'text-emerald-700' : 'text-slate-500' }}">{{ $bank->status }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button type="button" onclick="document.getElementById('edit-{{ $bank->id }}').classList.toggle('hidden')"
                                        class="text-sm font-medium text-indigo-600 hover:text-indigo-500">Edit</button>
                                </td>
                            </tr>
                            <tr id="edit-{{ $bank->id }}" class="hidden bg-slate-50">
                                <td colspan="6" class="px-6 py-6">
                                    <form method="POST" action="{{ route('banks.update', $bank) }}" class="space-y-4">
                                        @csrf
                                        @method('PATCH')
                                        <div class="grid gap-4 sm:grid-cols-2">
                                            <div class="sm:col-span-2">
                                                <label class="mb-1 block text-xs font-medium text-slate-600">Bank name</label>
                                                <input name="bank_name" type="text" value="{{ old('bank_name', $bank->bank_name) }}" required maxlength="120"
                                                    class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label class="mb-1 block text-xs font-medium text-slate-600">Account holder name</label>
                                                <input name="account_holder_name" type="text" value="{{ old('account_holder_name', $bank->account_holder_name) }}" required maxlength="120"
                                                    class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                                            </div>
                                            <div>
                                                <label class="mb-1 block text-xs font-medium text-slate-600">IFSC</label>
                                                <input name="ifsc" type="text" value="{{ old('ifsc', $bank->ifsc) }}" required maxlength="11"
                                                    class="font-mono uppercase block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                                            </div>
                                            <div>
                                                <label class="mb-1 block text-xs font-medium text-slate-600">Account number</label>
                                                <input name="account_no" type="text" value="{{ old('account_no', $bank->account_no) }}" required maxlength="32"
                                                    class="font-mono block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                                            </div>
                                            <div>
                                                <label class="mb-1 block text-xs font-medium text-slate-600">Status</label>
                                                <select name="status" class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                                                    <option value="active" @selected(old('status', $bank->status) === 'active')>Active</option>
                                                    <option value="inactive" @selected(old('status', $bank->status) === 'inactive')>Inactive</option>
                                                </select>
                                            </div>
                                            <div class="flex items-center gap-2 pt-6">
                                                <input id="primary-{{ $bank->id }}" name="is_primary" type="checkbox" value="1" @checked(old('is_primary', $bank->is_primary))
                                                    class="rounded border-slate-300 text-indigo-600">
                                                <label for="primary-{{ $bank->id }}" class="text-sm text-slate-700">Primary</label>
                                            </div>
                                        </div>
                                        <div class="flex flex-wrap items-center gap-3">
                                            <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Save changes</button>
                                            <button type="button" onclick="document.getElementById('edit-{{ $bank->id }}').classList.add('hidden')" class="text-sm text-slate-600 hover:text-slate-900">Cancel</button>
                                        </div>
                                    </form>
                                    <form method="POST" action="{{ route('banks.destroy', $bank) }}" class="mt-4 border-t border-slate-200 pt-4" onsubmit="return confirm('Remove this bank account?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-500">Remove account</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
