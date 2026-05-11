@props([
    'variant' => 'dark',
    'size' => 'md',
])

@php
    $sizeClasses = match ($size) {
        'sm' => 'text-base gap-0 sm:gap-0.5',
        'lg' => 'text-2xl gap-0.5 sm:text-3xl sm:gap-1',
        default => 'text-lg gap-0 sm:text-xl sm:gap-0.5',
    };

    if ($variant === 'light') {
        $pay = 'text-indigo-600';
        $by = 'text-violet-600';
        $cc = 'text-amber-600';
    } else {
        $pay = 'text-cyan-300';
        $by = 'text-fuchsia-300';
        $cc = 'text-amber-300';
    }
@endphp

<span {{ $attributes->merge(['class' => 'brand-wordmark inline-flex items-baseline font-extrabold tracking-tight '.$sizeClasses]) }}>
    <span class="{{ $pay }}">Pay</span>
    <span class="{{ $by }}">By</span>
    <span class="{{ $cc }}">Cc</span>
</span>
