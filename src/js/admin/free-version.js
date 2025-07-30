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

});
