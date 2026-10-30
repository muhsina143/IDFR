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
    if (!form || form.dataset.profileKind !== 'creative') return;

    const placeEl = $('#placeCombo');
    const place = placeEl ? initPlaceCombo(placeEl, DATA.places) : null;
    const profileLabel = form.dataset.profileLabel || 'creative';

    const services = $$('input[name="service"]', form);
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

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        if (place) place.validate();
        syncServices();
        if (!validateForm(form)) return;
        const name = $('#pfName').value.trim();
        toast(`Thank you, ${name}. Your ${profileLabel} profile has been saved (demo).`);
    });

    form.addEventListener('reset', () => {
        if (place) place.reset();
        clearFormErrors(form);
        setTimeout(syncServices, 0);
    });
}
