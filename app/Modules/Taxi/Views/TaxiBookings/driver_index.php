<?php declare(strict_types=1); ?>
<div class="page-header">
    <div>
        <h1>My Taxi Trips</h1>
        <p>Bookings assigned to <?= htmlspecialchars((string)($driver['name'] ?? 'you'), ENT_QUOTES, 'UTF-8') ?>.</p>
    </div>
</div>

<div class="card">
    <?php if (empty($bookings)): ?>
        <div class="empty-state"><h3>No assigned trips</h3><p>New bookings assigned to you will appear here.</p></div>
    <?php else: ?>
        <div class="table">
        <table class="table">
            <thead><tr><th>Booking</th><th>Customer</th><th>Trip</th><th>Vehicle</th><th>Status</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($bookings as $b): ?>
                <tr>
                    <td><strong><?= htmlspecialchars((string)$b['booking_no'], ENT_QUOTES, 'UTF-8') ?></strong><br><small><?= htmlspecialchars((string)$b['booking_source'], ENT_QUOTES, 'UTF-8') ?></small></td>
                    <td><?= htmlspecialchars((string)$b['customer_name'], ENT_QUOTES, 'UTF-8') ?><br><small><?= htmlspecialchars((string)$b['customer_phone'], ENT_QUOTES, 'UTF-8') ?></small></td>
                    <td><?= htmlspecialchars((string)$b['pickup_address'], ENT_QUOTES, 'UTF-8') ?> → <?= htmlspecialchars((string)$b['destination_address'], ENT_QUOTES, 'UTF-8') ?><br><small><?= htmlspecialchars((string)($b['scheduled_at'] ?? 'Immediate'), ENT_QUOTES, 'UTF-8') ?></small></td>
                    <td><?= htmlspecialchars(trim((string)(($b['make'] ?? '').' '.($b['model'] ?? ''))) ?: (string)($b['vehicle_type'] ?? '—'), ENT_QUOTES, 'UTF-8') ?><br><small><?= htmlspecialchars((string)($b['vehicle_registration_no'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></small></td>
                    <td><strong><?= htmlspecialchars((string)$b['status'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                    <td><a class="btn btn-secondary" href="<?= config('app.base_path') ?>/taxi/driver/bookings/<?= (int)$b['id'] ?>">Open</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    <?php endif; ?>
</div>
