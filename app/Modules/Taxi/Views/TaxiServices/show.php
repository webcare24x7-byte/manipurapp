<?php declare(strict_types=1); ?>

<div class="page-header"><div><h1><?= htmlspecialchars((string) ($record['name'] ?? 'Taxi Service'), ENT_QUOTES, 'UTF-8') ?></h1><p>Taxi Service details.</p></div><div class="page-actions"><a href="<?= config('app.base_path') ?>/taxi/services" class="btn btn-secondary">Back</a><a href="<?= config('app.base_path') ?>/taxi/services/<?= (int) $record['id'] ?>/edit" class="btn btn-primary">Edit</a></div></div>
<div class="card"><table class="table"><tbody>
<tr><th>Taxi Vendor</th><td><?= htmlspecialchars((string) ($record['vendor_name'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></td></tr>
<tr><th>Service Name</th><td><?= htmlspecialchars((string) ($record['name'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></td></tr>
<tr><th>Service Code</th><td><?= htmlspecialchars((string) ($record['code'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></td></tr>
<tr><th>Service Type</th><td><?= htmlspecialchars((string) ($record['service_type'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></td></tr>
<tr><th>Pricing Mode</th><td><?= htmlspecialchars((string) ($record['pricing_mode'] ?? 'PER_RIDE'), ENT_QUOTES, 'UTF-8') ?></td></tr>
<tr><th>Base Fare</th><td>₹<?= number_format((float) ($record['base_fare'] ?? 0), 2) ?></td></tr>
<tr><th>Per KM</th><td>₹<?= number_format((float) ($record['per_km'] ?? 0), 2) ?></td></tr>
<tr><th>Per Minute</th><td>₹<?= number_format((float) ($record['per_minute'] ?? 0), 2) ?></td></tr>
<tr><th>Minimum Fare</th><td>₹<?= number_format((float) ($record['minimum_fare'] ?? 0), 2) ?></td></tr>
<tr><th>Included KM</th><td><?= number_format((float) ($record['included_km'] ?? 0), 2) ?> km</td></tr>
<tr><th>Daily Rate</th><td>₹<?= number_format((float) ($record['daily_rate'] ?? 0), 2) ?></td></tr>
<tr><th>Extra KM Rate</th><td>₹<?= number_format((float) ($record['extra_km_rate'] ?? 0), 2) ?></td></tr>
<tr><th>Status</th><td><?= htmlspecialchars((string) ($record['status'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></td></tr>
<tr><th>Description</th><td><?= nl2br(htmlspecialchars((string) ($record['description'] ?? '—'), ENT_QUOTES, 'UTF-8')) ?></td></tr>
</tbody></table></div>
