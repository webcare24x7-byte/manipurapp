<?php

declare(strict_types=1);

$summary = $summary ?? [];
?>

<div class="page-header">
    <div>
        <h1>Taxi</h1>
        <p>Manage your taxi businesses, fleet, drivers, services and bookings.</p>
    </div>
</div>

<div class="card">
    <div class="form-grid">
        <div class="card">
            <h2>Taxi Businesses</h2>
            <p><?= (int) ($summary['businesses'] ?? 0) ?></p>
            <a class="link-button" href="<?= config('app.base_path') ?>/taxi/vendors">Manage Taxi Businesses</a>
        </div>
        <div class="card">
            <h2>Vehicles</h2>
            <p><?= (int) ($summary['vehicles'] ?? 0) ?></p>
            <a class="link-button" href="<?= config('app.base_path') ?>/taxi/vehicles">Manage Vehicles</a>
        </div>

        <div class="card">
            <h2>Drivers</h2>
            <p><?= (int) ($summary['drivers'] ?? 0) ?></p>
            <a class="link-button" href="<?= config('app.base_path') ?>/taxi/drivers">Manage Drivers</a>
        </div>

        <div class="card">
            <h2>Services</h2>
            <p><?= (int) ($summary['services'] ?? 0) ?></p>
            <a class="link-button" href="<?= config('app.base_path') ?>/taxi/services">Manage Services</a>
        </div>

        <div class="card">
            <h2>Bookings</h2>
            <p><?= (int) ($summary['bookings'] ?? 0) ?></p>
            <a class="link-button" href="<?= config('app.base_path') ?>/taxi/bookings">Manage Bookings</a>
        </div>

        <div class="card">
            <h2>Active Work</h2>
            <p><?= (int) ($summary['pending_bookings'] ?? 0) ?></p>
            <span>Pending / active bookings</span>
        </div>
    </div>
</div>
