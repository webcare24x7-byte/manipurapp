<?php
declare(strict_types=1);

$base = config('app.base_path');
?>

<div class="page-header">
    <div>
        <h1>Fresh Food Inventory</h1>
        <p>Monitor current stock across all Fresh Food businesses.</p>
    </div>

    <div class="page-actions">
        <a class="btn btn-primary" href="<?= $base ?>/fresh-food/inventory/adjust">Adjust Stock</a>
    </div>
</div>

<div class="card">
    <table class="table">
        <thead>
        <tr>
            <th>Business</th>
            <th>Product</th>
            <th>Category</th>
            <th>Unit</th>
            <th>Current Stock</th>
            <th>Availability</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($inventory)): ?>
            <tr>
                <td colspan="7">
                    <div class="empty-state">No inventory records yet. Add stock to a product to create its inventory record.</div>
                </td>
            </tr>
        <?php else: ?>
            <?php foreach ($inventory as $row): ?>
                <tr>
                    <td><?= htmlspecialchars((string) $row['business_name'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><strong><?= htmlspecialchars((string) $row['product_name'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                    <td><?= htmlspecialchars((string) ($row['category_name'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string) $row['unit'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><strong><?= number_format((float) $row['current_quantity'], 3) ?></strong></td>
                    <td><?= !empty($row['is_available']) ? 'Available' : 'Unavailable' ?></td>
                    <td>
                        <a class="link-button" href="<?= $base ?>/fresh-food/inventory/adjust?product_id=<?= (int) $row['product_id'] ?>">Adjust</a>
                        <a class="link-button" href="<?= $base ?>/fresh-food/inventory/<?= (int) $row['product_id'] ?>/movements">History</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>
