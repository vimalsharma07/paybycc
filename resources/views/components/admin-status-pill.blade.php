@props([
    'status',
    'size' => 'sm',
])

@php
    $raw = trim((string) $status);
    $s = strtolower($raw);
    $label = $raw === '' ? '—' : \Illuminate\Support\Str::title(str_replace('_', ' ', $raw));

    $successStates = ['completed', 'success', 'succeeded', 'paid', 'active'];
    $failedStates = ['failed', 'error', 'declined', 'rejected', 'denied', 'expired', 'terminated', 'cancelled', 'canceled'];
    $pendingStates = ['pending', 'processing', 'awaiting'];

    $palette = match (true) {
        in_array($s, $successStates, true) => 'bg-emerald-100 text-emerald-900 ring-1 ring-emerald-500/35 shadow-sm shadow-emerald-900/5',
        in_array($s, $failedStates, true) => 'bg-rose-100 text-rose-900 ring-1 ring-rose-500/35 shadow-sm shadow-rose-900/5',
        in_array($s, $pendingStates, true) => 'bg-amber-100 text-amber-900 ring-1 ring-amber-500/35 shadow-sm shadow-amber-900/5',
        default => 'bg-slate-100 text-slate-800 ring-1 ring-slate-400/40',
    };

    $sizeClasses = $size === 'xs' ? 'px-2 py-0.5 text-[10px] leading-tight' : 'px-2.5 py-1 text-xs leading-tight';
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex max-w-full items-center truncate rounded-full font-semibold '.$sizeClasses.' '.$palette]) }}>{{ $label }}</span>
