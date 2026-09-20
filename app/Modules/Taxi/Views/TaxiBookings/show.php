<?php declare(strict_types=1); ?>

<div class="page-header">
    <div>
        <h1><?= htmlspecialchars((string) ($record['booking_no'] ?? 'Taxi Booking'), ENT_QUOTES, 'UTF-8') ?></h1>
        <p>Taxi booking details.</p>
    </div>
    <div class="page-actions">
        <a href="<?= config('app.base_path') ?>/taxi/bookings" class="btn btn-secondary">Back</a>
        <a href="<?= config('app.base_path') ?>/taxi/bookings/<?= (int) $record['id'] ?>/edit" class="btn btn-primary">Edit</a>
    </div>
</div>


<?php
$currentStatus = (string) ($record['status'] ?? 'Pending');
$statusOptions = [
    'Pending' => ['Confirmed', 'Cancelled'],
    'Confirmed' => ['Assigned', 'Cancelled', 'No Show'],
    'Assigned' => ['Driver Arrived', 'Cancelled', 'No Show'],
    'Driver Arrived' => ['In Progress', 'Cancelled', 'No Show'],
    'In Progress' => ['Completed'],
    'Completed' => [],
    'Cancelled' => [],
    'No Show' => [],
];
$nextStatuses = $statusOptions[$currentStatus] ?? [];
$vendorId = (int) ($record['vendor_id'] ?? 0);
?>
<div class="card" style="margin-bottom:20px;">
    <h2 style="margin-top:0;">Booking Status</h2>
    <p style="color:#6b7280;">Move this booking through the controlled taxi lifecycle. Assign a driver and vehicle when moving to <strong>Assigned</strong>.</p>

    <?php if ($nextStatuses): ?>
    <form method="post" action="<?= config('app.base_path') ?>/taxi/bookings/<?= (int) $record['id'] ?>/status" id="booking-status-form">
        <div class="form-grid">
            <div class="form-group">
                <label for="status_action">New Status</label>
                <select name="status" id="status_action" required>
                    <option value="">Select status...</option>
                    <?php foreach ($nextStatuses as $next): ?>
                        <option value="<?= htmlspecialchars($next, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($next, ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" id="assign-driver-field" style="display:none;">
                <label for="status_driver_id">Driver</label>
                <select name="driver_id" id="status_driver_id">
                    <option value="">Select driver...</option>
                    <?php foreach (($drivers ?? []) as $driver): ?>
                        <?php if ((int) ($driver['vendor_id'] ?? 0) === $vendorId): ?>
                            <option value="<?= (int) $driver['id'] ?>"><?= htmlspecialchars((string) ($driver['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?><?= !empty($driver['phone']) ? ' — '.htmlspecialchars((string)$driver['phone'], ENT_QUOTES, 'UTF-8') : '' ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" id="assign-vehicle-field" style="display:none;">
                <label for="status_vehicle_id">Vehicle</label>
                <select name="vehicle_id" id="status_vehicle_id">
                    <option value="">Select vehicle...</option>
                    <?php foreach (($vehicles ?? []) as $vehicle): ?>
                        <?php if ((int) ($vehicle['vendor_id'] ?? 0) === $vendorId): ?>
                            <option value="<?= (int) $vehicle['id'] ?>"><?= htmlspecialchars(trim((string) (($vehicle['make'] ?? '').' '.($vehicle['model'] ?? ''))) ?: (string)($vehicle['vehicle_type'] ?? 'Vehicle'), ENT_QUOTES, 'UTF-8') ?> — <?= htmlspecialchars((string)($vehicle['registration_no'] ?? ''), ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="grid-column:1/-1;">
                <label for="status_notes">Status Note (optional)</label>
                <input type="text" name="notes" id="status_notes" maxlength="500" placeholder="Optional note for the member/status history">
            </div>
        </div>
        <div class="page-actions" style="margin-top:12px;">
            <button class="btn btn-primary" type="submit">Update Booking Status</button>
        </div>
    </form>
    <?php else: ?>
        <p style="margin:0;color:#6b7280;">No further status transition is available for <strong><?= htmlspecialchars($currentStatus, ENT_QUOTES, 'UTF-8') ?></strong>.</p>
    <?php endif; ?>
</div>

<div class="card" style="margin-bottom:20px;">
    <h2 style="margin-top:0;">Status History</h2>
    <?php if (!empty($status_history)): ?>
        <table class="table">
            <thead><tr><th>Time</th><th>From</th><th>To</th><th>Actor</th><th>Note</th></tr></thead>
            <tbody>
            <?php foreach ($status_history as $event): ?>
                <tr>
                    <td><?= htmlspecialchars((string)($event['created_at'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string)($event['old_status'] ?? 'New Booking'), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><strong><?= htmlspecialchars((string)($event['new_status'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></strong></td>
                    <td><?= htmlspecialchars((string)($event['changed_by_name'] ?? ucfirst(strtolower((string)($event['actor_type'] ?? 'System')))), ENT_QUOTES, 'UTF-8') ?> <small>(<?= htmlspecialchars((string)($event['actor_type'] ?? 'SYSTEM'), ENT_QUOTES, 'UTF-8') ?>)</small></td>
                    <td><?= htmlspecialchars((string)($event['notes'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="color:#6b7280;">No status history has been recorded for this booking yet.</p>
    <?php endif; ?>
</div>

<script>
(function(){
    const status=document.getElementById('status_action');
    const driver=document.getElementById('assign-driver-field');
    const vehicle=document.getElementById('assign-vehicle-field');
    const driverSelect=document.getElementById('status_driver_id');
    const vehicleSelect=document.getElementById('status_vehicle_id');
    function sync(){
        const assigned=status && status.value==='Assigned';
        if(driver) driver.style.display=assigned?'block':'none';
        if(vehicle) vehicle.style.display=assigned?'block':'none';
        if(driverSelect) driverSelect.required=assigned;
        if(vehicleSelect) vehicleSelect.required=assigned;
    }
    status?.addEventListener('change',sync); sync();
})();
</script>

<div class="card">
    <table class="table">
        <tbody>
            <tr><th>Taxi Business</th><td><?= htmlspecialchars((string) ($record['vendor_name'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></td></tr>
            <tr><th>Taxi Service</th><td><?= htmlspecialchars((string) ($record['service_name'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></td></tr>
            <tr><th>Vehicle</th><td><?= htmlspecialchars((string) ($record['vehicle_registration_no'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></td></tr>
            <tr><th>Driver</th><td><?= htmlspecialchars((string) ($record['driver_name'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></td></tr>
            <tr><th>Booking Number</th><td><?= htmlspecialchars((string) ($record['booking_no'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></td></tr>
            <tr><th>Booking Source</th><td><?= htmlspecialchars((string) ($record['booking_source'] ?? 'ADMIN'), ENT_QUOTES, 'UTF-8') ?></td></tr>
            <tr><th>Trip Type</th><td><?= ($record['trip_type'] ?? 'ONE_WAY') === 'ROUND_TRIP' ? 'Round Trip' : 'One Way' ?></td></tr>
            <tr>
                <th>Customer Type</th>
                <td><?= !empty($record['member_id']) ? 'Platform Member' : 'Non-member / Guest' ?></td>
            </tr>
            <?php if (!empty($record['member_id'])): ?>
                <tr><th>Member</th><td><?= htmlspecialchars((string) ($record['member_name'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></td></tr>
                <tr><th>Member Number</th><td><?= htmlspecialchars((string) ($record['member_no'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></td></tr>
            <?php endif; ?>
            <tr><th>Customer Name</th><td><?= htmlspecialchars((string) ($record['customer_name'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></td></tr>
            <tr><th>Customer Phone</th><td><?= htmlspecialchars((string) ($record['customer_phone'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></td></tr>
            <tr><th>Pickup Address</th><td><?= htmlspecialchars((string) ($record['pickup_address'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></td></tr>
            <tr><th>Pickup Latitude</th><td><?= htmlspecialchars((string) ($record['pickup_lat'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></td></tr>
            <tr><th>Pickup Longitude</th><td><?= htmlspecialchars((string) ($record['pickup_lng'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></td></tr>
            <tr><th>Destination Address</th><td><?= htmlspecialchars((string) ($record['destination_address'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></td></tr>
            <tr><th>Destination Latitude</th><td><?= htmlspecialchars((string) ($record['destination_lat'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></td></tr>
            <tr><th>Destination Longitude</th><td><?= htmlspecialchars((string) ($record['destination_lng'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></td></tr>
            <tr><th>Booking Type</th><td><?= htmlspecialchars((string) ($record['booking_type'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></td></tr>
            <tr><th>Scheduled At</th><td><?= htmlspecialchars((string) ($record['scheduled_at'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></td></tr>
            <?php if (($record['trip_type'] ?? 'ONE_WAY') === 'ROUND_TRIP'): ?><tr><th>Return Date &amp; Time</th><td><?= htmlspecialchars((string) ($record['return_scheduled_at'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></td></tr><?php endif; ?>
            <tr><th>Fare</th><td><?= htmlspecialchars((string) ($record['fare'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></td></tr>
            <?php if (!empty($record['total_distance_km']) || !empty($record['distance_km'])): ?>
                <?php if (($record['trip_type'] ?? 'ONE_WAY') === 'ROUND_TRIP'): ?>
                    <tr><th>Outbound Distance</th><td><?= htmlspecialchars((string) ($record['distance_km'] ?? '—'), ENT_QUOTES, 'UTF-8') ?> km</td></tr>
                    <tr><th>Outbound ETA</th><td><?= htmlspecialchars((string) ($record['eta_minutes'] ?? '—'), ENT_QUOTES, 'UTF-8') ?> minutes</td></tr>
                    <tr><th>Return Distance</th><td><?= htmlspecialchars((string) ($record['return_distance_km'] ?? '—'), ENT_QUOTES, 'UTF-8') ?> km</td></tr>
                    <tr><th>Return ETA</th><td><?= htmlspecialchars((string) ($record['return_eta_minutes'] ?? '—'), ENT_QUOTES, 'UTF-8') ?> minutes</td></tr>
                    <tr><th>Total Estimated Distance</th><td><?= htmlspecialchars((string) ($record['total_distance_km'] ?? '—'), ENT_QUOTES, 'UTF-8') ?> km</td></tr>
                    <tr><th>Total Estimated ETA</th><td><?= htmlspecialchars((string) ($record['total_eta_minutes'] ?? '—'), ENT_QUOTES, 'UTF-8') ?> minutes</td></tr>
                <?php else: ?>
                    <tr><th>Estimated Road Distance</th><td><?= htmlspecialchars((string) ($record['distance_km'] ?? '—'), ENT_QUOTES, 'UTF-8') ?> km</td></tr>
                    <tr><th>Estimated Driving Time</th><td><?= htmlspecialchars((string) ($record['eta_minutes'] ?? '—'), ENT_QUOTES, 'UTF-8') ?> minutes</td></tr>
                <?php endif; ?>
                <tr><th>Route Source</th><td><?= htmlspecialchars((string) ($record['route_source'] ?? 'GEMINI'), ENT_QUOTES, 'UTF-8') ?><?php if (!empty($record['route_calculated_at'])): ?> · <?= htmlspecialchars((string) $record['route_calculated_at'], ENT_QUOTES, 'UTF-8') ?><?php endif; ?></td></tr>
                <tr><th>Route Estimate Status</th><td><?php if (!empty($record['route_estimate_committed_at'])): ?><strong>Committed</strong> · <?= htmlspecialchars((string) $record['route_estimate_committed_at'], ENT_QUOTES, 'UTF-8') ?><?php else: ?>Calculated / not committed<?php endif; ?></td></tr>
            <?php endif; ?>
            <tr><th>Status</th><td><?= htmlspecialchars((string) ($record['status'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></td></tr>
            <tr><th>Notes</th><td><?= htmlspecialchars((string) ($record['notes'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></td></tr>
            <tr><th>UUID</th><td><code><?= htmlspecialchars((string) ($record['uuid'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></code></td></tr>
            <tr><th>Created At</th><td><?= htmlspecialchars((string) ($record['created_at'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></td></tr>
            <tr><th>Updated At</th><td><?= htmlspecialchars((string) ($record['updated_at'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></td></tr>
        </tbody>
    </table>
</div>
