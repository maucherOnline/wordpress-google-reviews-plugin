import $ from 'jquery';

const $sidebar = $('.g-review-sidebar');
const $revButton = $('#g-review .g-badge');
const $close = $('.grwp-header .grwp-close');

$revButton.click(function (e) {
    e.preventDefault();
    $sidebar.removeClass('hide');
})

$close.click(function () {
    $sidebar.addClass('hide');
});