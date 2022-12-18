(function( $ ) {
    'use strict';

    let swiper;

    if ( $('.reviews_embedder_slider').length ) {

        $('.reviews_embedder_slider').each(function (i, slider) {

            swiper = new Swiper(slider, {
                cssMode: true,
                slidesPerView: 1,
                spaceBetween: 0,
                autoplay: false,
                loop: true,
                breakpointsBase: 'container',
                breakpoints: {
                    690: {
                        slidesPerView: 2,
                        spaceBetween: 0
                    },
                    1024: {
                        slidesPerView: 3,
                        spaceBetween: 0
                    },
                    1200: {
                        slidesPerView: 4,
                        spaceBetween: 0
                    }
                },
                // Navigation arrows
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
            });
        });

    }

    /*
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
        $('.reviews_embedder_slider').css('width', width + 'px');
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

     */

})( jQuery );
