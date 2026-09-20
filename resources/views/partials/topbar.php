<?php

declare(strict_types=1);

$userName = $authUser['name'] ?? 'Guest';

$initials = '';

foreach (explode(' ', $userName) as $part) {

    if ($part !== '') {
        $initials .= strtoupper($part[0]);
    }
}

$initials = substr($initials, 0, 2);

?>

<header class="topbar">

    <div class="topbar-left">

        <div>

            <h1 class="page-title">

                <?= htmlspecialchars($title ?? 'Dashboard') ?>

            </h1>

            <p class="page-subtitle">

                Welcome back, <?= htmlspecialchars($userName) ?>

            </p>

        </div>

    </div>

    <div class="topbar-center">

        <div class="search-wrapper">

            <input
                type="text"
                class="search-box"
                placeholder="Search members, attendance, events..."
            >

        </div>

    </div>

    <div class="topbar-right">

        <button class="notification-btn">

            🔔

        </button>

        <div class="profile-card">

            <div class="avatar">

                <?= htmlspecialchars($initials) ?>

            </div>

            <div class="profile-info">

                <div class="profile-name">

                    <?= htmlspecialchars($userName) ?>

                </div>

                <div class="profile-role">

                    Administrator

                </div>

            </div>

            <div class="profile-arrow">

                ▼

            </div>

        </div>

    </div>

</header>