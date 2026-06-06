@props(['paymentLink', 'prefix' => 'pl'])

@php
    $publicUrl = $paymentLink->publicUrl();
    $canvasId = $prefix.'-qr-canvas';
    $wrapId = $prefix.'-qr-wrap';
    $generateId = $prefix.'-qr-generate';
    $downloadId = $prefix.'-qr-download';
    $urlInputId = $prefix.'-qr-url';
@endphp

<div {{ $attributes->merge(['class' => 'space-y-4']) }}>
    <input id="{{ $urlInputId }}" type="hidden" value="{{ $publicUrl }}">

    <div class="flex flex-wrap gap-2">
        <button type="button" id="{{ $generateId }}"
            class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-bold text-white shadow hover:bg-indigo-500">
            Generate QR
        </button>
        <a id="{{ $downloadId }}" href="#" download="payment-qr.png"
            class="hidden rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-800 shadow-sm hover:bg-slate-50">
            Download QR
        </a>
    </div>

    <div id="{{ $wrapId }}" class="hidden rounded-2xl border border-slate-200 bg-white p-4 text-center shadow-inner">
        <canvas id="{{ $canvasId }}" class="mx-auto max-w-full"></canvas>
        <p class="mt-3 text-xs text-slate-500">Scan to pay · client enters amount</p>
    </div>
</div>

@once
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
    <script>
        document.querySelectorAll('[id$="-qr-generate"]').forEach((btn) => {
            const prefix = btn.id.replace('-qr-generate', '');
            const urlInput = document.getElementById(prefix + '-qr-url');
            const wrap = document.getElementById(prefix + '-qr-wrap');
            const canvas = document.getElementById(prefix + '-qr-canvas');
            const download = document.getElementById(prefix + '-qr-download');
            if (!urlInput || !wrap || !canvas || !download) return;

            btn.addEventListener('click', () => {
                const url = urlInput.value;
                if (!url || typeof QRCode === 'undefined') return;

                QRCode.toCanvas(canvas, url, { width: 220, margin: 2 }, (error) => {
                    if (error) return;
                    wrap.classList.remove('hidden');
                    download.classList.remove('hidden');
                    download.href = canvas.toDataURL('image/png');
                });
            });

            download.addEventListener('click', (event) => {
                if (!download.href || download.href === '#') {
                    event.preventDefault();
                }
            });
        });
    </script>
    @endpush
@endonce
