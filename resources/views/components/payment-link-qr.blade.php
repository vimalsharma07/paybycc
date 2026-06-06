@props(['paymentLink', 'prefix' => 'pl'])

@php
    $publicUrl = $paymentLink->publicUrl();
    $canvasId = $prefix.'-qr-canvas';
    $wrapId = $prefix.'-qr-wrap';
    $generateId = $prefix.'-qr-generate';
    $downloadId = $prefix.'-qr-download';
    $errorId = $prefix.'-qr-error';
@endphp

<div {{ $attributes->merge(['class' => 'space-y-4']) }}>
    <div class="flex flex-wrap gap-2">
        <button type="button" id="{{ $generateId }}"
            class="js-payment-link-qr-generate rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-bold text-white shadow hover:bg-indigo-500 disabled:cursor-wait disabled:opacity-70"
            data-qr-url="{{ $publicUrl }}"
            data-qr-wrap="{{ $wrapId }}"
            data-qr-canvas="{{ $canvasId }}"
            data-qr-download="{{ $downloadId }}"
            data-qr-error="{{ $errorId }}">
            Generate QR
        </button>
        <a id="{{ $downloadId }}" href="#" download="payment-qr.png"
            class="hidden rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-800 shadow-sm hover:bg-slate-50">
            Download QR
        </a>
    </div>

    <p id="{{ $errorId }}" class="hidden text-sm text-rose-600"></p>

    <div id="{{ $wrapId }}" class="hidden rounded-2xl border border-slate-200 bg-white p-4 text-center shadow-inner">
        <canvas id="{{ $canvasId }}" class="mx-auto max-w-full"></canvas>
        <p class="mt-3 text-xs text-slate-500">Scan to pay · client enters amount</p>
    </div>
</div>
