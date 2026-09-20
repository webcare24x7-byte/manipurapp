<?php declare(strict_types=1); $data = $record ?? $old ?? []; ?>

<style>
    .taxi-media-box{grid-column:1/-1;border:1px solid #e5e7eb;border-radius:14px;padding:18px;background:#fafafa}
    .taxi-media-preview{display:flex;gap:16px;align-items:center;flex-wrap:wrap;margin-top:12px}
    .taxi-media-preview img{width:180px;height:110px;object-fit:cover;border-radius:12px;border:1px solid #ddd;background:#fff}
    .taxi-media-help{margin:6px 0 0;color:#6b7280;font-size:13px}
</style>

<div class="card"><div class="form-grid">
<div class="form-group"><label for="vendor_id">Taxi Business <span class="required">*</span></label><select id="vendor_id" name="vendor_id" required><option value="">Select taxi business...</option><?php foreach (($vendors ?? []) as $vendor): ?><option value="<?= (int) $vendor['id'] ?>" <?= (string) ($data['vendor_id'] ?? '') === (string) $vendor['id'] ? 'selected' : '' ?>><?= htmlspecialchars((string) $vendor['business_name'], ENT_QUOTES, 'UTF-8') ?></option><?php endforeach; ?></select></div>

<div class="form-group"><label for="registration_no">Registration Number <span class="required">*</span></label><input type="text" id="registration_no" name="registration_no" value="<?= htmlspecialchars((string) ($data['registration_no'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required></div>

<div class="form-group"><label for="vehicle_type">Vehicle Type <span class="required">*</span></label><input type="text" id="vehicle_type" name="vehicle_type" value="<?= htmlspecialchars((string) ($data['vehicle_type'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required></div>

<div class="form-group"><label for="make">Make</label><input type="text" id="make" name="make" value="<?= htmlspecialchars((string) ($data['make'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></div>

<div class="form-group"><label for="model">Model</label><input type="text" id="model" name="model" value="<?= htmlspecialchars((string) ($data['model'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></div>

<div class="form-group"><label for="model_year">Model Year</label><input type="number" id="model_year" name="model_year" value="<?= htmlspecialchars((string) ($data['model_year'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></div>

<div class="form-group"><label for="color">Color</label><input type="text" id="color" name="color" value="<?= htmlspecialchars((string) ($data['color'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></div>

<div class="form-group"><label for="seating_capacity">Seating Capacity <span class="required">*</span></label><input type="number" id="seating_capacity" name="seating_capacity" value="<?= htmlspecialchars((string) ($data['seating_capacity'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" min="1" required></div>

<div class="form-group"><label for="status">Status</label><select id="status" name="status"><option value="">Select...</option><option value="Active" <?= ($data['status'] ?? '') === 'Active' ? 'selected' : '' ?>>Active</option><option value="Inactive" <?= ($data['status'] ?? '') === 'Inactive' ? 'selected' : '' ?>>Inactive</option><option value="Maintenance" <?= ($data['status'] ?? '') === 'Maintenance' ? 'selected' : '' ?>>Maintenance</option></select></div>

<div class="taxi-media-box">
    <strong>Vehicle Photo</strong>
    <p class="taxi-media-help">Upload a clear vehicle photo for the Member PWA. JPG, PNG or WebP. Maximum 5 MB.</p>
    <input type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/webp">
    <?php if (!empty($data['photo_path'])): ?>
        <div class="taxi-media-preview">
            <img src="<?= htmlspecialchars(config('app.base_path') . '/' . ltrim((string) $data['photo_path'], '/'), ENT_QUOTES, 'UTF-8') ?>" alt="Vehicle photo">
            <label><input type="checkbox" name="remove_photo" value="1"> Remove current photo</label>
        </div>
    <?php endif; ?>
</div>
</div></div>
