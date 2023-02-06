(function ($) {
    'use strict';

    $(document).ready(function () {

        const $search = $('.js-serp-business-search');
        const $searchButtonPro = $('.button.search-business.pro');
        const $pullButtonPro = $('.button.pull-reviews.pro');
        const $pullButtonFee = $('.button.pull-reviews.free');
        const $buttonRow = $('.serp-container .button-row');
        const $error = $('#errors');
        const $languageDropdown = $('#reviews_language_3');
        const $submitButton = $('input[type="submit"]');
        const $showDummyContent = $('#show_dummy_content');


        // remove disabled attribute when search field is changed
        $search.on('keyup change', function () {
            $searchButtonPro.removeAttr('disabled');
        });

        // prevent 'enter' from submitting form
        $search.on('keypress', function (e) {
            if (e.keyCode == '10' || e.keyCode == '13') {
                e.preventDefault();
            }
        })

        // Search for business
        $searchButtonPro.click(function () {

            const $that = $(this);

            if ($that.attr('disabled')) {
                return;
            }

            $.ajax({
                url: js_global.wp_ajax_url,
                data: {
                    action: 'handle_serp_business_search',
                    search: $search.val(),
                    language: js_global.language
                },
                beforeSend: function () {
                    $buttonRow
                        .addClass('busy');

                    $searchButtonPro
                        .attr('disabled', true);
                },
                success: function (response) {

                    if (!response) {
                        $error.html('Error in search response. Please try again.');
                    } else if (undefined === response.data || undefined === response.data.html) {
                        $error.html('Search response failed. Please try again.');
                    } else if (response && response.data.html === '') {
                        $error.html('Results empty. Please try again.');
                    } else if (!response.success) {
                        $error.html(response.data.html);
                    } else {
                        if ($search.hasClass('has-error')) {
                            $search.removeClass('has-error');
                            $error.fadeOut().empty();
                        }

                        $search.siblings('.serp-results').html(response.data.html).slideDown();
                    }
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {

                },
                complete: function () {
                    $buttonRow
                        .removeClass('busy');
                    $searchButtonPro
                        .removeAttr('disabled');
                }
            });
        });

        // handle clicks on location dropdown (selection)
        $('.serp-search').on('click', '.js-serp-result', function () {
            const $this = $(this);
            const data_id = $this.val();
            const location_name = $this.parent().text();

            $this.closest('.serp-results').slideUp();

            $('.js-serp-data-id').attr('value', $this.val());
            $('.js-serp-business-search').val($.trim($this.parent().text()));

            $('.button.pull-reviews').attr('disabled', true);

            $.ajax({
                url: js_global.wp_ajax_url,
                data: {
                    action: 'handle_location_saving',
                    data_id: data_id,
                    location_name: location_name
                },
                beforeSend: function () {
                    disableButtonsWhileSaving();
                    $searchButtonPro
                        .attr('disabled', true);
                    $error.hide();
                },
                complete: function () {
                    enableButtonsAfterSaving()
                }
            });

        });

        $search.on('click', function () {
            const $this = $(this);
            const $resultsContainer = $('.serp-results');

            if (!$resultsContainer.children().length || !$this.text().length) {
                return;
            }

            $resultsContainer.slideDown();
        });

        $search.on('search', function () {
            $('.js-serp-data-id').attr('value', '');
            $('.serp-results').slideUp();
        });

        // remove list, when user clicks anywhere else
        $(document).on('click', function (e) {
            const $container = $('.serp-search');
            const $resultsContainer = $('.serp-results');

            if (!$(e.target).closest($container).length) {
                $resultsContainer.slideUp();
            }
        });

        // save dropdown language on change
        $languageDropdown.change(function () {
            const language = $(this).val();

            $.ajax({
                url: js_global.wp_ajax_url,
                data: {
                    action: 'handle_language_saving',
                    search: language,
                },
                beforeSend: function () {
                    disableButtonsWhileSaving();
                },
                complete: function () {
                    enableButtonsAfterSaving()
                }
            });

        });

        // PRO: pull reviews button
        $pullButtonPro.on('click', function () {

            const $that = $(this);
            if ($that.attr('disabled')) {
                return;
            }

            const $submit = $('#submit');
            let has_error = false;

            $.ajax({
                url: js_global.wp_ajax_url,
                data: {
                    action: 'handle_get_reviews_pro_api'
                },
                beforeSend: function () {
                    $buttonRow
                        .addClass('busy');

                    $pullButtonPro
                        .attr('disabled', true);
                },
                success: function (response) {
                    // if everything's ok, do nothing
                    if (response === "0") {
                        return false;
                    } else if (!response) {
                        $error.html('Error in reviews response. Please try again.');
                        has_error = true;
                    } else if (undefined === response.data || undefined === response.data.html) {
                        $error.html('Reviews response failed. Please try again.');
                        has_error = true;
                    } else if (response && response.data.html === '') {
                        $error.html('Reviews results empty. Please try again.');
                        has_error = true;
                    } else if (!response.success) {
                        $error.html(response.data.html);
                        has_error = true;
                    }
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {

                },
                complete: function (jqXHR, textStatus) {
                    $buttonRow
                        .removeClass('busy');
                    if (!has_error) {
                        if ( $showDummyContent.is(':checked') ) {
                            $showDummyContent.click();
                        }
                        $submit.click();
                    } else {
                        $pullButtonPro
                            .removeAttr('disabled');
                    }
                }
            });
        });


        /**
         * FREE: pull reviews button
         */
        $pullButtonFee.on('click', function () {

            const $that = $(this);
            const $submit = $('#submit');
            const place_id = $('input[name="google_reviews_option_name[gmb_id_1]"]').val();
            const language = $('select#reviews_language_3').val();
            const $errors = $('#errors');

            $.ajax({
                url: js_global.wp_ajax_url,
                data: {
                    action: 'get_reviews_free_api',
                    place_id: place_id,
                    language: language
                },
                beforeSend: function () {
                    $that
                        .addClass('pulling')
                        .attr('disabled', true);
                },
                success: function (response) {

                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    const message = errorThrown + ' - Please double-check your Place ID.';
                    $errors.text(message);
                },
                complete: function (XMLHttpRequest, textStatus) {
                    $that
                        .removeClass('pulling')
                        .attr('disabled', false);

                    if (textStatus !== 'error') {

                        if ( $showDummyContent.is(':checked') ) {
                            $showDummyContent.click();
                        }
                        $submit.click();
                    }
                }
            });
        });

        // disable buttons when ajax saving
        function disableButtonsWhileSaving() {
            $submitButton
                .attr('disabled', true);
            $pullButtonPro
                .attr('disabled', true);
            $pullButtonFee
                .attr('disabled', true);
        }

        // enable buttons after ajax saving
        function enableButtonsAfterSaving() {
            $pullButtonPro
                .removeAttr('disabled');
            $submitButton
                .removeAttr('disabled');
            $pullButtonFee
                .removeAttr('disabled');
        }

        // show video modal in free version
        function trigger_modal() {
            const $modal = $("#how_to_modal");
            if (localStorage.hideModal || !$modal ) return;

            const $close = $("#modal_close");
            const $overlay = $('.modal .modal-overlay');
            const $body = $('body');
            const $video = $('.responsive_iframe iframe');

            $modal.show();

            // make body fixed to prevent scrolling
            $body.addClass('fixed');
            $modal.removeClass('hide');

            $close.click(function () {
                hideModal();
            });

            $overlay.click(function () {
                hideModal();
            });

            function hideModal() {
                $modal.hide();
                $body.removeClass('fixed');
                var iframeSrc = $video.attr('src');
                $video.attr('src', iframeSrc);
                localStorage.hideModal = true;
            }

        }

        trigger_modal();

    });
})(jQuery);
