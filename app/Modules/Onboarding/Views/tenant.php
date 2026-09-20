<?php

$results = $results ?? [];
$search  = $search ?? '';
$error   = $error ?? '';

?>

<div class="onboarding-page">


    <!-- =====================================================
         MOBILE BRAND
    ====================================================== -->

    <div class="mobile-onboarding-brand">

        <a
            href="<?= config('app.base_path') ?>/"
            class="onboarding-brand"
        >

            <span class="onboarding-brand-mark">

                <svg
                    viewBox="0 0 40 40"
                    fill="none"
                    aria-hidden="true"
                >

                    <path
                        d="M20 4L34 14V34H6V14L20 4Z"
                        stroke="currentColor"
                        stroke-width="2.5"
                        stroke-linejoin="round"
                    />

                    <path
                        d="M20 13V25"
                        stroke="currentColor"
                        stroke-width="2.5"
                        stroke-linecap="round"
                    />

                    <path
                        d="M15 19H25"
                        stroke="currentColor"
                        stroke-width="2.5"
                        stroke-linecap="round"
                    />

                </svg>

            </span>

            <span class="onboarding-brand-name">
                Church<span>OS</span>
            </span>

        </a>

    </div>


    <!-- =====================================================
         PAGE HEADER
    ====================================================== -->

    <div class="onboarding-header">

        <div class="welcome-icon">

            <svg
                viewBox="0 0 48 48"
                fill="none"
                aria-hidden="true"
            >

                <rect
                    x="5"
                    y="9"
                    width="38"
                    height="30"
                    rx="8"
                    stroke="currentColor"
                    stroke-width="2"
                />

                <path
                    d="M15 20H33"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                />

                <path
                    d="M15 27H27"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                />

            </svg>

        </div>


        <div class="onboarding-eyebrow">
            WELCOME TO CHURCHOS
        </div>


        <h1>
            Find your church
        </h1>


        <p>
            Search for your church to continue
            to your ChurchOS workspace.
        </p>

    </div>


    <!-- =====================================================
         SEARCH CARD
    ====================================================== -->

    <div class="search-card">


        <form
            method="POST"
            action="<?= config('app.base_path') ?>/"
            class="church-search-form"
        >

            <label
                for="church"
                class="field-label"
            >
                Church name
            </label>


            <div class="search-input-wrapper">

                <span class="search-input-icon">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        aria-hidden="true"
                    >

                        <circle
                            cx="11"
                            cy="11"
                            r="6.5"
                            stroke="currentColor"
                            stroke-width="1.8"
                        />

                        <path
                            d="M16 16L21 21"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                        />

                    </svg>

                </span>


                <input
                    type="text"
                    id="church"
                    name="church"
                    value="<?= htmlspecialchars($search) ?>"
                    placeholder="Search your church..."
                    autocomplete="organization"
                    required
                    autofocus
                />


                <?php if ($search !== ''): ?>

                    <button
                        type="button"
                        class="clear-search"
                        data-clear-search
                        aria-label="Clear search"
                    >
                        ×
                    </button>

                <?php endif; ?>

            </div>


            <button
                type="submit"
                class="onboarding-button onboarding-button-primary"
            >

                <span>
                    Find my church
                </span>

                <span class="button-arrow">
                    →
                </span>

            </button>

        </form>


        <!-- Error -->

        <?php if (!empty($error)): ?>

            <div
                class="onboarding-alert onboarding-alert-error"
                role="alert"
            >

                <span class="alert-icon">
                    !
                </span>

                <div>

                    <strong>
                        We couldn't complete your search
                    </strong>

                    <p>
                        <?= htmlspecialchars($error) ?>
                    </p>

                </div>

            </div>

        <?php endif; ?>


        <!-- =================================================
             RESULTS
        ================================================== -->

        <?php if (!empty($results)): ?>

            <div class="church-results">

                <div class="results-heading">

                    <div>

                        <span class="results-eyebrow">
                            SEARCH RESULTS
                        </span>

                        <h2>
                            Select your church
                        </h2>

                    </div>

                    <span class="results-count">
                        <?= count($results) ?>
                        <?= count($results) === 1 ? 'church' : 'churches' ?>
                    </span>

                </div>


                <div class="church-result-list">

                    <?php foreach ($results as $tenant): ?>

                        <?php
                        $tenantName =
                            $tenant['name'] ?? 'Church';

                        $tenantSlug =
                            $tenant['slug'] ?? '';

                        $tenantCountry =
                            $tenant['country'] ?? '';

                        $initial =
                            function_exists('mb_substr')
                                ? mb_strtoupper(
                                    mb_substr(
                                        $tenantName,
                                        0,
                                        1
                                    )
                                )
                                : strtoupper(
                                    substr(
                                        $tenantName,
                                        0,
                                        1
                                    )
                                );
                        ?>


                        <a
                            href="<?= config('app.base_path') ?>/c/<?= urlencode($tenantSlug) ?>/login"
                            class="church-result-card"
                        >

                            <span class="church-result-avatar">
                                <?= htmlspecialchars($initial) ?>
                            </span>


                            <span class="church-result-information">

                                <strong>
                                    <?= htmlspecialchars($tenantName) ?>
                                </strong>


                                <?php if ($tenantCountry !== ''): ?>

                                    <small>

                                        <span class="location-dot">
                                            ●
                                        </span>

                                        <?= htmlspecialchars($tenantCountry) ?>

                                    </small>

                                <?php else: ?>

                                    <small>
                                        ChurchOS workspace
                                    </small>

                                <?php endif; ?>

                            </span>


                            <span class="church-result-arrow">
                                →
                            </span>

                        </a>

                    <?php endforeach; ?>

                </div>

            </div>


        <?php elseif ($search !== '' && empty($error)): ?>


            <!-- =================================================
                 EMPTY STATE
            ================================================== -->

            <div class="church-empty-state">

                <div class="empty-state-icon">

                    <svg
                        viewBox="0 0 48 48"
                        fill="none"
                        aria-hidden="true"
                    >

                        <circle
                            cx="21"
                            cy="21"
                            r="11"
                            stroke="currentColor"
                            stroke-width="2"
                        />

                        <path
                            d="M29 29L38 38"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                        />

                        <path
                            d="M17 21H25"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                        />

                    </svg>

                </div>


                <h3>
                    We couldn't find that church
                </h3>


                <p>
                    No ChurchOS workspace matched
                    <strong>
                        "<?= htmlspecialchars($search) ?>"
                    </strong>.
                </p>


                <button
                    type="button"
                    class="text-button"
                    data-clear-search
                >
                    Try another search
                </button>

            </div>


        <?php endif; ?>


    </div>


    <!-- =====================================================
         CREATE CHURCH
    ====================================================== -->

    <div class="create-church-card">

        <div class="create-church-icon">
            +
        </div>


        <div class="create-church-content">

            <strong>
                Don't see your church?
            </strong>

            <span>
                Create your church workspace with ChurchOS.
            </span>

        </div>


        <a
            href="<?= config('app.base_path') ?>/register"
            class="create-church-link"
        >
            Create church
            <span>→</span>
        </a>

    </div>


    <!-- =====================================================
         FOOTER
    ====================================================== -->

    <div class="onboarding-mini-footer">

        <span>
            © <?= date('Y') ?> ChurchOS
        </span>

        <span class="footer-separator">
            •
        </span>

        <span>
            Secure church management
        </span>

    </div>


</div>