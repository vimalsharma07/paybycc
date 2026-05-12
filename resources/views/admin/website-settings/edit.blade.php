@extends('layouts.admin')

@section('title', 'Website settings — '.config('app.name'))

@section('content')
    <div class="mb-8">
        <a href="{{ route('admin.dashboard') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">← Admin dashboard</a>
        <h1 class="mt-4 text-2xl font-semibold text-slate-900">Website settings</h1>
        <p class="mt-1 text-sm text-slate-600">Branding, contact details, and social links appear across the public site and app chrome where used.</p>
    </div>

    <div class="max-w-4xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <form method="POST" action="{{ route('admin.website-settings.update') }}" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @method('PATCH')

            <div>
                <h2 class="text-sm font-bold uppercase tracking-wide text-slate-500">Brand</h2>
                <div class="mt-4 grid gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="site_name" class="mb-1 block text-sm font-medium text-slate-700">Site name</label>
                        <input id="site_name" name="site_name" type="text" value="{{ old('site_name', $settings->site_name) }}" required maxlength="160"
                            class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('site_name') border-red-500 @enderror">
                        @error('site_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label for="tagline" class="mb-1 block text-sm font-medium text-slate-700">Tagline</label>
                        <input id="tagline" name="tagline" type="text" value="{{ old('tagline', $settings->tagline) }}" maxlength="255" placeholder="Short line for SEO / footers"
                            class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('tagline') border-red-500 @enderror">
                        @error('tagline')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label for="logo" class="mb-1 block text-sm font-medium text-slate-700">Logo</label>
                        <p class="mb-2 text-xs text-slate-500">JPEG, PNG, WebP, or SVG — max 4&nbsp;MB. Saved under <code class="rounded bg-slate-100 px-1">public/uploads/site/</code> (full URL stored in database).</p>
                        @if ($settings->logoUrl())
                            <div class="mb-3 flex items-center gap-4">
                                <img src="{{ $settings->logoUrl() }}" alt="Current logo" class="h-12 w-auto max-w-[200px] rounded border border-slate-200 bg-white object-contain p-1" loading="lazy" />
                                <span class="text-xs text-slate-500 break-all">{{ $settings->logo_path }}</span>
                            </div>
                        @endif
                        <input id="logo" name="logo" type="file" accept=".jpg,.jpeg,.png,.webp,.svg,image/*"
                            class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100 @error('logo') border border-red-500 rounded-lg @enderror">
                        @error('logo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div>
                <h2 class="text-sm font-bold uppercase tracking-wide text-slate-500">Contact</h2>
                <div class="mt-4 grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="email" class="mb-1 block text-sm font-medium text-slate-700">General email</label>
                        <input id="email" name="email" type="email" value="{{ old('email', $settings->email) }}" maxlength="255" placeholder="hello@example.com"
                            class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="support_email" class="mb-1 block text-sm font-medium text-slate-700">Support email</label>
                        <input id="support_email" name="support_email" type="email" value="{{ old('support_email', $settings->support_email) }}" maxlength="255" placeholder="support@example.com"
                            class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('support_email') border-red-500 @enderror">
                        @error('support_email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label for="phone" class="mb-1 block text-sm font-medium text-slate-700">Phone</label>
                        <input id="phone" name="phone" type="text" value="{{ old('phone', $settings->phone) }}" maxlength="40" placeholder="+91 …"
                            class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('phone') border-red-500 @enderror">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label for="address" class="mb-1 block text-sm font-medium text-slate-700">Address</label>
                        <textarea id="address" name="address" rows="3" maxlength="2000" placeholder="Registered business address"
                            class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('address') border-red-500 @enderror">{{ old('address', $settings->address) }}</textarea>
                        @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div>
                <h2 class="text-sm font-bold uppercase tracking-wide text-slate-500">Social</h2>
                <p class="mt-1 text-xs text-slate-500">Full URLs (https://…). Leave blank to hide.</p>
                <div class="mt-4 grid gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="instagram_url" class="mb-1 block text-sm font-medium text-slate-700">Instagram</label>
                        <input id="instagram_url" name="instagram_url" type="url" value="{{ old('instagram_url', $settings->instagram_url) }}" maxlength="500" placeholder="https://instagram.com/…"
                            class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('instagram_url') border-red-500 @enderror">
                        @error('instagram_url')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label for="linkedin_url" class="mb-1 block text-sm font-medium text-slate-700">LinkedIn</label>
                        <input id="linkedin_url" name="linkedin_url" type="url" value="{{ old('linkedin_url', $settings->linkedin_url) }}" maxlength="500" placeholder="https://linkedin.com/…"
                            class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('linkedin_url') border-red-500 @enderror">
                        @error('linkedin_url')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label for="facebook_url" class="mb-1 block text-sm font-medium text-slate-700">Facebook</label>
                        <input id="facebook_url" name="facebook_url" type="url" value="{{ old('facebook_url', $settings->facebook_url) }}" maxlength="500" placeholder="https://facebook.com/…"
                            class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('facebook_url') border-red-500 @enderror">
                        @error('facebook_url')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label for="twitter_url" class="mb-1 block text-sm font-medium text-slate-700">X (Twitter)</label>
                        <input id="twitter_url" name="twitter_url" type="url" value="{{ old('twitter_url', $settings->twitter_url) }}" maxlength="500" placeholder="https://x.com/…"
                            class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('twitter_url') border-red-500 @enderror">
                        @error('twitter_url')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div>
                <label for="status" class="mb-1 block text-sm font-medium text-slate-700">Settings row status</label>
                <select id="status" name="status" class="block max-w-xs rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('status') border-red-500 @enderror">
                    <option value="active" @selected(old('status', $settings->status) === 'active')>Active</option>
                    <option value="inactive" @selected(old('status', $settings->status) === 'inactive')>Inactive</option>
                </select>
                <p class="mt-1 text-xs text-slate-500">Inactive is reserved for future use (e.g. maintenance); content still loads for now.</p>
                @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="inline-flex rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2">
                Save settings
            </button>
        </form>
    </div>
@endsection
