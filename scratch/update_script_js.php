<?php

$files = [
    'public/js/script.js',
    'IDFR_LATEST/js/script.js',
];

$targetSwitchTab = "        panels.forEach((p) => {

            if (name === 'login') p.hidden = p.id !== 'panelLogin';

            else if (name === 'register') p.hidden = p.id !== 'panelRegister';

            else if (name === 'verify') p.hidden = p.id !== 'panelVerifyOtp';

        });



        if (name === 'login') $('#authTitle').textContent = 'Welcome back';

        else if (name === 'register') $('#authTitle').textContent = 'Join IDFR';

        else if (name === 'verify') $('#authTitle').textContent = 'Verify Email';";

$replacementSwitchTab = "        panels.forEach((p) => {

            if (name === 'login') p.hidden = p.id !== 'panelLogin';

            else if (name === 'register') p.hidden = p.id !== 'panelRegister';

            else if (name === 'verify') p.hidden = p.id !== 'panelVerifyOtp';

            else if (name === 'forgot') p.hidden = p.id !== 'panelForgotPassword';

        });



        const authTabsEl = $('.auth-tabs', modal);

        if (authTabsEl) {

            authTabsEl.style.display = (name === 'login' || name === 'register') ? '' : 'none';

        }



        if (name === 'login') $('#authTitle').textContent = 'Welcome back';

        else if (name === 'register') $('#authTitle').textContent = 'Join IDFR';

        else if (name === 'verify') $('#authTitle').textContent = 'Verify Email';

        else if (name === 'forgot') $('#authTitle').textContent = 'Forgot Your Password?';";

$targetBackBtn = "        const backBtn = $('#backToRegisterBtn');

        if (backBtn) {

            backBtn.addEventListener('click', () => switchTab('register'));

        }";

$replacementBackBtn = "        const backBtn = $('#backToRegisterBtn');

        if (backBtn) {

            backBtn.addEventListener('click', () => switchTab('register'));

        }

        const forgotBtn = $('#forgotBtn');

        if (forgotBtn) {

            forgotBtn.addEventListener('click', (e) => {

                e.preventDefault();

                switchTab('forgot');

            });

        }

        const backToLoginFromForgotBtn = $('#backToLoginFromForgotBtn');

        if (backToLoginFromForgotBtn) {

            backToLoginFromForgotBtn.addEventListener('click', (e) => {

                e.preventDefault();

                switchTab('login');

            });

        }

        const forgotForm = $('#panelForgotPassword');

        if (forgotForm) {

            forgotForm.addEventListener('submit', async (e) => {

                e.preventDefault();

                clearFormErrors(e.target);

                const errAlert = $('#forgotErrorAlert');

                const succAlert = $('#forgotSuccessAlert');

                if (errAlert) { errAlert.style.display = 'none'; errAlert.textContent = ''; }

                if (succAlert) { succAlert.style.display = 'none'; succAlert.textContent = ''; }

                const emailInput = $('#forgotEmail');

                const emailVal = emailInput ? emailInput.value.trim() : '';

                if (!emailVal) {

                    if (emailInput) setFieldError(emailInput, 'Please enter your email address.');

                    return;

                }

                const emailRegex = /^[^\\s@]+@[^\\s@]+\\.[^\\s@]+$/;

                if (!emailRegex.test(emailVal)) {

                    if (emailInput) setFieldError(emailInput, 'Please enter a valid email address.');

                    return;

                }

                const submitBtn = $('#forgotSubmitBtn') || $('button[type=\"submit\"]', e.target);

                const originalHtml = submitBtn ? submitBtn.innerHTML : '';

                if (submitBtn) {

                    submitBtn.disabled = true;

                    submitBtn.innerHTML = `<span>Sending Link...</span>`;

                }

                try {

                    const csrfToken = $('meta[name=\"csrf-token\"]')?.getAttribute('content') || '';

                    const res = await fetch('/forgot-password', {

                        method: 'POST',

                        headers: {

                            'Content-Type': 'application/json',

                            'X-CSRF-TOKEN': csrfToken,

                            'Accept': 'application/json',

                            'X-Requested-With': 'XMLHttpRequest'

                        },

                        body: JSON.stringify({ email: emailVal })

                    });

                    const data = await res.json();

                    if (res.ok && data.success) {

                        if (succAlert) {

                            succAlert.textContent = data.message || 'Password reset link sent to your email address.';

                            succAlert.style.display = 'block';

                        }

                        if (typeof toast === 'function') {

                            toast(data.message || 'Password reset link sent.');

                        }

                    } else {

                        const errMsg = data.message || (data.errors?.email ? data.errors.email[0] : 'Failed to send password reset link.');

                        if (errAlert) {

                            errAlert.textContent = errMsg;

                            errAlert.style.display = 'block';

                        } else if (emailInput) {

                            setFieldError(emailInput, errMsg);

                        }

                    }

                } catch (err) {

                    if (errAlert) {

                        errAlert.textContent = 'Failed to deliver the password reset email. Please try again later.';

                        errAlert.style.display = 'block';

                    }

                } finally {

                    if (submitBtn) {

                        submitBtn.disabled = false;

                        submitBtn.innerHTML = originalHtml;

                    }

                }

            });

        }";

foreach ($files as $file) {
    if (! file_exists($file)) {
        continue;
    }
    $content = file_get_contents($file);
    $isCrlf = strpos($content, "\r\n") !== false;
    $normalized = str_replace("\r\n", "\n", $content);

    if (strpos($normalized, 'panelForgotPassword') === false) {
        $normalized = str_replace($targetSwitchTab, $replacementSwitchTab, $normalized);
        $normalized = str_replace($targetBackBtn, $replacementBackBtn, $normalized);
        if ($isCrlf) {
            $normalized = str_replace("\n", "\r\n", $normalized);
        }
        file_put_contents($file, $normalized);
        echo "Updated $file\n";
    } else {
        echo "Already updated $file\n";
    }
}
