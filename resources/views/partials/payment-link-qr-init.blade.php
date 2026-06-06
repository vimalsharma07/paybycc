<script>
(function () {
    var qrLibPromise = null;

    function loadQrLibrary() {
        if (window.QRCode) {
            return Promise.resolve();
        }

        if (qrLibPromise) {
            return qrLibPromise;
        }

        qrLibPromise = new Promise(function (resolve, reject) {
            var script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js';
            script.async = true;
            script.onload = function () {
                if (window.QRCode) {
                    resolve();
                } else {
                    reject(new Error('QR library failed to initialize.'));
                }
            };
            script.onerror = function () {
                reject(new Error('Could not load QR library. Check your internet connection.'));
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

    document.addEventListener('click', function (event) {
        var btn = event.target.closest('.js-payment-link-qr-generate');
        if (!btn || btn.disabled) {
            return;
        }

        var url = (btn.dataset.qrUrl || '').trim();
        var wrap = document.getElementById(btn.dataset.qrWrap || '');
        var canvas = document.getElementById(btn.dataset.qrCanvas || '');
        var download = document.getElementById(btn.dataset.qrDownload || '');

        if (!url || !wrap || !canvas || !download) {
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
                    window.QRCode.toCanvas(canvas, url, { width: 220, margin: 2 }, function (error) {
                        if (error) {
                            reject(error);
                        } else {
                            resolve();
                        }
                    });
                });
            })
            .then(function () {
                wrap.classList.remove('hidden');
                download.classList.remove('hidden');
                download.href = canvas.toDataURL('image/png');
                btn.textContent = 'Regenerate QR';
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
        if (!link || link.href && link.href !== '#') {
            return;
        }
        event.preventDefault();
    });
})();
</script>
