<?php
declare(strict_types=1);
$base = config('app.base_path');
$restaurants = $restaurants ?? [];
$menu = $menu ?? null;
$selectedId = (int) ($selected_restaurant_id ?? 0);

$esc = static fn($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
?>

<style>
.menu-page { display:flex; flex-direction:column; gap:18px; }
.menu-toolbar { display:flex; gap:12px; align-items:end; justify-content:space-between; flex-wrap:wrap; }
.menu-select { min-width:280px; }
.menu-actions { display:flex; gap:8px; flex-wrap:wrap; }
.menu-stats { display:grid; grid-template-columns:repeat(6,minmax(0,1fr)); gap:12px; }
.menu-stat { padding:16px; border:1px solid #e5e7eb; border-radius:10px; background:#fff; }
.menu-stat strong { display:block; font-size:1.45rem; margin-top:5px; }
.menu-stat small { color:#6b7280; }
.menu-category { overflow:hidden; }
.menu-category-header { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:16px 18px; border-bottom:1px solid #e5e7eb; }
.menu-category-header h2 { margin:0; font-size:1.1rem; }
.menu-category-header p { margin:4px 0 0; color:#6b7280; font-size:.9rem; }
.menu-item { padding:16px 18px; border-bottom:1px solid #f0f1f3; }
.menu-item:last-child { border-bottom:0; }
.menu-item-main { display:flex; align-items:flex-start; justify-content:space-between; gap:16px; }
.menu-item-info { min-width:0; flex:1; }
.menu-item-title { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
.menu-item-title h3 { margin:0; font-size:1rem; }
.menu-price { font-weight:700; white-space:nowrap; }
.menu-meta { color:#6b7280; font-size:.86rem; margin-top:5px; }
.menu-description { color:#6b7280; margin:7px 0 0; font-size:.9rem; }
.menu-item-actions { display:flex; align-items:center; gap:7px; flex-wrap:wrap; justify-content:flex-end; }
.menu-pill { display:inline-flex; align-items:center; padding:3px 8px; border-radius:999px; font-size:.75rem; border:1px solid #d1d5db; }
.menu-pill.veg { border-color:#86efac; }
.menu-pill.sold { border-color:#fca5a5; }
.menu-subsection { margin-top:12px; padding-left:14px; border-left:2px solid #e5e7eb; }
.menu-subsection-title { font-size:.78rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em; color:#6b7280; margin-bottom:6px; }
.menu-row-list { display:flex; flex-direction:column; gap:5px; }
.menu-row { display:flex; align-items:center; justify-content:space-between; gap:10px; font-size:.88rem; }
.menu-row-name { min-width:0; }
.menu-row-muted { color:#6b7280; }
.menu-modifier { margin-top:4px; }
.menu-empty { padding:20px; color:#6b7280; }
.menu-uncategorized { border:1px dashed #d1d5db; }
.menu-library { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:12px; }
.menu-library-card { border:1px solid #e5e7eb; border-radius:10px; padding:14px; }
.menu-library-card h3 { margin:0 0 5px; font-size:.98rem; }
.menu-library-card p { margin:0; color:#6b7280; font-size:.86rem; }
@media (max-width:1000px) { .menu-stats { grid-template-columns:repeat(3,minmax(0,1fr)); } .menu-library { grid-template-columns:1fr 1fr; } }
@media (max-width:650px) { .menu-stats { grid-template-columns:repeat(2,minmax(0,1fr)); } .menu-library { grid-template-columns:1fr; } .menu-select { min-width:100%; } .menu-item-main { flex-direction:column; } .menu-item-actions { justify-content:flex-start; } }
</style>

<div class="page-header">
    <div>
        <h1>Menu Management</h1>
        <p>Manage the complete customer-facing menu from one place.</p>
    </div>
</div>

<div class="menu-page">
    <div class="card">
        <form method="get" action="<?= $base ?>/restaurant/menu" class="menu-toolbar">
            <div class="form-group menu-select">
                <label for="restaurant_id">Restaurant</label>
                <select id="restaurant_id" name="restaurant_id" onchange="this.form.submit()">
                    <?php if (empty($restaurants)): ?>
                        <option value="">No active restaurants</option>
                    <?php else: ?>
                        <?php foreach ($restaurants as $restaurant): ?>
                            <option value="<?= (int) $restaurant['id'] ?>" <?= $selectedId === (int) $restaurant['id'] ? 'selected' : '' ?>>
                                <?= $esc($restaurant['business_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <?php if ($menu): ?>
                <div class="menu-actions"><a class="btn btn-secondary" href="<?= $base ?>/restaurant/orders?restaurant_id=<?= (int) $selectedId ?>">Orders</a><a class="btn btn-secondary" href="<?= $base ?>/restaurant/coupons">Coupons</a>
                    <a class="btn btn-secondary" href="<?= $base ?>/restaurant/restaurants/<?= (int) $menu['restaurant']['id'] ?>">Restaurant Details</a>
                    <a class="btn btn-secondary" href="<?= $base ?>/restaurant/categories?restaurant_id=<?= (int) $menu['restaurant']['id'] ?>">Categories</a>
                    <a class="btn btn-primary" href="<?= $base ?>/restaurant/items/create?restaurant_id=<?= (int) $menu['restaurant']['id'] ?>">+ Add Food Item</a>
                </div>
            <?php endif; ?>
        </form>
    </div>

    <?php if (!$menu): ?>
        <div class="card menu-empty">
            <?= empty($restaurants) ? 'Create a restaurant first to start building its menu.' : 'Select a restaurant to manage its menu.' ?>
        </div>
    <?php else: ?>
        <?php $s = $menu['summary']; ?>
        <div class="menu-stats">
            <div class="menu-stat"><small>Categories</small><strong><?= (int) $s['categories'] ?></strong></div>
            <div class="menu-stat"><small>Menu Items</small><strong><?= (int) $s['items'] ?></strong></div>
            <div class="menu-stat"><small>Available</small><strong><?= (int) $s['available_items'] ?></strong></div>
            <div class="menu-stat"><small>Sold Out</small><strong><?= (int) $s['sold_out_items'] ?></strong></div>
            <div class="menu-stat"><small>Variants</small><strong><?= (int) $s['variants'] ?></strong></div>
            <div class="menu-stat"><small>Modifier Groups</small><strong><?= (int) $s['modifier_groups'] ?></strong></div>
        </div>

        <?php foreach ($menu['categories'] as $category): ?>
            <div class="card menu-category">
                <div class="menu-category-header">
                    <div>
                        <h2><?= $esc($category['name']) ?></h2>
                        <?php if (!empty($category['description'])): ?><p><?= $esc($category['description']) ?></p><?php endif; ?>
                    </div>
                    <div class="menu-actions">
                        <span class="menu-pill"><?= (int) $category['item_count'] ?> item<?= (int) $category['item_count'] === 1 ? '' : 's' ?></span>
                        <a class="link-button" href="<?= $base ?>/restaurant/categories/<?= (int) $category['id'] ?>/edit">Edit</a>
                    </div>
                </div>

                <?php if (empty($category['items'])): ?>
                    <div class="menu-empty">No active items in this category.</div>
                <?php else: ?>
                    <?php foreach ($category['items'] as $item): ?>
                        <div class="menu-item">
                            <div class="menu-item-main">
                                <div class="menu-item-info">
                                    <div class="menu-item-title">
                                        <h3><?= $esc($item['name']) ?></h3>
                                        <?php if (!empty($item['is_veg'])): ?><span class="menu-pill veg">VEG</span><?php endif; ?>
                                        <?php if (empty($item['is_available'])): ?><span class="menu-pill sold">SOLD OUT</span><?php endif; ?>
                                    </div>
                                    <div class="menu-meta">Base price: <strong>₹<?= number_format((float) $item['price'], 2) ?></strong><?php if ((float)($item['discount_value'] ?? 0) > 0): ?> <span class="menu-pill discount"><?= $item['discount_type'] === 'FLAT' ? '₹'.number_format((float)$item['discount_value'],2).' off' : number_format((float)$item['discount_value'],2).'% off' ?></span><?php endif; ?></div>
                                    <?php if (!empty($item['description'])): ?><div class="menu-description"><?= nl2br($esc($item['description'])) ?></div><?php endif; ?>

                                    <?php if (!empty($item['variants'])): ?>
                                        <div class="menu-subsection">
                                            <div class="menu-subsection-title">Variants</div>
                                            <div class="menu-row-list">
                                                <?php foreach ($item['variants'] as $variant): ?>
                                                    <div class="menu-row">
                                                        <span class="menu-row-name">
                                                            <?= $esc($variant['name']) ?>
                                                            <span class="menu-row-muted"> · ₹<?= number_format((float) $variant['price'], 2) ?></span>
                                                        </span>
                                                        <span>
                                                            <?= !empty($variant['is_available']) ? 'Available' : 'Unavailable' ?>
                                                            <a class="link-button" href="<?= $base ?>/restaurant/variants/<?= (int) $variant['id'] ?>/edit">Edit</a>
                                                        </span>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($item['modifier_groups'])): ?>
                                        <div class="menu-subsection">
                                            <div class="menu-subsection-title">Modifier Groups</div>
                                            <div class="menu-row-list">
                                                <?php foreach ($item['modifier_groups'] as $group): ?>
                                                    <div class="menu-row menu-modifier">
                                                        <span class="menu-row-name">
                                                            <?= $esc($group['name']) ?>
                                                            <span class="menu-row-muted"> · <?= $esc($group['selection_type']) ?><?= !empty($group['is_required']) ? ' · Required' : '' ?></span>
                                                        </span>
                                                        <a class="link-button" href="<?= $base ?>/restaurant/modifiers/<?= (int) $group['modifier_group_id'] ?>">Manage</a>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="menu-item-actions">
                                    <form method="post" action="<?= $base ?>/restaurant/menu/<?= (int) $menu['restaurant']['id'] ?>/items/<?= (int) $item['id'] ?>/availability">
                                        <input type="hidden" name="is_available" value="<?= !empty($item['is_available']) ? '0' : '1' ?>">
                                        <button class="btn btn-secondary" type="submit">
                                            <?= !empty($item['is_available']) ? 'Mark Sold Out' : 'Mark Available' ?>
                                        </button>
                                    </form>
                                    <a class="btn btn-secondary" href="<?= $base ?>/restaurant/items/<?= (int) $item['id'] ?>">View</a>
                                    <a class="btn btn-primary" href="<?= $base ?>/restaurant/items/<?= (int) $item['id'] ?>/edit">Edit</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <?php if (!empty($menu['uncategorized'])): ?>
            <div class="card menu-category menu-uncategorized">
                <div class="menu-category-header"><div><h2>Uncategorized</h2><p>Active items that are not assigned to a menu category.</p></div></div>
                <?php foreach ($menu['uncategorized'] as $item): ?>
                    <div class="menu-item">
                        <div class="menu-item-main">
                            <div class="menu-item-info">
                                <div class="menu-item-title"><h3><?= $esc($item['name']) ?></h3><?php if (!empty($item['is_veg'])): ?><span class="menu-pill veg">VEG</span><?php endif; ?><?php if (empty($item['is_available'])): ?><span class="menu-pill sold">SOLD OUT</span><?php endif; ?></div>
                                <div class="menu-meta">₹<?= number_format((float) $item['price'], 2) ?></div>
                            </div>
                            <div class="menu-item-actions">
                                <a class="btn btn-primary" href="<?= $base ?>/restaurant/items/<?= (int) $item['id'] ?>/edit">Edit</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="page-header" style="margin-bottom:14px;">
                <div><h2>Modifier Library</h2><p>Reusable modifier groups can be assigned to multiple menu items.</p></div>
                <div class="page-actions"><a class="btn btn-primary" href="<?= $base ?>/restaurant/modifiers/create">+ Add Modifier Group</a></div>
            </div>
            <?php if (empty($menu['modifier_groups'])): ?>
                <div class="menu-empty">No active modifier groups for this restaurant.</div>
            <?php else: ?>
                <div class="menu-library">
                    <?php foreach ($menu['modifier_groups'] as $group): ?>
                        <div class="menu-library-card">
                            <h3><?= $esc($group['name']) ?></h3>
                            <p><?= (int) $group['option_count'] ?> option<?= (int) $group['option_count'] === 1 ? '' : 's' ?> · <?= (int) $group['item_count'] ?> item<?= (int) $group['item_count'] === 1 ? '' : 's' ?></p>
                            <div style="margin-top:9px;"><a class="link-button" href="<?= $base ?>/restaurant/modifiers/<?= (int) $group['id'] ?>">Manage Group</a></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
