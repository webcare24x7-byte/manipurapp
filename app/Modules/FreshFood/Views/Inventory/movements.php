<?php
declare(strict_types=1);

$base = config('app.base_path');
?>

<div class="page-header">
    <div>
        <h1>Inventory History</h1>
        <p><?= htmlspecialchars((string) $product['business_name'], ENT_QUOTES, 'UTF-8') ?> — <?= htmlspecialchars((string) $product['name'], ENT_QUOTES, 'UTF-8') ?></p>
    </div>

    <div class="page-actions">
        <a class="btn btn-primary" href="<?= $base ?>/fresh-food/inventory/adjust?product_id=<?= (int) $product['id'] ?>">Adjust Stock</a>
        <a class="btn btn-secondary" href="<?= $base ?>/fresh-food/inventory">Back</a>
    </div>
</div>

<div class="card">
    <p><strong>Current Stock:</strong> <?= number_format((float) $product['current_quantity'], 3) ?> <?= htmlspecialchars((string) $product['unit'], ENT_QUOTES, 'UTF-8') ?></p>
</div>

<div class="card">
    <table class="table">
        <thead>
        <tr>
            <th>Date</th>
            <th>Type</th>
            <th>Change</th>
            <th>Before</th>
            <th>After</th>
            <th>Reference</th>
            <th>Created By</th>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($movements)): ?>
            <tr><td colspan="7"><div class="empty-state">No inventory movements found.</div></td></tr>
        <?php else: ?>
            <?php foreach ($movements as $movement): ?>
                <tr>
                    <td><?= htmlspecialchars((string) $movement['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string) $movement['movement_type'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= number_format((float) $movement['quantity_change'], 3) ?></td>
                    <td><?= number_format((float) $movement['quantity_before'], 3) ?></td>
                    <td><strong><?= number_format((float) $movement['quantity_after'], 3) ?></strong></td>
                    <td><?= htmlspecialchars((string) ($movement['reference'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string) ($movement['created_by_name'] ?? 'System'), ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
                <?php if (!empty($movement['notes'])): ?>
                    <tr><td colspan="7"><small><?= htmlspecialchars((string) $movement['notes'], ENT_QUOTES, 'UTF-8') ?></small></td></tr>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>
