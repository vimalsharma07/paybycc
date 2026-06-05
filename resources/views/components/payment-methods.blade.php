@props([
    'variant' => 'dark',
    'showLabel' => true,
    'size' => 'md',
])

@php
    $methods = [
        ['code' => 'upi', 'label' => 'UPI', 'sub' => 'GPay · PhonePe · Paytm', 'tone' => 'emerald'],
        ['code' => 'cc', 'label' => 'Credit card', 'sub' => 'Visa · Mastercard · RuPay', 'tone' => 'indigo'],
        ['code' => 'dc', 'label' => 'Debit card', 'sub' => 'All major banks', 'tone' => 'violet'],
        ['code' => 'nb', 'label' => 'Net banking', 'sub' => '50+ banks', 'tone' => 'cyan'],
        ['code' => 'wallet', 'label' => 'Wallets', 'sub' => 'Paytm & more', 'tone' => 'fuchsia'],
    ];

    $isDark = $variant === 'dark';
    $isCompact = $size === 'sm';
@endphp

<div {{ $attributes->class(['pay-methods', $isCompact ? 'pay-methods--compact' : '']) }}>
    @if ($showLabel)
        <p class="{{ $isDark ? 'text-xs font-bold uppercase tracking-widest text-slate-500' : 'text-xs font-bold uppercase tracking-widest text-slate-500' }}">
            {{ $slot->isEmpty() ? 'Accept payments via' : $slot }}
        </p>
    @endif
    <ul class="pay-methods__list {{ $showLabel ? 'mt-3' : '' }} flex flex-wrap gap-2">
        @foreach ($methods as $method)
            <li class="pay-method-pill pay-method-pill--{{ $method['tone'] }} {{ $isDark ? 'pay-method-pill--dark' : 'pay-method-pill--light' }} {{ $isCompact ? 'pay-method-pill--sm' : '' }}">
                <span class="pay-method-pill__code" aria-hidden="true">{{ strtoupper($method['code']) }}</span>
                <span class="pay-method-pill__text">
                    <span class="pay-method-pill__label">{{ $method['label'] }}</span>
                    @unless ($isCompact)
                        <span class="pay-method-pill__sub">{{ $method['sub'] }}</span>
                    @endunless
                </span>
            </li>
        @endforeach
    </ul>
</div>
