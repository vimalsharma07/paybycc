@props(['status'])
@php
    $s = strtolower((string) $status);
    $class = match ($s) {
        'completed', 'paid' => 'bg-emerald-100 text-emerald-900 ring-emerald-500/30',
        'pending', 'authorized' => 'bg-amber-100 text-amber-900 ring-amber-500/30',
        'failed' => 'bg-rose-100 text-rose-900 ring-rose-500/30',
        default => 'bg-slate-100 text-slate-800 ring-slate-400/30',
    };
@endphp
<span {{ $attributes->merge(['class' => 'inline-flex rounded-full px-2.5 py-0.5 text-xs font-bold capitalize ring-1 '.$class]) }}>
    {{ $status }}
</span>
