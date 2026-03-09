import $ from 'jquery';

$(document).ready(function() {

    const $search = $('.js-serp-business-search');
    const $searchButton = $('.button.search-business.pro');
    const $pullButton = $('.button.pull-reviews.pro');
    const $buttonRow = $('.serp-container .button-row');
    const $error = $('#errors');
    const $languageDropdown = $('#reviews_language_3');
    const $submitButton = $('input[type="submit"]');
    const $showDummyContent = $('#show_dummy_content');
    const $serpSearch = $('.serp-search');

    // remove disabled attribute when search field is changed
    $search.on('keyup change', function () {
        $searchButton.removeAttr('disabled');
    });

    // prevent 'enter' from submitting form
    $search.on('keypress', function (e) {
        if (e.keyCode == '10' || e.keyCode == '13') {
            e.preventDefault();
        }
    })

    // Search for business
    $searchButton.click(function () {

        const $that = $(this);

        if ($that.attr('disabled')) {
            return;
        }

        $.ajax({
            url: js_global.wp_ajax_url,
            data: {
                action: 'handle_serp_business_search',
                search: $search.val(),
                language: js_global.language,
                _ajax_nonce: js_global.nonce
            },
            beforeSend: function () {
                $buttonRow
                    .addClass('busy');

                $searchButton
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
                $searchButton
                    .removeAttr('disabled');
            }
        });
    });

    // handle clicks on location dropdown (selection)
    $serpSearch.on('click', '.js-serp-result', function () {
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
                location_name: location_name,
                _ajax_nonce: js_global.nonce
            },
            beforeSend: function () {
                disableButtonsWhileSaving();
                $searchButton
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
                _ajax_nonce: js_global.nonce
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
    $pullButton.on('click', function () {

        const $that = $(this);
        if ($that.attr('disabled')) {
            return;
        }

        const $submit = $('#submit');
        let has_error = false;

        $.ajax({
            url: js_global.wp_ajax_url,
            data: {
                action: 'handle_get_reviews_pro_api',
                _ajax_nonce: js_global.nonce
            },
            beforeSend: function () {
                $buttonRow
                    .addClass('busy');

                $pullButton
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

                const test = 1;
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
                    $pullButton
                        .removeAttr('disabled');
                    $error.show();
                }
            }
        });
    });


    // disable buttons when ajax saving
    function disableButtonsWhileSaving() {
        $submitButton
            .attr('disabled', true);
        $pullButton
            .attr('disabled', true);
    }

    // enable buttons after ajax saving
    function enableButtonsAfterSaving() {
        $pullButton
            .removeAttr('disabled');
        $submitButton
            .removeAttr('disabled');
    }



});
