@props([
    'status',
    'type' => 'order',
    'size' => 'sm',
])

@php
    use App\Constants\OrderStatuses;
    use App\Constants\TransactionStatuses;

    $slug = match ($type) {
        'transaction' => TransactionStatuses::slug($status),
        'settlement' => OrderStatuses::statusSlug($status, 'settlement'),
        'payment' => OrderStatuses::statusSlug($status, 'payment'),
        'safe' => OrderStatuses::statusSlug($status, 'safe'),
        default => OrderStatuses::statusSlug($status, 'order'),
    };

    $label = match ($type) {
        'transaction' => TransactionStatuses::label($status),
        'payment' => OrderStatuses::paymentLabel($status),
        'settlement' => OrderStatuses::settlementLabel($status),
        'safe' => OrderStatuses::safeLabel($status),
        default => OrderStatuses::orderLabel($status),
    };

    $successStates = ['completed', 'success', 'succeeded', 'paid', 'active', 'safe', 'eligible', 'settled', 'accepted'];
    $failedStates = ['failed', 'error', 'declined', 'rejected', 'denied', 'expired', 'terminated', 'cancelled', 'canceled'];
    $pendingStates = ['pending', 'processing', 'awaiting', 'pending_review', 'hold', 'created'];

    $palette = match (true) {
        in_array($slug, $successStates, true) => 'bg-emerald-100 text-emerald-900 ring-1 ring-emerald-500/35 shadow-sm shadow-emerald-900/5',
        in_array($slug, $failedStates, true) => 'bg-rose-100 text-rose-900 ring-1 ring-rose-500/35 shadow-sm shadow-rose-900/5',
        in_array($slug, $pendingStates, true) => 'bg-amber-100 text-amber-900 ring-1 ring-amber-500/35 shadow-sm shadow-amber-900/5',
        default => 'bg-slate-100 text-slate-800 ring-1 ring-slate-400/40',
    };

    $sizeClasses = $size === 'xs' ? 'px-2 py-0.5 text-[10px] leading-tight' : 'px-2.5 py-1 text-xs leading-tight';
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex max-w-full items-center truncate rounded-full font-semibold '.$sizeClasses.' '.$palette]) }}>{{ $label }}</span>
