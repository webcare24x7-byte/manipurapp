<?php

declare(strict_types=1);

$data = $record ?? $old ?? [];
$selectedMemberId = (string) ($data['member_id'] ?? '');
$selectedSource = (string) ($data['booking_source'] ?? 'ADMIN');
$tripType = strtoupper((string) ($data['trip_type'] ?? 'ONE_WAY'));
$bookingId = (int) ($data['id'] ?? 0);
$servicesForJs = [];
foreach (($services ?? []) as $serviceOption) {
    $servicesForJs[(string) $serviceOption['id']] = [
        'id' => (int) $serviceOption['id'],
        'vendor_id' => (int) ($serviceOption['vendor_id'] ?? 0),
        'name' => (string) ($serviceOption['name'] ?? ''),
        'service_type' => (string) ($serviceOption['service_type'] ?? ''),
        'pricing_mode' => (string) ($serviceOption['pricing_mode'] ?? 'PER_RIDE'),
        'base_fare' => (float) ($serviceOption['base_fare'] ?? 0),
        'per_km' => (float) ($serviceOption['per_km'] ?? 0),
        'per_minute' => (float) ($serviceOption['per_minute'] ?? 0),
        'minimum_fare' => (float) ($serviceOption['minimum_fare'] ?? 0),
        'included_km' => (float) ($serviceOption['included_km'] ?? 0),
        'daily_rate' => (float) ($serviceOption['daily_rate'] ?? 0),
        'extra_km_rate' => (float) ($serviceOption['extra_km_rate'] ?? 0),
    ];
}
?>

<style>
    .taxi-customer-box{padding:18px;border:1px solid #e5e7eb;border-radius:12px;background:#fafafa;margin-bottom:20px}
    .taxi-customer-box h3{margin:0 0 6px;font-size:16px}.taxi-customer-box p{margin:0 0 16px;color:#6b7280;font-size:13px}
    .member-meta,.customer-type-note{margin-top:7px;color:#6b7280;font-size:12px}.member-actions{display:flex;gap:10px;align-items:center;flex-wrap:wrap;margin-top:10px}
    .trip-type-box{grid-column:1/-1;padding:18px;border:1px solid #e5e7eb;border-radius:12px;background:#fafafa}
    .trip-type-box .trip-options{display:flex;gap:10px;flex-wrap:wrap;margin-top:10px}.trip-option{display:flex;align-items:center;gap:8px;padding:10px 14px;border:1px solid #d1d5db;border-radius:10px;background:#fff;cursor:pointer}.trip-option input{margin:0}
    .round-trip-fields{grid-column:1/-1;display:contents}.round-trip-note{grid-column:1/-1;padding:10px 12px;background:#ecfdf5;border:1px solid #bbf7d0;border-radius:9px;color:#166534;font-size:13px}
    .taxi-location-card{grid-column:1/-1;border:1px solid #e5e7eb;border-radius:14px;padding:18px;background:#fff}
    .taxi-location-card h3{margin:0 0 5px;font-size:16px}.taxi-location-card>p{margin:0 0 16px;color:#6b7280;font-size:13px}
    .taxi-location-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}.taxi-location-panel{border:1px solid #e5e7eb;border-radius:12px;padding:14px;background:#fafafa}
    .taxi-location-panel .location-title{font-weight:700;margin-bottom:10px}.taxi-location-panel .location-actions{display:flex;gap:8px;flex-wrap:wrap;margin-top:10px}
    .taxi-location-panel .location-actions button{border:1px solid #d1d5db;background:#fff;color:#111827;border-radius:9px;padding:9px 12px;cursor:pointer;font:inherit}
    .taxi-location-panel .location-actions button.primary-location{background:#ecfdf5;border-color:#86efac;color:#166534}
    .taxi-route-box{grid-column:1/-1;margin-top:2px;padding:16px;border:1px solid #d1fae5;border-radius:12px;background:#f0fdf4}.taxi-route-header{display:flex;justify-content:space-between;align-items:center;gap:14px;flex-wrap:wrap}.taxi-route-header h3{margin:0;font-size:16px}.taxi-route-header p{margin:4px 0 0;color:#6b7280;font-size:12px}.taxi-route-button{border:1px solid #047857;background:#047857;color:#fff;border-radius:9px;padding:10px 14px;cursor:pointer;font:inherit;font-weight:600}.taxi-route-button:disabled{opacity:.65;cursor:wait}.taxi-route-result{display:none;margin-top:14px}.taxi-route-result.is-visible{display:block}.taxi-route-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:10px}.taxi-route-stat{background:#fff;border:1px solid #d1fae5;border-radius:10px;padding:12px}.taxi-route-stat span{display:block;color:#6b7280;font-size:11px;margin-bottom:4px}.taxi-route-stat strong{font-size:18px;color:#065f46}.taxi-route-meta{margin-top:10px;color:#6b7280;font-size:11px}.taxi-route-error{display:none;margin-top:10px;padding:10px 12px;border-radius:9px;background:#fef2f2;border:1px solid #fecaca;color:#991b1b;font-size:12px}.taxi-route-error.is-visible{display:block}@media(max-width:760px){.taxi-route-grid{grid-template-columns:1fr}}

    .taxi-service-pricing{grid-column:1/-1;padding:18px;border:1px solid #dbeafe;border-radius:14px;background:#f8fbff;margin-top:-4px}.taxi-service-pricing h3{margin:0 0 5px;font-size:16px}.taxi-service-pricing .pricing-intro{margin:0 0 14px;color:#64748b;font-size:12px}.pricing-rule-head{display:flex;justify-content:space-between;align-items:flex-start;gap:12px;flex-wrap:wrap}.pricing-service-name{font-weight:700;color:#0f172a}.pricing-service-type{display:inline-flex;align-items:center;padding:4px 9px;border-radius:999px;background:#e0f2fe;color:#075985;font-size:11px;font-weight:700}.pricing-mode-badge{display:inline-flex;align-items:center;padding:5px 10px;border-radius:999px;background:#dcfce7;color:#166534;font-size:11px;font-weight:700}.pricing-rules-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:9px;margin-top:12px}.pricing-rule{background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:11px}.pricing-rule span{display:block;color:#64748b;font-size:10px;text-transform:uppercase;letter-spacing:.04em;margin-bottom:4px}.pricing-rule strong{font-size:15px;color:#0f172a}.pricing-calculation-note{margin-top:12px;padding:11px 12px;border-radius:10px;background:#eff6ff;color:#1e3a8a;font-size:12px;line-height:1.5}.taxi-fare-estimate{grid-column:1/-1;padding:18px;border:1px solid #f59e0b;border-radius:14px;background:#fffbeb}.taxi-fare-estimate h3{margin:0;font-size:17px}.fare-estimate-subtitle{margin:4px 0 14px;color:#92400e;font-size:12px}.fare-breakdown{display:grid;gap:7px}.fare-line{display:flex;justify-content:space-between;gap:12px;padding:8px 0;border-bottom:1px dashed #fde68a;font-size:13px}.fare-line:last-child{border-bottom:0}.fare-line span{color:#57534e}.fare-line strong{color:#292524}.fare-total{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-top:10px;padding-top:13px;border-top:2px solid #fbbf24}.fare-total span{font-weight:700;color:#78350f}.fare-total strong{font-size:24px;color:#92400e}.fare-estimate-note{margin-top:10px;font-size:11px;color:#78716c;line-height:1.5}.fare-estimate-status{display:none;margin-top:10px;padding:9px 11px;border-radius:9px;font-size:12px}.fare-estimate-status.is-visible{display:block}.fare-estimate-status.error{background:#fef2f2;border:1px solid #fecaca;color:#991b1b}.fare-estimate-status.ok{background:#ecfdf5;border:1px solid #bbf7d0;color:#166534}.fare-quote-input{margin-top:12px}.fare-quote-input input{font-size:18px;font-weight:700}.route-commit-row{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-top:14px;padding-top:13px;border-top:1px solid #d1fae5}.route-commit-info{font-size:11px;color:#6b7280;line-height:1.45}.route-commit-button{border:1px solid #1d4ed8;background:#1d4ed8;color:#fff;border-radius:9px;padding:10px 14px;cursor:pointer;font:inherit;font-weight:700}.route-commit-button:disabled{opacity:.65;cursor:wait}.route-commit-badge{display:inline-flex;padding:5px 9px;border-radius:999px;background:#dcfce7;color:#166534;font-size:11px;font-weight:700}.pricing-empty{padding:12px;background:#fff;border:1px dashed #cbd5e1;border-radius:10px;color:#64748b;font-size:12px}
    @media(max-width:900px){.pricing-rules-grid{grid-template-columns:repeat(2,1fr)}}
    @media(max-width:760px){.pricing-rules-grid{grid-template-columns:1fr}.route-commit-row{align-items:flex-start;flex-direction:column}.route-commit-button{width:100%}}
    .taxi-coordinate-status{margin-top:9px;font-size:12px;color:#6b7280;line-height:1.45}.taxi-coordinate-status strong{color:#374151}
    .taxi-map-modal{position:fixed;inset:0;z-index:9999;background:rgba(17,24,39,.58);display:none;align-items:center;justify-content:center;padding:20px}
    .taxi-map-modal.is-open{display:flex}.taxi-map-dialog{width:min(1000px,96vw);height:min(760px,92vh);background:#fff;border-radius:16px;box-shadow:0 25px 70px rgba(0,0,0,.25);overflow:hidden;display:flex;flex-direction:column}
    .taxi-map-header{display:flex;justify-content:space-between;align-items:center;gap:16px;padding:14px 18px;border-bottom:1px solid #e5e7eb}.taxi-map-header h3{margin:0;font-size:17px}.taxi-map-header p{margin:3px 0 0;color:#6b7280;font-size:12px}.taxi-map-close{border:0;background:#f3f4f6;border-radius:9px;width:36px;height:36px;font-size:20px;cursor:pointer}.taxi-map-body{position:relative;flex:1;min-height:0}.taxi-map{position:absolute;inset:0}.taxi-map-footer{display:flex;justify-content:space-between;align-items:center;gap:12px;padding:12px 16px;border-top:1px solid #e5e7eb}.taxi-map-coordinates{font-size:12px;color:#4b5563}.taxi-map-footer-actions{display:flex;gap:8px}.taxi-map-footer button{border:1px solid #d1d5db;background:#fff;border-radius:9px;padding:9px 13px;cursor:pointer;font:inherit}.taxi-map-footer .taxi-map-use{background:#047857;border-color:#047857;color:#fff}
    .taxi-map-hint{position:absolute;z-index:500;top:12px;left:50%;transform:translateX(-50%);background:rgba(255,255,255,.96);border:1px solid #e5e7eb;border-radius:999px;padding:8px 13px;font-size:12px;color:#374151;box-shadow:0 3px 12px rgba(0,0,0,.12);pointer-events:none}
    @media (max-width:760px){.taxi-location-grid{grid-template-columns:1fr}.taxi-map-modal{padding:8px}.taxi-map-dialog{width:100%;height:96vh}.taxi-map-footer{align-items:flex-start;flex-direction:column}.taxi-map-footer-actions{width:100%}.taxi-map-footer button{flex:1}}
</style>

<div class="card">
    <div class="taxi-customer-box">
        <h3>Customer</h3>
        <p>Select an existing platform member, or leave this as a non-member customer.</p>
        <div class="form-grid">
            <div class="form-group">
                <label for="member_id">Platform Member</label>
                <select id="member_id" name="member_id">
                    <option value="">Non-member / Guest Customer</option>
                    <?php foreach (($members ?? []) as $member): ?>
                        <option value="<?= (int) $member['id'] ?>" data-name="<?= htmlspecialchars((string) $member['display_name'], ENT_QUOTES, 'UTF-8') ?>" data-phone="<?= htmlspecialchars((string) ($member['phone'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" data-email="<?= htmlspecialchars((string) ($member['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" <?= $selectedMemberId === (string) $member['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars((string) $member['display_name'], ENT_QUOTES, 'UTF-8') ?> — <?= htmlspecialchars((string) ($member['member_no'] ?? ''), ENT_QUOTES, 'UTF-8') ?><?php if (!empty($member['phone'])): ?> — <?= htmlspecialchars((string) $member['phone'], ENT_QUOTES, 'UTF-8') ?><?php endif; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="customer-type-note" id="member_note">Non-member bookings are allowed. Customer name and phone are stored directly on the booking.</div>
                <div class="member-actions"><a href="<?= config('app.base_path') ?>/members/create" target="_blank" rel="noopener" class="link-button">Create New Member</a><a href="<?= config('app.base_path') ?>/members" target="_blank" rel="noopener" class="link-button">Manage Members</a></div>
            </div>
            <div class="form-group">
                <label for="booking_source">Booking Source <span class="required">*</span></label>
                <select id="booking_source" name="booking_source" required>
                    <option value="APP" <?= $selectedSource === 'APP' ? 'selected' : '' ?>>APP</option><option value="PHONE" <?= $selectedSource === 'PHONE' ? 'selected' : '' ?>>Phone</option><option value="WALK_IN" <?= $selectedSource === 'WALK_IN' ? 'selected' : '' ?>>Walk-in</option><option value="ADMIN" <?= $selectedSource === 'ADMIN' ? 'selected' : '' ?>>Admin</option>
                </select>
                <div class="customer-type-note">Use Phone or Walk-in for customers who are not platform members.</div>
            </div>
        </div>
    </div>

    <div class="form-grid">
        <div class="form-group"><label for="vendor_id">Taxi Business <span class="required">*</span></label><select id="vendor_id" name="vendor_id" required><option value="">Select taxi business...</option><?php foreach (($vendors ?? []) as $vendor): ?><option value="<?= (int) $vendor['id'] ?>" <?= (string) ($data['vendor_id'] ?? '') === (string) $vendor['id'] ? 'selected' : '' ?>><?= htmlspecialchars((string) $vendor['business_name'], ENT_QUOTES, 'UTF-8') ?></option><?php endforeach; ?></select></div>

        <div class="form-group"><label for="service_id">Taxi Service <span class="required">*</span></label><select id="service_id" name="service_id" required><option value="">Select service...</option><?php foreach (($services ?? []) as $item): ?><option value="<?= (int) $item['id'] ?>" data-vendor-id="<?= (int) ($item['vendor_id'] ?? 0) ?>" <?= (string) ($data['service_id'] ?? '') === (string) $item['id'] ? 'selected' : '' ?>><?= htmlspecialchars((string) $item['name'], ENT_QUOTES, 'UTF-8') ?> — <?= htmlspecialchars((string) ($item['service_type'] ?? ''), ENT_QUOTES, 'UTF-8') ?></option><?php endforeach; ?></select></div>

        <div class="taxi-service-pricing" id="taxi_service_pricing">
            <div class="pricing-empty">Select a taxi service to see its pricing rules before creating the booking.</div>
        </div>

        <div class="form-group"><label for="vehicle_id">Vehicle</label><select id="vehicle_id" name="vehicle_id"><option value="">Select vehicle...</option><?php foreach (($vehicles ?? []) as $item): ?><option value="<?= (int) $item['id'] ?>" <?= (string) ($data['vehicle_id'] ?? '') === (string) $item['id'] ? 'selected' : '' ?>><?= htmlspecialchars((string) ($item['registration_no'] ?? ''), ENT_QUOTES, 'UTF-8') ?></option><?php endforeach; ?></select></div>

        <div class="form-group"><label for="driver_id">Driver</label><select id="driver_id" name="driver_id"><option value="">Select driver...</option><?php foreach (($drivers ?? []) as $item): ?><option value="<?= (int) $item['id'] ?>" <?= (string) ($data['driver_id'] ?? '') === (string) $item['id'] ? 'selected' : '' ?>><?= htmlspecialchars((string) $item['name'], ENT_QUOTES, 'UTF-8') ?> — <?= htmlspecialchars((string) $item['phone'], ENT_QUOTES, 'UTF-8') ?></option><?php endforeach; ?></select></div>

        <div class="form-group"><label for="booking_no">Booking Number <span class="required">*</span></label><input type="text" id="booking_no" name="booking_no" value="<?= htmlspecialchars((string) ($data['booking_no'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required></div>
        <div class="form-group"><label for="customer_name">Customer Name <span class="required">*</span></label><input type="text" id="customer_name" name="customer_name" value="<?= htmlspecialchars((string) ($data['customer_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required></div>
        <div class="form-group"><label for="customer_phone">Customer Phone <span class="required">*</span></label><input type="text" id="customer_phone" name="customer_phone" value="<?= htmlspecialchars((string) ($data['customer_phone'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required></div>

        <div class="trip-type-box">
            <strong>Trip Type</strong>
            <div class="trip-options">
                <label class="trip-option"><input type="radio" name="trip_type" value="ONE_WAY" <?= $tripType === 'ONE_WAY' ? 'checked' : '' ?>> One Way</label>
                <label class="trip-option"><input type="radio" name="trip_type" value="ROUND_TRIP" <?= $tripType === 'ROUND_TRIP' ? 'checked' : '' ?>> Round Trip</label>
            </div>
        </div>

        <div class="taxi-location-card">
            <h3>Trip Locations</h3>
            <p>Enter a description or address, then use the map to place the exact pickup and destination coordinates. The map pin is the authoritative location when an address is not precisely recognised.</p>
            <div class="taxi-location-grid">
                <div class="taxi-location-panel">
                    <div class="location-title">Pickup Location</div>
                    <div class="form-group">
                        <label for="pickup_address">Pickup Address <span class="required">*</span></label>
                        <input type="text" id="pickup_address" name="pickup_address" value="<?= htmlspecialchars((string) ($data['pickup_address'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <div class="location-actions">
                        <button type="button" class="primary-location" data-map-target="pickup">📍 Select on Map</button>
                        <button type="button" data-use-location="pickup">◎ Use My Location</button>
                    </div>
                    <div class="taxi-coordinate-status" id="pickup_coordinate_status">
                        Coordinates: <strong><?= ($data['pickup_lat'] ?? '') !== '' && ($data['pickup_lng'] ?? '') !== '' ? htmlspecialchars((string) $data['pickup_lat'], ENT_QUOTES, 'UTF-8') . ', ' . htmlspecialchars((string) $data['pickup_lng'], ENT_QUOTES, 'UTF-8') : 'Not selected' ?></strong>
                    </div>
                    <input type="hidden" id="pickup_lat" name="pickup_lat" value="<?= htmlspecialchars((string) ($data['pickup_lat'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" id="pickup_lng" name="pickup_lng" value="<?= htmlspecialchars((string) ($data['pickup_lng'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                </div>

                <div class="taxi-location-panel">
                    <div class="location-title">Destination</div>
                    <div class="form-group">
                        <label for="destination_address">Destination Address <span class="required">*</span></label>
                        <input type="text" id="destination_address" name="destination_address" value="<?= htmlspecialchars((string) ($data['destination_address'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <div class="location-actions">
                        <button type="button" class="primary-location" data-map-target="destination">📍 Select on Map</button>
                        <button type="button" data-use-location="destination">◎ Use My Location</button>
                    </div>
                    <div class="taxi-coordinate-status" id="destination_coordinate_status">
                        Coordinates: <strong><?= ($data['destination_lat'] ?? '') !== '' && ($data['destination_lng'] ?? '') !== '' ? htmlspecialchars((string) $data['destination_lat'], ENT_QUOTES, 'UTF-8') . ', ' . htmlspecialchars((string) $data['destination_lng'], ENT_QUOTES, 'UTF-8') : 'Not selected' ?></strong>
                    </div>
                    <input type="hidden" id="destination_lat" name="destination_lat" value="<?= htmlspecialchars((string) ($data['destination_lat'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" id="destination_lng" name="destination_lng" value="<?= htmlspecialchars((string) ($data['destination_lng'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                </div>
            </div>
        </div>

        <div class="taxi-route-box">
            <div class="taxi-route-header">
                <div>
                    <h3>Estimated Road Distance &amp; ETA</h3>
                    <p>Gemini provides an estimated road distance and driving time. Calculate first, review it, then explicitly commit it to the booking.</p>
                </div>
                <button type="button" class="taxi-route-button" id="taxi_calculate_route">Calculate Distance &amp; ETA</button>
            </div>
            <div class="taxi-route-error" id="taxi_route_error"></div>
            <div class="taxi-route-result" id="taxi_route_result">
                <div class="taxi-route-grid" id="taxi_route_one_way">
                    <div class="taxi-route-stat"><span>Estimated Road Distance</span><strong id="route_distance_value">—</strong></div>
                    <div class="taxi-route-stat"><span>Estimated Driving Time</span><strong id="route_eta_value">—</strong></div>
                </div>
                <div class="taxi-route-grid" id="taxi_route_round_trip" style="display:none;">
                    <div class="taxi-route-stat"><span>Outbound Distance</span><strong id="route_outbound_distance">—</strong></div>
                    <div class="taxi-route-stat"><span>Outbound ETA</span><strong id="route_outbound_eta">—</strong></div>
                    <div class="taxi-route-stat"><span>Return Distance</span><strong id="route_return_distance">—</strong></div>
                    <div class="taxi-route-stat"><span>Return ETA</span><strong id="route_return_eta">—</strong></div>
                    <div class="taxi-route-stat"><span>Total Distance</span><strong id="route_total_distance">—</strong></div>
                    <div class="taxi-route-stat"><span>Total ETA</span><strong id="route_total_eta">—</strong></div>
                </div>
                <div class="taxi-route-meta">Source: <strong>Gemini</strong> · Estimated road route · Last calculated: <span id="route_calculated_at_display">—</span></div>
            <div class="route-commit-row">
                <div class="route-commit-info" id="route_commit_info">Calculated values are a preview until you explicitly commit them.</div>
                <button type="button" class="route-commit-button" id="taxi_commit_route" disabled>Commit Route Estimate</button>
            </div>
            </div>
            <input type="hidden" id="distance_km" name="distance_km" value="<?= htmlspecialchars((string) ($data['distance_km'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" id="eta_minutes" name="eta_minutes" value="<?= htmlspecialchars((string) ($data['eta_minutes'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" id="return_distance_km" name="return_distance_km" value="<?= htmlspecialchars((string) ($data['return_distance_km'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" id="return_eta_minutes" name="return_eta_minutes" value="<?= htmlspecialchars((string) ($data['return_eta_minutes'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" id="total_distance_km" name="total_distance_km" value="<?= htmlspecialchars((string) ($data['total_distance_km'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" id="total_eta_minutes" name="total_eta_minutes" value="<?= htmlspecialchars((string) ($data['total_eta_minutes'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" id="route_source" name="route_source" value="<?= htmlspecialchars((string) ($data['route_source'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" id="route_calculated_at" name="route_calculated_at" value="<?= htmlspecialchars((string) ($data['route_calculated_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" id="route_estimate_commit_requested" name="route_estimate_commit_requested" value="<?= !empty($data['route_estimate_committed_at']) ? '1' : '0' ?>">
            <input type="hidden" id="route_estimate_committed_at" name="route_estimate_committed_at" value="<?= htmlspecialchars((string) ($data['route_estimate_committed_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" id="route_estimate_committed_by" name="route_estimate_committed_by" value="<?= htmlspecialchars((string) ($data['route_estimate_committed_by'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="form-group"><label for="booking_type">Booking Type</label><select id="booking_type" name="booking_type"><option value="Immediate" <?= ($data['booking_type'] ?? '') === 'Immediate' ? 'selected' : '' ?>>Immediate</option><option value="Scheduled" <?= ($data['booking_type'] ?? '') === 'Scheduled' ? 'selected' : '' ?>>Scheduled</option></select></div>
        <div class="form-group"><label for="scheduled_at">Departure / Scheduled At</label><input type="datetime-local" id="scheduled_at" name="scheduled_at" value="<?= htmlspecialchars((string) ($data['scheduled_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></div>

        <div class="form-group round-trip-field"><label for="return_scheduled_at">Return Date &amp; Time</label><input type="datetime-local" id="return_scheduled_at" name="return_scheduled_at" value="<?= htmlspecialchars((string) ($data['return_scheduled_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"><small>Required for round trips. Return route is treated as destination → pickup.</small></div>
        <div class="round-trip-note round-trip-field">Round trip: the customer travels from pickup to destination and returns from destination to pickup at the selected return time.</div>

        <div class="taxi-fare-estimate" id="taxi_fare_estimate">
            <h3>Total Estimated Fare</h3>
            <p class="fare-estimate-subtitle" id="fare_estimate_subtitle">Select a taxi service to see how the booking fare will be calculated.</p>
            <div class="fare-breakdown" id="fare_breakdown"></div>
            <div class="fare-total"><span id="fare_total_label">Estimated Fare</span><strong id="fare_total_value">—</strong></div>
            <div class="fare-estimate-note" id="fare_estimate_note">This amount is calculated from the selected service's configured pricing rules. For distance-based services, the estimate uses the Gemini road-distance/ETA estimate.</div>
            <div class="fare-estimate-status" id="fare_estimate_status"></div>
            <div class="fare-quote-input form-group" id="fare_quote_input" style="display:none;">
                <label for="fare">Approved Quoted Fare <span class="required">*</span></label>
                <input type="number" id="fare" name="fare" value="<?= htmlspecialchars((string) ($data['fare'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" step="0.01" min="0">
                <small>Required because this service uses Custom Quote pricing. Enter the approved quote.</small>
            </div>
            <input type="hidden" id="fare_auto_value" value="<?= htmlspecialchars((string) ($data['fare'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="form-group"><label for="status">Status</label><select id="status" name="status"><option value="Pending" <?= ($data['status'] ?? '') === 'Pending' ? 'selected' : '' ?>>Pending</option><option value="Confirmed" <?= ($data['status'] ?? '') === 'Confirmed' ? 'selected' : '' ?>>Confirmed</option><option value="Assigned" <?= ($data['status'] ?? '') === 'Assigned' ? 'selected' : '' ?>>Assigned</option><option value="Driver Arrived" <?= ($data['status'] ?? '') === 'Driver Arrived' ? 'selected' : '' ?>>Driver Arrived</option><option value="In Progress" <?= ($data['status'] ?? '') === 'In Progress' ? 'selected' : '' ?>>In Progress</option><option value="Completed" <?= ($data['status'] ?? '') === 'Completed' ? 'selected' : '' ?>>Completed</option><option value="Cancelled" <?= ($data['status'] ?? '') === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option><option value="No Show" <?= ($data['status'] ?? '') === 'No Show' ? 'selected' : '' ?>>No Show</option></select></div>
        <div class="form-group full-width"><label for="notes">Notes</label><textarea id="notes" name="notes" rows="5"><?= htmlspecialchars((string) ($data['notes'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea></div>
    </div>
</div>

<div class="taxi-map-modal" id="taxi_map_modal" aria-hidden="true">
    <div class="taxi-map-dialog" role="dialog" aria-modal="true" aria-labelledby="taxi_map_title">
        <div class="taxi-map-header">
            <div>
                <h3 id="taxi_map_title">Select Location</h3>
                <p>Click the map or drag the pin to the exact pickup/drop point.</p>
            </div>
            <button type="button" class="taxi-map-close" id="taxi_map_close" aria-label="Close">&times;</button>
        </div>
        <div class="taxi-map-body">
            <div class="taxi-map" id="taxi_location_map"></div>
            <div class="taxi-map-hint">Click anywhere on the map or drag the marker</div>
        </div>
        <div class="taxi-map-footer">
            <div class="taxi-map-coordinates" id="taxi_map_coordinates">No location selected</div>
            <div class="taxi-map-footer-actions">
                <button type="button" id="taxi_map_cancel">Cancel</button>
                <button type="button" class="taxi-map-use" id="taxi_map_use">Use This Location</button>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

<script>
(function () {
    const memberSelect = document.getElementById('member_id');
    const nameInput = document.getElementById('customer_name');
    const phoneInput = document.getElementById('customer_phone');
    const note = document.getElementById('member_note');
    const tripRadios = document.querySelectorAll('input[name="trip_type"]');
    const returnInput = document.getElementById('return_scheduled_at');
    const returnFields = document.querySelectorAll('.round-trip-field');

    if (memberSelect) {
        memberSelect.addEventListener('change', function () {
            const option = memberSelect.options[memberSelect.selectedIndex];
            if (!memberSelect.value) {
                note.textContent = 'Non-member bookings are allowed. Customer name and phone are stored directly on the booking.';
                return;
            }
            if (option.dataset.name) nameInput.value = option.dataset.name;
            if (option.dataset.phone) phoneInput.value = option.dataset.phone;
            note.textContent = 'Platform member selected. Name and phone have been filled from the member profile and remain stored as the booking snapshot.';
        });
    }

    function syncTripType() {
        const selected = document.querySelector('input[name="trip_type"]:checked');
        const roundTrip = selected && selected.value === 'ROUND_TRIP';
        returnFields.forEach(function (el) { el.style.display = roundTrip ? '' : 'none'; });
        if (returnInput) returnInput.required = !!roundTrip;
    }
    tripRadios.forEach(function (radio) { radio.addEventListener('change', syncTripType); });
    syncTripType();

    // ------------------------------------------------------------
    // Coordinate selector
    // ------------------------------------------------------------
    const mapModal = document.getElementById('taxi_map_modal');
    const mapClose = document.getElementById('taxi_map_close');
    const mapCancel = document.getElementById('taxi_map_cancel');
    const mapUse = document.getElementById('taxi_map_use');
    const mapTitle = document.getElementById('taxi_map_title');
    const mapCoordinates = document.getElementById('taxi_map_coordinates');

    const defaultCenter = [24.8170, 93.9368];
    const manipurBounds = [
        [23.80, 93.00],
        [25.70, 94.80]
    ];

    let locationMap = null;
    let locationMarker = null;
    let activeLocationType = null;
    let pendingLat = null;
    let pendingLng = null;

    function inputValue(id) {
        const el = document.getElementById(id);
        return el ? parseFloat(el.value) : NaN;
    }

    function validCoordinate(lat, lng) {
        return Number.isFinite(lat) && Number.isFinite(lng)
            && lat >= -90 && lat <= 90
            && lng >= -180 && lng <= 180;
    }

    function ensureMap() {
        if (locationMap) return;

        locationMap = L.map('taxi_location_map', {
            center: defaultCenter,
            zoom: 10,
            minZoom: 7,
            maxZoom: 19,
            maxBounds: manipurBounds,
            maxBoundsViscosity: 0.75,
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(locationMap);

        locationMap.on('click', function (event) {
            setPendingLocation(event.latlng.lat, event.latlng.lng);
        });
    }

    function setPendingLocation(lat, lng) {
        if (!validCoordinate(lat, lng)) return;

        pendingLat = Number(lat);
        pendingLng = Number(lng);

        if (!locationMarker) {
            locationMarker = L.marker([pendingLat, pendingLng], { draggable: true }).addTo(locationMap);
            locationMarker.on('dragend', function () {
                const point = locationMarker.getLatLng();
                setPendingLocation(point.lat, point.lng);
            });
        } else {
            locationMarker.setLatLng([pendingLat, pendingLng]);
        }

        mapCoordinates.textContent =
            'Selected: ' + pendingLat.toFixed(7) + ', ' + pendingLng.toFixed(7);
    }

    function openLocationMap(type) {
        activeLocationType = type;
        ensureMap();

        const prefix = type === 'pickup' ? 'pickup' : 'destination';
        const lat = inputValue(prefix + '_lat');
        const lng = inputValue(prefix + '_lng');

        if (validCoordinate(lat, lng)) {
            pendingLat = lat;
            pendingLng = lng;
            locationMap.setView([lat, lng], Math.max(locationMap.getZoom(), 14));
            setPendingLocation(lat, lng);
        } else {
            pendingLat = null;
            pendingLng = null;
            locationMap.setView(defaultCenter, 10);
            if (locationMarker) {
                locationMap.removeLayer(locationMarker);
                locationMarker = null;
            }
            mapCoordinates.textContent = 'Click the map to select a location';
        }

        mapTitle.textContent = type === 'pickup' ? 'Select Pickup Location' : 'Select Destination';
        mapModal.classList.add('is-open');
        mapModal.setAttribute('aria-hidden', 'false');

        setTimeout(function () {
            locationMap.invalidateSize();
        }, 50);
    }

    function closeLocationMap() {
        mapModal.classList.remove('is-open');
        mapModal.setAttribute('aria-hidden', 'true');
        activeLocationType = null;
    }

    function updateCoordinateStatus(type, lat, lng) {
        const status = document.getElementById(type + '_coordinate_status');
        if (!status) return;
        const strong = status.querySelector('strong');
        if (strong) {
            strong.textContent = lat.toFixed(7) + ', ' + lng.toFixed(7);
        }
    }

    function applyPendingLocation() {
        if (!activeLocationType || !validCoordinate(pendingLat, pendingLng)) {
            alert('Please select a location on the map first.');
            return;
        }

        const prefix = activeLocationType;
        document.getElementById(prefix + '_lat').value = pendingLat.toFixed(7);
        document.getElementById(prefix + '_lng').value = pendingLng.toFixed(7);
        updateCoordinateStatus(prefix, pendingLat, pendingLng);
        closeLocationMap();
    }

    document.querySelectorAll('[data-map-target]').forEach(function (button) {
        button.addEventListener('click', function () {
            openLocationMap(button.dataset.mapTarget);
        });
    });

    document.querySelectorAll('[data-use-location]').forEach(function (button) {
        button.addEventListener('click', function () {
            const type = button.dataset.useLocation;

            if (!navigator.geolocation) {
                alert('Your browser does not support location detection. Please use the map selector.');
                return;
            }

            button.disabled = true;
            button.textContent = 'Getting location...';

            navigator.geolocation.getCurrentPosition(
                function (position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    document.getElementById(type + '_lat').value = lat.toFixed(7);
                    document.getElementById(type + '_lng').value = lng.toFixed(7);
                    updateCoordinateStatus(type, lat, lng);
                    button.disabled = false;
                    button.textContent = '◎ Use My Location';
                },
                function () {
                    alert('Unable to read your current location. Please select the point on the map.');
                    button.disabled = false;
                    button.textContent = '◎ Use My Location';
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 30000 }
            );
        });
    });

    mapUse.addEventListener('click', applyPendingLocation);
    mapClose.addEventListener('click', closeLocationMap);
    mapCancel.addEventListener('click', closeLocationMap);

    mapModal.addEventListener('click', function (event) {
        if (event.target === mapModal) closeLocationMap();
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && mapModal.classList.contains('is-open')) {
            closeLocationMap();
        }
    });


    // ------------------------------------------------------------
    // Gemini route estimate
    // ------------------------------------------------------------
    const routeButton = document.getElementById('taxi_calculate_route');
    const routeResult = document.getElementById('taxi_route_result');
    const routeError = document.getElementById('taxi_route_error');
    const routeOneWay = document.getElementById('taxi_route_one_way');
    const routeRoundTrip = document.getElementById('taxi_route_round_trip');
    const basePath = <?= json_encode((string) config('app.base_path')) ?>;

    const taxiServices = <?= json_encode($servicesForJs, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    const currentBookingId = <?= $bookingId ?>;
    const servicePricingBox = document.getElementById('taxi_service_pricing');
    const serviceSelect = document.getElementById('service_id');
    const vendorSelect = document.getElementById('vendor_id');
    const fareEstimateBox = document.getElementById('taxi_fare_estimate');
    const fareBreakdown = document.getElementById('fare_breakdown');
    const fareEstimateSubtitle = document.getElementById('fare_estimate_subtitle');
    const fareEstimateNote = document.getElementById('fare_estimate_note');
    const fareTotalValue = document.getElementById('fare_total_value');
    const fareTotalLabel = document.getElementById('fare_total_label');
    const fareQuoteInput = document.getElementById('fare_quote_input');
    const fareInput = document.getElementById('fare');
    const fareEstimateStatus = document.getElementById('fare_estimate_status');
    const routeCommitButton = document.getElementById('taxi_commit_route');
    const routeCommitInfo = document.getElementById('route_commit_info');
    const routeCommitRequested = document.getElementById('route_estimate_commit_requested');
    const routeCommittedAt = document.getElementById('route_estimate_committed_at');
    const routeCommittedBy = document.getElementById('route_estimate_committed_by');

    function money(value) {
        return '₹' + Number(value || 0).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    function pricingModeLabel(mode) {
        return {
            PER_RIDE: 'Per Ride',
            PER_KM: 'Per KM',
            PER_DAY: 'Per Day / Rental',
            PER_KM_WITH_MINIMUM: 'Per KM with Minimum',
            CUSTOM_QUOTE: 'Custom Quote'
        }[mode] || mode;
    }

    function selectedTaxiService() {
        return taxiServices[String(serviceSelect?.value || '')] || null;
    }

    function renderServicePricing() {
        if (!servicePricingBox) return;
        const service = selectedTaxiService();
        if (!service) {
            servicePricingBox.innerHTML = '<div class="pricing-empty">Select a taxi service to see its pricing rules before creating the booking.</div>';
            return;
        }

        const mode = service.pricing_mode;
        let calculation = '';
        if (mode === 'PER_RIDE') {
            calculation = 'Fixed ride fare up to the configured Included KM. If the committed route exceeds Included KM, the Extra KM Rate is charged for the additional distance. Minimum Fare is then enforced.';
        } else if (mode === 'PER_KM') {
            calculation = 'Estimated fare = base fare + road distance × per-KM rate. If a per-minute rate is configured, the Gemini ETA is also used for that time charge.';
        } else if (mode === 'PER_KM_WITH_MINIMUM') {
            calculation = 'Estimated fare = base fare + road distance × per-KM rate + configured time charge, if any. The minimum fare is then enforced.';
        } else if (mode === 'PER_DAY') {
            calculation = 'Estimated rental fare = rental days × Daily Rate. If the committed route exceeds Included KM, Extra KM Rate is charged for the additional distance.';
        } else {
            calculation = 'No automatic fare is calculated. The approved manual quote must be entered before the booking can be saved.';
        }

        servicePricingBox.innerHTML = `
            <div class="pricing-rule-head">
                <div>
                    <div class="pricing-service-name">${escapeHtml(service.name)}</div>
                    <span class="pricing-service-type">${escapeHtml(service.service_type)}</span>
                </div>
                <span class="pricing-mode-badge">${pricingModeLabel(mode)}</span>
            </div>
            <p class="pricing-intro">These are the fare rules configured by the taxi business for this service. The booking uses this service's rules; the admin should not manually invent a fare for automatic pricing modes.</p>
            <div class="pricing-rules-grid">
                <div class="pricing-rule"><span>Base Fare</span><strong>${money(service.base_fare)}</strong></div>
                <div class="pricing-rule"><span>Per KM</span><strong>${money(service.per_km)}</strong></div>
                <div class="pricing-rule"><span>Per Minute</span><strong>${money(service.per_minute)}</strong></div>
                <div class="pricing-rule"><span>Minimum Fare</span><strong>${money(service.minimum_fare)}</strong></div>
                <div class="pricing-rule"><span>Included KM</span><strong>${Number(service.included_km || 0).toFixed(2)} km</strong></div>
                <div class="pricing-rule"><span>Daily Rate</span><strong>${money(service.daily_rate)}</strong></div>
                <div class="pricing-rule"><span>Extra KM Rate</span><strong>${money(service.extra_km_rate)}</strong></div>
            </div>
            <div class="pricing-calculation-note"><strong>How this booking is calculated:</strong> ${escapeHtml(calculation)}</div>
        `;
    }

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>'"]/g, function (char) {
            return {'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#039;','"':'&quot;'}[char];
        });
    }

    function setFareStatus(message, type) {
        if (!fareEstimateStatus) return;
        fareEstimateStatus.textContent = message || '';
        fareEstimateStatus.className = 'fare-estimate-status' + (message ? ' is-visible ' + type : '');
    }

    function renderFareResult(result) {
        if (!result) return;
        if (fareQuoteInput) fareQuoteInput.style.display = result.quote_required ? '' : 'none';
        if (fareBreakdown) {
            if (result.line_items && result.line_items.length) {
                fareBreakdown.innerHTML = result.line_items.map(function (item) {
                    const displayValue = item.meta
                        ? escapeHtml(item.meta)
                        : money(item.amount);
                    return '<div class="fare-line"><span>' + escapeHtml(item.label) + '</span><strong>' + displayValue + '</strong></div>';
                }).join('');
            } else if (result.quote_required) {
                fareBreakdown.innerHTML = '<div class="fare-line"><span>Pricing method</span><strong>Manual quote</strong></div>';
            } else {
                fareBreakdown.innerHTML = '<div class="fare-line"><span>Calculation</span><strong>Route estimate required</strong></div>';
            }
        }

        if (result.quote_required) {
            fareTotalLabel.textContent = 'Approved Quote';
            fareTotalValue.textContent = fareInput && fareInput.value ? money(fareInput.value) : 'Quote required';
            fareEstimateSubtitle.textContent = result.service_name + ' · ' + result.service_type + ' · Custom Quote';
            fareEstimateNote.textContent = 'This service is not automatically priced. Enter the approved quote; the booking will store that quoted amount.';
        } else if (result.estimated_fare !== null && result.estimated_fare !== undefined) {
            fareTotalLabel.textContent = 'Total Estimated Fare';
            fareTotalValue.textContent = money(result.estimated_fare);
            fareEstimateSubtitle.textContent = result.service_name + ' · ' + result.service_type + ' · ' + result.pricing_mode_label;
            fareEstimateNote.textContent = result.requires_route
                ? 'Estimated fare based on the Gemini AI estimated road distance' + (result.eta_minutes ? ' and driving time' : '') + '. This is an estimate and may differ from the final actual charge.'
                : result.pricing_mode === 'PER_DAY'
                    ? 'Estimated rental fare based on the configured daily rate and rental duration. This is an estimate.'
                    : 'Estimated fare based on the selected taxi service pricing rules. The configured minimum fare is enforced where applicable.';
        } else {
            fareTotalLabel.textContent = 'Estimated Fare';
            fareTotalValue.textContent = '—';
            fareEstimateSubtitle.textContent = result.service_name + ' · ' + result.service_type + ' · ' + result.pricing_mode_label;
            fareEstimateNote.textContent = (result.notes || []).join(' ') || 'More information is required before the fare can be calculated.';
        }

        if (fareInput && !result.quote_required) {
            fareInput.value = '';
        }
        if (fareInput && result.quote_required) {
            fareTotalValue.textContent = fareInput.value ? money(fareInput.value) : 'Quote required';
        }
    }

    async function refreshFareEstimate() {
        const service = selectedTaxiService();
        if (!service) return;

        const payload = {
            service_id: service.id,
            trip_type: routeTripType(),
            distance_km: document.getElementById('distance_km')?.value || null,
            eta_minutes: document.getElementById('eta_minutes')?.value || null,
            return_distance_km: document.getElementById('return_distance_km')?.value || null,
            return_eta_minutes: document.getElementById('return_eta_minutes')?.value || null,
            total_distance_km: document.getElementById('total_distance_km')?.value || null,
            total_eta_minutes: document.getElementById('total_eta_minutes')?.value || null,
            scheduled_at: document.getElementById('scheduled_at')?.value || null,
            return_scheduled_at: document.getElementById('return_scheduled_at')?.value || null
        };

        try {
            const response = await fetch(basePath + '/taxi/bookings/fare-estimate', {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'Accept': 'application/json'},
                body: JSON.stringify(payload)
            });
            const data = await response.json();
            if (!response.ok || !data.success) throw new Error(data.message || 'Unable to calculate fare.');
            renderFareResult(data.data || {});
            setFareStatus('Fare calculation refreshed from the selected service rules.', 'ok');
        } catch (error) {
            setFareStatus(error.message || 'Unable to calculate fare.', 'error');
        }
    }

    function filterServicesByVendor() {
        if (!serviceSelect) return;
        const vendorId = vendorSelect?.value || '';
        let selectedStillVisible = false;
        Array.from(serviceSelect.options).forEach(function (option, index) {
            if (index === 0) return;
            const visible = !vendorId || option.dataset.vendorId === vendorId;
            option.hidden = !visible;
            if (visible && option.selected) selectedStillVisible = true;
        });
        if (!selectedStillVisible && vendorId) serviceSelect.value = '';
        renderServicePricing();
        refreshFareEstimate();
    }

    if (vendorSelect) vendorSelect.addEventListener('change', filterServicesByVendor);
    if (serviceSelect) serviceSelect.addEventListener('change', function () {
        renderServicePricing();
        refreshFareEstimate();
    });
    ['scheduled_at', 'return_scheduled_at'].forEach(function (id) {
        const input = document.getElementById(id);
        if (input) input.addEventListener('change', refreshFareEstimate);
    });

    function routeTripType() {
        const selected = document.querySelector('input[name="trip_type"]:checked');
        return selected ? selected.value : 'ONE_WAY';
    }

    function clearRouteEstimate() {
        [
            'distance_km', 'eta_minutes', 'return_distance_km',
            'return_eta_minutes', 'total_distance_km',
            'total_eta_minutes', 'route_source', 'route_calculated_at'
        ].forEach(function (id) {
            const input = document.getElementById(id);
            if (input) input.value = '';
        });
        routeResult.classList.remove('is-visible');
        routeError.classList.remove('is-visible');
        routeError.textContent = '';
        if (routeCommitRequested) routeCommitRequested.value = '0';
        if (routeCommittedAt) routeCommittedAt.value = '';
        if (routeCommittedBy) routeCommittedBy.value = '';
        if (routeCommitButton) { routeCommitButton.disabled = true; routeCommitButton.textContent = 'Commit Route Estimate'; }
        if (routeCommitInfo) routeCommitInfo.innerHTML = 'Calculated values are a preview until you explicitly commit them.';
        refreshFareEstimate();
    }

    function setHidden(id, value) {
        const input = document.getElementById(id);
        if (input) input.value = value ?? '';
    }

    function formatKm(value) {
        return Number(value).toFixed(2) + ' km';
    }

    function formatMinutes(value) {
        const minutes = Math.max(0, Math.round(Number(value)));
        const hours = Math.floor(minutes / 60);
        const mins = minutes % 60;
        if (hours > 0) return hours + ' hr ' + mins + ' min';
        return mins + ' min';
    }

    function showRouteEstimate(result, tripType, calculatedAt) {
        routeResult.classList.add('is-visible');
        routeError.classList.remove('is-visible');
        routeError.textContent = '';

        if (tripType === 'ROUND_TRIP') {
            routeOneWay.style.display = 'none';
            routeRoundTrip.style.display = 'grid';

            document.getElementById('route_outbound_distance').textContent = formatKm(result.outbound_distance_km);
            document.getElementById('route_outbound_eta').textContent = formatMinutes(result.outbound_eta_minutes);
            document.getElementById('route_return_distance').textContent = formatKm(result.return_distance_km);
            document.getElementById('route_return_eta').textContent = formatMinutes(result.return_eta_minutes);
            document.getElementById('route_total_distance').textContent = formatKm(result.total_distance_km);
            document.getElementById('route_total_eta').textContent = formatMinutes(result.total_eta_minutes);

            setHidden('distance_km', result.outbound_distance_km);
            setHidden('eta_minutes', result.outbound_eta_minutes);
            setHidden('return_distance_km', result.return_distance_km);
            setHidden('return_eta_minutes', result.return_eta_minutes);
            setHidden('total_distance_km', result.total_distance_km);
            setHidden('total_eta_minutes', result.total_eta_minutes);
        } else {
            routeOneWay.style.display = 'grid';
            routeRoundTrip.style.display = 'none';

            document.getElementById('route_distance_value').textContent = formatKm(result.distance_km);
            document.getElementById('route_eta_value').textContent = formatMinutes(result.eta_minutes);

            setHidden('distance_km', result.distance_km);
            setHidden('eta_minutes', result.eta_minutes);
            setHidden('return_distance_km', '');
            setHidden('return_eta_minutes', '');
            setHidden('total_distance_km', result.distance_km);
            setHidden('total_eta_minutes', result.eta_minutes);
        }

        setHidden('route_source', 'GEMINI');
        setHidden('route_calculated_at', calculatedAt || '');
        document.getElementById('route_calculated_at_display').textContent = calculatedAt || 'Just now';
        if (routeCommitButton) {
            routeCommitButton.disabled = false;
            if (routeCommitRequested && routeCommitRequested.value === '1' && currentBookingId > 0) {
                routeCommitButton.textContent = 'Route Estimate Committed';
                routeCommitButton.disabled = true;
                if (routeCommitInfo) routeCommitInfo.innerHTML = '<span class="route-commit-badge">Committed to database</span>';
            } else {
                routeCommitButton.textContent = currentBookingId > 0 ? 'Commit Estimate to Database' : 'Commit to Booking';
                if (routeCommitInfo) routeCommitInfo.textContent = currentBookingId > 0 ? 'Calculated values are still a preview. Commit them to persist this estimate as the accepted route for this booking.' : 'On a new booking, commit marks this estimate for saving when the booking is created.';
            }
        }
        refreshFareEstimate();
    }

    async function calculateRouteEstimate() {
        if (!routeButton) return;

        const pickupLat = document.getElementById('pickup_lat')?.value.trim();
        const pickupLng = document.getElementById('pickup_lng')?.value.trim();
        const destinationLat = document.getElementById('destination_lat')?.value.trim();
        const destinationLng = document.getElementById('destination_lng')?.value.trim();
        const pickupAddress = document.getElementById('pickup_address')?.value.trim() || '';
        const destinationAddress = document.getElementById('destination_address')?.value.trim() || '';
        const tripType = routeTripType();

        if (!pickupLat || !pickupLng || !destinationLat || !destinationLng) {
            routeError.textContent = 'Please select both pickup and destination coordinates on the map before calculating the route.';
            routeError.classList.add('is-visible');
            routeResult.classList.remove('is-visible');
            return;
        }

        routeButton.disabled = true;
        routeButton.textContent = 'Calculating...';
        routeError.classList.remove('is-visible');
        routeResult.classList.remove('is-visible');

        try {
            const response = await fetch(basePath + '/taxi/bookings/route-estimate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    pickup_lat: pickupLat,
                    pickup_lng: pickupLng,
                    destination_lat: destinationLat,
                    destination_lng: destinationLng,
                    pickup_address: pickupAddress,
                    destination_address: destinationAddress,
                    trip_type: tripType
                })
            });

            const payload = await response.json();

            if (!response.ok || !payload.success) {
                throw new Error(payload.message || 'Unable to calculate route estimate.');
            }

            showRouteEstimate(payload.data || {}, tripType, payload.route_calculated_at || '');
        } catch (error) {
            routeError.textContent = error.message || 'Unable to calculate route estimate.';
            routeError.classList.add('is-visible');
        } finally {
            routeButton.disabled = false;
            routeButton.textContent = 'Calculate Distance & ETA';
        }
    }

    if (routeButton) {
        routeButton.addEventListener('click', calculateRouteEstimate);
    }

    // Changing a coordinate invalidates a previous Gemini estimate.
    ['pickup_lat', 'pickup_lng', 'destination_lat', 'destination_lng'].forEach(function (id) {
        const input = document.getElementById(id);
        if (input) input.addEventListener('change', clearRouteEstimate);
    });

    tripRadios.forEach(function (radio) {
        radio.addEventListener('change', clearRouteEstimate);
    });

    if (routeCommitButton) {
        routeCommitButton.addEventListener('click', async function () {
            const distance = document.getElementById('distance_km')?.value;
            const eta = document.getElementById('eta_minutes')?.value;
            if (!distance || !eta) {
                routeError.textContent = 'Calculate the route estimate before committing it.';
                routeError.classList.add('is-visible');
                return;
            }

            if (currentBookingId <= 0) {
                if (routeCommitRequested) routeCommitRequested.value = '1';
                if (routeCommitInfo) routeCommitInfo.innerHTML = '<span class="route-commit-badge">Marked for commit when booking is saved</span>';
                routeCommitButton.textContent = 'Estimate Marked for Commit';
                routeCommitButton.disabled = true;
                return;
            }

            routeCommitButton.disabled = true;
            routeCommitButton.textContent = 'Committing...';
            try {
                const response = await fetch(basePath + '/taxi/bookings/' + currentBookingId + '/commit-route-estimate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        service_id: serviceSelect?.value || null,
                        trip_type: routeTripType(),
                        distance_km: document.getElementById('distance_km')?.value || null,
                        eta_minutes: document.getElementById('eta_minutes')?.value || null,
                        return_distance_km: document.getElementById('return_distance_km')?.value || null,
                        return_eta_minutes: document.getElementById('return_eta_minutes')?.value || null,
                        total_distance_km: document.getElementById('total_distance_km')?.value || null,
                        total_eta_minutes: document.getElementById('total_eta_minutes')?.value || null,
                        scheduled_at: document.getElementById('scheduled_at')?.value || null,
                        return_scheduled_at: document.getElementById('return_scheduled_at')?.value || null,
                        route_source: document.getElementById('route_source')?.value || 'GEMINI',
                        route_calculated_at: document.getElementById('route_calculated_at')?.value || null,
                        fare: fareInput?.value || null
                    })
                });
                const payload = await response.json();
                if (!response.ok || !payload.success) throw new Error(payload.message || 'Unable to commit route estimate.');

                // The commit operation persists the route AND recalculates the fare
                // from the selected Taxi Service pricing rules on the server.
                if (payload.route) {
                    setHidden('distance_km', payload.route.distance_km);
                    setHidden('eta_minutes', payload.route.eta_minutes);
                    setHidden('return_distance_km', payload.route.return_distance_km);
                    setHidden('return_eta_minutes', payload.route.return_eta_minutes);
                    setHidden('total_distance_km', payload.route.total_distance_km);
                    setHidden('total_eta_minutes', payload.route.total_eta_minutes);
                    setHidden('route_source', payload.route.route_source || 'GEMINI');
                    setHidden('route_calculated_at', payload.route.route_calculated_at || '');
                }
                if (payload.fare_result) {
                    renderFareResult(payload.fare_result);
                }
                if (routeCommitRequested) routeCommitRequested.value = '1';
                if (routeCommittedAt) routeCommittedAt.value = payload.committed_at || '';
                if (routeCommittedBy) routeCommittedBy.value = payload.committed_by || '';
                if (routeCommitInfo) routeCommitInfo.innerHTML = '<span class="route-commit-badge">Route + fare committed to database at ' + escapeHtml(payload.committed_at || 'now') + '</span>';
                routeCommitButton.textContent = 'Route Estimate Committed';
                setFareStatus('Route estimate committed. Fare recalculated from the selected taxi service pricing rules.', 'ok');
            } catch (error) {
                routeCommitButton.disabled = false;
                routeCommitButton.textContent = 'Commit Estimate to Database';
                routeError.textContent = error.message || 'Unable to commit route estimate.';
                routeError.classList.add('is-visible');
            }
        });
    }

    renderServicePricing();
    filterServicesByVendor();
    if (fareInput) { fareInput.addEventListener('input', function () { if (fareTotalValue && fareInput.value) fareTotalValue.textContent = money(fareInput.value); }); }

    // Restore a saved estimate when opening the edit form.
    (function restoreSavedRoute() {
        const tripType = routeTripType();
        const distance = document.getElementById('distance_km')?.value;
        const eta = document.getElementById('eta_minutes')?.value;
        const calculatedAt = document.getElementById('route_calculated_at')?.value;

        if (tripType === 'ROUND_TRIP') {
            const outboundDistance = distance;
            const outboundEta = eta;
            const returnDistance = document.getElementById('return_distance_km')?.value;
            const returnEta = document.getElementById('return_eta_minutes')?.value;
            const totalDistance = document.getElementById('total_distance_km')?.value;
            const totalEta = document.getElementById('total_eta_minutes')?.value;

            if (outboundDistance && outboundEta && returnDistance && returnEta && totalDistance && totalEta) {
                showRouteEstimate({
                    outbound_distance_km: outboundDistance,
                    outbound_eta_minutes: outboundEta,
                    return_distance_km: returnDistance,
                    return_eta_minutes: returnEta,
                    total_distance_km: totalDistance,
                    total_eta_minutes: totalEta
                }, tripType, calculatedAt);
            }
        } else if (distance && eta) {
            showRouteEstimate({
                distance_km: distance,
                eta_minutes: eta
            }, tripType, calculatedAt);
        }
    })();
})();
</script>
