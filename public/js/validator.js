/**
 * IDFR / ASDreamStudio Centralized Reusable Client-Side Form Validation Engine
 *
 * Works via event delegation across static, modal, template-injected, and dynamic forms
 * marked with [data-validate]. Supports data-rules, data-label, data-message,
 * data-error-target, data-server-errors, password visibility toggles, phone character filtering,
 * submit loading states, and Laravel 422 / SSR error hydration.
 */
(function (window, document) {
    'use strict';

    const REGEX = {
        email: /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/,
        phoneAllowedChars: /^[0-9+\s\-()]+$/,
        phone: /^\+?[1-9]\d{6,14}$/,
        name: /^[A-Za-z\s.]+$/,
        numeric: /^-?\d+(\.\d+)?$/,
        digits: /^\d+$/,
        alpha: /^[A-Za-z\s]+$/,
        alphanum: /^[A-Za-z0-9\s\-_.,]+$/,
        password: /^(?=.*[A-Za-z])(?=.*\d).{8,}$/,
        url: /^https?:\/\/(?:[\w-]+\.)+[a-z]{2,}(?:[\/?#][^\s]*)?$/i
    };

    const EYE_OPEN_SVG = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
    const EYE_CLOSED_SVG = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>';

    /**
     * Check whether a form control is effectively visible and enabled for validation.
     */
    function isFieldActive(el) {
        if (!el || el.disabled) {
            return false;
        }
        if (el.type === 'hidden') {
            return el.dataset.validateHidden === 'true';
        }
        if (el.dataset.validateHidden === 'true') {
            return true;
        }
        // Check hidden/display:none ancestors
        let cur = el;
        while (cur && cur !== document.body) {
            if (cur.hidden) {
                return false;
            }
            if (cur.style && cur.style.display === 'none') {
                return false;
            }
            const computed = window.getComputedStyle ? window.getComputedStyle(cur) : null;
            if (computed && (computed.display === 'none' || computed.visibility === 'hidden')) {
                return false;
            }
            cur = cur.parentElement;
        }
        return true;
    }

    /**
     * Build a human-readable label for a form field.
     */
    function getFieldLabel(el) {
        if (el.dataset.label) {
            return el.dataset.label.trim();
        }
        const form = el.closest('form') || document;
        if (el.id) {
            const explicitLabel = form.querySelector(`label[for="${CSS.escape(el.id)}"]`);
            if (explicitLabel) {
                const clone = explicitLabel.cloneNode(true);
                clone.querySelectorAll('.required, .req, span.badge').forEach((s) => s.remove());
                const text = clone.textContent.replace(/\*/g, '').replace(/\(Optional\)/gi, '').trim();
                if (text) {
                    return text;
                }
            }
        }
        const fieldBox = el.closest('.field, .form-group, .dyn-field');
        if (fieldBox) {
            const boxLabel = fieldBox.querySelector('label, .field__label');
            if (boxLabel) {
                const text = boxLabel.textContent.replace(/\*/g, '').replace(/\(Optional\)/gi, '').trim();
                if (text) {
                    return text;
                }
            }
        }
        const raw = el.getAttribute('name') || el.id || 'This field';
        return raw
            .replace(/\[\]$/, '')
            .replace(/[_-]+/g, ' ')
            .replace(/\b\w/g, (c) => c.toUpperCase())
            .trim();
    }

    /**
     * Parse data-rules plus fallback HTML constraints into ordered rule objects.
     */
    function parseRules(el) {
        const rawRules = (el.dataset.rules || '').trim();
        const tokens = rawRules ? rawRules.split('|').map((t) => t.trim()).filter(Boolean) : [];
        const parsed = [];
        const seenNames = new Set();

        for (const token of tokens) {
            const colonIdx = token.indexOf(':');
            const name = (colonIdx === -1 ? token : token.slice(0, colonIdx)).toLowerCase();
            const param = colonIdx === -1 ? null : token.slice(colonIdx + 1);
            parsed.push({ name, param });
            seenNames.add(name);
        }

        // Fallback HTML constraint synthesis if not explicitly overridden
        if ((el.required || el.hasAttribute('required')) && !seenNames.has('required') && !seenNames.has('checked') && !seenNames.has('group_min') && !seenNames.has('select')) {
            if (el.type === 'checkbox') {
                parsed.unshift({ name: 'checked', param: null });
            } else if (el.tagName === 'SELECT') {
                parsed.unshift({ name: 'select', param: null });
            } else {
                parsed.unshift({ name: 'required', param: null });
            }
            seenNames.add('required');
        }

        if (el.type === 'email' && !seenNames.has('email')) {
            parsed.push({ name: 'email', param: null });
            seenNames.add('email');
        }
        if (el.type === 'url' && !seenNames.has('url')) {
            parsed.push({ name: 'url', param: null });
            seenNames.add('url');
        }
        const minLenAttr = el.getAttribute('minlength');
        if (minLenAttr && !seenNames.has('min') && !seenNames.has('password')) {
            parsed.push({ name: 'min', param: minLenAttr });
            seenNames.add('min');
        }
        const maxLenAttr = el.getAttribute('maxlength');
        if (maxLenAttr && !seenNames.has('max')) {
            parsed.push({ name: 'max', param: maxLenAttr });
            seenNames.add('max');
        }

        return parsed;
    }

    /**
     * Format YYYY-MM-DD local date string.
     */
    function getTodayIso() {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    /**
     * Find checkbox/radio group wrapper and checkboxes when validating group_min.
     */
    function getCheckboxGroup(el, form) {
        const groupContainer = el.closest('[data-group], .checkbox-group, .pill-group, .model-type-grid, .field');
        const name = el.getAttribute('name');
        let boxes = [];
        if (name && form) {
            boxes = Array.from(form.querySelectorAll(`input[type="${el.type}"][name="${CSS.escape(name)}"]`));
        }
        if (!boxes.length && groupContainer) {
            boxes = Array.from(groupContainer.querySelectorAll('input[type="checkbox"]'));
        }
        return { container: groupContainer || el.parentElement, boxes };
    }

    /**
     * Locate or create the feedback element for a field or group.
     */
    function getOrCreateErrorEl(el, form) {
        if (el.dataset.errorTarget) {
            const target = (form && form.querySelector(el.dataset.errorTarget)) || document.querySelector(el.dataset.errorTarget);
            if (target) {
                target.classList.add('field-error');
                return target;
            }
        }

        const anchor = el.closest('.pw-field-wrap') || el;
        const fieldBox = anchor.closest('.field, .form-group, .dyn-field');

        if (fieldBox) {
            const existing = fieldBox.querySelector(':scope > .field-error, :scope > .field__error, :scope > small.field-error');
            if (existing) {
                existing.classList.add('field-error');
                return existing;
            }
            const err = document.createElement('small');
            err.className = 'field-error';
            err.setAttribute('role', 'alert');
            fieldBox.appendChild(err);
            return err;
        }

        const next = anchor.nextElementSibling;
        if (next && (next.classList.contains('field-error') || next.classList.contains('field__error'))) {
            next.classList.add('field-error');
            return next;
        }

        const err = document.createElement('small');
        err.className = 'field-error';
        err.setAttribute('role', 'alert');
        anchor.insertAdjacentElement('afterend', err);
        return err;
    }

    /**
     * Render validation result onto the DOM control and its error container.
     */
    function setFieldState(el, isValid, errorMessage, form, showValidState) {
        if (el.type === 'checkbox' && (el.getAttribute('name') || '').endsWith('[]')) {
            const { container } = getCheckboxGroup(el, form);
            if (container) {
                container.classList.toggle('is-invalid', !isValid);
                container.classList.toggle('is-valid', isValid && showValidState);
                container.classList.toggle('has-error', !isValid);
                let errEl = container.querySelector(':scope > .field-error, :scope > .field__error');
                if (!errEl && !isValid) {
                    errEl = document.createElement('small');
                    errEl.className = 'field-error';
                    errEl.setAttribute('role', 'alert');
                    container.appendChild(errEl);
                }
                if (errEl) {
                    errEl.textContent = isValid ? '' : (errorMessage || '');
                }
            }
            return;
        }

        const fieldBox = el.closest('.field, .form-group, .dyn-field');
        const errEl = getOrCreateErrorEl(el, form);

        if (!isValid) {
            el.classList.add('is-invalid');
            el.classList.remove('is-valid');
            el.setAttribute('aria-invalid', 'true');
            if (fieldBox) {
                fieldBox.classList.add('has-error');
            }
            if (errEl) {
                errEl.textContent = errorMessage || 'Invalid value.';
            }
        } else {
            el.classList.remove('is-invalid');
            el.removeAttribute('aria-invalid');
            const hasValue = el.type === 'checkbox'
                ? el.checked
                : el.type === 'file'
                    ? (el.files && el.files.length > 0)
                    : el.value.trim() !== '';

            if (showValidState && hasValue) {
                el.classList.add('is-valid');
            } else {
                el.classList.remove('is-valid');
            }

            if (fieldBox) {
                fieldBox.classList.remove('has-error');
            }
            if (errEl) {
                errEl.textContent = '';
            }
        }
    }

    /**
     * Evaluate a parsed rule against the control value. Returns error string or null.
     */
    function evaluateRule(el, rule, form, allRuleNames) {
        const label = getFieldLabel(el);
        const rawVal = el.value ?? '';
        const val = typeof rawVal === 'string' ? rawVal.trim() : '';
        const isEmpty = el.type === 'checkbox'
            ? !el.checked
            : el.type === 'file'
                ? !(el.files && el.files.length > 0)
                : val === '';

        // Allow empty values for optional fields unless checking required/checked/select/group_min/same
        if (
            isEmpty &&
            !['required', 'checked', 'select', 'group_min', 'same'].includes(rule.name)
        ) {
            return null;
        }

        switch (rule.name) {
            case 'required': {
                if (el.type === 'checkbox' && !el.checked) {
                    return el.dataset.message || `${label} must be checked.`;
                }
                if (el.type === 'file' && !(el.files && el.files.length > 0)) {
                    return el.dataset.message || `Please upload ${label.toLowerCase()}.`;
                }
                if (val === '') {
                    return el.dataset.message || `${label} is required.`;
                }
                return null;
            }

            case 'checked': {
                if (!el.checked) {
                    return el.dataset.message || `Please accept ${label.toLowerCase()}.`;
                }
                return null;
            }

            case 'select': {
                if (val === '') {
                    return el.dataset.message || `Please select ${label.toLowerCase()}.`;
                }
                return null;
            }

            case 'group_min': {
                const minCount = parseInt(rule.param || '1', 10) || 1;
                const { boxes } = getCheckboxGroup(el, form);
                const checkedCount = boxes.filter((b) => b.checked).length;
                if (checkedCount < minCount) {
                    return el.dataset.message || `Please select at least ${minCount} option${minCount > 1 ? 's' : ''}.`;
                }
                return null;
            }

            case 'min': {
                const limit = Number(rule.param);
                if (Number.isNaN(limit)) {
                    return null;
                }
                if (allRuleNames.has('numeric') || el.type === 'number') {
                    const num = Number(val);
                    if (!Number.isNaN(num) && num < limit) {
                        return el.dataset.message || `${label} must be at least ${limit}.`;
                    }
                } else if (val.length < limit) {
                    return el.dataset.message || `${label} must be at least ${limit} characters.`;
                }
                return null;
            }

            case 'max': {
                const limit = Number(rule.param);
                if (Number.isNaN(limit)) {
                    return null;
                }
                if (allRuleNames.has('numeric') || el.type === 'number') {
                    const num = Number(val);
                    if (!Number.isNaN(num) && num > limit) {
                        return el.dataset.message || `${label} must not exceed ${limit}.`;
                    }
                } else if (val.length > limit) {
                    return el.dataset.message || `${label} must not exceed ${limit} characters.`;
                }
                return null;
            }

            case 'email': {
                if (!REGEX.email.test(val)) {
                    return el.dataset.message || 'Please enter a valid email address.';
                }
                return null;
            }

            case 'phone': {
                if (!REGEX.phoneAllowedChars.test(val)) { return el.dataset.message || 'Please enter a valid phone number with country code (e.g. +971 54 178 6786 or +91 98765 43210).'; } const normalized = val.replace(/[\s\-()]+/g, '');
                if (!REGEX.phone.test(normalized)) {
                    return el.dataset.message || 'Please enter a valid phone number with country code (e.g. +971 54 178 6786 or +91 98765 43210).';
                }
                return null;
            }

            case 'name': {
                if (!REGEX.name.test(val)) {
                    return el.dataset.message || `${label} may only contain letters, spaces, and dots.`;
                }
                return null;
            }

            case 'numeric': {
                if (!REGEX.numeric.test(val)) {
                    return el.dataset.message || `${label} must be a valid number.`;
                }
                return null;
            }

            case 'digits': {
                if (!REGEX.digits.test(val)) {
                    return el.dataset.message || `${label} must contain only digits.`;
                }
                if (rule.param && val.length !== parseInt(rule.param, 10)) {
                    return el.dataset.message || `${label} must be exactly ${rule.param} digits.`;
                }
                return null;
            }

            case 'alpha': {
                if (!REGEX.alpha.test(val)) {
                    return el.dataset.message || `${label} may only contain letters and spaces.`;
                }
                return null;
            }

            case 'alphanum': {
                if (!REGEX.alphanum.test(val)) {
                    return el.dataset.message || `${label} contains unsupported characters.`;
                }
                return null;
            }

            case 'password': {
                if (val.length < 8) {
                    return el.dataset.message || 'Password must be at least 8 characters long.';
                }
                if (!/[A-Za-z]/.test(val) || !/\d/.test(val)) {
                    return el.dataset.message || 'Password must contain at least one letter and one number.';
                }
                return null;
            }

            case 'same': {
                if (!rule.param) {
                    return null;
                }
                const target = rule.param.startsWith('#')
                    ? (form ? form.querySelector(rule.param) : null) || document.querySelector(rule.param)
                    : (form ? form.querySelector(`[name="${CSS.escape(rule.param)}"]`) : null);
                const targetVal = target ? target.value : '';
                if (val !== targetVal) {
                    return el.dataset.message || 'Passwords do not match.';
                }
                return null;
            }

            case 'url': {
                if (!REGEX.url.test(val)) {
                    return el.dataset.message || 'Please enter a valid URL starting with http:// or https://.';
                }
                return null;
            }

            case 'date': {
                if (Number.isNaN(Date.parse(val))) {
                    return el.dataset.message || `Please enter a valid ${label.toLowerCase()}.`;
                }
                return null;
            }

            case 'date_after_or_equal': {
                if (!rule.param) {
                    return null;
                }
                let compareVal = rule.param === 'today' ? getTodayIso() : '';
                if (rule.param !== 'today') {
                    const target = rule.param.startsWith('#')
                        ? (form ? form.querySelector(rule.param) : null) || document.querySelector(rule.param)
                        : (form ? form.querySelector(`[name="${CSS.escape(rule.param)}"]`) : null);
                    compareVal = target ? target.value.trim() : '';
                }
                if (compareVal && val < compareVal) {
                    return el.dataset.message || (
                        rule.param === 'today'
                            ? `${label} cannot be in the past.`
                            : `${label} must be on or after the start date.`
                    );
                }
                return null;
            }

            case 'file_max': {
                if (!el.files || !el.files.length) {
                    return null;
                }
                const maxMb = Number(rule.param);
                if (Number.isNaN(maxMb) || maxMb <= 0) {
                    return null;
                }
                const maxBytes = maxMb * 1024 * 1024;
                for (const file of Array.from(el.files)) {
                    if (file.size > maxBytes) {
                        return el.dataset.message || `${file.name} exceeds the maximum size of ${maxMb}MB.`;
                    }
                }
                return null;
            }

            case 'file_types': {
                if (!el.files || !el.files.length || !rule.param) {
                    return null;
                }
                const allowed = rule.param.split(',').map((s) => s.trim().toLowerCase()).filter(Boolean);
                for (const file of Array.from(el.files)) {
                    const ext = (file.name.split('.').pop() || '').toLowerCase();
                    if (!allowed.includes(ext)) {
                        return el.dataset.message || `Allowed file types: ${allowed.join(', ')}.`;
                    }
                }
                return null;
            }

            default:
                return null;
        }
    }

    /**
     * Collect all validatable controls inside a form.
     */
    function getFormFields(form) {
        const controls = Array.from(
            form.querySelectorAll('input, select, textarea')
        );
        const seenCheckboxNames = new Set();
        const result = [];

        for (const el of controls) {
            if (el.type === 'submit' || el.type === 'button' || el.type === 'reset') {
                continue;
            }
            // Only validate fields that have data-rules OR native constraint attributes inside [data-validate]
            const hasRules = Boolean(
                (el.dataset.rules && el.dataset.rules.trim()) ||
                el.required ||
                el.hasAttribute('required') ||
                el.getAttribute('minlength') ||
                el.type === 'email' ||
                el.type === 'url'
            );
            if (!hasRules) {
                continue;
            }
            if (el.type === 'checkbox' && (el.getAttribute('name') || '').endsWith('[]')) {
                const name = el.getAttribute('name');
                if (seenCheckboxNames.has(name)) {
                    continue;
                }
                seenCheckboxNames.add(name);
            }
            result.push(el);
        }

        return result;
    }

    /**
     * Validate a single field and optionally show .is-valid border styling.
     */
    function validateField(el, options = {}) {
        const form = el.closest('form');
        if (!isFieldActive(el)) {
            setFieldState(el, true, '', form, false);
            return true;
        }

        const rules = parseRules(el);
        const ruleNames = new Set(rules.map((r) => r.name));
        let firstError = null;

        for (const rule of rules) {
            const msg = evaluateRule(el, rule, form, ruleNames);
            if (msg) {
                firstError = msg;
                break;
            }
        }

        const showValid = options.showValid !== false;
        setFieldState(el, !firstError, firstError || '', form, showValid);
        return !firstError;
    }

    /**
     * Validate an entire form. Returns true when all active rules pass.
     */
    function validateForm(form, options = {}) {
        if (!form) {
            return true;
        }

        form.setAttribute('novalidate', 'novalidate');
        const fields = getFormFields(form);
        let firstInvalid = null;

        for (const field of fields) {
            const ok = validateField(field, { showValid: options.showValid !== false });
            if (!ok && !firstInvalid) {
                firstInvalid = field;
            }
        }

        if (firstInvalid && options.focusFirst !== false) {
            try {
                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstInvalid.focus({ preventScroll: true });
            } catch (_) {
                firstInvalid.focus();
            }
        }

        return !firstInvalid;
    }

    /**
     * Apply Laravel 422 or session $errors map { fieldName: ["Message"] } onto the form UI.
     */
    function applyServerErrors(form, errors) {
        if (!form || !errors || typeof errors !== 'object') {
            return null;
        }

        let firstInvalid = null;
        const entries = Object.entries(errors);

        for (const [rawKey, messages] of entries) {
            const msg = Array.isArray(messages) ? messages[0] : String(messages || '');
            if (!msg) {
                continue;
            }

            // Map dotted array field names (e.g. modelType.0 -> modelType[], service.0 -> service[])
            const baseKey = rawKey.split('.')[0];
            const el =
                form.querySelector(`[name="${CSS.escape(rawKey)}"]`) ||
                form.querySelector(`[name="${CSS.escape(baseKey)}"]`) ||
                form.querySelector(`[name="${CSS.escape(baseKey + '[]')}"]`);

            if (el) {
                setFieldState(el, false, msg, form, false);
                if (!firstInvalid) {
                    firstInvalid = el;
                }
            }
        }

        if (firstInvalid) {
            try {
                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstInvalid.focus({ preventScroll: true });
            } catch (_) {
                firstInvalid.focus();
            }
        }

        return firstInvalid;
    }

    /**
     * Clear all validation states and error messages inside a form.
     */
    function clearForm(form) {
        if (!form) {
            return;
        }
        form.querySelectorAll('.is-invalid, .is-valid').forEach((el) => {
            el.classList.remove('is-invalid', 'is-valid');
            el.removeAttribute('aria-invalid');
        });
        form.querySelectorAll('.has-error').forEach((el) => el.classList.remove('has-error'));
        form.querySelectorAll('.field-error, .field__error').forEach((el) => {
            el.textContent = '';
        });
        setLoading(form, false);
    }

    /**
     * Toggle loading/disabled state cleanly on a form's submit button(s).
     */
    function setLoading(form, isLoading, loadingText) {
        if (!form) {
            return;
        }
        const buttons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
        buttons.forEach((btn) => {
            const textTarget = btn.querySelector('span:not(.spinner)') || btn;
            if (isLoading) {
                if (!btn.dataset.originalText) {
                    btn.dataset.originalText = textTarget.textContent;
                }
                btn.disabled = true;
                btn.classList.add('is-loading');
                if (loadingText) {
                    textTarget.textContent = loadingText;
                }
            } else {
                btn.disabled = false;
                btn.classList.remove('is-loading');
                if (btn.dataset.originalText) {
                    textTarget.textContent = btn.dataset.originalText;
                }
            }
        });
    }

    /**
     * Inject accessible show/hide password toggles on all password inputs within [data-validate]
     * unless opted out or already wrapped by a custom toggle (e.g. admin login).
     */
    function attachPasswordToggles(root = document) {
        const scope = root instanceof Element ? root : document;
        const forms = scope.matches && scope.matches('form[data-validate]')
            ? [scope]
            : Array.from(scope.querySelectorAll('form[data-validate]'));

        forms.forEach((form) => {
            if (form.id === 'loginForm' && form.querySelector('.password-toggle')) {
                return;
            }
            form.setAttribute('novalidate', 'novalidate');

            const pwInputs = form.querySelectorAll('input[type="password"]');
            pwInputs.forEach((input) => {
                if (input.dataset.noPwToggle === 'true' || input.dataset.pwEnhanced === 'true') {
                    return;
                }
                const parent = input.parentElement;
                if (parent && parent.querySelector('.pw-toggle, .password-toggle')) {
                    input.dataset.pwEnhanced = 'true';
                    return;
                }

                input.dataset.pwEnhanced = 'true';
                const wrap = document.createElement('div');
                wrap.className = 'pw-field-wrap';
                parent.insertBefore(wrap, input);
                wrap.appendChild(input);

                const toggle = document.createElement('button');
                toggle.type = 'button';
                toggle.className = 'pw-toggle';
                toggle.setAttribute('aria-label', 'Show password');
                toggle.setAttribute('aria-pressed', 'false');
                toggle.innerHTML = EYE_OPEN_SVG;

                toggle.addEventListener('click', () => {
                    const reveal = input.type === 'password';
                    input.type = reveal ? 'text' : 'password';
                    toggle.setAttribute('aria-label', reveal ? 'Hide password' : 'Show password');
                    toggle.setAttribute('aria-pressed', reveal ? 'true' : 'false');
                    toggle.innerHTML = reveal ? EYE_CLOSED_SVG : EYE_OPEN_SVG;
                });

                wrap.appendChild(toggle);
            });
        });
    }

    /**
     * Hydrate Laravel Blade $errors passed via data-server-errors JSON attribute.
     */
    function hydrateServerErrors(root = document) {
        const forms = Array.from((root || document).querySelectorAll('form[data-validate][data-server-errors]'));
        forms.forEach((form) => {
            const raw = form.getAttribute('data-server-errors');
            if (!raw) {
                return;
            }
            try {
                const parsed = JSON.parse(raw);
                if (parsed && Object.keys(parsed).length > 0) {
                    applyServerErrors(form, parsed);
                }
            } catch (_) {
                // Ignore malformed JSON payloads
            }
        });
    }

    /**
     * Trigger dependent cross-field validation (confirmation passwords and end dates).
     */
    function triggerLinkedFields(el, form) {
        if (!el || !el.id || !form) {
            return;
        }
        const dependents = form.querySelectorAll(
            `[data-rules*="same:#${CSS.escape(el.id)}"], [data-rules*="date_after_or_equal:#${CSS.escape(el.id)}"]`
        );
        dependents.forEach((dep) => {
            if (dep.value.trim() !== '' || dep.classList.contains('is-invalid')) {
                validateField(dep);
            }
        });
    }

    let initialized = false;

    function init() {
        if (initialized) {
            return;
        }
        initialized = true;

        attachPasswordToggles(document);
        hydrateServerErrors(document);

        // Filter phone keystrokes & live-validate on input
        document.addEventListener('input', (e) => {
            const el = e.target;
            if (!(el instanceof HTMLInputElement || el instanceof HTMLTextAreaElement || el instanceof HTMLSelectElement)) {
                return;
            }
            const form = el.closest('form[data-validate]');
            if (!form) {
                return;
            }

            const rawRules = (el.dataset.rules || '').toLowerCase();
            if (rawRules.split('|').includes('phone') && typeof el.value === 'string') {
                const cleaned = el.value.replace(/[^0-9+\s\-()]/g, '');
                if (cleaned !== el.value) {
                    const pos = el.selectionStart;
                    el.value = cleaned;
                    if (typeof pos === 'number') {
                        el.setSelectionRange(Math.max(0, pos - 1), Math.max(0, pos - 1));
                    }
                }
            }
            if (rawRules.split('|').includes('digits') && typeof el.value === 'string') {
                const cleaned = el.value.replace(/\D/g, '');
                if (cleaned !== el.value) {
                    el.value = cleaned;
                }
            }

            // Re-validate while typing if previously invalid or already touched/valid
            if (
                el.classList.contains('is-invalid') ||
                el.classList.contains('is-valid') ||
                el.dataset.touched === 'true'
            ) {
                validateField(el);
            }
            triggerLinkedFields(el, form);
        });

        // Validate on change (selects, checkboxes, dates, files)
        document.addEventListener('change', (e) => {
            const el = e.target;
            if (!(el instanceof HTMLInputElement || el instanceof HTMLTextAreaElement || el instanceof HTMLSelectElement)) {
                return;
            }
            const form = el.closest('form[data-validate]');
            if (!form) {
                return;
            }
            el.dataset.touched = 'true';
            validateField(el);
            triggerLinkedFields(el, form);
        });

        // Validate on blur (capture phase because blur does not bubble)
        document.addEventListener('blur', (e) => {
            const el = e.target;
            if (!(el instanceof HTMLInputElement || el instanceof HTMLTextAreaElement || el instanceof HTMLSelectElement)) {
                return;
            }
            const form = el.closest('form[data-validate]');
            if (!form) {
                return;
            }
            el.dataset.touched = 'true';
            validateField(el);
        }, true);

        // Global submit interception in capture phase so invalid submits block custom handlers
        document.addEventListener('submit', (e) => {
            const form = e.target;
            if (!(form instanceof HTMLFormElement) || !form.hasAttribute('data-validate')) {
                return;
            }

            const ok = validateForm(form, { focusFirst: true });
            if (!ok) {
                e.preventDefault();
                e.stopImmediatePropagation();
                return;
            }

            // For standard non-AJAX form submissions (e.g. Admin Login, Reset Password),
            // disable submit button and show loading label automatically.
            if (form.dataset.ajax !== 'true') {
                setLoading(form, true, form.dataset.loadingText || 'Please wait...');
            }
        }, true);

        // Watch dynamically mounted modals/templates (e.g. #enquiryForm)
        if (window.MutationObserver && document.body) {
            const observer = new MutationObserver((mutations) => {
                for (const m of mutations) {
                    m.addedNodes.forEach((node) => {
                        if (node instanceof Element) {
                            if (node.matches('form[data-validate]') || node.querySelector('form[data-validate]')) {
                                attachPasswordToggles(node);
                                hydrateServerErrors(node);
                            }
                        }
                    });
                }
            });
            observer.observe(document.body, { childList: true, subtree: true });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    window.IDFRValidator = {
        init,
        validateForm,
        validateField,
        applyServerErrors,
        clearForm,
        setLoading,
        attachPasswordToggles,
        hydrateServerErrors,
        REGEX
    };
})(window, document);

