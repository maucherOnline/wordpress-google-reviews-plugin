import $ from 'jquery';
import Swiper, {Autoplay, Navigation} from 'swiper';


$(document).ready(function() {

    const $grwp_swiper_container = $('.reviews_embedder_slider');
    if ( $grwp_swiper_container.length ) {

        $grwp_swiper_container.each(function (i, slider) {

            let autoplay = false;
            if (swiperSettings.autoplayDelay > 0) {
                autoplay = {delay: parseInt(swiperSettings.autoplayDelay) * 1000}
            }

            let loop = true;
            if (swiperSettings.disableLoop) {
                loop = false;
            }

            //console.log("swiperSettings", swiperSettings);
            //console.log("autoplay", autoplay);
            //console.log("loop", loop);

            const grwp_swiper = new Swiper(slider, {
                modules: [ Autoplay, Navigation ],
                spaceBetween: 15,
                autoplay: autoplay,
                loop: loop,
                //grabCursor: true,
                //pauseOnMouseEnter: true,
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
