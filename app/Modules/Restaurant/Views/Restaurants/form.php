<?php declare(strict_types=1); $data = $record ?? $old ?? []; $locations = $locations ?? ['state'=>['Manipur'],'districts'=>[],'cities'=>[]]; $base=config('app.base_path'); ?>
<style>
.restaurant-media-box{grid-column:1/-1;border:1px solid #e5e7eb;border-radius:14px;padding:18px;background:#fafafa}.restaurant-media-preview{display:flex;gap:18px;align-items:flex-start;flex-wrap:wrap;margin-top:14px}.restaurant-media-preview figure{margin:0}.restaurant-media-preview img{display:block;width:180px;height:120px;object-fit:cover;border-radius:12px;border:1px solid #ddd;background:#fff}.restaurant-media-preview figcaption{font-size:12px;color:#6b7280;margin-top:6px}.restaurant-media-help{margin:6px 0 12px;color:#6b7280;font-size:13px}
.restaurant-location-card{border:1px solid #dbe4ee}.restaurant-location-help{margin:0 0 16px;color:#64748b;font-size:13px}.restaurant-location-note{margin:8px 0 0;color:#64748b;font-size:12px}.restaurant-location-status{margin-left:10px;color:#475569;font-size:13px}.restaurant-location-card small{display:block;margin-top:5px;color:#64748b}
</style>
<div class="card"><div class="form-grid">
<div class="form-group"><label for="business_name">Restaurant / Business Name <span class="required">*</span></label><input type="text" id="business_name" name="business_name" value="<?= htmlspecialchars((string)($data['business_name']??''),ENT_QUOTES,'UTF-8') ?>" required></div>
<div class="form-group"><label for="phone">Phone</label><input type="text" id="phone" name="phone" value="<?= htmlspecialchars((string)($data['phone']??''),ENT_QUOTES,'UTF-8') ?>"></div>
<div class="form-group"><label for="email">Email</label><input type="email" id="email" name="email" value="<?= htmlspecialchars((string)($data['email']??''),ENT_QUOTES,'UTF-8') ?>"></div>
<div class="form-group full-width"><label for="address">Address</label><input type="text" id="address" name="address" value="<?= htmlspecialchars((string)($data['address']??''),ENT_QUOTES,'UTF-8') ?>"></div>
<div class="form-group"><label for="city">City / Town <span class="required">*</span></label><select id="city" name="city" required><option value="">Select city / town...</option><?php foreach($locations['cities'] as $city): ?><option value="<?= htmlspecialchars($city,ENT_QUOTES,'UTF-8') ?>" <?= (string)($data['city']??'')===$city?'selected':'' ?>><?= htmlspecialchars($city,ENT_QUOTES,'UTF-8') ?></option><?php endforeach; ?></select></div>
<div class="form-group"><label for="district">District <span class="required">*</span></label><select id="district" name="district" required><option value="">Select district...</option><?php foreach($locations['districts'] as $district): ?><option value="<?= htmlspecialchars($district,ENT_QUOTES,'UTF-8') ?>" <?= (string)($data['district']??'')===$district?'selected':'' ?>><?= htmlspecialchars($district,ENT_QUOTES,'UTF-8') ?></option><?php endforeach; ?></select></div>
<div class="form-group"><label for="state">State <span class="required">*</span></label><select id="state" name="state" required><?php foreach($locations['state'] as $state): ?><option value="<?= htmlspecialchars($state,ENT_QUOTES,'UTF-8') ?>" <?= (string)($data['state']??'Manipur')===$state?'selected':'' ?>><?= htmlspecialchars($state,ENT_QUOTES,'UTF-8') ?></option><?php endforeach; ?></select></div>
<div class="form-group"><label for="postal_code">Postal Code</label><input type="text" id="postal_code" name="postal_code" value="<?= htmlspecialchars((string)($data['postal_code']??''),ENT_QUOTES,'UTF-8') ?>"></div>
</div></div>

<div class="card restaurant-location-card">
    <h2>Restaurant Location</h2>
    <p class="restaurant-location-help">Add the restaurant's exact map coordinates. These will be used by the Member PWA for nearby restaurant discovery and distance-based results.</p>
    <div class="form-grid">
        <div class="form-group">
            <label for="latitude">Latitude</label>
            <input type="number" step="0.0000001" min="-90" max="90" id="latitude" name="latitude" value="<?= htmlspecialchars((string)($data['latitude']??''),ENT_QUOTES,'UTF-8') ?>" placeholder="e.g. 24.8170">
            <small>Range: -90 to 90</small>
        </div>
        <div class="form-group">
            <label for="longitude">Longitude</label>
            <input type="number" step="0.0000001" min="-180" max="180" id="longitude" name="longitude" value="<?= htmlspecialchars((string)($data['longitude']??''),ENT_QUOTES,'UTF-8') ?>" placeholder="e.g. 93.9368">
            <small>Range: -180 to 180</small>
        </div>
        <div class="form-group full-width">
            <button type="button" class="btn btn-secondary" id="use-current-location">Use Current Location</button>
            <span id="location-status" class="restaurant-location-status"></span>
            <p class="restaurant-location-note">You can enter coordinates manually or allow this browser to provide the current location. HTTPS may be required by the browser.</p>
        </div>
        <div class="form-group full-width" id="map-link-wrap" style="display:none;">
            <a class="link-button" id="map-link" href="#" target="_blank" rel="noopener noreferrer">Open Location in Maps</a>
        </div>
    </div>
</div>

<div class="card"><h2>Restaurant Information</h2><div class="form-grid">
<div class="form-group"><label for="cuisine_type">Cuisine Type</label><input type="text" id="cuisine_type" name="cuisine_type" value="<?= htmlspecialchars((string)($data['cuisine_type']??''),ENT_QUOTES,'UTF-8') ?>" placeholder="e.g. Manipuri, Indian, Chinese"></div>
<div class="form-group"><label for="minimum_order_amount">Minimum Order</label><input type="number" step="0.01" min="0" id="minimum_order_amount" name="minimum_order_amount" value="<?= htmlspecialchars((string)($data['minimum_order_amount']??'0.00'),ENT_QUOTES,'UTF-8') ?>"></div>
<div class="form-group"><label for="delivery_fee">Delivery Fee</label><input type="number" step="0.01" min="0" id="delivery_fee" name="delivery_fee" value="<?= htmlspecialchars((string)($data['delivery_fee']??'0.00'),ENT_QUOTES,'UTF-8') ?>"></div>
<div class="form-group"><label for="free_delivery_above">Free Delivery Above</label><input type="number" step="0.01" min="0" id="free_delivery_above" name="free_delivery_above" value="<?= htmlspecialchars((string)($data['free_delivery_above']??'0.00'),ENT_QUOTES,'UTF-8') ?>"></div>
<div class="form-group"><label for="estimated_prep_minutes">Estimated Prep (minutes)</label><input type="number" min="1" id="estimated_prep_minutes" name="estimated_prep_minutes" value="<?= htmlspecialchars((string)($data['estimated_prep_minutes']??30),ENT_QUOTES,'UTF-8') ?>"></div>
<div class="form-group"><label for="status">Status</label><select id="status" name="status"><option value="Active" <?= ($data['status']??'Active')==='Active'?'selected':'' ?>>Active</option><option value="Inactive" <?= ($data['status']??'')==='Inactive'?'selected':'' ?>>Inactive</option><option value="Suspended" <?= ($data['status']??'')==='Suspended'?'selected':'' ?>>Suspended</option></select></div>
<div class="form-group full-width"><label for="description">Description</label><textarea id="description" name="description" rows="5"><?= htmlspecialchars((string)($data['description']??''),ENT_QUOTES,'UTF-8') ?></textarea></div>
<div class="restaurant-media-box"><strong>Restaurant Logo</strong><p class="restaurant-media-help">Upload JPG, PNG or WebP. Maximum 5 MB.</p><input type="file" id="logo" name="logo" accept="image/jpeg,image/png,image/webp"><?php if(!empty($data['logo_path'])): ?><div class="restaurant-media-preview"><figure><img src="<?= htmlspecialchars($base.'/'.ltrim((string)$data['logo_path'],'/'),ENT_QUOTES,'UTF-8') ?>" alt="Restaurant logo"><figcaption>Current logo</figcaption></figure><label><input type="checkbox" name="remove_logo" value="1"> Remove current logo</label></div><?php endif; ?></div>
<div class="restaurant-media-box"><strong>Cover Image</strong><p class="restaurant-media-help">Upload JPG, PNG or WebP. Maximum 5 MB.</p><input type="file" id="cover_image" name="cover_image" accept="image/jpeg,image/png,image/webp"><?php if(!empty($data['cover_image_path'])): ?><div class="restaurant-media-preview"><figure><img src="<?= htmlspecialchars($base.'/'.ltrim((string)$data['cover_image_path'],'/'),ENT_QUOTES,'UTF-8') ?>" alt="Restaurant cover image"><figcaption>Current cover image</figcaption></figure><label><input type="checkbox" name="remove_cover_image" value="1"> Remove current cover image</label></div><?php endif; ?></div>
<div class="form-group full-width"><label><input type="checkbox" name="delivery_available" value="1" <?= !isset($data['delivery_available'])||!empty($data['delivery_available'])?'checked':'' ?>> Delivery available</label> <label><input type="checkbox" name="pickup_available" value="1" <?= !isset($data['pickup_available'])||!empty($data['pickup_available'])?'checked':'' ?>> Pickup available</label> <label><input type="checkbox" name="accepting_orders" value="1" <?= !isset($data['accepting_orders'])||!empty($data['accepting_orders'])?'checked':'' ?>> Accepting orders</label></div>
</div></div>

<script>
(function () {
    const lat = document.getElementById('latitude');
    const lng = document.getElementById('longitude');
    const button = document.getElementById('use-current-location');
    const status = document.getElementById('location-status');
    const mapWrap = document.getElementById('map-link-wrap');
    const mapLink = document.getElementById('map-link');

    function updateMapLink() {
        const latitude = parseFloat(lat.value);
        const longitude = parseFloat(lng.value);
        if (Number.isFinite(latitude) && Number.isFinite(longitude)) {
            mapLink.href = 'https://www.google.com/maps/search/?api=1&query=' +
                encodeURIComponent(latitude + ',' + longitude);
            mapWrap.style.display = '';
        } else {
            mapWrap.style.display = 'none';
        }
    }

    lat.addEventListener('input', updateMapLink);
    lng.addEventListener('input', updateMapLink);

    button.addEventListener('click', function () {
        if (!navigator.geolocation) {
            status.textContent = 'Geolocation is not supported by this browser.';
            return;
        }

        status.textContent = 'Getting location…';
        button.disabled = true;

        navigator.geolocation.getCurrentPosition(
            function (position) {
                lat.value = position.coords.latitude.toFixed(7);
                lng.value = position.coords.longitude.toFixed(7);
                updateMapLink();
                status.textContent = 'Location captured.';
                button.disabled = false;
            },
            function (error) {
                const messages = {
                    1: 'Location permission was denied.',
                    2: 'Location is unavailable.',
                    3: 'Location request timed out.'
                };
                status.textContent = messages[error.code] || 'Unable to get location.';
                button.disabled = false;
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    });

    updateMapLink();
})();
</script>
