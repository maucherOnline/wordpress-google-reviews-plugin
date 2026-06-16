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
            var $btn  = $(this);
            var input = $btn.prevAll('input[type="text"]').first()[0];
            if (!input) return;

            navigator.clipboard.writeText(input.value).then(function () {
                var original = $btn.html();
                $btn.html('<span class="dashicons dashicons-yes"></span> Copied').addClass('grwp-copied');
                setTimeout(function () {
                    $btn.html(original).removeClass('grwp-copied');
                }, 1800);
            });
        });

    });
})(jQuery);

