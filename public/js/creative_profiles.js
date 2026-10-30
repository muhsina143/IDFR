/* =========================================================================
   IDFR — Creative professional profile pages
   Form behaviour only. Profile copy, fields, gallery images and mock data
   live in each *_profile.html page.
   Depends on helpers from js/script.js (load script.js first).
   ========================================================================= */
'use strict';

/**
 * Wire #profileForm[data-profile-kind="creative"] — specialisation checkboxes,
 * place combobox validation, save/reset. Values come from the HTML.
 */
function initCreativeProfileForm(form) {
    if (!form || form.dataset.profileKind !== 'creative' || form.dataset.initCreativeDone) return;
    form.dataset.initCreativeDone = 'true';

    const placeEl = $('#placeCombo');
    const place = placeEl ? initPlaceCombo(placeEl, DATA.places) : null;
    const profileLabel = form.dataset.profileLabel || 'creative';

    const services = $$('input[name="service"], input[name="service[]"]', form);
    const syncServices = () => {
        if (!services.length) return;
        services[0].setCustomValidity(
            services.some((s) => s.checked)
                ? ''
                : 'Please select at least one specialisation or service.'
        );
    };
    services.forEach((s) => s.addEventListener('change', syncServices));
    syncServices();

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (place) place.validate();
        syncServices();
        if (!validateForm(form)) return;

        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) submitBtn.disabled = true;

        if (form.action && form.action !== '' && !form.action.endsWith('#')) {
            try {
                const formData = new FormData(form);
                if (window.__profilePendingFiles) {
                    if (window.__profilePendingFiles.profile_photo) {
                        formData.set('profile_photo', window.__profilePendingFiles.profile_photo);
                    }
                    if (window.__profilePendingFiles.portfolio_image_1) {
                        formData.set('portfolio_image_1', window.__profilePendingFiles.portfolio_image_1);
                    }
                    if (window.__profilePendingFiles.portfolio_image_2) {
                        formData.set('portfolio_image_2', window.__profilePendingFiles.portfolio_image_2);
                    }
                }
                const csrf = form.querySelector('input[name="_token"]')?.value || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const res = await fetch(form.action, {
                    method: form.method || 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        ...(csrf ? { 'X-CSRF-TOKEN': csrf } : {}),
                    },
                    body: formData,
                });
                const data = await res.json().catch(() => null);
                if (res.ok && data?.success) {
                    window.__profilePendingFiles = {};
                    toast(data.message || `Your ${profileLabel} profile has been saved.`);
                    if (submitBtn) submitBtn.disabled = false;
                    return;
                } else if (res.status === 422 && data?.errors) {
                    const firstErr = Object.values(data.errors)[0];
                    toast(Array.isArray(firstErr) ? firstErr[0] : 'Validation failed.');
                    if (submitBtn) submitBtn.disabled = false;
                    return;
                }
            } catch (err) {
                console.warn('Creative profile save fetch error, falling back to native submit', err);
            }
            if (submitBtn) submitBtn.disabled = false;
            form.submit();
            return;
        }

        const name = $('#pfName') ? $('#pfName').value.trim() : 'User';
        toast(`Thank you, ${name}. Your ${profileLabel} profile has been saved.`);
        if (submitBtn) submitBtn.disabled = false;
    });

    form.addEventListener('reset', () => {
        if (place) place.reset();
        clearFormErrors(form);
        setTimeout(syncServices, 0);
    });
}

// Auto-initialize when loaded on creative profile pages
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('profileForm');
        if (form && form.dataset.profileKind === 'creative') {
            initCreativeProfileForm(form);
        }
    });
} else {
    const form = document.getElementById('profileForm');
    if (form && form.dataset.profileKind === 'creative') {
        initCreativeProfileForm(form);
    }
}
