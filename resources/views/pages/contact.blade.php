@extends('layouts.marketing')

@section('title', 'Contact us — '.$siteSettings->displayName())
@section('meta_description', 'Reach '.$siteSettings->displayName().' for support, partnerships, or privacy questions.')

@section('content')
    <div class="mx-auto grid max-w-6xl gap-12 px-4 py-16 lg:grid-cols-2 lg:py-24">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wider text-indigo-400">Contact</p>
            <h1 class="mt-3 text-4xl font-bold tracking-tight text-white">We’re listening.</h1>
            <p class="mt-6 text-lg leading-relaxed text-slate-400">Billing questions, privacy concerns, or feedback about tuition &amp; household payments — send a note. We aim to reply within a few business days.</p>

            <div class="mt-10 space-y-6 rounded-2xl border border-white/10 bg-white/5 p-8">
                @if ($siteSettings->support_email || $siteSettings->email)
                    <div class="flex gap-4">
                        <span class="text-2xl">📧</span>
                        <div>
                            <p class="font-semibold text-white">Email</p>
                            <p class="mt-1 text-sm text-slate-400">Reach us directly, or use the form for structured requests.</p>
                            <ul class="mt-3 space-y-1 text-sm">
                                @if ($siteSettings->support_email)
                                    <li><a href="mailto:{{ $siteSettings->support_email }}" class="font-medium text-indigo-400 underline-offset-2 hover:underline">{{ $siteSettings->support_email }}</a> <span class="text-slate-500">· support</span></li>
                                @endif
                                @if ($siteSettings->email && $siteSettings->email !== $siteSettings->support_email)
                                    <li><a href="mailto:{{ $siteSettings->email }}" class="font-medium text-indigo-400 underline-offset-2 hover:underline">{{ $siteSettings->email }}</a> <span class="text-slate-500">· general</span></li>
                                @endif
                            </ul>
                        </div>
                    </div>
                @else
                    <div class="flex gap-4">
                        <span class="text-2xl">📧</span>
                        <div>
                            <p class="font-semibold text-white">Email</p>
                            <p class="mt-1 text-sm text-slate-400">Use this form for the fastest response. Add support and general addresses in <strong class="text-slate-300">Admin → Website</strong> so they appear here.</p>
                        </div>
                    </div>
                @endif
                @if (filled($siteSettings->phone) || filled($siteSettings->address))
                    <div class="flex gap-4">
                        <span class="text-2xl">📍</span>
                        <div>
                            <p class="font-semibold text-white">Location &amp; phone</p>
                            @if (filled($siteSettings->phone))
                                <p class="mt-1 text-sm text-slate-400"><a href="tel:{{ preg_replace('/\s+/', '', $siteSettings->phone) }}" class="font-medium text-indigo-400 underline-offset-2 hover:underline">{{ $siteSettings->phone }}</a></p>
                            @endif
                            @if (filled($siteSettings->address))
                                <p class="mt-2 whitespace-pre-line text-sm text-slate-300">{{ $siteSettings->address }}</p>
                            @endif
                        </div>
                    </div>
                @endif
                <div class="flex gap-4">
                    <span class="text-2xl">🔒</span>
                    <div>
                        <p class="font-semibold text-white">Privacy &amp; data</p>
                        <p class="mt-1 text-sm text-slate-400">For privacy-specific requests, mention “Privacy” in the subject line so we route your message correctly.</p>
                    </div>
                </div>
            </div>

            <img src="https://images.unsplash.com/photo-1423666639421-p9a89bdca83b?auto=format&amp;fit=crop&amp;w=800&amp;q=80" alt="" class="mt-10 hidden rounded-3xl border border-white/10 object-cover lg:block" loading="lazy" />
        </div>

        <div class="rounded-3xl border border-white/10 bg-slate-900/60 p-8 shadow-xl backdrop-blur-sm">
            <form method="POST" action="{{ route('contact.store') }}" class="space-y-6">
                @csrf
                <div>
                    <label for="name" class="mb-2 block text-sm font-medium text-slate-300">Name</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required maxlength="120"
                        class="w-full rounded-xl border border-white/10 bg-slate-950/80 px-4 py-3 text-white placeholder-slate-600 outline-none ring-indigo-500/0 transition focus:border-indigo-500/50 focus:ring-2 focus:ring-indigo-500/30" />
                    @error('name')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="email" class="mb-2 block text-sm font-medium text-slate-300">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required maxlength="255"
                        class="w-full rounded-xl border border-white/10 bg-slate-950/80 px-4 py-3 text-white placeholder-slate-600 outline-none focus:border-indigo-500/50 focus:ring-2 focus:ring-indigo-500/30" />
                    @error('email')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="subject" class="mb-2 block text-sm font-medium text-slate-300">Subject <span class="font-normal text-slate-500">(optional)</span></label>
                    <input id="subject" name="subject" type="text" value="{{ old('subject') }}" maxlength="200"
                        class="w-full rounded-xl border border-white/10 bg-slate-950/80 px-4 py-3 text-white placeholder-slate-600 outline-none focus:border-indigo-500/50 focus:ring-2 focus:ring-indigo-500/30" />
                    @error('subject')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="message" class="mb-2 block text-sm font-medium text-slate-300">Message</label>
                    <textarea id="message" name="message" rows="6" required maxlength="5000"
                        class="w-full resize-y rounded-xl border border-white/10 bg-slate-950/80 px-4 py-3 text-white placeholder-slate-600 outline-none focus:border-indigo-500/50 focus:ring-2 focus:ring-indigo-500/30">{{ old('message') }}</textarea>
                    @error('message')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-indigo-500 to-violet-600 py-3.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/25 transition hover:brightness-110">
                    Send message
                </button>
            </form>
        </div>
    </div>
@endsection
