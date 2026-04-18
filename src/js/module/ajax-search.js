/**
 * Load the ajax search
 *
 * Dom element with the class ajax-search-hide will be hidden
 * Dom element with the class ajax-search-remove will be deleted
 * Dom element with the class ajax-search-empty will be cleaned
 * Dom element with an data-ajax-search-replace attribute will see its content replace by the value of this attribute
 * Dom element with an data-ajax-search-load-more-url attribute will do an ajax query with the url defined in this attribute
 * Dom form.data-ajax-search-form on change
 *
 * Dom element with aria-live will be updated by the result of ajax query (loop on all json atributes starting by the string 'html_'
 *      the content is replaced if the role="status" or if it's change event on the search form
 *      the content is append if it's a "load more" event (and the role != "status")
 *
 * Dom element with data-ajax-search-loading-hide class are hidden during ajax calls and displayed after
 * Dom element with data-ajax-search-loading-show class are show during ajax call and hiddent after (i.e. loaders)
 *
 */
import adminMenu from "@elasticms/admin-menu";

export default function ajaxSearch(submitOnKeyStroke = false) {
    let requestInProgress = false;
    let nextRequest;
    let pushData = null;

    document.querySelectorAll('.ajax-search-hide').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.ajax-search-empty').forEach(el => el.innerHTML = '');
    document.querySelectorAll('.ajax-search-remove').forEach(el => el.remove());

    const updateDom = (msg) => {
        for (const key in msg) {
            if (key.startsWith('html_')) {
                const wrapper = document.createElement('div');
                wrapper.innerHTML = msg[key];

                wrapper.querySelectorAll('[aria-live]').forEach(item => {
                    const id = item.id;
                    if (!id) {
                        console.log('aria-live without id!');
                        return;
                    }
                    const target = document.getElementById(id);
                    if (target) {
                        if (msg.page === 0 || target.getAttribute('role') === 'status') {
                            target.innerHTML = item.innerHTML;
                        } else {
                            target.insertAdjacentHTML('beforeend', item.innerHTML);
                        }
                    }
                });

                if (msg.title) {
                    console.log(msg.title);
                    document.title = msg.title;
                }

                if (msg.title_header) {
                    const textarea = document.createElement('textarea');
                    textarea.innerHTML = msg.title_header;
                    document.title = textarea.value;
                }

                const loadMore = document.querySelector('[data-ajax-search-load-more-url]');
                if (loadMore) {
                    if (msg.load_more_path && msg.load_more_path !== '') {
                        loadMore.style.display = '';
                        loadMore.dataset.ajaxSearchLoadMoreUrl = msg.load_more_path;
                    } else {
                        loadMore.style.display = 'none';
                        loadMore.dataset.ajaxSearchLoadMoreUrl = '';
                    }
                }

                adminMenu();
            }
        }
    };

    const doAjaxRequest = (url) => {
        if (requestInProgress) {
            nextRequest = url;
            return;
        }
        requestInProgress = true;

        document.querySelectorAll('.data-ajax-search-loading-hide').forEach(el => el.style.display = 'none');
        document.querySelectorAll('.data-ajax-search-loading-show').forEach(el => el.style.display = '');

        fetch(url)
            .then(res => res.json())
            .then(updateDom)
            .catch(() => console.log('error with this page'))
            .finally(() => {
                requestInProgress = false;
                if (nextRequest && nextRequest !== url) {
                    const newUrl = nextRequest;
                    nextRequest = false;
                    doAjaxRequest(newUrl);
                    return;
                }
                nextRequest = false;
                document.querySelectorAll('.data-ajax-search-loading-hide').forEach(el => el.style.display = '');
                document.querySelectorAll('.data-ajax-search-loading-show').forEach(el => el.style.display = 'none');
            });
    };

    const loadMore = (event) => {
        const url = event.currentTarget.dataset.ajaxSearchLoadMoreUrl;
        if (url) {
            doAjaxRequest(url);
        }
    };

    document.querySelectorAll('[data-ajax-search-replace]').forEach(item => {
        const targetSelector = item.dataset.ajaxSearchReplace;
        const replacement = document.querySelector(targetSelector);
        if (replacement) {
            replacement.querySelectorAll('[data-ajax-search-load-more-url]').forEach(el => {
                el.addEventListener('click', loadMore);
            });
            item.innerHTML = '';
            item.appendChild(replacement);
        }
    });

    const form = document.querySelector('[data-ajax-search-form]');
    if (!form) return;

    const formChangeFunction = (event) => {
        formSubmitFunction(event, false);
    };

    const formSubmitFunction = (event, clearAriaLive = true) => {
        event.preventDefault();

        const arias = {};
        document.querySelectorAll('[aria-live]').forEach(el => {
            const id = el.id;
            if (id) arias[id] = el.innerHTML;
        });

        const h1 = document.querySelector('h1');
        const loadMoreEl = document.querySelector('[data-ajax-search-load-more-url]');
        const loadMorePath = loadMoreEl ? loadMoreEl.dataset.ajaxSearchLoadMoreUrl : '';

        pushData = {
            title: h1 ? h1.textContent : '',
            documentTitle: document.title,
            arias,
            loadMorePath
        };

        document.querySelectorAll('[aria-live]').forEach(el => {
            if (clearAriaLive || el.getAttribute('role') !== 'status') {
                el.innerHTML = '';
            }
        });

        const formAction = form.getAttribute('action');
        const query = new URLSearchParams(new FormData(form)).toString();
        const ajaxUrl = form.dataset.ajaxSearchForm;

        history.pushState(pushData, pushData.title, `${formAction}?${query}`);
        doAjaxRequest(`${ajaxUrl}?${query}`);
    };

    window.onpopstate = (event) => {
        if (!pushData) return;

        for (const id in pushData.arias) {
            const el = document.getElementById(id);
            if (el) {
                el.innerHTML = pushData.arias[id];
            }
        }

        const h1 = document.querySelector('h1');
        if (h1) h1.textContent = pushData.title;

        document.title = pushData.documentTitle;

        const loadMore = document.querySelector('[data-ajax-search-load-more-url]');
        if (loadMore) {
            if (pushData.loadMorePath && pushData.loadMorePath !== '') {
                loadMore.style.display = '';
                loadMore.dataset.ajaxSearchLoadMoreUrl = pushData.loadMorePath;
            } else {
                loadMore.style.display = 'none';
                loadMore.dataset.ajaxSearchLoadMoreUrl = '';
            }
        }

        pushData = event.state;
    };

    form.addEventListener('change', formChangeFunction);
    form.addEventListener('submit', e => e.preventDefault());

    if (submitOnKeyStroke) {
        form.querySelectorAll('input[type=text], textarea').forEach(input => {
            input.addEventListener('input', formSubmitFunction);
        });
    }
}

