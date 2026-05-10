@extends('layouts.admin')

@section('title', 'Edit user — '.config('app.name'))

@section('content')
    <div class="mb-8">
        <a href="{{ route('admin.users.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">← Back to users</a>
        <h1 class="mt-4 text-2xl font-semibold text-slate-900">Edit user</h1>
        <p class="mt-1 text-sm text-slate-600">{{ $editUser->name }} · <span class="font-mono text-xs">{{ $editUser->user_code }}</span></p>
        <p class="mt-3">
            <a href="{{ route('admin.banks.index', ['q' => $editUser->email]) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">Bank accounts for this user →</a>
        </p>
    </div>

    <div class="max-w-3xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <form method="POST" action="{{ route('admin.users.update', $editUser) }}" class="space-y-8">
            @csrf
            @method('PATCH')

            <div>
                <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Profile</h2>
                <div class="mt-4 grid gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="user_code_display" class="mb-1 block text-sm font-medium text-slate-700">User code</label>
                        <input id="user_code_display" type="text" value="{{ $editUser->user_code }}" disabled
                            class="block w-full cursor-not-allowed rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 font-mono text-sm text-slate-600">
                    </div>
                    <div class="sm:col-span-2">
                        <label for="name" class="mb-1 block text-sm font-medium text-slate-700">Name</label>
                        <input id="name" name="name" type="text" value="{{ old('name', $editUser->name) }}" required maxlength="255"
                            class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label for="email" class="mb-1 block text-sm font-medium text-slate-700">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email', $editUser->email) }}" required maxlength="255"
                            class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label for="phone" class="mb-1 block text-sm font-medium text-slate-700">Phone</label>
                        <input id="phone" name="phone" type="text" value="{{ old('phone', $editUser->phone) }}" maxlength="20"
                            class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('phone') border-red-500 @enderror">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div>
                <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Account &amp; KYC</h2>
                <div class="mt-4 grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="status" class="mb-1 block text-sm font-medium text-slate-700">Account status</label>
                        <select id="status" name="status"
                            class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('status') border-red-500 @enderror">
                            <option value="active" @selected(old('status', $editUser->status) === 'active')>Active</option>
                            <option value="inactive" @selected(old('status', $editUser->status) === 'inactive')>Inactive</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="kyc_status" class="mb-1 block text-sm font-medium text-slate-700">KYC status</label>
                        <select id="kyc_status" name="kyc_status"
                            class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('kyc_status') border-red-500 @enderror">
                            <option value="-1" @selected((int) old('kyc_status', $editUser->kyc_status) === -1)>Incomplete (−1)</option>
                            <option value="0" @selected((int) old('kyc_status', $editUser->kyc_status) === 0)>Inactive (0)</option>
                            <option value="1" @selected((int) old('kyc_status', $editUser->kyc_status) === 1)>Active (1)</option>
                        </select>
                        @error('kyc_status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="sm:col-span-2 flex items-start gap-3 rounded-lg border border-slate-200 bg-slate-50 px-4 py-3">
                        <input type="hidden" name="is_admin" value="0">
                        <input id="is_admin" name="is_admin" type="checkbox" value="1" class="mt-1 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                            @checked(old('is_admin', $editUser->is_admin))>
                        <div>
                            <label for="is_admin" class="text-sm font-medium text-slate-800">Administrator</label>
                            <p class="mt-1 text-xs text-slate-600">Grants access to this admin panel. You cannot remove admin access from your own account here.</p>
                        </div>
                    </div>
                    @error('is_admin')
                        <p class="sm:col-span-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Limits</h2>
                <div class="mt-4 grid gap-5 sm:grid-cols-3">
                    <div>
                        <label for="daily_limit" class="mb-1 block text-sm font-medium text-slate-700">Daily</label>
                        <input id="daily_limit" name="daily_limit" type="text" inputmode="decimal" value="{{ old('daily_limit', $editUser->daily_limit) }}" required
                            class="block w-full rounded-lg border border-slate-300 px-3 py-2 font-mono text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('daily_limit') border-red-500 @enderror">
                        @error('daily_limit')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="monthly_limit" class="mb-1 block text-sm font-medium text-slate-700">Monthly</label>
                        <input id="monthly_limit" name="monthly_limit" type="text" inputmode="decimal" value="{{ old('monthly_limit', $editUser->monthly_limit) }}" required
                            class="block w-full rounded-lg border border-slate-300 px-3 py-2 font-mono text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('monthly_limit') border-red-500 @enderror">
                        @error('monthly_limit')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="yearly_limit" class="mb-1 block text-sm font-medium text-slate-700">Yearly</label>
                        <input id="yearly_limit" name="yearly_limit" type="text" inputmode="decimal" value="{{ old('yearly_limit', $editUser->yearly_limit) }}" required
                            class="block w-full rounded-lg border border-slate-300 px-3 py-2 font-mono text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('yearly_limit') border-red-500 @enderror">
                        @error('yearly_limit')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div>
                <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Identity <span class="font-normal normal-case text-slate-400">(optional)</span></h2>
                <div class="mt-4 grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="pan" class="mb-1 block text-sm font-medium text-slate-700">PAN</label>
                        <input id="pan" name="pan" type="text" value="{{ old('pan', $editUser->pan) }}" maxlength="10" autocomplete="off"
                            class="font-mono uppercase block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('pan') border-red-500 @enderror">
                        @error('pan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="pan_name" class="mb-1 block text-sm font-medium text-slate-700">Name as per PAN</label>
                        <input id="pan_name" name="pan_name" type="text" value="{{ old('pan_name', $editUser->pan_name) }}" maxlength="255"
                            class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('pan_name') border-red-500 @enderror">
                        @error('pan_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label for="aadhar" class="mb-1 block text-sm font-medium text-slate-700">Aadhaar</label>
                        <input id="aadhar" name="aadhar" type="text" value="{{ old('aadhar', $editUser->aadhar) }}" maxlength="12" inputmode="numeric"
                            class="block w-full rounded-lg border border-slate-300 px-3 py-2 font-mono text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('aadhar') border-red-500 @enderror">
                        @error('aadhar')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div>
                <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Security</h2>
                <div class="mt-4 grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="password" class="mb-1 block text-sm font-medium text-slate-700">New password</label>
                        <input id="password" name="password" type="password" autocomplete="new-password"
                            class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('password') border-red-500 @enderror">
                        <p class="mt-1 text-xs text-slate-500">Leave blank to keep the current password.</p>
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="mb-1 block text-sm font-medium text-slate-700">Confirm new password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                            class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-4 border-t border-slate-100 pt-6">
                <button type="submit" class="inline-flex rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2">
                    Save changes
                </button>
                <a href="{{ route('admin.users.index') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">Cancel</a>
            </div>
        </form>
    </div>
@endsection
