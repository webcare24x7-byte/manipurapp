<?php declare(strict_types=1);
$record = $record ?? [];
$status = (string)($record['status'] ?? 'Assigned');
$allowed = [
    'Assigned' => ['Driver Arrived'],
    'Driver Arrived' => ['In Progress'],
    'In Progress' => ['Completed'],
][$status] ?? [];
?>
<div class="page-header">
    <div>
        <h1><?= htmlspecialchars((string)($record['booking_no'] ?? 'Trip'), ENT_QUOTES, 'UTF-8') ?></h1>
        <p>Driver trip control.</p>
    </div>
    <div class="page-actions"><a href="<?= config('app.base_path') ?>/taxi/driver/bookings" class="btn btn-secondary">My Trips</a></div>
</div>

<div class="card">
    <h2 style="margin-top:0;">Current Status: <?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?></h2>
    <p><strong><?= htmlspecialchars((string)($record['customer_name'] ?? 'Customer'), ENT_QUOTES, 'UTF-8') ?></strong> · <?= htmlspecialchars((string)($record['customer_phone'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
    <p><?= htmlspecialchars((string)($record['pickup_address'] ?? ''), ENT_QUOTES, 'UTF-8') ?> → <?= htmlspecialchars((string)($record['destination_address'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
    <?php if (!empty($record['scheduled_at'])): ?><p><strong>Scheduled:</strong> <?= htmlspecialchars((string)$record['scheduled_at'], ENT_QUOTES, 'UTF-8') ?></p><?php else: ?><p><strong>Trip:</strong> Immediate</p><?php endif; ?>

    <?php if ($allowed): ?>
        <form method="post" action="<?= config('app.base_path') ?>/taxi/driver/bookings/<?= (int)$record['id'] ?>/status">
            <label for="driver_status">Next status</label>
            <select id="driver_status" name="status" required>
                <?php foreach ($allowed as $next): ?><option value="<?= htmlspecialchars($next, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($next, ENT_QUOTES, 'UTF-8') ?></option><?php endforeach; ?>
            </select>
            <div class="form-group" style="margin-top:12px;"><label for="driver_note">Note (optional)</label><input id="driver_note" name="notes" maxlength="500" placeholder="Optional trip note"></div>
            <button class="btn btn-primary" type="submit" style="margin-top:12px;">Update Trip Status</button>
        </form>
    <?php else: ?>
        <p><strong>No driver action is available for this status.</strong></p>
    <?php endif; ?>
</div>

<div class="card">
    <h2 style="margin-top:0;">Status History</h2>
    <?php if (!empty($status_history)): ?>
    <table class="table"><thead><tr><th>Time</th><th>From</th><th>To</th><th>Actor</th></tr></thead><tbody>
    <?php foreach ($status_history as $e): ?><tr>
        <td><?= htmlspecialchars((string)$e['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
        <td><?= htmlspecialchars((string)($e['old_status'] ?? 'New Booking'), ENT_QUOTES, 'UTF-8') ?></td>
        <td><strong><?= htmlspecialchars((string)$e['new_status'], ENT_QUOTES, 'UTF-8') ?></strong></td>
        <td><?= htmlspecialchars((string)($e['changed_by_name'] ?? 'Driver'), ENT_QUOTES, 'UTF-8') ?></td>
    </tr><?php endforeach; ?>
    </tbody></table>
    <?php else: ?><p>No status history recorded.</p><?php endif; ?>
</div>
