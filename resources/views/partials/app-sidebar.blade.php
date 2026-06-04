@php
    use App\Support\AppNavigation;
    $navUser = auth()->user();
    $navItems = AppNavigation::main($navUser);
@endphp
<aside class="app-sidebar hidden w-64 shrink-0 flex-col border-r border-slate-200/90 bg-white lg:flex" aria-label="Account navigation">
    <div class="border-b border-slate-100 px-5 py-5">
        @include('partials.site-brand-header', ['href' => route('dashboard'), 'variant' => 'light', 'wrapperClass' => ''])
        <p class="mt-4 truncate text-sm font-semibold text-slate-900">{{ $navUser->name }}</p>
        <p class="truncate font-mono text-xs text-slate-500">{{ $navUser->user_code }}</p>
    </div>

    <nav class="flex flex-1 flex-col gap-0.5 overflow-y-auto p-3">
        @foreach ($navItems as $item)
            <a href="{{ $item['href'] }}"
                class="app-sidebar-link flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition {{ $item['active'] ? 'bg-gradient-to-r from-indigo-600 to-violet-600 text-white shadow-md shadow-indigo-900/15' : 'text-slate-700 hover:bg-indigo-50 hover:text-indigo-900' }}">
                @include('partials.app-nav-icon', ['icon' => $item['icon'], 'active' => $item['active']])
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    @if ($navUser->canUsePlatform())
        <div class="border-t border-slate-100 p-3">
            <a href="{{ route('payments.create') }}" class="flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-cyan-500 via-indigo-600 to-violet-600 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-500/25 transition hover:brightness-110">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Pay now
            </a>
        </div>
    @endif

    <div class="border-t border-slate-100 p-3">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left text-sm font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-slate-900">
                @include('partials.app-nav-icon', ['icon' => 'logout', 'active' => false])
                Log out
            </button>
        </form>
    </div>
</aside>
