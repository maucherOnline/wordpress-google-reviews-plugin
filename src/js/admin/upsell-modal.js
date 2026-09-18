import $ from 'jquery';

// Upsell modal on the plugin start screen (markup + state from GRWP_Upsell_Modal).
// Opens on load unless the user already closed it today; closing it stores the
// dismissal server-side so it stays hidden until the next day.
$(document).ready(function () {

    const config = window.grwp_upsell;
    const $overlay = $('#grwp-upsell-modal');

    if (!config || !$overlay.length) {
        return;
    }

    let dismissed = false;
    let $lastFocus = null;

    const dismiss = function () {
        if (dismissed) {
            return;
        }
        dismissed = true;

        const data = new FormData();
        data.append('action', config.action);
        data.append('nonce', config.nonce);

        // sendBeacon survives page navigation (e.g. when the CTA is clicked).
        if (navigator.sendBeacon && navigator.sendBeacon(config.ajax_url, data)) {
            return;
        }
        $.post(config.ajax_url, { action: config.action, nonce: config.nonce });
    };

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

    if (config.auto_open) {
        open();
    }

});
