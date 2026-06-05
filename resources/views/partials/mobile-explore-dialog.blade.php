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
    $navTiles = [];
    $logoutTile = null;
    foreach ($tiles as $tile) {
        if (! empty($tile['logout'])) {
            $logoutTile = $tile;
        } else {
            $navTiles[] = $tile;
        }
    }
@endphp

<dialog id="{{ $dialogId }}" class="mobile-explore-dialog {{ $isLight ? 'mobile-explore-dialog--light' : '' }} lg:hidden" aria-labelledby="{{ $dialogId }}-title">
    <div class="mobile-explore-dialog__inner {{ $isLight ? 'bg-white text-slate-900 ring-slate-200' : 'bg-slate-900 text-slate-100 ring-white/10' }} ring-1">
        <div class="flex shrink-0 items-center justify-between gap-3 border-b {{ $isLight ? 'border-slate-200' : 'border-white/10' }} px-4 py-3">
            <h2 id="{{ $dialogId }}-title" class="text-base font-bold tracking-tight">Menu</h2>
            <button type="button" class="mobile-explore-close flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $isLight ? 'bg-slate-100 text-slate-700 hover:bg-slate-200' : 'bg-white/10 text-white hover:bg-white/15' }}" data-close-dialog="{{ $dialogId }}" aria-label="Close menu">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="mobile-explore-dialog__body px-3 py-3 sm:px-4">
            <div class="mobile-explore-dialog__grid grid grid-cols-3 gap-2">
                @foreach ($navTiles as $tile)
                    <a href="{{ $tile['href'] }}" class="mobile-explore-tile {{ $isLight ? 'mobile-explore-tile--light border-slate-200 bg-slate-50 hover:border-indigo-300 hover:bg-indigo-50' : 'border-white/10 bg-white/[0.04] hover:border-cyan-400/30 hover:bg-white/[0.08]' }}">
                        <span class="mobile-explore-tile__icon {{ $isLight ? 'bg-indigo-100 text-indigo-700' : 'bg-cyan-500/15 text-cyan-200' }}">
                            @include('partials.mobile-explore-icon', ['name' => $tile['icon'] ?? 'info'])
                        </span>
                        <span class="mt-2 text-center text-[11px] font-semibold leading-tight sm:text-xs">{{ $tile['label'] ?? 'Link' }}</span>
                    </a>
                @endforeach
            </div>
        </div>
        @if ($logoutTile)
            <div class="mobile-explore-dialog__footer border-t {{ $isLight ? 'border-slate-200 bg-slate-50/90' : 'border-white/10 bg-slate-950/40' }} px-4 py-3">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="mobile-explore-logout flex w-full items-center justify-center gap-2.5 rounded-xl border px-4 py-3.5 text-sm font-bold {{ $isLight ? 'border-red-200 bg-red-50 text-red-800 hover:bg-red-100' : 'border-red-500/35 bg-red-500/10 text-red-200 hover:bg-red-500/20' }}">
                        <span class="flex h-9 w-9 items-center justify-center rounded-lg {{ $isLight ? 'bg-red-100 text-red-700' : 'bg-red-500/20 text-red-100' }}">
                            @include('partials.mobile-explore-icon', ['name' => $logoutTile['icon'] ?? 'logout'])
                        </span>
                        {{ $logoutTile['label'] ?? 'Log out' }}
                    </button>
                </form>
            </div>
        @endif
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
