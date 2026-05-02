import '../css/globalview.scss';
import $ from 'jquery';
import * as bootstrap from 'bootstrap';
import SmoothScroll from 'smooth-scroll';
import form from "./module/form/form.js";

window.$ = window.jQuery = $;

form();

function registerJQueryLegacyHelpers() {
    $.isArray = $.isArray || Array.isArray;
    $.isFunction = $.isFunction || ((value) => typeof value === 'function');
}

function registerBootstrapJQueryBridge() {
    $.fn.carousel = function carousel(option) {
        return this.each(function initCarousel() {
            const instance = bootstrap.Carousel.getOrCreateInstance(this, {
                interval: false,
                ride: false,
                pause: true
            });

            if (typeof option === 'number') {
                instance.to(option);
            } else if (typeof option === 'string' && typeof instance[option] === 'function') {
                instance[option]();
            }
        });
    };
}

function lazyLoadImages() {
    $('img.lazy').show().lazyload({
        effect: 'fadeIn',
        threshold: 200
    });
}

function setupScrollPanels() {
    const niceScrollConfig = {
        cursorcolor: '#7C7B7B',
        cursorborderradius: 0,
        cursorminheight: 32,
        spacebarenabled: true,
        railpadding: { top: 0, right: 0, left: 0, bottom: 0 },
        background: '#000',
        cursorwidth: '15',
        cursorborder: '0',
        touchbehavior: false,
        hwacceleration: true,
        grabcursorenabled: true,
        enabletranslate3d: true,
        autohidemode: 'scroll',
        smoothscroll: true,
        cursorfixedheight: '50'
    };

    $('.galerie-thumb-scroll').niceScroll(niceScrollConfig);
    $('.carousel-inner .left-panel').niceScroll(niceScrollConfig);
    $('.carousel-inner .right-panel').niceScroll(niceScrollConfig);
}

function setGalerieHeight() {
    const height = $('.carousel-inner .carousel-item.active .middle-panel img, .carousel-inner .item.active .middle-panel img').height();
    $('.carousel-inner .carousel-item.active .left-panel, .carousel-inner .item.active .left-panel').css('max-height', `${height}px`);
    $('.carousel-inner .carousel-item.active .right-panel, .carousel-inner .item.active .right-panel').css('max-height', `${height}px`);
}

function hidePreloader() {
    $('#status').fadeOut();
    $('#preloader').delay(350).fadeOut('slow');
    $('body').delay(350).css({ overflow: 'visible' });
}

function getIdFromHash(hash) {
    if (!hash || hash === '#') {
        return null;
    }

    try {
        return decodeURIComponent(hash.slice(1));
    } catch (e) {
        return hash.slice(1);
    }
}

function openAccordionPanelFromHash(hash, scrollIfOpen = false) {
    const id = getIdFromHash(hash);
    const header = id ? document.getElementById(id) : null;

    if (!header || !header.classList.contains('accordion-header')) {
        return false;
    }

    const collapse = header.nextElementSibling;
    if (!collapse || !collapse.classList.contains('accordion-collapse')) {
        return false;
    }

    const wasOpen = collapse.classList.contains('show');
    bootstrap.Collapse.getOrCreateInstance(collapse, { toggle: false }).show();

    if (wasOpen && scrollIfOpen) {
        $('html, body').animate({ scrollTop: $(header).offset().top - 200 }, 500);
    }

    return true;
}

function setupAccordionHashLinks() {
    setTimeout(() => openAccordionPanelFromHash(window.location.hash), 0);

    $(window).on('hashchange', function openHashAccordion() {
        openAccordionPanelFromHash(window.location.hash, true);
    });

    $(document).on('click', 'a[href*="#"]', function openLinkedAccordion() {
        const url = new URL(this.href, window.location.href);

        if (url.origin !== window.location.origin || url.pathname !== window.location.pathname || url.search !== window.location.search) {
            return;
        }

        setTimeout(() => openAccordionPanelFromHash(url.hash, true), 0);
    });
}

function setupLegacyInteractions() {
    const url = location.pathname + window.location.hash;
    if (url !== '/') {
        $(`nav .nav a[href="${url}"]`).parents('li').addClass('active');
        $('nav .nav a').on('click', function activateNavItem() {
            $(this).parents('li').addClass('active').siblings().removeClass('active');
        });
    }

    $('.admin-galery').on('click', 'h2', function toggleAdminGallery() {
        $(this).next('form').toggle();
    });

    $("a[class^='technics']").on('click', function openLinkedCollapse() {
        const collapseToOpen = $(this).attr('class');
        $('.panel-heading h4 a').removeClass('in').attr('aria-expanded', 'false').addClass('collapsed');
        $('div.panel-collapse').removeClass('in show').attr('aria-expanded', 'false');
        $(`#${collapseToOpen}`).prev('div').find('a').attr('aria-expanded', 'true').removeClass('collapsed');
        $(`#${collapseToOpen}`).addClass('in show').attr('aria-expanded', 'true');
    });

    $('#accordion').on('shown.bs.collapse', function scrollToPanel(e) {
        const panel = $(e.target);
        if (panel.length) {
            $('html, body').animate({ scrollTop: panel.offset().top - 200 }, 500);
        }
    });

    $('.search-trigger, .search_button').on('click', function toggleSearch(e) {
        e.preventDefault();
        $('.search-form').slideToggle(() => {
            $('.search_button').attr('aria-expanded', $('.search-form').is(':visible') ? 'true' : 'false');
        });
        $('nav.navbar-inverse.navbar-fixed-top').toggleClass('black');
    });

    $('label img').on('click', function checkImageInput() {
        $(this).prev('input').prop('checked', true);
    });

    $('.image-box').matchHeight();
    $('#flights .col-md-4 .box').matchHeight();

    $('a[href="#"][data-top!=true]').on('click', function preventEmptyHash(e) {
        e.preventDefault();
    });

    new SmoothScroll('a[href*="#"]', { offset: 55 });
    setupAccordionHashLinks();

    $(window).on('scroll', function updateScrollState() {
        const scroll = $(window).scrollTop();
        $('.navbar-inverse').toggleClass('navbar-scroll', scroll >= 100);
        $('.scrollToTop').toggle(scroll > 100);
    });

    const sections = $('section');
    const nav = $('nav');
    const navHeight = nav.outerHeight() || 0;
    $(window).on('scroll', function updateOnePageNav() {
        const currentPosition = $(this).scrollTop();
        sections.each(function setActiveSection() {
            const top = $(this).offset().top - navHeight;
            const bottom = top + $(this).outerHeight();
            if (currentPosition >= top && currentPosition <= bottom) {
                nav.find('a').removeClass('active');
                sections.removeClass('active');
                $(this).addClass('active');
                nav.find(`a[href="#${$(this).attr('id')}"]`).addClass('active');
            }
        });
    });

    if ($('.slider-background').length > 0) {
        const images = JSON.parse(document.body.attributes.getNamedItem('data-slider-images').value);
        $.backstretch(images, { duration: 4000, fade: 1000 });
    }

    $('.menu-toggle').on('click', function toggleMenu() {
        $('.menu').slideToggle('slow');
    });

    $('.scrollToTop').on('click', function scrollToTop() {
        $('html, body').animate({ scrollTop: 0 }, 800);
        return false;
    });

    if (document.readyState === 'complete') {
        hidePreloader();
    } else {
        $(window).one('load', hidePreloader);
    }

    setupWowAnimations();
}

function setupWowAnimations() {
    const elements = document.querySelectorAll('.wow');

    if (elements.length === 0) {
        return;
    }

    const reveal = (element) => {
        element.style.visibility = 'visible';
        element.classList.add('animated');
    };

    elements.forEach((element) => {
        element.style.visibility = 'hidden';
    });

    if (!('IntersectionObserver' in window)) {
        elements.forEach(reveal);
        return;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) {
                return;
            }

            reveal(entry.target);
            observer.unobserve(entry.target);
        });
    }, {
        rootMargin: '0px 0px -100px 0px',
        threshold: 0
    });

    elements.forEach((element) => observer.observe(element));
}

function setupGallery() {
    $('#GalerieCarousel').carousel({
        pause: true,
        interval: false
    });

    $('#infoCarousel').carousel({
        pause: true,
        interval: false
    });

    $('img.lazy, img.toLoad, img.preview').on('loadImage', function loadImage() {
        if ($(this).attr('data-src') !== $(this).attr('src')) {
            $(this).attr('src', $(this).attr('data-src'));
        }
    });

    $('img.lazy-thumb, img.thumb').on('loadImage', function loadThumb() {
        if ($(this).attr('data-src') !== $(this).attr('src')) {
            $(this).attr('src', $(this).attr('data-src'));
        }
    });

    $('div.map-div').on('loadMap', function loadMap() {
        if (!window.google || $(this).attr('data-map-set')) {
            return;
        }

        const position = new window.google.maps.LatLng($(this).attr('data-lat'), $(this).attr('data-lng'));
        const map = new window.google.maps.Map(this, {
            center: position,
            maxZoom: 16,
            minZoom: 7,
            zoom: 12
        });
        new window.google.maps.Marker({ position, map });
        $(this).attr('data-map-set', true);
    });

    $('#GalerieCarousel').on('slide.bs.carousel', function onGallerySlide(e) {
        const elem = $(e.relatedTarget);
        const currentIdx = elem.index();
        $('#infoCarousel').carousel(currentIdx);
        elem.find('img').trigger('loadImage');
        $(`#map-canvas-${currentIdx}`).trigger('loadMap');
        $('.wrapper-info').find('.infos').removeClass('open');
    });

    $(window).on('load', function initialGalleryLoad() {
        $('#map-canvas-0').trigger('loadMap');
        $('img.preview').each(function loadPreview(index, element) {
            setTimeout(() => $(element).trigger('loadImage'), 1000 * index);
        });
        $('img.lazy-thumb, img.thumb').each(function loadLazyThumb(index, element) {
            setTimeout(() => $(element).trigger('loadImage'), 100 * index);
        });
    });

    if ($('.carousel-inner').length >= 1) {
        $(window).on('resize', setGalerieHeight);
        $(document).on('ajaxComplete', setGalerieHeight);
        setGalerieHeight();
    }

    window.loadStackImage = function loadStackImage(elem) {
        $('#GalerieCarousel div.active img, #GalerieCarousel div.carousel-item.active img').attr('src', $(elem).attr('data-stack-uuid'));
        return false;
    };
}

async function init() {
    registerJQueryLegacyHelpers();
    registerBootstrapJQueryBridge();
    await Promise.all([
        import('jquery-lazyload/jquery.lazyload.js'),
        import('jquery-match-height'),
        import('jquery.nicescroll'),
        import('jquery-backstretch')
    ]);

    $(window).on('load', lazyLoadImages);
    $(setupScrollPanels);
    $(setupLegacyInteractions);
    $(setupGallery);
}

init();
