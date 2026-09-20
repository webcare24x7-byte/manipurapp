<?php declare(strict_types=1); ?>

<div class="page-header">
    <div>
        <h1>Taxi Bookings</h1>
        <p>Manage member and non-member taxi bookings for this tenant.</p>
    </div>
    <div class="page-actions">
        <a href="<?= config('app.base_path') ?>/taxi/bookings/create" class="btn btn-primary">Add Taxi Booking</a>
    </div>
</div>

<div class="card">
    <div class="table-toolbar">
        <div class="table-search"><input type="search" placeholder="Search bookings..."></div>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Booking</th>
                    <th>Customer</th>
                    <th>Member</th>
                    <th>Source</th>
                    <th>Trip</th>
                    <th>Taxi Business</th>
                    <th>Service</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($bookings)): ?>
                <tr><td colspan="9"><div class="empty-state">No taxi bookings found.</div></td></tr>
            <?php else: ?>
                <?php foreach ($bookings as $row): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars((string) ($row['booking_no'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></strong>
                        </td>
                        <td>
                            <?= htmlspecialchars((string) ($row['customer_name'] ?? '—'), ENT_QUOTES, 'UTF-8') ?><br>
                            <small><?= htmlspecialchars((string) ($row['customer_phone'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></small>
                        </td>
                        <td>
                            <?php if (!empty($row['member_id'])): ?>
                                <?= htmlspecialchars((string) ($row['member_name'] ?? 'Member'), ENT_QUOTES, 'UTF-8') ?><br>
                                <small><?= htmlspecialchars((string) ($row['member_no'] ?? ''), ENT_QUOTES, 'UTF-8') ?></small>
                            <?php else: ?>
                                <span class="badge">Non-member</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars((string) ($row['booking_source'] ?? 'ADMIN'), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= ($row['trip_type'] ?? 'ONE_WAY') === 'ROUND_TRIP' ? 'Round Trip' : 'One Way' ?></td>
                        <td><?= htmlspecialchars((string) ($row['vendor_name'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string) ($row['service_name'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string) ($row['status'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <a class="link-button" href="<?= config('app.base_path') ?>/taxi/bookings/<?= (int) $row['id'] ?>">View</a>
                            <a class="link-button" href="<?= config('app.base_path') ?>/taxi/bookings/<?= (int) $row['id'] ?>/edit">Edit</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
