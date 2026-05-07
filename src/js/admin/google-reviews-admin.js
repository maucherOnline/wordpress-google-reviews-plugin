(function ($) {
    'use strict';

    $(document).ready(function () {

        // ── Tab switching ──────────────────────────────────
        $('.grwp-tab-btn').on('click', function () {
            var tab = $(this).data('tab');
            $('.grwp-tab-btn').removeClass('active');
            $(this).addClass('active');
            $('.grwp-tab-panel').removeClass('active');
            $('#grwp-panel-' + tab).addClass('active');
            sessionStorage.setItem('grwp_active_tab', tab);
        });

        var lastTab = sessionStorage.getItem('grwp_active_tab');
        if (lastTab) {
            $('.grwp-tab-btn[data-tab="' + lastTab + '"]').trigger('click');
        }

        // ── Collapsible preview (standardmaessig offen) ────
        $('#grwp-preview-toggle-btn').on('click', function () {
            var $body  = $('#grwp-preview-body');
            var $arrow = $('#grwp-preview-arrow');
            $body.toggleClass('closed');
            $arrow.text($body.hasClass('closed') ? '▼' : '▲');
        });

        // ── Copy shortcode buttons ─────────────────────────
        $('.grwp-copy-btn').on('click', function () {
            var btn = $(this);
            var val = btn.data('clipboard');

            function onSuccess() {
                btn.text('✅ Copied!');
                setTimeout(function () {
                    btn.html('📋 Copy');
                }, 2000);
            }

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(val).then(onSuccess);
            } else {
                var $tmp = $('<textarea>').val(val).css({ position: 'fixed', top: 0, left: 0, opacity: 0 });
                $('body').append($tmp);
                $tmp[0].focus();
                $tmp[0].select();
                try {
                    document.execCommand('copy');
                    onSuccess();
                } catch (e) {}
                $tmp.remove();
            }
        });

    });
})(jQuery);
