@php
    $qrLib = public_path('js/vendor/qrcode.min.js');
    $qrLibVersion = is_file($qrLib) ? (string) filemtime($qrLib) : '1';
@endphp
<script>
(function () {
    var qrLibUrl = @json(asset('js/vendor/qrcode.min.js').'?v='.$qrLibVersion);
    var qrLibPromise = null;

    function loadQrLibrary() {
        if (typeof window.QRCode === 'function') {
            return Promise.resolve();
        }

        if (qrLibPromise) {
            return qrLibPromise;
        }

        qrLibPromise = new Promise(function (resolve, reject) {
            var script = document.createElement('script');
            script.src = qrLibUrl;
            script.async = true;
            script.onload = function () {
                if (typeof window.QRCode === 'function') {
                    resolve();
                } else {
                    reject(new Error('QR library failed to initialize.'));
                }
            };
            script.onerror = function () {
                reject(new Error('Could not load QR library from this site.'));
            };
            document.head.appendChild(script);
        });

        return qrLibPromise;
    }

    function setError(btn, message) {
        var errorEl = document.getElementById(btn.dataset.qrError || '');
        if (!errorEl) {
            return;
        }

        if (message) {
            errorEl.textContent = message;
            errorEl.classList.remove('hidden');
        } else {
            errorEl.textContent = '';
            errorEl.classList.add('hidden');
        }
    }

    function qrImageDataUrl(target) {
        var canvas = target.querySelector('canvas');
        if (canvas) {
            return canvas.toDataURL('image/png');
        }

        var img = target.querySelector('img');
        if (img && img.src) {
            return img.src;
        }

        return null;
    }

    document.addEventListener('click', function (event) {
        var btn = event.target.closest('.js-payment-link-qr-generate');
        if (!btn || btn.disabled) {
            return;
        }

        var url = (btn.dataset.qrUrl || '').trim();
        var wrap = document.getElementById(btn.dataset.qrWrap || '');
        var target = document.getElementById(btn.dataset.qrTarget || '');
        var download = document.getElementById(btn.dataset.qrDownload || '');

        if (!url || !wrap || !target || !download) {
            setError(btn, 'QR setup is incomplete. Please refresh the page.');
            return;
        }

        setError(btn, '');
        btn.disabled = true;
        var originalLabel = btn.textContent;
        btn.textContent = 'Generating…';

        loadQrLibrary()
            .then(function () {
                return new Promise(function (resolve, reject) {
                    target.innerHTML = '';

                    new window.QRCode(target, {
                        text: url,
                        width: 220,
                        height: 220,
                        colorDark: '#000000',
                        colorLight: '#ffffff',
                        correctLevel: window.QRCode.CorrectLevel.M,
                    });

                    window.setTimeout(function () {
                        var dataUrl = qrImageDataUrl(target);
                        if (!dataUrl) {
                            reject(new Error('Could not render QR image.'));
                            return;
                        }

                        wrap.classList.remove('hidden');
                        download.classList.remove('hidden');
                        download.href = dataUrl;
                        btn.textContent = 'Regenerate QR';
                        resolve();
                    }, 80);
                });
            })
            .catch(function (error) {
                btn.textContent = originalLabel;
                setError(btn, (error && error.message) ? error.message : 'Could not generate QR code.');
            })
            .finally(function () {
                btn.disabled = false;
            });
    });

    document.addEventListener('click', function (event) {
        var link = event.target.closest('a[id$="-qr-download"]');
        if (!link) {
            return;
        }
        if (!link.href || link.href === '#') {
            event.preventDefault();
        }
    });
})();
</script>
