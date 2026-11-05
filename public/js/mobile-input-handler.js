/**
 * ALTAMASH MOBILE - Common Mobile Number Input Restriction Handler
 * Enforces:
 *  1. Only digits (0-9) accepted - no letters or special characters
 *  2. Maximum and exact 10 digits allowed
 *  3. Dynamic handling for normal forms, Ajax, Bootstrap modals, and dynamically added rows
 *  4. Native numeric keypad on mobile devices (inputmode="numeric")
 */
(function () {
    'use strict';

    // Selector targeting all mobile/phone inputs across storefront and admin
    const MOBILE_SELECTOR = [
        'input[name="phone"]',
        'input[name="customer_phone"]',
        'input[name="whatsapp_number"]',
        'input[name*="phone" i]',
        'input[name*="mobile" i]',
        'input[id*="phone" i]',
        'input[id*="mobile" i]',
        'input[type="tel"]',
        '.mobile-number',
        '.phone-number',
        '.phone-input',
        '.mobile-only',
        'input[data-type="mobile"]',
        'input[data-type="phone"]'
    ].join(', ');

    function isMobileInput(el) {
        if (!el || el.tagName !== 'INPUT') return false;
        // Ignore file, hidden, submit, checkbox, radio
        const ignoredTypes = ['file', 'hidden', 'submit', 'checkbox', 'radio', 'button'];
        if (ignoredTypes.includes((el.type || '').toLowerCase())) return false;
        // Ignore if explicitly exempted
        if (el.classList.contains('no-mobile-mask') || el.dataset.mobileMask === 'false') return false;

        return el.matches(MOBILE_SELECTOR);
    }

    function sanitizeMobileValue(val, maxDigits = 10) {
        if (!val) return '';
        // Keep only numbers 0-9
        const digitsOnly = String(val).replace(/[^0-9]/g, '');
        // Slice to max digits (10 digits)
        return digitsOnly.slice(0, maxDigits);
    }

    function setupMobileInput(input) {
        if (!isMobileInput(input)) return;

        const maxDigits = parseInt(input.dataset.maxDigits, 10) || 10;
        input.maxLength = maxDigits;
        input.setAttribute('inputmode', 'numeric');
        input.setAttribute('autocomplete', 'tel');
        if (!input.getAttribute('pattern')) {
            input.setAttribute('pattern', '[0-9]{10}');
        }
        if (!input.getAttribute('title')) {
            input.setAttribute('title', 'Please enter a 10-digit mobile number');
        }

        const sanitized = sanitizeMobileValue(input.value, maxDigits);
        if (input.value !== sanitized) {
            input.value = sanitized;
        }
    }

    // Initialize all existing inputs on DOM ready
    function initAll() {
        document.querySelectorAll(MOBILE_SELECTOR).forEach(setupMobileInput);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }

    // 1. Keypress handler: block non-numeric keys and prevent typing > 10 digits
    document.addEventListener('keypress', function (e) {
        const input = e.target;
        if (!isMobileInput(input)) return;

        const maxDigits = parseInt(input.dataset.maxDigits, 10) || 10;
        const charCode = (e.which) ? e.which : e.keyCode;

        // Allow control keys (backspace, tab, enter, delete, arrows)
        if (e.ctrlKey || e.altKey || e.metaKey || charCode < 32) {
            return;
        }

        // Only allow 0-9 (charCode 48 to 57)
        if (charCode < 48 || charCode > 57) {
            e.preventDefault();
            return false;
        }

        // If selection range is 0 (cursor insertion) and already has 10 digits, block input
        const selStart = input.selectionStart || 0;
        const selEnd = input.selectionEnd || 0;
        const selectedLength = selEnd - selStart;
        const currentLength = (input.value || '').length;

        if (currentLength - selectedLength >= maxDigits) {
            e.preventDefault();
            return false;
        }
    }, true);

    // 2. Input handler: cleans pasted or auto-filled content and caps strictly at 10 digits
    document.addEventListener('input', function (e) {
        const input = e.target;
        if (!isMobileInput(input)) return;

        const maxDigits = parseInt(input.dataset.maxDigits, 10) || 10;
        const originalVal = input.value;
        const cleanVal = sanitizeMobileValue(originalVal, maxDigits);

        if (originalVal !== cleanVal) {
            input.value = cleanVal;
            // Trigger change event if needed
            input.dispatchEvent(new Event('change', { bubbles: true }));
        }
    }, true);

    // 3. Paste handler: filter out non-numeric characters and insert up to 10 digits
    document.addEventListener('paste', function (e) {
        const input = e.target;
        if (!isMobileInput(input)) return;

        e.preventDefault();
        const maxDigits = parseInt(input.dataset.maxDigits, 10) || 10;
        const pastedText = (e.clipboardData || window.clipboardData).getData('text') || '';
        const cleanPaste = sanitizeMobileValue(pastedText, maxDigits);

        const selStart = input.selectionStart || 0;
        const selEnd = input.selectionEnd || 0;
        const currentVal = input.value || '';

        const before = currentVal.slice(0, selStart);
        const after = currentVal.slice(selEnd);
        const combined = sanitizeMobileValue(before + cleanPaste + after, maxDigits);

        input.value = combined;

        // Set cursor position right after pasted content
        const newPos = Math.min(before.length + cleanPaste.length, combined.length);
        if (typeof input.setSelectionRange === 'function') {
            input.setSelectionRange(newPos, newPos);
        }

        input.dispatchEvent(new Event('input', { bubbles: true }));
        input.dispatchEvent(new Event('change', { bubbles: true }));
    }, true);

    // 4. Focus handler: ensure maxLength & inputmode on focus for dynamically created elements
    document.addEventListener('focusin', function (e) {
        if (isMobileInput(e.target)) {
            setupMobileInput(e.target);
        }
    }, true);

    // Expose utility globally if developers need programmatic access
    window.AltamashMobileUtils = window.AltamashMobileUtils || {};
    window.AltamashMobileUtils.sanitizeMobile = sanitizeMobileValue;
    window.AltamashMobileUtils.setupMobileInput = setupMobileInput;
})();
