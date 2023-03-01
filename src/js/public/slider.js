import $ from 'jquery';
import Swiper, {Navigation} from 'swiper';

$(document).ready(function() {

    const $grwp_swiper_container = $('.reviews_embedder_slider');
    if ( $grwp_swiper_container.length ) {


        $grwp_swiper_container.each(function (i, slider) {

            const grwp_swiper = new Swiper(slider, {
                modules: [ Navigation ],
                slidesPerView: 1,
                spaceBetween: 30,
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
                    nextEl: '.grwp-swiper-button-next',
                    prevEl: '.grwp-swiper-button-prev',
                },
            });
        });

    }
});
