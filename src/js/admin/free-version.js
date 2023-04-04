import $ from 'jquery';

$(document).ready(function() {

    // show video modal in free version
    function trigger_modal() {

        const $modal = $("#how_to_modal");
        if (localStorage.hideModal || !$modal) return;

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

    /*
    const $pullButtonFree = $('.button.pull-reviews.free');

    $pullButtonFree.on('click', function () {

        const $that = $(this);
        const $submit = $('#submit');
        const place_id = $('input[name="google_reviews_option_name[gmb_id_1]"]').val();
        const language = $('select#reviews_language_3').val();
        const $errors = $('#errors');

        if (place_id === '') return false;

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
     */

});
