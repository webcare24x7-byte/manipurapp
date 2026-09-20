(function () {
    'use strict';

    var app = window.app || {};
    var basePath = (app.basePath || '').replace(/\/$/, '');

    function showToast(message) {
        var toast = document.getElementById('memberapp-toast');
        if (!toast) return;
        toast.textContent = message;
        toast.classList.add('show');
        window.clearTimeout(showToast.timer);
        showToast.timer = window.setTimeout(function () {
            toast.classList.remove('show');
        }, 2200);
    }

    window.memberAppToast = showToast;

    /* -------------------------------------------------------------
     * Search
     * ------------------------------------------------------------- */
    var search = document.getElementById('service-search');
    var searchWrap = search ? search.closest('.ma-search') : null;
    var clearSearch = document.querySelector('[data-search-clear]');
    var serviceCards = Array.prototype.slice.call(document.querySelectorAll('[data-service]'));

    function filterServices() {
        if (!search) return;
        var query = search.value.trim().toLowerCase();
        if (searchWrap) searchWrap.classList.toggle('has-value', query !== '');

        serviceCards.forEach(function (card) {
            var text = (card.getAttribute('data-service') || '').toLowerCase();
            var visible = !query || text.indexOf(query) !== -1;
            card.hidden = !visible;
        });
    }

    if (search) {
        search.addEventListener('input', filterServices);
        search.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                search.value = '';
                filterServices();
                search.blur();
            }
        });
    }

    if (clearSearch) {
        clearSearch.addEventListener('click', function () {
            if (!search) return;
            search.value = '';
            filterServices();
            search.focus();
        });
    }

    /* -------------------------------------------------------------
     * Coming soon actions
     * ------------------------------------------------------------- */
    document.querySelectorAll('[data-coming-soon]').forEach(function (element) {
        element.addEventListener('click', function (event) {
            var href = element.getAttribute('href');
            if (href && href.indexOf(basePath + '/member') === 0) return;
            event.preventDefault();
            showToast('This service is coming soon.');
        });
    });

    /* -------------------------------------------------------------
     * Language menu
     * ------------------------------------------------------------- */
    var languageToggle = document.querySelector('[data-language-toggle]');
    var languageMenu = document.querySelector('[data-language-menu]');

    function closeLanguageMenu() {
        if (languageMenu) languageMenu.hidden = true;
    }

    if (languageToggle && languageMenu) {
        languageToggle.addEventListener('click', function (event) {
            event.stopPropagation();
            languageMenu.hidden = !languageMenu.hidden;
        });

        languageMenu.querySelectorAll('[data-language]').forEach(function (button) {
            button.addEventListener('click', function () {
                var language = button.getAttribute('data-language');
                if (language === 'mni') {
                    showToast('Meiteilon language support is being prepared.');
                } else {
                    showToast('English selected.');
                }
                closeLanguageMenu();
            });
        });

        document.addEventListener('click', function (event) {
            if (!languageMenu.contains(event.target) && event.target !== languageToggle && !languageToggle.contains(event.target)) {
                closeLanguageMenu();
            }
        });
    }

    /* -------------------------------------------------------------
     * Location picker placeholder
     * ------------------------------------------------------------- */
    var locationButton = document.querySelector('[data-location-picker]');
    if (locationButton) {
        locationButton.addEventListener('click', function () {
            showToast('Location selection will be available soon.');
        });
    }

    /* -------------------------------------------------------------
     * Touch feedback
     * ------------------------------------------------------------- */
    document.querySelectorAll('a, button').forEach(function (element) {
        element.addEventListener('touchstart', function () {
            element.classList.add('ma-touching');
        }, { passive: true });
        element.addEventListener('touchend', function () {
            element.classList.remove('ma-touching');
        }, { passive: true });
        element.addEventListener('touchcancel', function () {
            element.classList.remove('ma-touching');
        }, { passive: true });
    });

    /* -------------------------------------------------------------
     * PWA service worker
     * ------------------------------------------------------------- */
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            var swUrl = basePath + '/member-service-worker.js';
            navigator.serviceWorker.register(swUrl).catch(function () {
                // PWA is progressive enhancement; the web app remains usable.
            });
        });
    }
})();
