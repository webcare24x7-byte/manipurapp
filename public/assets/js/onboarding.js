document.addEventListener('DOMContentLoaded', () => {

    /*
     * Clear church search
     */

    const clearButtons =
        document.querySelectorAll(
            '[data-clear-search]'
        );

    const searchInput =
        document.getElementById('church');


    clearButtons.forEach(button => {

        button.addEventListener('click', () => {

            if (!searchInput) {
                return;
            }

            searchInput.value = '';

            searchInput.focus();

        });

    });


    /*
     * Submit loading state
     */

    const searchForm =
        document.querySelector(
            '.church-search-form'
        );


    if (searchForm) {

        searchForm.addEventListener(
            'submit',
            () => {

                const button =
                    searchForm.querySelector(
                        '.onboarding-button'
                    );

                if (!button) {
                    return;
                }

                button.classList.add(
                    'is-loading'
                );

                button.disabled = true;

                const label =
                    button.querySelector(
                        'span:first-child'
                    );

                if (label) {

                    label.textContent =
                        'Searching...';

                }

            }
        );

    }


    /*
     * Small keyboard improvement:
     * focus search with "/" when no input is active.
     */

    document.addEventListener(
        'keydown',
        event => {

            if (
                event.key !== '/' ||
                !searchInput
            ) {
                return;
            }


            const active =
                document.activeElement;


            if (
                active &&
                (
                    active.tagName === 'INPUT' ||
                    active.tagName === 'TEXTAREA' ||
                    active.tagName === 'SELECT'
                )
            ) {
                return;
            }


            event.preventDefault();

            searchInput.focus();

        }
    );

});