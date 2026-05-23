(function () {
    const form = document.getElementById('register-form');
    if (!form) return;

    const phoneInput = document.getElementById('phone');
    const sendBtn = document.getElementById('send-otp-btn');
    const otpPanel = document.getElementById('otp-panel');
    const otpInput = document.getElementById('otp');
    const verifyBtn = document.getElementById('verify-otp-btn');
    const resendBtn = document.getElementById('resend-otp-btn');
    const otpStatus = document.getElementById('otp-status');
    const verifiedBadge = document.getElementById('phone-verified-badge');
    const submitBtn = document.getElementById('register-submit');
    const phoneHint = document.getElementById('phone-hint');

    const sendUrl = form.dataset.sendOtpUrl;
    const verifyUrl = form.dataset.verifyOtpUrl;
    const otpLength = parseInt(form.dataset.otpLength || '6', 10);
    const resendSeconds = parseInt(form.dataset.resendSeconds || '60', 10);
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    let phoneVerified = false;
    let resendTimer = null;
    let resendRemaining = 0;

    const phonePattern = /^[6-9]\d{9}$/;

    function setStatus(message, type) {
        otpStatus.textContent = message || '';
        otpStatus.className = 'text-sm';
        if (type === 'success') otpStatus.classList.add('text-emerald-300');
        else if (type === 'error') otpStatus.classList.add('text-red-300');
        else otpStatus.classList.add('text-slate-400');
    }

    function setLoading(button, loading) {
        button.disabled = loading || button.dataset.forceDisabled === 'true';
        if (loading) button.dataset.loading = 'true';
        else delete button.dataset.loading;
    }

    function normalizedPhone() {
        return (phoneInput.value || '').replace(/\D/g, '');
    }

    function isValidPhone() {
        return phonePattern.test(normalizedPhone());
    }

    function updateSendButton() {
        if (phoneVerified) {
            sendBtn.disabled = true;
            return;
        }
        sendBtn.disabled = !isValidPhone() || sendBtn.dataset.loading === 'true';
    }

    function updateVerifyButton() {
        const code = (otpInput.value || '').replace(/\D/g, '');
        verifyBtn.disabled =
            phoneVerified ||
            code.length !== otpLength ||
            verifyBtn.dataset.loading === 'true';
    }

    function setVerifiedState(verified) {
        phoneVerified = verified;
        phoneInput.readOnly = verified;
        sendBtn.disabled = true;
        otpPanel.classList.toggle('hidden', !verified && !otpPanel.dataset.sent);
        verifiedBadge.classList.toggle('hidden', !verified);
        submitBtn.disabled = !verified;
        phoneHint.classList.toggle('hidden', verified);

        if (verified) {
            otpInput.readOnly = true;
            verifyBtn.disabled = true;
            resendBtn.disabled = true;
            setStatus('', '');
        }
    }

    function startResendCountdown(seconds) {
        resendRemaining = seconds;
        resendBtn.disabled = true;
        clearInterval(resendTimer);
        resendTimer = setInterval(() => {
            resendRemaining -= 1;
            if (resendRemaining <= 0) {
                clearInterval(resendTimer);
                resendBtn.textContent = 'Resend code';
                resendBtn.disabled = phoneVerified;
                return;
            }
            resendBtn.textContent = 'Resend in ' + resendRemaining + 's';
        }, 1000);
        resendBtn.textContent = 'Resend in ' + resendRemaining + 's';
    }

    async function postJson(url, body) {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(body),
        });
        const data = await response.json().catch(() => ({}));
        return { ok: response.ok, status: response.status, data, message: extractMessage(data) };
    }

    function extractMessage(data) {
        if (data && typeof data.message === 'string' && data.message !== '') {
            return data.message;
        }
        if (data && data.errors && typeof data.errors === 'object') {
            const firstKey = Object.keys(data.errors)[0];
            const first = firstKey && Array.isArray(data.errors[firstKey]) ? data.errors[firstKey][0] : null;
            if (typeof first === 'string') {
                return first;
            }
        }

        return 'Something went wrong. Please try again.';
    }

    async function sendOtp() {
        const phone = normalizedPhone();
        if (!phonePattern.test(phone)) return;

        setLoading(sendBtn, true);
        setStatus('Sending code…', '');

        const { ok, data, message } = await postJson(sendUrl, { phone });

        setLoading(sendBtn, false);
        updateSendButton();

        if (!ok) {
            setStatus(message, 'error');
            if (data.retry_after) startResendCountdown(data.retry_after);
            return;
        }

        otpPanel.classList.remove('hidden');
        otpPanel.dataset.sent = '1';
        otpInput.focus();
        setStatus(message || 'Code sent.', 'success');
        startResendCountdown(data.retry_after || resendSeconds);
        updateVerifyButton();
    }

    async function verifyOtp() {
        const phone = normalizedPhone();
        const otp = (otpInput.value || '').replace(/\D/g, '');
        if (!phonePattern.test(phone) || otp.length !== otpLength) return;

        setLoading(verifyBtn, true);
        setStatus('Verifying…', '');

        const { ok, message } = await postJson(verifyUrl, { phone, otp });

        setLoading(verifyBtn, false);
        updateVerifyButton();

        if (!ok) {
            setStatus(message, 'error');
            return;
        }

        setVerifiedState(true);
        setStatus(message || 'Verified.', 'success');
    }

    phoneInput.addEventListener('input', () => {
        phoneInput.value = normalizedPhone().slice(0, 10);
        if (phoneVerified || otpPanel.dataset.sent) {
            setVerifiedState(false);
            delete otpPanel.dataset.sent;
            otpPanel.classList.add('hidden');
            otpInput.value = '';
            otpInput.readOnly = false;
            clearInterval(resendTimer);
            resendBtn.textContent = 'Resend code';
            resendBtn.disabled = true;
        }
        updateSendButton();
        updateVerifyButton();
    });

    otpInput.addEventListener('input', () => {
        otpInput.value = (otpInput.value || '').replace(/\D/g, '').slice(0, otpLength);
        updateVerifyButton();
    });

    sendBtn.addEventListener('click', sendOtp);
    verifyBtn.addEventListener('click', verifyOtp);
    resendBtn.addEventListener('click', sendOtp);

    updateSendButton();
    updateVerifyButton();
})();
