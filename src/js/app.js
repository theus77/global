import '../css/app.scss';
import * as bootstrap from 'bootstrap'; // Import Bootstrap's JS API
import { mainNavigation } from './module/main-navigation.js';
import form from "./module/form/form.js";
import ajaxSearch from "./module/ajax-search.js";
import { heroSlider } from './module/hero-slider.js';
import adminMenu from '@elasticms/admin-menu';
import cookies from './module/cookies.js';
import { showConsoleWarning } from './module/warningConsole.js';
import back2top from "./module/back2top.js";

mainNavigation(bootstrap).init();
adminMenu();
form();
ajaxSearch();
heroSlider.init();
cookies();
showConsoleWarning();
back2top("#wrapper > .back2top");

(() => {
    const searchModal = document.getElementById('searchModal')
    searchModal.addEventListener('shown.bs.modal', event => {
        document.querySelector('.search-bar [name="q"]').focus({
            preventScroll: false,
            focusVisible: true
        });
    })
})();
