import $ from 'jquery';

$(document).ready(function() {
    const is_badge = $('#style_2').val() === 'Badge';

    if (is_badge) {
        $('.preview_section').addClass('is_badge');
    }
});
