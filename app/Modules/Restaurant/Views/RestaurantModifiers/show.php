<?php declare(strict_types=1); $base = config('app.base_path'); ?>

<div class="page-header">
    <div>
        <h1><?= htmlspecialchars((string) $record['name'], ENT_QUOTES, 'UTF-8') ?></h1>
        <p><?= htmlspecialchars((string) $record['restaurant_name'], ENT_QUOTES, 'UTF-8') ?></p>
    </div>
    <div class="page-actions">
        <a class="btn btn-primary" href="<?= $base ?>/restaurant/modifiers/<?= (int) $record['id'] ?>/edit">Edit Group</a>
        <a class="btn btn-secondary" href="<?= $base ?>/restaurant/modifiers">Back</a>
    </div>
</div>

<div class="card">
    <h2>Modifier Group</h2>
    <?php if (!empty($record['description'])): ?>
        <p><?= htmlspecialchars((string) $record['description'], ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>
    <p>
        <strong><?= htmlspecialchars((string) $record['selection_type'], ENT_QUOTES, 'UTF-8') ?></strong>
        · min <?= (int) $record['min_selections'] ?>
        · max <?= $record['max_selections'] === null ? 'no limit' : (int) $record['max_selections'] ?>
        · <?= !empty($record['is_required']) ? 'Required' : 'Optional' ?>
    </p>
</div>

<div class="card">
    <div class="page-header">
        <div>
            <h2>Attached Menu Items</h2>
            <p>Customers will see this modifier group when ordering these items.</p>
        </div>
    </div>

    <?php if (empty($attached_items)): ?>
        <div class="empty-state">No menu items are attached to this modifier group yet.</div>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Category</th>
                    <th>Base Price</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($attached_items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars((string) $item['name'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string) ($item['category_name'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></td>
                    <td>₹<?= number_format((float) $item['price'], 2) ?></td>
                    <td>
                        <form method="post" action="<?= $base ?>/restaurant/modifiers/<?= (int) $record['id'] ?>/items/<?= (int) $item['id'] ?>/delete" style="display:inline">
                            <button class="link-button" type="submit">Detach</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<div class="card">
    <h2>Attach Menu Item</h2>
    <p>Select a menu item from <strong><?= htmlspecialchars((string) $record['restaurant_name'], ENT_QUOTES, 'UTF-8') ?></strong>.</p>

    <?php if (empty($available_items)): ?>
        <div class="empty-state">All available menu items are already attached to this modifier group, or this restaurant has no menu items yet.</div>
    <?php else: ?>
        <form method="post" action="<?= $base ?>/restaurant/modifiers/<?= (int) $record['id'] ?>/items">
            <div class="form-grid">
                <div class="form-group full-width">
                    <label for="item_id">Menu Item <span class="required">*</span></label>
                    <select id="item_id" name="item_id" required>
                        <option value="">Select menu item...</option>
                        <?php foreach ($available_items as $item): ?>
                            <option value="<?= (int) $item['id'] ?>">
                                <?= htmlspecialchars((string) $item['name'], ENT_QUOTES, 'UTF-8') ?>
                                <?php if (!empty($item['category_name'])): ?>
                                    — <?= htmlspecialchars((string) $item['category_name'], ENT_QUOTES, 'UTF-8') ?>
                                <?php endif; ?>
                                — ₹<?= number_format((float) $item['price'], 2) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="page-actions">
                <button class="btn btn-primary" type="submit">Attach Item</button>
            </div>
        </form>
    <?php endif; ?>
</div>

<div class="card">
    <h2>Options</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Price Adjustment</th>
                <th>Available</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($options)): ?>
            <tr><td colspan="4"><div class="empty-state">No options yet.</div></td></tr>
        <?php else: ?>
            <?php foreach ($options as $o): ?>
                <tr>
                    <td><?= htmlspecialchars((string) $o['name'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= ((float) $o['price_adjustment'] >= 0 ? '+' : '') ?>₹<?= number_format((float) $o['price_adjustment'], 2) ?></td>
                    <td><?= !empty($o['is_available']) ? 'Yes' : 'No' ?></td>
                    <td>
                        <form method="post" action="<?= $base ?>/restaurant/modifiers/<?= (int) $record['id'] ?>/options/<?= (int) $o['id'] ?>/delete" style="display:inline">
                            <button class="link-button" type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>


<div class="card">
    <h2>Deleted Options</h2>
    <p>Deleted options remain available here so an administrator can restore them.</p>
    <?php if (empty($deleted_options)): ?>
        <div class="empty-state">No deleted options.</div>
    <?php else: ?>
        <table class="table"><thead><tr><th>Name</th><th>Price Adjustment</th><th>Deleted At</th><th>Action</th></tr></thead><tbody>
        <?php foreach ($deleted_options as $o): ?>
            <tr><td><?= htmlspecialchars((string)$o['name'], ENT_QUOTES, 'UTF-8') ?></td><td>+₹<?= number_format((float)$o['price_adjustment'],2) ?></td><td><?= htmlspecialchars((string)($o['deleted_at']??'—'), ENT_QUOTES, 'UTF-8') ?></td><td><form method="post" action="<?= $base ?>/restaurant/modifiers/<?= (int)$record['id'] ?>/options/<?= (int)$o['id'] ?>/restore"><button class="btn btn-secondary" type="submit">Restore</button></form></td></tr>
        <?php endforeach; ?>
        </tbody></table>
    <?php endif; ?>
</div>
<div class="card">
    <h2>Add Option</h2>
    <form method="post" action="<?= $base ?>/restaurant/modifiers/<?= (int) $record['id'] ?>/options">
        <div class="form-grid">
            <div class="form-group">
                <label>Name</label>
                <input name="name" required placeholder="Extra cheese">
            </div>
            <div class="form-group">
                <label>Price Adjustment</label>
                <input type="number" step="0.01" name="price_adjustment" value="0.00">
            </div>
            <div class="form-group">
                <label>Sort Order</label>
                <input type="number" name="sort_order" value="0">
            </div>
            <div class="form-group">
                <label><input type="checkbox" name="is_available" value="1" checked> Available</label>
            </div>
        </div>
        <div class="page-actions">
            <button class="btn btn-primary" type="submit">Add Option</button>
        </div>
    </form>
</div>
