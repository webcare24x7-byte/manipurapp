<?php

declare(strict_types=1);

$request = app()->get('request');

$active = static function (string $path) use ($request): string {
    return $request->is($path)
        ? 'sidebar-item active'
        : 'sidebar-item';
};

/*
|--------------------------------------------------------------------------
| Sidebar SVG Icons
|--------------------------------------------------------------------------
| All icons use currentColor so the existing sidebar CSS controls
| their visual state, including hover and active states.
|--------------------------------------------------------------------------
*/

$icon = static function (string $name): string {

    $icons = [

        'compass' => '
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <circle cx="12" cy="12" r="8.5" stroke="currentColor" stroke-width="1.7"/>
                <path d="m14.8 9.2-2.1 5.1-5.1 2.1 2.1-5.1 5.1-2.1Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
            </svg>
        ',

        'dashboard' => '
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <rect x="3.5" y="3.5" width="7" height="7" rx="1.5"
                    stroke="currentColor" stroke-width="1.7"/>
                <rect x="13.5" y="3.5" width="7" height="7" rx="1.5"
                    stroke="currentColor" stroke-width="1.7"/>
                <rect x="3.5" y="13.5" width="7" height="7" rx="1.5"
                    stroke="currentColor" stroke-width="1.7"/>
                <rect x="13.5" y="13.5" width="7" height="7" rx="1.5"
                    stroke="currentColor" stroke-width="1.7"/>
            </svg>
        ',

        'structure' => '
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path
                    d="M4 20V9l8-5 8 5v11"
                    stroke="currentColor"
                    stroke-width="1.7"
                    stroke-linejoin="round"
                />
                <path
                    d="M8 20v-5h8v5M3 20h18"
                    stroke="currentColor"
                    stroke-width="1.7"
                    stroke-linecap="round"
                />
                <path
                    d="M12 8v3M10.5 9.5h3"
                    stroke="currentColor"
                    stroke-width="1.7"
                    stroke-linecap="round"
                />
            </svg>
        ',

        'media' => '
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <rect
                    x="3.5"
                    y="4"
                    width="17"
                    height="16"
                    rx="2"
                    stroke="currentColor"
                    stroke-width="1.7"
                />
                <circle
                    cx="8.5"
                    cy="9"
                    r="1.5"
                    stroke="currentColor"
                    stroke-width="1.7"
                />
                <path
                    d="m4.5 17 4.5-4 3.2 2.7 2.2-2 5.1 4.3"
                    stroke="currentColor"
                    stroke-width="1.7"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                />
            </svg>
        ',

        'categories' => '
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <rect
                    x="4"
                    y="4"
                    width="7"
                    height="6"
                    rx="1.5"
                    stroke="currentColor"
                    stroke-width="1.7"
                />
                <rect
                    x="13"
                    y="14"
                    width="7"
                    height="6"
                    rx="1.5"
                    stroke="currentColor"
                    stroke-width="1.7"
                />
                <path
                    d="M11 7h2M7.5 10v4h9"
                    stroke="currentColor"
                    stroke-width="1.7"
                    stroke-linecap="round"
                />
            </svg>
        ',

        'positions' => '
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <rect
                    x="3.5"
                    y="6"
                    width="17"
                    height="14"
                    rx="2"
                    stroke="currentColor"
                    stroke-width="1.7"
                />
                <path
                    d="M8 6V4.5A1.5 1.5 0 0 1 9.5 3h5A1.5 1.5 0 0 1 16 4.5V6"
                    stroke="currentColor"
                    stroke-width="1.7"
                />
                <path
                    d="M3.5 11h17M10 14h4"
                    stroke="currentColor"
                    stroke-width="1.7"
                    stroke-linecap="round"
                />
            </svg>
        ',

        'leadership' => '
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <circle
                    cx="12"
                    cy="8"
                    r="3.5"
                    stroke="currentColor"
                    stroke-width="1.7"
                />
                <path
                    d="M5 20a7 7 0 0 1 14 0"
                    stroke="currentColor"
                    stroke-width="1.7"
                    stroke-linecap="round"
                />
                <path
                    d="m17 4 .8 1.6L19.5 6l-1.7.4L17 8l-.8-1.6L14.5 6l1.7-.4L17 4Z"
                    stroke="currentColor"
                    stroke-width="1.2"
                    stroke-linejoin="round"
                />
            </svg>
        ',

        'ministries' => '
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <circle
                    cx="9"
                    cy="8"
                    r="3"
                    stroke="currentColor"
                    stroke-width="1.7"
                />
                <circle
                    cx="16.5"
                    cy="9"
                    r="2.2"
                    stroke="currentColor"
                    stroke-width="1.7"
                />
                <path
                    d="M3.5 19a5.5 5.5 0 0 1 11 0"
                    stroke="currentColor"
                    stroke-width="1.7"
                    stroke-linecap="round"
                />
                <path
                    d="M14.5 16a4.5 4.5 0 0 1 6 3"
                    stroke="currentColor"
                    stroke-width="1.7"
                    stroke-linecap="round"
                />
            </svg>
        ',

        'committees' => '
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <circle
                    cx="12"
                    cy="7"
                    r="2.8"
                    stroke="currentColor"
                    stroke-width="1.7"
                />
                <circle
                    cx="6.5"
                    cy="11"
                    r="2.2"
                    stroke="currentColor"
                    stroke-width="1.7"
                />
                <circle
                    cx="17.5"
                    cy="11"
                    r="2.2"
                    stroke="currentColor"
                    stroke-width="1.7"
                />
                <path
                    d="M7.5 19a4.5 4.5 0 0 1 9 0"
                    stroke="currentColor"
                    stroke-width="1.7"
                    stroke-linecap="round"
                />
                <path
                    d="M2.8 18a3.8 3.8 0 0 1 3-3.7M21.2 18a3.8 3.8 0 0 0-3-3.7"
                    stroke="currentColor"
                    stroke-width="1.7"
                    stroke-linecap="round"
                />
            </svg>
        ',

        'staff' => '
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <circle
                    cx="12"
                    cy="8"
                    r="3.5"
                    stroke="currentColor"
                    stroke-width="1.7"
                />
                <path
                    d="M5 20a7 7 0 0 1 14 0"
                    stroke="currentColor"
                    stroke-width="1.7"
                    stroke-linecap="round"
                />
            </svg>
        ',

        'members' => '
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <circle
                    cx="9"
                    cy="8"
                    r="3"
                    stroke="currentColor"
                    stroke-width="1.7"
                />
                <path
                    d="M3.5 19a5.5 5.5 0 0 1 11 0"
                    stroke="currentColor"
                    stroke-width="1.7"
                    stroke-linecap="round"
                />
                <path
                    d="M16 6.5a2.5 2.5 0 0 1 0 4.8M17 14a4.5 4.5 0 0 1 3.5 4"
                    stroke="currentColor"
                    stroke-width="1.7"
                    stroke-linecap="round"
                />
            </svg>
        ',

        'families' => '
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <circle
                    cx="9"
                    cy="8"
                    r="2.8"
                    stroke="currentColor"
                    stroke-width="1.7"
                />
                <circle
                    cx="16.5"
                    cy="9"
                    r="2.2"
                    stroke="currentColor"
                    stroke-width="1.7"
                />
                <path
                    d="M3.5 19a5.5 5.5 0 0 1 11 0"
                    stroke="currentColor"
                    stroke-width="1.7"
                    stroke-linecap="round"
                />
                <path
                    d="M14.5 16a4.5 4.5 0 0 1 6 3"
                    stroke="currentColor"
                    stroke-width="1.7"
                    stroke-linecap="round"
                />
                <path
                    d="M9 13v4M7 15h4"
                    stroke="currentColor"
                    stroke-width="1.4"
                    stroke-linecap="round"
                />
            </svg>
        ',

        'volunteers' => '
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path
                    d="M8 11V6.5a1.5 1.5 0 0 1 3 0V11"
                    stroke="currentColor"
                    stroke-width="1.7"
                    stroke-linecap="round"
                />
                <path
                    d="M11 10V5.5a1.5 1.5 0 0 1 3 0V11"
                    stroke="currentColor"
                    stroke-width="1.7"
                    stroke-linecap="round"
                />
                <path
                    d="M14 10V7a1.5 1.5 0 0 1 3 0v6"
                    stroke="currentColor"
                    stroke-width="1.7"
                    stroke-linecap="round"
                />
                <path
                    d="M17 11v-1a1.5 1.5 0 0 1 3 0v4.5A5.5 5.5 0 0 1 14.5 20h-2.2A5.3 5.3 0 0 1 8 17.7l-3-4.2a1.5 1.5 0 0 1 2.4-1.8L9 13"
                    stroke="currentColor"
                    stroke-width="1.7"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                />
            </svg>
        ',

        'giving' => '
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path
                    d="M20.8 8.8c0 5.2-8.8 10-8.8 10s-8.8-4.8-8.8-10A4.8 4.8 0 0 1 12 6.3a4.8 4.8 0 0 1 8.8 2.5Z"
                    stroke="currentColor"
                    stroke-width="1.7"
                    stroke-linejoin="round"
                />
            </svg>
        ',

        'finance' => '
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <rect
                    x="3.5"
                    y="5"
                    width="17"
                    height="14"
                    rx="2"
                    stroke="currentColor"
                    stroke-width="1.7"
                />
                <path
                    d="M3.5 9h17"
                    stroke="currentColor"
                    stroke-width="1.7"
                />
                <path
                    d="M16 14h2"
                    stroke="currentColor"
                    stroke-width="1.7"
                    stroke-linecap="round"
                />
            </svg>
        ',

        'meetings' => '
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <rect
                    x="4"
                    y="4"
                    width="16"
                    height="16"
                    rx="2"
                    stroke="currentColor"
                    stroke-width="1.7"
                />
                <path
                    d="M8 8h8M8 12h8M8 16h5"
                    stroke="currentColor"
                    stroke-width="1.7"
                    stroke-linecap="round"
                />
            </svg>
        ',

        'events' => '
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <rect
                    x="3.5"
                    y="5"
                    width="17"
                    height="15"
                    rx="2"
                    stroke="currentColor"
                    stroke-width="1.7"
                />
                <path
                    d="M7 3v4M17 3v4M3.5 9.5h17"
                    stroke="currentColor"
                    stroke-width="1.7"
                    stroke-linecap="round"
                />
                <path
                    d="M8 13h3M13 13h3M8 16h3"
                    stroke="currentColor"
                    stroke-width="1.5"
                    stroke-linecap="round"
                />
            </svg>
        ',

        'reports' => '
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path
                    d="M4 20V10M10 20V5M16 20v-8M22 20V3"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                />
                <path
                    d="M2.5 20h21"
                    stroke="currentColor"
                    stroke-width="1.7"
                    stroke-linecap="round"
                />
            </svg>
        ',

        'settings' => '
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path
                    d="M9.7 4.1 10.5 3h3l.8 1.1 1.8.8 1.4-.2 1.5 1.5-.2 1.4.8 1.8 1.1.8v3l-1.1.8-.8 1.8.2 1.4-1.5 1.5-1.4-.2-1.8.8-.8 1.1h-3l-.8-1.1-1.8-.8-1.4.2-1.5-1.5.2-1.4-.8-1.8L3 13.5v-3l1.1-.8.8-1.8-.2-1.4 1.5-1.5 1.4.2 1.8-.8Z"
                    stroke="currentColor"
                    stroke-width="1.5"
                    stroke-linejoin="round"
                />
                <circle
                    cx="12"
                    cy="12"
                    r="3"
                    stroke="currentColor"
                    stroke-width="1.7"
                />
            </svg>
        ',

        'roles' => '
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path
                    d="M12 3.5 20 7v5.5c0 4.5-3.1 7.2-8 8-4.9-.8-8-3.5-8-8V7l8-3.5Z"
                    stroke="currentColor"
                    stroke-width="1.7"
                    stroke-linejoin="round"
                />
                <path
                    d="m8.5 12 2.2 2.2 4.8-5"
                    stroke="currentColor"
                    stroke-width="1.7"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                />
            </svg>
        ',

        'packages' => '
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path
                    d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Z"
                    stroke="currentColor"
                    stroke-width="1.7"
                    stroke-linejoin="round"
                />
                <path
                    d="m4.5 7.5 7.5 4 7.5-4M12 11.5V21"
                    stroke="currentColor"
                    stroke-width="1.7"
                    stroke-linejoin="round"
                />
            </svg>
        ',

        'lookup' => '
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path
                    d="M5 6h14M5 12h14M5 18h14"
                    stroke="currentColor"
                    stroke-width="1.7"
                    stroke-linecap="round"
                />
                <circle cx="8" cy="6" r="1" fill="currentColor"/>
                <circle cx="16" cy="12" r="1" fill="currentColor"/>
                <circle cx="10" cy="18" r="1" fill="currentColor"/>
            </svg>
        ',

                'textbook' => '
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path
                    d="M5 4.5h10.5A3.5 3.5 0 0 1 19 8v11.5H8.5A3.5 3.5 0 0 0 5 23V4.5Z"
                    stroke="currentColor"
                    stroke-width="1.7"
                    stroke-linejoin="round"
                />
                <path
                    d="M5 19.5h10.5M8.5 8h7M8.5 11h7M8.5 14h4.5"
                    stroke="currentColor"
                    stroke-width="1.7"
                    stroke-linecap="round"
                />
            </svg>
        ',
    ];

    return $icons[$name] ?? '';
};

?>

<aside class="sidebar">

    <div class="sidebar-header">

        <a
            href="<?= config('app.base_path') ?>/dashboard"
            class="sidebar-logo"
        >
            ManipurApp
        </a>

        <span class="sidebar-version">
            Version 1.0
        </span>

    </div>

<nav class="sidebar-nav">

    <!-- Dashboard -->

    <div class="sidebar-section">

        <div class="sidebar-title">
            DASHBOARD
        </div>

        <a
            href="<?= config('app.base_path') ?>/dashboard"
            class="<?= $active('/dashboard') ?>"
        >
            <span class="menu-icon">
                <?= $icon('dashboard') ?>
            </span>

            <span class="menu-text">
                Dashboard
            </span>
        </a>

        

    </div>

    <!-- ManipurApp -->

    <div class="sidebar-section">

        <div class="sidebar-title">
            Modules
        </div>
        <a
    href="<?= config('app.base_path') ?>/taxi"
    class="<?= $active('/taxi') ?>"
>
    <span class="menu-icon">
        <?= $icon('car') ?>
    </span>

    <span class="menu-text">
        Taxi
    </span>
</a>

<a
    href="<?= config('app.base_path') ?>/commercial-rentals"
    class="<?= $active('/commercial-rentals') ?>"
>
    <span class="menu-icon">
        <?= $icon('truck') ?>
    </span>

    <span class="menu-text">
        Commercial Vehicle Rentals
    </span>
</a>

 <a
    href="<?= config('app.base_path') ?>/restaurant"
    class="<?= $active('/restaurant') ?>"
>
    <span class="menu-icon">
        <?= $icon('utensils') ?>
    </span>

    <span class="menu-text">
        Restaurants
    </span>
</a>


 <a
    href="<?= config('app.base_path') ?>/fresh-food"
    class="<?= $active('/fresh-food') ?>"
>
    <span class="menu-icon">
        <?= $icon('shopping-basket') ?>
    </span>

    <span class="menu-text">
        Fresh Food & Grocery
    </span>
</a>

<a
    href="<?= config('app.base_path') ?>/tourism"
    class="<?= $active('/tourism') ?>"
>
    <span class="menu-icon">
        <?= $icon('compass') ?>
    </span>

    <span class="menu-text">
        Tourism
    </span>
</a>

    </div>


    <!-- People -->

    <div class="sidebar-section">

        <div class="sidebar-title">
            PEOPLE
        </div>

        <a
            href="<?= config('app.base_path') ?>/members"
            class="<?= $active('/members') ?>"
        >
            <span class="menu-icon">
                <?= $icon('members') ?>
            </span>

            <span class="menu-text">
                Members
            </span>
        </a>
                <a
            href="<?= config('app.base_path') ?>/staff"
            class="<?= $active('/staff') ?>"
        >
            <span class="menu-icon">
                <?= $icon('staff') ?>
            </span>

            <span class="menu-text">
                Staff
            </span>
        </a>

    </div>


    <!-- System -->

    <div class="sidebar-section">

        <div class="sidebar-title">
            SYSTEM
        </div>

        <a
            href="<?= config('app.base_path') ?>/roles"
            class="<?= $active('/roles') ?>"
        >
            <span class="menu-icon">
                <?= $icon('roles') ?>
            </span>

            <span class="menu-text">
                Roles & Permissions
            </span>
        </a>


        <a
            href="<?= config('app.base_path') ?>/lookup-types"
            class="<?= $active('/lookup-types') ?>"
        >
            <span class="menu-icon">
                <?= $icon('lookup') ?>
            </span>

            <span class="menu-text">
                Lookup Types
            </span>
        </a>

    </div>

</nav>

    <div class="sidebar-footer">

        <div class="sidebar-user-name">
            <?= htmlspecialchars($authUser['name'] ?? 'Guest') ?>
        </div>

        <div class="sidebar-user-role">
            Business Owner / Vendor
        </div>

        <a
            href="<?= config('app.base_path') ?>/logout"
            class="sidebar-logout"
        >
            Logout
        </a>

    </div>

</aside>


<script>
(function () {

    'use strict';

    const STORAGE_KEY = 'manipurapp_sidebar_collapsed';
    const body = document.body;

    function isMobile() {
        return window.innerWidth <= 768;
    }

    function createToggle() {

        if (document.querySelector('.sidebar-toggle')) {
            return;
        }

        const button = document.createElement('button');

        button.type = 'button';
        button.className = 'sidebar-toggle';
        button.setAttribute('aria-label', 'Toggle sidebar');
        button.setAttribute('aria-expanded', 'true');

        button.innerHTML = `
            <span class="sidebar-toggle-icon">‹</span>
        `;

        document.body.appendChild(button);

        button.addEventListener('click', function () {

            if (isMobile()) {
                body.classList.toggle('sidebar-mobile-open');
                return;
            }

            const collapsed =
                body.classList.toggle('sidebar-collapsed');

            localStorage.setItem(
                STORAGE_KEY,
                collapsed ? '1' : '0'
            );

            updateButton();
        });
    }

    function updateButton() {

        const button =
            document.querySelector('.sidebar-toggle');

        if (!button) {
            return;
        }

        const collapsed =
            body.classList.contains('sidebar-collapsed');

        button.setAttribute(
            'aria-expanded',
            collapsed ? 'false' : 'true'
        );
    }

function setupTooltips() {

    document
        .querySelectorAll('.sidebar-item')
        .forEach(function (item) {

            const text =
                item.querySelector('.menu-text');

            if (!text) {
                return;
            }

            const tooltipText =
                text.textContent
                    .trim()
                    .replace(/\s+/g, ' ');

            item.setAttribute(
                'data-tooltip',
                tooltipText
            );

            item.setAttribute(
                'title',
                tooltipText
            );

        });

}

    function restoreState() {

        if (isMobile()) {
            return;
        }

        if (
            localStorage.getItem(STORAGE_KEY) === '1'
        ) {
            body.classList.add('sidebar-collapsed');
        }
    }

    function createMobileOverlay() {

        if (
            document.querySelector('.sidebar-overlay')
        ) {
            return;
        }

        const overlay =
            document.createElement('div');

        overlay.className = 'sidebar-overlay';

        document.body.appendChild(overlay);

        overlay.addEventListener('click', function () {

            body.classList.remove(
                'sidebar-mobile-open'
            );

        });
    }

    function setupLinks() {

        document
            .querySelectorAll('.sidebar a')
            .forEach(function (link) {

                link.addEventListener('click', function () {

                    if (isMobile()) {
                        body.classList.remove(
                            'sidebar-mobile-open'
                        );
                    }

                });

            });
    }

    function init() {

        restoreState();
        createToggle();
        setupTooltips();
        setupPremiumTooltips();
        createMobileOverlay();
        setupLinks();
        updateButton();

    }

    if (document.readyState === 'loading') {

        document.addEventListener(
            'DOMContentLoaded',
            init
        );

    } else {

        init();

    }

    function setupPremiumTooltips() {

    let tooltip = document.querySelector('.sidebar-premium-tooltip');

    if (!tooltip) {

        tooltip = document.createElement('div');

        tooltip.className =
            'sidebar-premium-tooltip';

        document.body.appendChild(tooltip);
    }


    document
        .querySelectorAll('.sidebar-item')
        .forEach(function (item) {

            item.addEventListener('mouseenter', function () {

                if (!document.body.classList.contains('sidebar-collapsed')) {
                    return;
                }

                const text =
                    item.getAttribute('data-tooltip');

                if (!text) {
                    return;
                }

                tooltip.textContent = text;

                const rect =
                    item.getBoundingClientRect();

                tooltip.style.left =
                    (rect.right + 12) + 'px';

                tooltip.style.top =
                    (rect.top + rect.height / 2) + 'px';

                tooltip.classList.add('visible');

            });


            item.addEventListener('mouseleave', function () {

                tooltip.classList.remove('visible');

            });

        });

}

})();
</script>