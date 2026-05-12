{{-- Expects $siteSettings (WebsiteSetting), $href (url), $variant: 'dark' | 'light' --}}
@php
    $logo = $siteSettings->logoUrl();
    $name = $siteSettings->displayName();
    $wordmarkVariant = $variant === 'dark' ? 'dark' : 'light';
    $wmSize = $logoSize ?? 'md';
@endphp
<a href="{{ $href }}" class="flex shrink-0 items-center transition hover:opacity-90 {{ trim($wrapperClass ?? '') }}">
    @if ($logo)
        <img src="{{ $logo }}" alt="{{ $name }}" class="h-8 w-auto max-w-[180px] object-contain object-left sm:h-9 {{ $imgClass ?? '' }}" width="180" height="36" loading="eager" decoding="async" />
    @else
        <x-brand-wordmark :variant="$wordmarkVariant" :size="$wmSize" class="leading-none" />
    @endif
</a>
