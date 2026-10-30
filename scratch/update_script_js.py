with open('public/js/script.js', 'r', encoding='utf-8') as f:
    code = f.read()

# 1. Update creativePros mapping to include dbId and proType
old_pro_map = """    creativePros: tplItems('creativeProsData').map((a) => ({

        id: a.dataset?.id || '',

        category: a.dataset?.category || '',"""

new_pro_map = """    creativePros: tplItems('creativeProsData').map((a) => ({

        id: a.dataset?.id || '',

        dbId: a.dataset?.dbId || '',

        proType: a.dataset?.proType || '',

        category: a.dataset?.category || '',"""

if old_pro_map in code:
    code = code.replace(old_pro_map, new_pro_map, 1)
    print("1. Updated creativePros mapping")
else:
    print("1. old_pro_map not found")

# 2. Update showEnquiry function
old_show_enquiry = """// The form is the page's <template id="modelEnquiryTemplate">

function showEnquiry(body, m) {

    body.replaceChildren($('#modelEnquiryTemplate').content.cloneNode(true));

    $('#modelModalName', body).textContent = `Book ${m.name.split(' ')[0]}`;

}"""

new_show_enquiry = """// Booking Enquiry with automatic talent association and email OTP verification
let enquiryOtpTimer = null;
let enquiryCooldownTimer = null;
let enquiryEmailVerified = false;
let verifiedEmailAddress = '';

function showEnquiry(body, person, isModel = null) {
    if (isModel === null) {
        isModel = Boolean(currentModel && person === currentModel);
    }
    const tpl = $('#modelEnquiryTemplate');
    if (!tpl) return;

    body.replaceChildren(tpl.content.cloneNode(true));

    const proType = isModel ? 'model' : (person.category === 'fashion-designer' ? 'designer' : 'creative_professional');
    const proCategory = isModel ? 'Model' : (person.profession || person.category || 'Creative Professional');
    const proId = person.dbId || person.id || '';

    const nameEl = $('#modelModalName', body);
    if (nameEl) nameEl.textContent = `Book ${person.name}`;

    const catEl = $('#modelModalCategory', body);
    if (catEl) catEl.textContent = `${proCategory} \u00b7 IDFR Official Profile`;

    const proTypeInput = $('#enqProType', body);
    if (proTypeInput) proTypeInput.value = proType;

    const proIdInput = $('#enqProId', body);
    if (proIdInput) proIdInput.value = proId;

    const proNameInput = $('#enqProName', body);
    if (proNameInput) proNameInput.value = person.name;

    const proCatInput = $('#enqProCategory', body);
    if (proCatInput) proCatInput.value = proCategory;

    // Reset OTP verification state
    enquiryEmailVerified = false;
    verifiedEmailAddress = '';

    const sendOtpBtn = $('#enqSendOtpBtn', body);
    const verifyOtpBtn = $('#enqVerifyOtpBtn', body);
    const resendOtpBtn = $('#enqResendOtpBtn', body);
    const emailInput = $('#enqEmail', body);
    const otpInput = $('#enqOtp', body);
    const otpSection = $('#enqOtpSection', body);
    const otpTimerEl = $('#enqOtpTimer', body);
    const otpStatusEl = $('#enqOtpStatus', body);
    const emailErrorEl = $('#enqEmailError', body);
    const verifiedBadge = $('#enqEmailVerifiedBadge', body);
    const formStatus = $('#enqFormStatus', body);
    const submitBtn = $('#enqSubmitBtn', body);
    const form = $('#enquiryForm', body);

    const csrfToken = $('meta[name="csrf-token"]')?.getAttribute('content') || '';

    function startOtpCountdown(durationSeconds) {
        clearInterval(enquiryOtpTimer);
        let remaining = durationSeconds;
        function updateDisplay() {
            const m = Math.floor(remaining / 60);
            const s = remaining % 60;
            if (otpTimerEl) otpTimerEl.textContent = `Expires in ${pad(m)}:${pad(s)}`;
            if (remaining <= 0) {
                clearInterval(enquiryOtpTimer);
                if (otpTimerEl) otpTimerEl.textContent = 'Code expired';
            }
            remaining--;
        }
        updateDisplay();
        enquiryOtpTimer = setInterval(updateDisplay, 1000);
    }

    function startCooldown(seconds) {
        clearInterval(enquiryCooldownTimer);
        let cooldown = seconds;
        if (resendOtpBtn) {
            resendOtpBtn.disabled = true;
            resendOtpBtn.textContent = `Resend Code (${cooldown}s)`;
        }
        enquiryCooldownTimer = setInterval(() => {
            cooldown--;
            if (cooldown <= 0) {
                clearInterval(enquiryCooldownTimer);
                if (resendOtpBtn) {
                    resendOtpBtn.disabled = false;
                    resendOtpBtn.textContent = 'Resend Code';
                }
            } else if (resendOtpBtn) {
                resendOtpBtn.textContent = `Resend Code (${cooldown}s)`;
            }
        }, 1000);
    }

    async function handleSendOtp() {
        const email = emailInput?.value?.trim() || '';
        const name = $('#enqName', body)?.value?.trim() || '';

        if (!email || !email.includes('@')) {
            if (emailErrorEl) emailErrorEl.textContent = 'Please enter a valid email address.';
            emailInput?.focus();
            return;
        }
        if (emailErrorEl) emailErrorEl.textContent = '';
        if (otpStatusEl) otpStatusEl.textContent = '';

        if (sendOtpBtn) {
            sendOtpBtn.disabled = true;
            sendOtpBtn.textContent = 'Sending...';
        }

        try {
            const res = await fetch('/enquiry/send-otp', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ email, name })
            });
            const data = await res.json();

            if (res.ok && data.success) {
                if (otpSection) otpSection.style.display = 'block';
                startOtpCountdown(data.expires_in_seconds || 300);
                startCooldown(data.cooldown_seconds || 60);
                toast(data.message || 'Verification code sent to your email.');
                if (otpInput) {
                    otpInput.value = '';
                    otpInput.focus();
                }
            } else {
                if (emailErrorEl) emailErrorEl.textContent = data.message || 'Failed to send verification code.';
                if (data.cooldown_remaining) startCooldown(data.cooldown_remaining);
            }
        } catch (err) {
            if (emailErrorEl) emailErrorEl.textContent = 'Network error while sending verification code. Please try again.';
        } finally {
            if (sendOtpBtn) {
                sendOtpBtn.disabled = false;
                sendOtpBtn.textContent = 'Send OTP';
            }
        }
    }

    async function handleVerifyOtp() {
        const email = emailInput?.value?.trim() || '';
        const otp = otpInput?.value?.trim() || '';

        if (!otp || otp.length !== 6) {
            if (otpStatusEl) otpStatusEl.textContent = 'Please enter the complete 6-digit code.';
            otpInput?.focus();
            return;
        }
        if (otpStatusEl) otpStatusEl.textContent = '';

        if (verifyOtpBtn) {
            verifyOtpBtn.disabled = true;
            verifyOtpBtn.textContent = 'Verifying...';
        }

        try {
            const res = await fetch('/enquiry/verify-otp', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ email, otp })
            });
            const data = await res.json();

            if (res.ok && data.success) {
                enquiryEmailVerified = true;
                verifiedEmailAddress = email;
                clearInterval(enquiryOtpTimer);
                clearInterval(enquiryCooldownTimer);

                if (otpSection) otpSection.style.display = 'none';
                if (verifiedBadge) verifiedBadge.style.display = 'inline-block';
                if (emailInput) {
                    emailInput.readOnly = true;
                    emailInput.style.borderColor = 'rgba(74, 222, 128, 0.4)';
                }
                if (sendOtpBtn) sendOtpBtn.style.display = 'none';
                toast('Email verified successfully!');
            } else {
                if (otpStatusEl) otpStatusEl.textContent = data.message || 'Invalid verification code.';
            }
        } catch (err) {
            if (otpStatusEl) otpStatusEl.textContent = 'Network error while verifying OTP.';
        } finally {
            if (verifyOtpBtn) {
                verifyOtpBtn.disabled = false;
                verifyOtpBtn.textContent = 'Verify Code';
            }
        }
    }

    if (sendOtpBtn) sendOtpBtn.addEventListener('click', handleSendOtp);
    if (resendOtpBtn) resendOtpBtn.addEventListener('click', handleSendOtp);
    if (verifyOtpBtn) verifyOtpBtn.addEventListener('click', handleVerifyOtp);

    if (otpInput) {
        otpInput.addEventListener('keyup', (e) => {
            if (otpInput.value.length === 6) {
                handleVerifyOtp();
            }
        });
    }

    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            if (formStatus) {
                formStatus.style.display = 'none';
                formStatus.textContent = '';
            }

            const currentEmail = emailInput?.value?.trim() || '';
            if (!enquiryEmailVerified || verifiedEmailAddress !== currentEmail) {
                if (emailErrorEl) emailErrorEl.textContent = 'Please verify your email address using the 6-digit OTP before submitting.';
                emailInput?.focus();
                toast('Please verify your email address first.');
                return;
            }

            const rawData = new FormData(form);
            const payload = Object.fromEntries(rawData.entries());

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span>Submitting Enquiry...</span>';
            }

            try {
                const res = await fetch('/enquiry/submit', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();

                if (res.ok && data.success) {
                    toast(data.message || 'Your enquiry has been submitted successfully. Our team will contact you soon.');
                    if (formStatus) {
                        formStatus.style.display = 'block';
                        formStatus.style.background = 'rgba(34, 197, 94, 0.15)';
                        formStatus.style.border = '1px solid #22c55e';
                        formStatus.style.color = '#4ade80';
                        formStatus.textContent = data.message || 'Your enquiry has been submitted successfully. Our team will contact you soon.';
                    }
                    form.reset();
                    if (submitBtn) submitBtn.style.display = 'none';

                    setTimeout(() => {
                        if (isModel && currentModel) {
                            body.innerHTML = modelProfileHTML(currentModel);
                        } else if (!isModel && currentPro) {
                            body.innerHTML = proProfileHTML(currentPro);
                        }
                    }, 2800);
                } else {
                    if (formStatus) {
                        formStatus.style.display = 'block';
                        formStatus.style.background = 'rgba(239, 68, 68, 0.15)';
                        formStatus.style.border = '1px solid #ef4444';
                        formStatus.style.color = '#f87171';
                        formStatus.textContent = data.message || 'Submission failed. Please check form inputs.';
                    }
                }
            } catch (err) {
                if (formStatus) {
                    formStatus.style.display = 'block';
                    formStatus.style.background = 'rgba(239, 68, 68, 0.15)';
                    formStatus.style.border = '1px solid #ef4444';
                    formStatus.style.color = '#f87171';
                    formStatus.textContent = 'A network error occurred. Please try again.';
                }
            } finally {
                if (submitBtn && submitBtn.style.display !== 'none') {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<span>Submit Booking Enquiry</span><svg class="icon" aria-hidden="true"><use href="#i-arrow-right" /></svg>';
                }
            }
        });
    }
}"""

if old_show_enquiry in code:
    code = code.replace(old_show_enquiry, new_show_enquiry, 1)
    print("2. Updated showEnquiry")
else:
    print("2. old_show_enquiry not found")

# 3. Update enquiry-back in initModelModal
old_back = """        } else if (e.target.closest('[data-enquiry-back]')) {

            body.innerHTML = modelProfileHTML(currentModel);

            $('[data-model-contact]', body)?.focus();"""

new_back = """        } else if (e.target.closest('[data-enquiry-back]')) {

            if (currentPro) {
                body.innerHTML = proProfileHTML(currentPro);
            } else if (currentModel) {
                body.innerHTML = modelProfileHTML(currentModel);
                $('[data-model-contact]', body)?.focus();
            }"""

if old_back in code:
    code = code.replace(old_back, new_back, 1)
    print("3. Updated enquiry-back")
else:
    print("3. old_back not found")

# 4. Remove dummy submit listener in initModelModal
old_submit = """    body.addEventListener('submit', (e) => {

        if (e.target.id !== 'enquiryForm') return;

        e.preventDefault();

        if (!validateForm(e.target)) return;

        const first = currentModel.name.split(' ')[0];

        toast(`Enquiry sent \u2014 ${first}'s management team will reply within 24 hours.`);

        body.innerHTML = modelProfileHTML(currentModel);

        $('[data-enquire]', body).focus();

    });"""

if old_submit in code:
    code = code.replace(old_submit, "// Form submission handled by showEnquiry()", 1)
    print("4. Removed dummy submit listener")
else:
    # Try regex if slight formatting diff
    import re
    submit_pat = r"body\.addEventListener\('submit',\s*\(e\)\s*=>\s*\{[\s\S]*?if\s*\(e\.target\.id\s*!==\s*'enquiryForm'\)[\s\S]*?\}\);"
    if re.search(submit_pat, code):
        code = re.sub(submit_pat, "// Form submission handled by showEnquiry()", code, count=1)
        print("4. Removed dummy submit listener (regex)")
    else:
        print("4. old_submit not found")

# 5. Remove login notice from proProfileHTML
old_pro_notice = """        <div class="mm__section creative-modal__contact" role="status" data-pro-notice>

            <p class="mm__note">Please <a href="#login" data-pro-enquire>log in</a> to contact or enquire about this professional.</p>

        </div>"""

if old_pro_notice in code:
    code = code.replace(old_pro_notice, "", 1)
    print("5. Removed login notice from proProfileHTML")
else:
    print("5. old_pro_notice not found")

# 6. Change data-pro-enquire to open showEnquiry directly (no login required!)
old_pro_enquire = """        else if (e.target.closest('[data-pro-enquire]')) {

            e.preventDefault();

            const pro = currentPro;

            if (demoSignedIn) { showNotice(pro); return; }

            const trigger = Modal.close(modal);

            pending = { pro, trigger };

            Auth.open('login', undefined, trigger);

            toast(`Please log in to contact or enquire about ${pro.name}.`);

        }"""

new_pro_enquire = """        else if (e.target.closest('[data-pro-enquire]')) {

            e.preventDefault();

            showEnquiry(body, currentPro, false);

        }"""

if old_pro_enquire in code:
    code = code.replace(old_pro_enquire, new_pro_enquire, 1)
    print("6. Updated data-pro-enquire to show enquiry directly")
else:
    print("6. old_pro_enquire not found")

with open('public/js/script.js', 'w', encoding='utf-8') as f:
    f.write(code)

print("public/js/script.js saved successfully")

