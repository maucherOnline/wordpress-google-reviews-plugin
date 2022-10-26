(function( $ ) {
    'use strict';

    let swiper;

    if ( $('.mySwiper').length ) {
        swiper = new Swiper('.mySwiper', {
            // Optional parameters
            direction: 'horizontal',
            slidesPerView: 3,
            spaceBetween: 0,
            autoplay: false,
            loop: true,
            breakpoints: {
                // when window width is >= 480px
                270: {
                    slidesPerView: 1,
                    spaceBetween: 16
                },
                690: {
                    slidesPerView: 2,
                    spaceBetween: 24
                },
                1024: {
                    slidesPerView: 3,
                    spaceBetween: 24
                }
            },
            // Navigation arrows
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
        });
    }

    if ( swiper ) {
        $(window).resize(function(){
            var ww = $(window).width()
            swiperWidth();
            swiper.update();
        })
    }
    $(window).trigger('resize')

    function swiperWidth(){

        let width = 1100;
        if ($('#main').length > 0){
            width = $('#main').innerWidth();
        }
        $('.mySwiper').css('width', width + 'px');
    }

    if ( swiper ) {
        swiperWidth();
    }

    $('.g-review').matchHeight({
        byRow: true,
        property: 'height',
        target: null,
        remove: false
    });

})( jQuery );
