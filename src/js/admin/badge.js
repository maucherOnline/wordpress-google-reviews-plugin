import $ from 'jquery';

$(document).ready(function() {
    const is_badge = $('#style_2').val() === 'Badge';

    // hide preview background for badge layout
    if (is_badge) {
        $('.preview_section').addClass('is_badge');
    }

    // hide style options for badge layout on change
    /*
    const $layout_select = $('#style_2');
    $layout_select.change(function () {
        const $layout_style_option = $('.form-table .layout_style');
        if ($(this).val() === 'Badge') {
            $layout_style_option.hide();
        } else {
            $layout_style_option.show();
        }
    })

    $layout_select.change();
    */

});
