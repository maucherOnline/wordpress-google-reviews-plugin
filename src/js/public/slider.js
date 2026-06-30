import $ from 'jquery';
import Swiper, {Autoplay, Navigation} from 'swiper';


$(document).ready(function() {

    const $grwp_swiper_container = $('.reviews_embedder_slider');
    if ( $grwp_swiper_container.length ) {

        const marquee = !! swiperSettings.marquee;

        $grwp_swiper_container.each(function (i, slider) {

            const breakpoints = {
                690: {
                    slidesPerView: 2,
                },
                1024: {
                    slidesPerView: 3,
                },
                1200: {
                    slidesPerView: 4,
                }
            };

            if ( marquee ) {
                // Marquee mode: slides scroll continuously at a constant speed.
                // A zero autoplay delay combined with a long transition speed and
                // a linear timing function (set on the wrapper below) produces a
                // smooth, never-stopping ticker rather than stepped slides.
                slider.classList.add('grwp-marquee');

                const marqueeSpeed = parseInt(swiperSettings.marqueeSpeed) > 0
                    ? parseInt(swiperSettings.marqueeSpeed)
                    : 5000;

                const grwp_swiper = new Swiper(slider, {
                    modules: [ Autoplay ],
                    spaceBetween: 15,
                    loop: true,
                    speed: marqueeSpeed,
                    allowTouchMove: false,
                    autoplay: {
                        delay: 0,
                        disableOnInteraction: false,
                    },
                    breakpointsBase: 'container',
                    breakpoints: breakpoints,
                });

                grwp_swiper.wrapperEl.style.transitionTimingFunction = 'linear';

                return;
            }

            let autoplay = false;
            if (swiperSettings.autoplayDelay > 0) {
                autoplay = {delay: parseInt(swiperSettings.autoplayDelay) * 1000}
                if (swiperSettings.pauseOnHover) {
                    autoplay.pauseOnMouseEnter = true;
                    autoplay.disableOnInteraction = false;
                }
            }

            let loop = true;
            if (swiperSettings.disableLoop) {
                loop = false;
            }

            const grwp_swiper = new Swiper(slider, {
                modules: [ Autoplay, Navigation ],
                spaceBetween: 15,
                autoplay: autoplay,
                loop: loop,
                breakpointsBase: 'container',
                breakpoints: breakpoints,
                // Navigation arrows
                navigation: {
                    nextEl: '.grwp-swiper-button-next',
                    prevEl: '.grwp-swiper-button-prev',
                },
            });
        });
    }
});
