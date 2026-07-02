(function ($) {
    'use strict';

    $(document).ready(function () {

        // ── Tab switching ──────────────────────────────────
        // Only buttons with a data-tab switch panels; plain link tabs
        // (e.g. "Translation") navigate to their own subpage instead.
        $('.grwp-tab-btn[data-tab]').on('click', function () {
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

        // ── Slider settings: marquee hides the options above it ──
        // With marquee active, autoplay/arrows/loop/pause don't apply, so all
        // rows above the marquee checkbox are hidden. In the free version the
        // checkbox is a disabled PRO teaser (no id on it), so nothing happens.
        function syncMarqueeRows() {
            var $cb = $('#grwp-panel-slider input[type="checkbox"]#marquee_slider');
            if (!$cb.length) return;
            $cb.closest('tr').prevAll('tr').toggleClass('hidden', $cb.is(':checked'));
        }
        $(document).on('change', '#marquee_slider', syncMarqueeRows);
        syncMarqueeRows();

        // ── Grid settings: "Load more" off hides the options below it ──
        function syncLoadMoreRows() {
            var $cb = $('#grwp-panel-grid input[type="checkbox"]#show_more_grid');
            if (!$cb.length) return;
            $cb.closest('tr').nextAll('tr').toggleClass('hidden', !$cb.is(':checked'));
        }
        $(document).on('change', '#show_more_grid', syncLoadMoreRows);
        syncLoadMoreRows();

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

