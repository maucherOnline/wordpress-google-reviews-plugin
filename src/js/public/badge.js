import $ from 'jquery';

const $sidebar = $('.g-review-sidebar');
const $revButton = $('#g-review .g-badge');

$revButton.click(function (e) {
    e.preventDefault();
    $sidebar.removeClass('hide');
})
