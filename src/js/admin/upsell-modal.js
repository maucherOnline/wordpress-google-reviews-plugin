import $ from 'jquery';

// Upsell modal on the plugin start screen (markup from GRWP_Upsell_Modal).
// Opens on load unless it was already closed today; the close date is kept in
// localStorage, so the modal stays hidden until the next (local) day.
$(document).ready(function () {

    const STORAGE_KEY = 'grwp_upsell_modal_dismissed';
    const $overlay = $('#grwp-upsell-modal');

    if (!$overlay.length) {
        return;
    }

    // Local calendar date as YYYY-MM-DD.
    const today = function () {
        const d = new Date();
        return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
    };

    // localStorage can throw (private mode, blocked site data) — then just show the modal.
    const dismissedToday = function () {
        try {
            return window.localStorage.getItem(STORAGE_KEY) === today();
        } catch (e) {
            return false;
        }
    };

    const dismiss = function () {
        try {
            window.localStorage.setItem(STORAGE_KEY, today());
        } catch (e) {
            // ignore
        }
    };

    let $lastFocus = null;

    const open = function () {
        $lastFocus = $(document.activeElement);
        $overlay.addClass('is-open').attr('aria-hidden', 'false');
        $('html').addClass('rem-locked');
        $overlay.find('[data-rem-close]').trigger('focus');
    };

    const close = function () {
        $overlay.removeClass('is-open').attr('aria-hidden', 'true');
        $('html').removeClass('rem-locked');
        dismiss();
        if ($lastFocus && $lastFocus.length) {
            $lastFocus.trigger('focus');
        }
    };

    $overlay.on('click', '[data-rem-close]', close);

    // Click on the backdrop (outside the dialog) closes the modal.
    $overlay.on('click', function (e) {
        if (e.target === this) {
            close();
        }
    });

    $(document).on('keydown', function (e) {
        if (e.key === 'Escape' && $overlay.hasClass('is-open')) {
            close();
        }
    });

    // Going for the upgrade also counts as "seen today".
    $overlay.on('click', '.rem-buy, .rem-foot a', dismiss);

    if (!dismissedToday()) {
        open();
    }

});
