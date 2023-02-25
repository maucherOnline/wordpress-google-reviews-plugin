import $ from 'jquery';

let swiper;

$(document).ready(function() {

    if ( $('.reviews_embedder_slider').length ) {

        console.log('loaded');

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
});