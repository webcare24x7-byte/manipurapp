<?php declare(strict_types=1); $data = $record ?? $old ?? []; ?>

<div class="card"><div class="form-grid">
<div class="form-group"><label for="business_name">Business Name <span class="required">*</span></label><input type="text" id="business_name" name="business_name" value="<?= htmlspecialchars((string) ($data['business_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required></div>

<div class="form-group"><label for="legal_name">Legal Name</label><input type="text" id="legal_name" name="legal_name" value="<?= htmlspecialchars((string) ($data['legal_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></div>

<div class="form-group"><label for="phone">Phone</label><input type="text" id="phone" name="phone" value="<?= htmlspecialchars((string) ($data['phone'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></div>

<div class="form-group"><label for="email">Email</label><input type="email" id="email" name="email" value="<?= htmlspecialchars((string) ($data['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></div>

<div class="form-group"><label for="address">Address</label><input type="text" id="address" name="address" value="<?= htmlspecialchars((string) ($data['address'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></div>

<div class="form-group"><label for="city">City / Town</label><input type="text" id="city" name="city" value="<?= htmlspecialchars((string) ($data['city'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></div>

<div class="form-group"><label for="district">District</label><input type="text" id="district" name="district" value="<?= htmlspecialchars((string) ($data['district'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></div>

<div class="form-group"><label for="state">State</label><input type="text" id="state" name="state" value="<?= htmlspecialchars((string) ($data['state'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></div>

<div class="form-group"><label for="postal_code">Postal Code</label><input type="text" id="postal_code" name="postal_code" value="<?= htmlspecialchars((string) ($data['postal_code'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></div>

<div class="form-group full-width"><label for="description">Description</label><textarea id="description" name="description" rows="5"><?= htmlspecialchars((string) ($data['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea></div>

<div class="form-group"><label for="status">Status</label><select id="status" name="status"><option value="">Select...</option><option value="Active" <?= ($data['status'] ?? '') === 'Active' ? 'selected' : '' ?>>Active</option>
<option value="Inactive" <?= ($data['status'] ?? '') === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
<option value="Suspended" <?= ($data['status'] ?? '') === 'Suspended' ? 'selected' : '' ?>>Suspended</option></select></div>
</div></div>
