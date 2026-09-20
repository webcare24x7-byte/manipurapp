<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Simulated Dashboard Data
|--------------------------------------------------------------------------
| Temporary UI data.
| This will later be replaced by DashboardService data.
*/

$dashboard = [

    'stats' => [
        [
            'label' => 'Members',
            'value' => '248',
            'meta' => '+12 this month',
            'trend' => 'up',
        ],
        [
            'label' => 'Families',
            'value' => '96',
            'meta' => '+4 this month',
            'trend' => 'up',
        ],
        [
            'label' => 'Volunteers',
            'value' => '42',
            'meta' => '31 active',
            'trend' => 'neutral',
        ],
        [
            'label' => 'Upcoming Events',
            'value' => '6',
            'meta' => 'Next 30 days',
            'trend' => 'neutral',
        ],
    ],

    'giving' => [
        'amount' => '84,500',
        'change' => '+18.4%',
        'period' => 'This month',
        'previous' => '₹71,300 last month',
        'chart' => [62, 71, 68, 79, 84],
    ],

    'events' => [
        [
            'day' => '24',
            'month' => 'AUG',
            'title' => 'Sunday Worship',
            'time' => '10:00 AM',
            'location' => 'Main Sanctuary',
        ],
        [
            'day' => '27',
            'month' => 'AUG',
            'title' => 'Youth Meeting',
            'time' => '6:30 PM',
            'location' => 'Fellowship Hall',
        ],
        [
            'day' => '30',
            'month' => 'AUG',
            'title' => 'Bible Study',
            'time' => '5:00 PM',
            'location' => 'Room 02',
        ],
    ],

    'activity' => [
        [
            'text' => 'John Doe added as member',
            'time' => '2m',
        ],
        [
            'text' => 'New family registered',
            'time' => '18m',
        ],
        [
            'text' => 'Giving of ₹5,000 recorded',
            'time' => '42m',
        ],
        [
            'text' => 'Sarah assigned to Worship',
            'time' => '1h',
        ],
        [
            'text' => 'Annual Church Anniversary updated',
            'time' => '2h',
        ],
    ],

    'quick_actions' => [
        [
            'label' => 'Add Member',
            'url' => '/members/create',
            'icon' => 'user-plus',
        ],
        [
            'label' => 'Add Family',
            'url' => '/families/create',
            'icon' => 'users',
        ],
        [
            'label' => 'Create Event',
            'url' => '/events/create',
            'icon' => 'calendar',
        ],
        [
            'label' => 'Record Giving',
            'url' => '/giving/create',
            'icon' => 'heart',
        ],
    ],
];

?>

<div class="dashboard-page">

    <!-- =====================================================
         HEADER
    ====================================================== -->

    <div class="dashboard-header">

        <div>

            <div class="dashboard-eyebrow">
                CHURCH OVERVIEW
            </div>

            <h1 class="dashboard-title">
                Dashboard
            </h1>

            <p class="dashboard-description">
                Good evening, <?= htmlspecialchars($user['name'] ?? 'Administrator') ?>.
                Here's what's happening with your church.
            </p>

        </div>

        <div class="dashboard-date">
            <?= date('l, M j, Y') ?>
        </div>

    </div>


    <!-- =====================================================
         KPI STATS
    ====================================================== -->

    <div class="dashboard-stats">

        <?php foreach ($dashboard['stats'] as $stat): ?>

            <div class="dashboard-stat">

                <div class="dashboard-stat-label">
                    <?= htmlspecialchars($stat['label']) ?>
                </div>

                <div class="dashboard-stat-value">
                    <?= htmlspecialchars($stat['value']) ?>
                </div>

                <div class="dashboard-stat-meta">

                    <?php if ($stat['trend'] === 'up'): ?>

                        <span class="dashboard-stat-trend">
                            ↑
                        </span>

                    <?php endif; ?>

                    <span>
                        <?= htmlspecialchars($stat['meta']) ?>
                    </span>

                </div>

            </div>

        <?php endforeach; ?>

    </div>


    <!-- =====================================================
         MAIN GRID
    ====================================================== -->

    <div class="dashboard-main-grid">


        <!-- =================================================
             LEFT COLUMN
        ================================================== -->

        <div class="dashboard-left-column">


            <!-- =============================================
                 GIVING
            ============================================== -->

            <section class="dashboard-panel">

                <div class="dashboard-panel-header">

                    <div>

                        <div class="dashboard-panel-title">
                            Giving Overview
                        </div>

                        <div class="dashboard-panel-subtitle">
                            Monthly giving
                        </div>

                    </div>

                    <a
                        href="<?= config('app.base_path') ?>/giving"
                        class="dashboard-panel-link"
                    >
                        View details →
                    </a>

                </div>


                <div class="dashboard-panel-body">

                    <div class="giving-summary">

                        <div>

                            <div class="giving-amount">
                                ₹<?= htmlspecialchars($dashboard['giving']['amount']) ?>
                            </div>

                            <div class="giving-period">
                                <?= htmlspecialchars($dashboard['giving']['period']) ?>
                                ·
                                <?= htmlspecialchars($dashboard['giving']['previous']) ?>
                            </div>

                        </div>


                        <div class="giving-change">
                            <?= htmlspecialchars($dashboard['giving']['change']) ?>
                        </div>

                    </div>


                    <!-- Simulated chart -->

                    <div class="giving-chart">

                        <div class="giving-chart-area">

                            <svg
                                viewBox="0 0 600 150"
                                preserveAspectRatio="none"
                                aria-label="Giving trend"
                            >

                                <defs>

                                    <linearGradient
                                        id="givingGradient"
                                        x1="0"
                                        y1="0"
                                        x2="0"
                                        y2="1"
                                    >

                                        <stop
                                            offset="0%"
                                            stop-color="#5146E5"
                                            stop-opacity=".18"
                                        />

                                        <stop
                                            offset="100%"
                                            stop-color="#5146E5"
                                            stop-opacity="0"
                                        />

                                    </linearGradient>

                                </defs>


                                <path
                                    class="giving-area"
                                    d="
                                        M0,108
                                        C55,96 65,84 120,91
                                        C175,98 190,70 240,76
                                        C295,82 310,50 360,61
                                        C415,72 430,35 480,42
                                        C525,48 555,28 600,31
                                        L600,150
                                        L0,150
                                        Z
                                    "
                                />


                                <path
                                    class="giving-line"
                                    d="
                                        M0,108
                                        C55,96 65,84 120,91
                                        C175,98 190,70 240,76
                                        C295,82 310,50 360,61
                                        C415,72 430,35 480,42
                                        C525,48 555,28 600,31
                                    "
                                />

                            </svg>

                        </div>


                        <div class="giving-chart-labels">

                            <span>Apr</span>
                            <span>May</span>
                            <span>Jun</span>
                            <span>Jul</span>
                            <span>Aug</span>

                        </div>

                    </div>

                </div>

            </section>


            <!-- =============================================
                 RECENT ACTIVITY
            ============================================== -->

            <section class="dashboard-panel">

                <div class="dashboard-panel-header">

                    <div>

                        <div class="dashboard-panel-title">
                            Recent Activity
                        </div>

                        <div class="dashboard-panel-subtitle">
                            Latest changes in your church
                        </div>

                    </div>

                </div>


                <div class="dashboard-panel-body dashboard-activity">

                    <?php foreach ($dashboard['activity'] as $activity): ?>

                        <div class="activity-item">

                            <span class="activity-dot"></span>

                            <span class="activity-text">
                                <?= htmlspecialchars($activity['text']) ?>
                            </span>

                            <span class="activity-time">
                                <?= htmlspecialchars($activity['time']) ?>
                            </span>

                        </div>

                    <?php endforeach; ?>

                </div>

            </section>

        </div>


        <!-- =================================================
             RIGHT COLUMN
        ================================================== -->

        <div class="dashboard-right-column">


            <!-- =============================================
                 UPCOMING EVENTS
            ============================================== -->

            <section class="dashboard-panel">

                <div class="dashboard-panel-header">

                    <div>

                        <div class="dashboard-panel-title">
                            Upcoming Events
                        </div>

                        <div class="dashboard-panel-subtitle">
                            Next events
                        </div>

                    </div>

                    <a
                        href="<?= config('app.base_path') ?>/events"
                        class="dashboard-panel-link"
                    >
                        View all →
                    </a>

                </div>


                <div class="dashboard-panel-body events-list">

                    <?php foreach ($dashboard['events'] as $event): ?>

                        <a
                            href="#"
                            class="event-item"
                        >

                            <div class="event-date">

                                <span class="event-date-month">
                                    <?= htmlspecialchars($event['month']) ?>
                                </span>

                                <span class="event-date-day">
                                    <?= htmlspecialchars($event['day']) ?>
                                </span>

                            </div>


                            <div class="event-information">

                                <div class="event-title">
                                    <?= htmlspecialchars($event['title']) ?>
                                </div>

                                <div class="event-meta">

                                    <?= htmlspecialchars($event['time']) ?>

                                    <span>·</span>

                                    <?= htmlspecialchars($event['location']) ?>

                                </div>

                            </div>

                        </a>

                    <?php endforeach; ?>

                </div>

            </section>


            <!-- =============================================
                 QUICK ACTIONS
            ============================================== -->

            <section class="dashboard-panel">

                <div class="dashboard-panel-header">

                    <div>

                        <div class="dashboard-panel-title">
                            Quick Actions
                        </div>

                        <div class="dashboard-panel-subtitle">
                            Common tasks
                        </div>

                    </div>

                </div>


                <div class="dashboard-panel-body">

                    <div class="quick-action-grid">

                        <?php foreach ($dashboard['quick_actions'] as $action): ?>

                            <a
                                href="<?= config('app.base_path') . htmlspecialchars($action['url']) ?>"
                                class="quick-action"
                            >

                                <span class="quick-action-icon">

                                    <?php if ($action['icon'] === 'user-plus'): ?>

                                        <svg viewBox="0 0 24 24" fill="none">
                                            <path
                                                d="M15 20v-1.5a4.5 4.5 0 0 0-4.5-4.5h-3A4.5 4.5 0 0 0 3 18.5V20"
                                                stroke="currentColor"
                                                stroke-width="1.7"
                                                stroke-linecap="round"
                                            />
                                            <circle
                                                cx="9"
                                                cy="7"
                                                r="3.5"
                                                stroke="currentColor"
                                                stroke-width="1.7"
                                            />
                                            <path
                                                d="M19 8v6M16 11h6"
                                                stroke="currentColor"
                                                stroke-width="1.7"
                                                stroke-linecap="round"
                                            />
                                        </svg>

                                    <?php elseif ($action['icon'] === 'users'): ?>

                                        <svg viewBox="0 0 24 24" fill="none">
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
                                                d="M16 5.5a3 3 0 0 1 0 5.8M18 14a4.5 4.5 0 0 1 2.5 4"
                                                stroke="currentColor"
                                                stroke-width="1.7"
                                                stroke-linecap="round"
                                            />
                                        </svg>

                                    <?php elseif ($action['icon'] === 'calendar'): ?>

                                        <svg viewBox="0 0 24 24" fill="none">
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
                                        </svg>

                                    <?php else: ?>

                                        <svg viewBox="0 0 24 24" fill="none">
                                            <path
                                                d="M20.8 8.8c0 5.2-8.8 10-8.8 10s-8.8-4.8-8.8-10A4.8 4.8 0 0 1 12 6.3a4.8 4.8 0 0 1 8.8 2.5Z"
                                                stroke="currentColor"
                                                stroke-width="1.7"
                                                stroke-linejoin="round"
                                            />
                                        </svg>

                                    <?php endif; ?>

                                </span>

                                <span>
                                    <?= htmlspecialchars($action['label']) ?>
                                </span>

                            </a>

                        <?php endforeach; ?>

                    </div>

                </div>

            </section>

        </div>

    </div>

</div>