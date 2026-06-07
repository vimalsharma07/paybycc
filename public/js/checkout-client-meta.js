(function () {
    function detectOs(ua) {
        if (/Android/i.test(ua)) {
            return 'Android';
        }
        if (/iPhone|iPad|iPod/i.test(ua)) {
            return 'iOS';
        }
        if (/Windows/i.test(ua)) {
            return 'Windows';
        }
        if (/Mac OS X|Macintosh/i.test(ua)) {
            return 'macOS';
        }
        if (/Linux/i.test(ua)) {
            return 'Linux';
        }

        return 'Unknown';
    }

    function detectBrowser(ua) {
        if (/Edg\//i.test(ua)) {
            return 'Edge';
        }
        if (/Chrome\//i.test(ua) && !/Edg\//i.test(ua)) {
            return 'Chrome';
        }
        if (/Firefox\//i.test(ua)) {
            return 'Firefox';
        }
        if (/Safari\//i.test(ua) && !/Chrome\//i.test(ua)) {
            return 'Safari';
        }

        return 'Unknown';
    }

    function detectDeviceType(ua) {
        if (/Mobile|Android|iPhone|iPod/i.test(ua)) {
            return 'Mobile';
        }
        if (/iPad|Tablet/i.test(ua)) {
            return 'Tablet';
        }

        return 'Desktop';
    }

    function buildClientMeta() {
        var ua = navigator.userAgent || '';
        var timezone = null;

        try {
            timezone = Intl.DateTimeFormat().resolvedOptions().timeZone || null;
        } catch (e) {
            timezone = null;
        }

        return {
            os: detectOs(ua),
            browser: detectBrowser(ua),
            deviceType: detectDeviceType(ua),
            screenWidth: window.screen && window.screen.width ? window.screen.width : null,
            screenHeight: window.screen && window.screen.height ? window.screen.height : null,
            timezone: timezone,
            language: navigator.language || null,
        };
    }

    function attach(form) {
        var field = form.querySelector('[data-checkout-meta]');
        if (!field) {
            return;
        }

        form.addEventListener('submit', function () {
            field.value = JSON.stringify(buildClientMeta());
        });
    }

    document.querySelectorAll('form').forEach(attach);
})();
