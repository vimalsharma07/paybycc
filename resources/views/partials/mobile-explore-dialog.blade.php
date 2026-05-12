{{--
  Mobile-only bottom sheet with square icon tiles.
  @include('partials.mobile-explore-dialog', [
    'dialogId' => 'mobile-explore-marketing',
    'skin' => 'dark', // 'light' for app shell
    'tiles' => [ ['href' => url, 'label' => 'Privacy', 'icon' => 'shield'], ... ['logout' => true, 'label' => 'Log out', 'icon' => 'logout'] ],
  ])
--}}
@php
    $dialogId = $dialogId ?? 'mobile-explore';
    $skin = $skin ?? 'dark';
    $tiles = $tiles ?? [];
    $isLight = $skin === 'light';
@endphp

<dialog id="{{ $dialogId }}" class="mobile-explore-dialog {{ $isLight ? 'mobile-explore-dialog--light' : '' }} lg:hidden" aria-labelledby="{{ $dialogId }}-title">
    <div class="mobile-explore-dialog__inner {{ $isLight ? 'bg-white text-slate-900 ring-slate-200' : 'bg-slate-900 text-slate-100 ring-white/10' }} ring-1">
        <div class="flex items-center justify-between gap-3 border-b {{ $isLight ? 'border-slate-200' : 'border-white/10' }} px-4 py-3">
            <h2 id="{{ $dialogId }}-title" class="text-base font-bold tracking-tight">Explore</h2>
            <button type="button" class="mobile-explore-close flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $isLight ? 'bg-slate-100 text-slate-700 hover:bg-slate-200' : 'bg-white/10 text-white hover:bg-white/15' }}" data-close-dialog="{{ $dialogId }}" aria-label="Close menu">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="max-h-[65vh] overflow-y-auto overscroll-contain px-3 py-4 sm:px-4">
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                @foreach ($tiles as $tile)
                    @if (! empty($tile['logout']))
                        <form method="POST" action="{{ route('logout') }}" class="block min-h-0">
                            @csrf
                            <button type="submit" class="mobile-explore-tile h-full w-full {{ $isLight ? 'mobile-explore-tile--light border-red-200 bg-red-50 text-red-800 hover:border-red-300 hover:bg-red-100' : 'border-red-500/30 bg-red-500/10 text-red-200 hover:border-red-400/50 hover:bg-red-500/20' }}">
                                <span class="mobile-explore-tile__icon {{ $isLight ? 'bg-red-100 text-red-700' : 'bg-red-500/20 text-red-100' }}">
                                    @include('partials.mobile-explore-icon', ['name' => $tile['icon'] ?? 'logout'])
                                </span>
                                <span class="mt-2 text-center text-[11px] font-semibold leading-tight sm:text-xs">{{ $tile['label'] ?? 'Log out' }}</span>
                            </button>
                        </form>
                    @else
                        <a href="{{ $tile['href'] }}" class="mobile-explore-tile {{ $isLight ? 'mobile-explore-tile--light border-slate-200 bg-slate-50 hover:border-indigo-300 hover:bg-indigo-50' : 'border-white/10 bg-white/[0.04] hover:border-cyan-400/30 hover:bg-white/[0.08]' }}">
                            <span class="mobile-explore-tile__icon {{ $isLight ? 'bg-indigo-100 text-indigo-700' : 'bg-cyan-500/15 text-cyan-200' }}">
                                @include('partials.mobile-explore-icon', ['name' => $tile['icon'] ?? 'info'])
                            </span>
                            <span class="mt-2 text-center text-[11px] font-semibold leading-tight sm:text-xs">{{ $tile['label'] ?? 'Link' }}</span>
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</dialog>

<script>
    (function () {
        var id = @json($dialogId);
        var dlg = document.getElementById(id);
        if (!dlg) return;
        function closeIt() {
            try { dlg.close(); } catch (e) {}
        }
        dlg.addEventListener('click', function (ev) {
            if (ev.target === dlg) closeIt();
        });
        document.querySelectorAll('[data-open-dialog="' + id + '"]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                if (typeof dlg.showModal === 'function') dlg.showModal();
            });
        });
        document.querySelectorAll('[data-close-dialog="' + id + '"]').forEach(function (btn) {
            btn.addEventListener('click', closeIt);
        });
    })();
</script>
