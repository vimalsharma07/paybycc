@extends('layouts.app')

@section('title', 'Bank accounts — '.config('app.name'))

@php
    $openAddPanel = old('_intent') === 'create' && $errors->any();
    $openEditBankId = (old('_intent') === 'update' && old('_bank_id')) ? (int) old('_bank_id') : null;
@endphp

@section('content')
    <div class="overflow-hidden rounded-3xl border border-violet-200/80 bg-gradient-to-br from-violet-600 via-indigo-600 to-slate-900 shadow-xl shadow-indigo-900/20">
        <div class="px-6 py-8 text-white sm:px-8 sm:py-10">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div class="flex items-start gap-4">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-white/15 ring-1 ring-white/25" aria-hidden="true">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-4h6v4"/></svg>
                    </span>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-white/70">Payouts</p>
                        <h1 class="mt-1 text-2xl font-bold tracking-tight sm:text-3xl">Bank accounts</h1>
                        <p class="mt-2 max-w-xl text-sm leading-relaxed text-white/85">Save the accounts you want to receive money in. You can add more anytime — details stay easy to review.</p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('profile.show') }}" class="inline-flex items-center rounded-xl border border-white/20 bg-white/10 px-4 py-2.5 text-sm font-semibold text-white backdrop-blur transition hover:bg-white/15">Profile</a>
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center rounded-xl border border-white/20 bg-white/10 px-4 py-2.5 text-sm font-semibold text-white backdrop-blur transition hover:bg-white/15">Dashboard</a>
                    <a href="{{ route('wallet.index') }}" class="inline-flex items-center rounded-xl bg-white px-4 py-2.5 text-sm font-bold text-indigo-700 shadow-lg shadow-black/10 transition hover:bg-violet-50">Wallet</a>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-lg font-bold text-slate-900">Your accounts</h2>
            <p class="text-sm text-slate-600">{{ $banks->count() }} account{{ $banks->count() === 1 ? '' : 's' }} saved</p>
        </div>
        <button type="button" id="btn-open-add-bank"
            class="inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-violet-600 to-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-900/25 transition hover:brightness-110 focus:outline-none focus-visible:ring-4 focus-visible:ring-indigo-500/30">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Add bank account
        </button>
    </div>

    @if ($banks->isEmpty())
        <div class="mt-6 rounded-3xl border border-dashed border-violet-200 bg-gradient-to-br from-violet-50/80 via-white to-indigo-50/60 p-10 text-center shadow-inner">
            <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-violet-100 text-violet-700 ring-1 ring-violet-200" aria-hidden="true">
                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6"><path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-4h6v4"/></svg>
            </span>
            <p class="mt-5 text-base font-semibold text-slate-900">No bank accounts yet</p>
            <p class="mx-auto mt-2 max-w-md text-sm text-slate-600">Add your first payout account. You’ll only need this once to get started.</p>
            <button type="button" data-open-add-bank class="mt-6 inline-flex items-center gap-2 rounded-2xl bg-indigo-600 px-6 py-3 text-sm font-bold text-white shadow-md transition hover:bg-indigo-500">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Add your first bank
            </button>
        </div>
    @else
        <div class="mt-6 grid gap-4 sm:grid-cols-2">
            @foreach ($banks as $bank)
                <article class="group relative overflow-hidden rounded-2xl border border-slate-200/90 bg-white p-5 shadow-md shadow-slate-200/50 ring-1 ring-slate-900/5 transition hover:border-violet-200 hover:shadow-lg">
                    @if ($bank->is_primary)
                        <span class="absolute right-4 top-4 inline-flex items-center rounded-full bg-indigo-100 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-indigo-900 ring-1 ring-indigo-300/50">Primary</span>
                    @endif
                    <div class="flex items-start gap-3 pr-20">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-violet-100 to-indigo-100 text-violet-700 ring-1 ring-violet-200/80" aria-hidden="true">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-4h6v4"/></svg>
                        </span>
                        <div class="min-w-0">
                            <h3 class="truncate text-base font-bold text-slate-900">{{ $bank->bank_name }}</h3>
                            <p class="mt-0.5 truncate text-sm text-slate-600">{{ $bank->account_holder_name }}</p>
                        </div>
                    </div>
                    <dl class="mt-4 space-y-2 rounded-xl bg-slate-50/90 px-4 py-3 text-sm">
                        <div class="flex justify-between gap-3">
                            <dt class="text-slate-500">IFSC</dt>
                            <dd class="font-mono font-semibold text-slate-900">{{ $bank->ifsc }}</dd>
                        </div>
                        <div class="flex justify-between gap-3">
                            <dt class="text-slate-500">Account</dt>
                            <dd class="font-mono font-semibold text-slate-900">
                                @php $len = strlen($bank->account_no); @endphp
                                @if ($len > 4)
                                    <span class="tracking-tight">{{ str_repeat('•', min(8, max(4, $len - 4))) }}</span><span>{{ substr($bank->account_no, -4) }}</span>
                                @else
                                    ••••
                                @endif
                            </dd>
                        </div>
                        <div class="flex items-center justify-between gap-3 pt-1">
                            <dt class="text-slate-500">Status</dt>
                            <dd>
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold {{ $bank->status === 'active' ? 'bg-emerald-100 text-emerald-900 ring-1 ring-emerald-400/40' : 'bg-slate-200 text-slate-800 ring-1 ring-slate-300/60' }}">{{ ucfirst($bank->status) }}</span>
                            </dd>
                        </div>
                    </dl>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <button type="button" onclick="document.getElementById('edit-{{ $bank->id }}').classList.toggle('hidden')"
                            class="inline-flex flex-1 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-800 shadow-sm transition hover:border-indigo-200 hover:bg-indigo-50/60 sm:flex-none">
                            Edit
                        </button>
                    </div>

                    <div id="edit-{{ $bank->id }}" class="{{ $openEditBankId === (int) $bank->id ? '' : 'hidden' }} mt-4 border-t border-slate-100 pt-4">
                        <form method="POST" action="{{ route('banks.update', $bank) }}" class="space-y-4">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="_intent" value="update">
                            <input type="hidden" name="_bank_id" value="{{ $bank->id }}">
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Bank name</label>
                                    <input name="bank_name" type="text" value="{{ (int) old('_bank_id') === (int) $bank->id ? old('bank_name', $bank->bank_name) : $bank->bank_name }}" required maxlength="120"
                                        class="block w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm shadow-inner focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Account holder name</label>
                                    <input name="account_holder_name" type="text" value="{{ (int) old('_bank_id') === (int) $bank->id ? old('account_holder_name', $bank->account_holder_name) : $bank->account_holder_name }}" required maxlength="120"
                                        class="block w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm shadow-inner focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">IFSC</label>
                                    <input name="ifsc" type="text" value="{{ (int) old('_bank_id') === (int) $bank->id ? old('ifsc', $bank->ifsc) : $bank->ifsc }}" required maxlength="11"
                                        class="font-mono uppercase block w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm shadow-inner focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Account number</label>
                                    <input name="account_no" type="text" value="{{ (int) old('_bank_id') === (int) $bank->id ? old('account_no', $bank->account_no) : $bank->account_no }}" required maxlength="32"
                                        class="font-mono block w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm shadow-inner focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Status</label>
                                    <select name="status" class="block w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm shadow-inner focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                                        <option value="active" @selected((int) old('_bank_id') === (int) $bank->id ? old('status', $bank->status) === 'active' : $bank->status === 'active')>Active</option>
                                        <option value="inactive" @selected((int) old('_bank_id') === (int) $bank->id ? old('status', $bank->status) === 'inactive' : $bank->status === 'inactive')>Inactive</option>
                                    </select>
                                </div>
                                <div class="flex items-center gap-2 pt-6">
                                    <input id="primary-{{ $bank->id }}" name="is_primary" type="checkbox" value="1" @checked((int) old('_bank_id') === (int) $bank->id ? (bool) old('is_primary', false) : $bank->is_primary)
                                        class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                    <label for="primary-{{ $bank->id }}" class="text-sm font-medium text-slate-700">Primary account</label>
                                </div>
                            </div>
                            @if ((int) old('_bank_id') === (int) $bank->id)
                                @foreach (['bank_name', 'account_holder_name', 'ifsc', 'account_no', 'status'] as $field)
                                    @error($field)
                                        <p class="text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                @endforeach
                            @endif
                            <div class="flex flex-wrap items-center gap-3">
                                <button type="submit" class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-bold text-white shadow hover:bg-indigo-500">Save changes</button>
                                <button type="button" onclick="document.getElementById('edit-{{ $bank->id }}').classList.add('hidden')" class="text-sm font-semibold text-slate-600 hover:text-slate-900">Cancel</button>
                            </div>
                        </form>
                        <form method="POST" action="{{ route('banks.destroy', $bank) }}" class="mt-4 border-t border-slate-100 pt-4" onsubmit="return confirm('Remove this bank account?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm font-bold text-red-600 hover:text-red-500">Remove account</button>
                        </form>
                    </div>
                </article>
            @endforeach
        </div>
    @endif

    <div id="add-bank-panel" class="{{ $openAddPanel ? '' : 'hidden' }} mt-8 overflow-hidden rounded-3xl border border-violet-200 bg-gradient-to-br from-white via-violet-50/40 to-indigo-50/50 shadow-lg ring-1 ring-violet-900/5">
        <div class="flex items-start justify-between gap-4 border-b border-violet-100 bg-white/60 px-6 py-5 backdrop-blur-sm sm:px-8">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Add bank account</h2>
                <p class="mt-1 text-sm text-slate-600">Enter payout details. You can set a primary account anytime.</p>
            </div>
            <button type="button" id="btn-close-add-bank" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:bg-slate-50" aria-label="Close">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="px-6 py-6 sm:px-8 sm:py-8">
            <form method="POST" action="{{ route('banks.store') }}" class="space-y-6">
                @csrf
                <input type="hidden" name="_intent" value="create">
                <div class="grid gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="bank_name" class="mb-1.5 block text-sm font-semibold text-slate-800">Bank name</label>
                        <input id="bank_name" name="bank_name" type="text" value="{{ old('_intent') === 'create' ? old('bank_name') : '' }}" required maxlength="120"
                            class="block w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm shadow-inner focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 @error('bank_name') border-red-400 ring-2 ring-red-100 @enderror">
                        @error('bank_name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label for="account_holder_name" class="mb-1.5 block text-sm font-semibold text-slate-800">Account holder name</label>
                        <input id="account_holder_name" name="account_holder_name" type="text" value="{{ old('_intent') === 'create' ? old('account_holder_name') : '' }}" required maxlength="120"
                            class="block w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm shadow-inner focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 @error('account_holder_name') border-red-400 ring-2 ring-red-100 @enderror">
                        @error('account_holder_name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="ifsc" class="mb-1.5 block text-sm font-semibold text-slate-800">IFSC</label>
                        <input id="ifsc" name="ifsc" type="text" value="{{ old('_intent') === 'create' ? old('ifsc') : '' }}" required maxlength="11" autocomplete="off" placeholder="e.g. HDFC0001234"
                            class="font-mono uppercase block w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm shadow-inner focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 @error('ifsc') border-red-400 ring-2 ring-red-100 @enderror">
                        @error('ifsc')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="account_no" class="mb-1.5 block text-sm font-semibold text-slate-800">Account number</label>
                        <input id="account_no" name="account_no" type="text" value="{{ old('_intent') === 'create' ? old('account_no') : '' }}" required maxlength="32" autocomplete="off"
                            class="font-mono block w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm shadow-inner focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 @error('account_no') border-red-400 ring-2 ring-red-100 @enderror">
                        @error('account_no')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex items-center gap-3 sm:col-span-2">
                        <input id="is_primary_new" name="is_primary" type="checkbox" value="1" @checked(old('_intent') === 'create' && (bool) old('is_primary', false))
                            class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <label for="is_primary_new" class="text-sm font-medium text-slate-800">Set as primary account</label>
                    </div>
                </div>
                <div class="flex flex-wrap gap-3">
                    <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-gradient-to-r from-violet-600 to-indigo-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-900/20 transition hover:brightness-110">
                        Save bank account
                    </button>
                    <button type="button" id="btn-cancel-add-bank" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-6 py-3 text-sm font-bold text-slate-700 shadow-sm hover:bg-slate-50">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        (function () {
            var panel = document.getElementById('add-bank-panel');
            var openers = [document.getElementById('btn-open-add-bank')].concat(Array.prototype.slice.call(document.querySelectorAll('[data-open-add-bank]')));
            var closeBtn = document.getElementById('btn-close-add-bank');
            var cancelBtn = document.getElementById('btn-cancel-add-bank');
            function openPanel() {
                if (!panel) return;
                panel.classList.remove('hidden');
                var first = panel.querySelector('input:not([type="hidden"])');
                if (first) { try { first.focus(); } catch (e) {} }
                try { panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' }); } catch (e) {}
            }
            function closePanel() {
                if (!panel) return;
                panel.classList.add('hidden');
            }
            openers.forEach(function (btn) {
                if (btn) btn.addEventListener('click', openPanel);
            });
            if (closeBtn) closeBtn.addEventListener('click', closePanel);
            if (cancelBtn) cancelBtn.addEventListener('click', closePanel);
        })();
    </script>
@endsection
