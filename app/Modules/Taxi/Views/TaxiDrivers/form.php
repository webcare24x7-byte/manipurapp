<?php declare(strict_types=1); $data = $record ?? $old ?? []; ?>

<style>
    .taxi-media-box{grid-column:1/-1;border:1px solid #e5e7eb;border-radius:14px;padding:18px;background:#fafafa}
    .taxi-media-preview{display:flex;gap:16px;align-items:center;flex-wrap:wrap;margin-top:12px}
    .taxi-media-preview img{width:120px;height:120px;object-fit:cover;border-radius:50%;border:1px solid #ddd;background:#fff}
    .taxi-media-help{margin:6px 0 0;color:#6b7280;font-size:13px}
</style>

<div class="card"><div class="form-grid">
<div class="form-group"><label for="vendor_id">Taxi Business <span class="required">*</span></label><select id="vendor_id" name="vendor_id" required><option value="">Select taxi business...</option><?php foreach (($vendors ?? []) as $vendor): ?><option value="<?= (int) $vendor['id'] ?>" <?= (string) ($data['vendor_id'] ?? '') === (string) $vendor['id'] ? 'selected' : '' ?>><?= htmlspecialchars((string) $vendor['business_name'], ENT_QUOTES, 'UTF-8') ?></option><?php endforeach; ?></select></div>

<div class="form-group"><label for="user_id">Linked Staff User</label><select id="user_id" name="user_id"><option value="">Not linked</option><?php foreach (($users ?? []) as $user): ?><option value="<?= (int) $user['id'] ?>" <?= (string) ($data['user_id'] ?? '') === (string) $user['id'] ? 'selected' : '' ?>><?= htmlspecialchars((string) $user['name'], ENT_QUOTES, 'UTF-8') ?><?= !empty($user['role_name']) ? ' — ' . htmlspecialchars((string) $user['role_name'], ENT_QUOTES, 'UTF-8') : '' ?></option><?php endforeach; ?></select></div>

<div class="form-group"><label for="name">Driver Name <span class="required">*</span></label><input type="text" id="name" name="name" value="<?= htmlspecialchars((string) ($data['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required></div>

<div class="form-group"><label for="phone">Phone <span class="required">*</span></label><input type="text" id="phone" name="phone" value="<?= htmlspecialchars((string) ($data['phone'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required></div>

<div class="form-group"><label for="license_no">Driving License Number</label><input type="text" id="license_no" name="license_no" value="<?= htmlspecialchars((string) ($data['license_no'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></div>

<div class="form-group"><label for="license_expiry">License Expiry</label><input type="date" id="license_expiry" name="license_expiry" value="<?= htmlspecialchars((string) ($data['license_expiry'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></div>

<div class="form-group"><label for="status">Status</label><select id="status" name="status"><option value="">Select...</option><option value="Active" <?= ($data['status'] ?? '') === 'Active' ? 'selected' : '' ?>>Active</option><option value="Inactive" <?= ($data['status'] ?? '') === 'Inactive' ? 'selected' : '' ?>>Inactive</option><option value="Suspended" <?= ($data['status'] ?? '') === 'Suspended' ? 'selected' : '' ?>>Suspended</option></select></div>

<div class="form-group"><label for="availability">Availability</label><select id="availability" name="availability"><option value="">Select...</option><option value="Offline" <?= ($data['availability'] ?? '') === 'Offline' ? 'selected' : '' ?>>Offline</option><option value="Available" <?= ($data['availability'] ?? '') === 'Available' ? 'selected' : '' ?>>Available</option><option value="On Trip" <?= ($data['availability'] ?? '') === 'On Trip' ? 'selected' : '' ?>>On Trip</option><option value="Unavailable" <?= ($data['availability'] ?? '') === 'Unavailable' ? 'selected' : '' ?>>Unavailable</option></select></div>

<div class="taxi-media-box">
    <strong>Driver Photo</strong>
    <p class="taxi-media-help">Upload a clear profile photo for customer trust in the Member PWA. JPG, PNG or WebP. Maximum 5 MB.</p>
    <input type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/webp">
    <?php if (!empty($data['photo_path'])): ?>
        <div class="taxi-media-preview">
            <img src="<?= htmlspecialchars(config('app.base_path') . '/' . ltrim((string) $data['photo_path'], '/'), ENT_QUOTES, 'UTF-8') ?>" alt="Driver photo">
            <label><input type="checkbox" name="remove_photo" value="1"> Remove current photo</label>
        </div>
    <?php endif; ?>
</div>
</div></div>
