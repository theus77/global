'use strict';
import Swiper from 'swiper';
import {Autoplay, Pagination} from 'swiper/modules';

export const heroSlider = {
    selector: null,
    init: function (selector) {
        const self = this;
        this.selector = selector ?? 'hero-swiper';

        // Fix ie11 : Object doesn't support property or method isNaN
        Number.isNaN = Number.isNaN || function (value) {
            return typeof value === 'number' && isNaN(value);
        };

        Swiper.use([Pagination, Autoplay]);
        window.addEventListener('load', () => self.setup(self.selector));
    },
    setup: function (selector) {
        const selectorSwiper = `#${selector}`;
        const selectorSwiperPagination = `.${selector}-pagination`;
        const selectorSwiperPause = `.${selector}-pause`;
        const selectorSwiperPlay = `.${selector}-play`;

        const swiperElement = document.querySelector(selectorSwiper);

        const heroSwiper = new Swiper(swiperElement, {
            slidesPerView: 1,
            pagination: {
                el: selectorSwiperPagination,
                clickable: true
            },
            keyboard: {
                enabled: true,
                onlyInViewport: false
            },
            loop: true,
            autoplay: {
                delay: 7000,
                disableOnInteraction: true
            },
            on: {
                slideChange: function (swiper) {
                    const slide = swiper.slides[swiper.activeIndex];
                    const title = slide.getAttribute('data-hero-slider-title');
                    const url = slide.getAttribute('data-hero-slider-url');

                    const titleTarget = document.querySelector('[data-hero-slider-title-target]');

                    if (url && url.trim() !== '') {
                        const link = document.createElement('a');
                        link.setAttribute('href', url);
                        link.classList.add('text-reset');
                        link.textContent = title;

                        titleTarget.innerHTML = '';
                        titleTarget.appendChild(link);
                    } else {
                        titleTarget.innerHTML = '';
                        titleTarget.textContent = title;
                    }
                }
            }
        });

        document.querySelectorAll(selectorSwiperPlay).forEach((playElement) => {
            playElement.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                heroSwiper.autoplay.start();
                playElement.classList.add('d-none');

                const pauseElement = document.querySelector(selectorSwiperPause);
                pauseElement.classList.remove('d-none');
                pauseElement.focus();
            });
        });

        document.querySelectorAll(selectorSwiperPause).forEach((pauseElement) => {
            pauseElement.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                heroSwiper.autoplay.stop();
                pauseElement.classList.add('d-none');

                const playElement = document.querySelector(selectorSwiperPlay);
                playElement.classList.remove('d-none');
                playElement.focus();
            });
        });
    }
};
