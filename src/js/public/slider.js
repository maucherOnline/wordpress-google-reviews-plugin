import $ from 'jquery';
import Swiper, {Navigation} from 'swiper';

$(document).ready(function() {

    const $grwp_swiper_container = $('.reviews_embedder_slider');
    if ( $grwp_swiper_container.length ) {


        $grwp_swiper_container.each(function (i, slider) {

            const grwp_swiper = new Swiper(slider, {
                modules: [ Navigation ],
                spaceBetween: 15,
                autoplay: false,
                loop: true,
                breakpointsBase: 'container',
                breakpoints: {
                    690: {
                        slidesPerView: 2,
                    },
                    1024: {
                        slidesPerView: 3,
                    },
                    1200: {
                        slidesPerView: 4,
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
