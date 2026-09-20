<?php

declare(strict_types=1);

$base = config('app.base_path');
$days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
$by = [];
foreach (($hours ?? []) as $h) {
    $by[(int) $h['day_of_week']] = $h;
}
$menu = $menu ?? [];

$esc = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$imageUrl = static function (string $path) use ($base): string {
    return $base . '/' . ltrim($path, '/');
};
?>

<style>
.restaurant-detail-hero { position: relative; overflow: hidden; padding: 0; }
.restaurant-detail-cover { height: 220px; background: linear-gradient(135deg, #eef2ff, #f8fafc); overflow: hidden; }
.restaurant-detail-cover img { width: 100%; height: 100%; object-fit: cover; display: block; }
.restaurant-detail-head { display: flex; gap: 20px; align-items: flex-end; padding: 0 24px 24px; margin-top: -46px; position: relative; }
.restaurant-detail-logo { width: 96px; height: 96px; flex: 0 0 96px; border-radius: 16px; border: 4px solid #fff; background: #fff; box-shadow: 0 8px 24px rgba(15,23,42,.12); overflow: hidden; }
.restaurant-detail-logo img { width: 100%; height: 100%; object-fit: cover; display: block; }
.restaurant-detail-logo-empty { width: 100%; height: 100%; display: grid; place-items: center; color: #64748b; font-weight: 700; font-size: 28px; background: #f1f5f9; }
.restaurant-detail-title { padding-bottom: 3px; min-width: 0; }
.restaurant-detail-title h1 { margin: 0 0 5px; }
.restaurant-detail-title p { margin: 0; color: #64748b; }
.restaurant-detail-grid { display: grid; grid-template-columns: repeat(4, minmax(0,1fr)); gap: 14px; }
.restaurant-detail-stat { padding: 18px; border: 1px solid #e5e7eb; border-radius: 12px; background: #fff; }
.restaurant-detail-stat strong { display: block; font-size: 12px; color: #64748b; text-transform: uppercase; letter-spacing: .04em; }
.restaurant-detail-stat span { display: block; margin-top: 6px; font-size: 25px; font-weight: 700; color: #111827; }
.restaurant-detail-section-title { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 16px; }
.restaurant-detail-section-title h2 { margin: 0; }
.restaurant-detail-kv { display: grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap: 0; }
.restaurant-detail-kv > div { padding: 14px 0; border-bottom: 1px solid #eef2f7; }
.restaurant-detail-kv > div:nth-last-child(-n+2) { border-bottom: 0; }
.restaurant-detail-kv strong { display: block; font-size: 12px; color: #64748b; margin-bottom: 4px; }
.restaurant-detail-kv p { margin: 0; color: #111827; }
.restaurant-detail-hours td, .restaurant-detail-hours th { padding: 12px 14px; }
.restaurant-detail-hours .today { font-weight: 700; background: #f8fafc; }
.restaurant-detail-images { display: grid; grid-template-columns: 180px minmax(0,1fr); gap: 20px; }
.restaurant-detail-image { margin: 0; }
.restaurant-detail-image img { display: block; width: 100%; height: 180px; object-fit: cover; border-radius: 12px; border: 1px solid #e5e7eb; background: #f8fafc; }
.restaurant-detail-cover-image img { height: 180px; }
.restaurant-detail-links { display: flex; flex-wrap: wrap; gap: 10px; }
@media (max-width: 900px) { .restaurant-detail-grid { grid-template-columns: repeat(2,minmax(0,1fr)); } .restaurant-detail-images { grid-template-columns: 1fr; } }
@media (max-width: 640px) { .restaurant-detail-head { align-items: flex-start; flex-direction: column; margin-top: -36px; } .restaurant-detail-grid, .restaurant-detail-kv { grid-template-columns: 1fr; } .restaurant-detail-kv > div:nth-last-child(-n+2) { border-bottom: 1px solid #eef2f7; } }
</style>

<div class="page-header">
    <div>
        <h1><?= $esc($record['business_name']) ?></h1>
        <p><?= $esc($record['cuisine_type'] ?? 'Restaurant') ?> · <?= $esc($record['city'] ?? 'Manipur') ?></p>
    </div>
    <div class="page-actions">
        <a class="btn btn-primary" href="<?= $base ?>/restaurant/restaurants/<?= (int) $record['id'] ?>/edit">Edit Restaurant</a>
        <a class="btn btn-secondary" href="<?= $base ?>/restaurant/restaurants">Back</a>
    </div>
</div>

<div class="card restaurant-detail-hero">
    <div class="restaurant-detail-cover">
        <?php if (!empty($record['cover_image_path'])): ?>
            <img src="<?= $esc($imageUrl((string) $record['cover_image_path'])) ?>" alt="<?= $esc($record['business_name']) ?> cover image">
        <?php endif; ?>
    </div>
    <div class="restaurant-detail-head">
        <div class="restaurant-detail-logo">
            <?php if (!empty($record['logo_path'])): ?>
                <img src="<?= $esc($imageUrl((string) $record['logo_path'])) ?>" alt="<?= $esc($record['business_name']) ?> logo">
            <?php else: ?>
                <div class="restaurant-detail-logo-empty">R</div>
            <?php endif; ?>
        </div>
        <div class="restaurant-detail-title">
            <h1><?= $esc($record['business_name']) ?></h1>
            <p><?= $esc($record['address'] ?? '—') ?> · <?= $esc($record['district'] ?? '—') ?>, <?= $esc($record['state'] ?? 'Manipur') ?></p>
        </div>
    </div>
</div>

<div class="card">
    <div class="restaurant-detail-grid">
        <?php foreach ([
            ['Categories', 'categories'],
            ['Menu Items', 'items'],
            ['Variants', 'variants'],
            ['Modifier Groups', 'modifier_groups'],
        ] as [$label, $key]): ?>
            <div class="restaurant-detail-stat"><strong><?= $label ?></strong><span><?= (int) ($menu[$key] ?? 0) ?></span></div>
        <?php endforeach; ?>
    </div>
</div>

<div class="card">
    <div class="restaurant-detail-section-title"><h2>Business Details</h2><a class="link-button" href="<?= $base ?>/restaurant/restaurants/<?= (int) $record['id'] ?>/edit">Edit</a></div>
    <div class="restaurant-detail-kv">
        <div><strong>Business Name</strong><p><?= $esc($record['business_name']) ?></p></div>
        <div><strong>Status</strong><p><?= $esc($record['status']) ?></p></div>
        <div><strong>Phone</strong><p><?= $esc($record['phone'] ?? '—') ?></p></div>
        <div><strong>Email</strong><p><?= $esc($record['email'] ?? '—') ?></p></div>
        <div><strong>Address</strong><p><?= $esc($record['address'] ?? '—') ?></p></div>
        <div><strong>City / Town</strong><p><?= $esc($record['city'] ?? '—') ?></p></div>
        <div><strong>District</strong><p><?= $esc($record['district'] ?? '—') ?></p></div>
        <div><strong>State</strong><p><?= $esc($record['state'] ?? '—') ?></p></div>
        <div><strong>Postal Code</strong><p><?= $esc($record['postal_code'] ?? '—') ?></p></div>
        <div><strong>Cuisine</strong><p><?= $esc($record['cuisine_type'] ?? '—') ?></p></div>
        <div><strong>Estimated Prep Time</strong><p><?= (int) ($record['estimated_prep_minutes'] ?? 0) ?> minutes</p></div>
        <div><strong>Accepting Orders</strong><p><?= !empty($record['accepting_orders']) ? 'Yes' : 'No' ?></p></div>
    </div>
</div>

<div class="card">
    <div class="restaurant-detail-section-title">
        <h2>Map Location</h2>
        <a class="link-button" href="<?= $base ?>/restaurant/restaurants/<?= (int) $record['id'] ?>/edit">Edit</a>
    </div>
    <?php if ($record['latitude'] !== null && $record['longitude'] !== null && $record['latitude'] !== '' && $record['longitude'] !== ''): ?>
        <div class="restaurant-detail-kv">
            <div><strong>Latitude</strong><p><?= $esc($record['latitude']) ?></p></div>
            <div><strong>Longitude</strong><p><?= $esc($record['longitude']) ?></p></div>
        </div>
        <p style="margin:16px 0 0;">
            <a class="btn btn-secondary" href="https://www.google.com/maps/search/?api=1&amp;query=<?= rawurlencode((string)$record['latitude'].','.(string)$record['longitude']) ?>" target="_blank" rel="noopener noreferrer">Open in Google Maps</a>
        </p>
    <?php else: ?>
        <div class="empty-state">Coordinates are not configured. Add them so the Member PWA can support nearby restaurant discovery.</div>
    <?php endif; ?>
</div>

<div class="card">
    <div class="restaurant-detail-section-title"><h2>Ordering & Delivery</h2></div>
    <div class="restaurant-detail-kv">
        <div><strong>Minimum Order</strong><p>₹<?= number_format((float) ($record['minimum_order_amount'] ?? 0), 2) ?></p></div>
        <div><strong>Delivery Fee</strong><p>₹<?= number_format((float) ($record['delivery_fee'] ?? 0), 2) ?></p></div>
        <div><strong>Free Delivery Above</strong><p><?= (float) ($record['free_delivery_above'] ?? 0) > 0 ? '₹' . number_format((float) $record['free_delivery_above'], 2) : 'Not configured' ?></p></div>
        <div><strong>Delivery</strong><p><?= !empty($record['delivery_available']) ? 'Available' : 'Unavailable' ?></p></div>
        <div><strong>Pickup</strong><p><?= !empty($record['pickup_available']) ? 'Available' : 'Unavailable' ?></p></div>
    </div>
</div>

<div class="card">
    <div class="restaurant-detail-section-title"><h2>Restaurant Description</h2></div>
    <p><?= !empty($record['description']) ? nl2br($esc($record['description'])) : 'No description added.' ?></p>
</div>

<div class="card">
    <div class="restaurant-detail-section-title"><h2>Restaurant Images</h2><a class="link-button" href="<?= $base ?>/restaurant/restaurants/<?= (int) $record['id'] ?>/edit">Manage Images</a></div>
    <?php if (!empty($record['logo_path']) || !empty($record['cover_image_path'])): ?>
        <div class="restaurant-detail-images">
            <?php if (!empty($record['logo_path'])): ?><figure class="restaurant-detail-image"><strong>Logo</strong><img src="<?= $esc($imageUrl((string) $record['logo_path'])) ?>" alt="Restaurant logo"></figure><?php endif; ?>
            <?php if (!empty($record['cover_image_path'])): ?><figure class="restaurant-detail-image restaurant-detail-cover-image"><strong>Cover Image</strong><img src="<?= $esc($imageUrl((string) $record['cover_image_path'])) ?>" alt="Restaurant cover image"></figure><?php endif; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">No restaurant images uploaded.</div>
    <?php endif; ?>
</div>

<div class="card">
    <div class="restaurant-detail-section-title"><h2>Operating Hours</h2><a class="link-button" href="<?= $base ?>/restaurant/restaurants/<?= (int) $record['id'] ?>/edit">Edit Hours</a></div>
    <div class="table-responsive">
        <table class="table restaurant-detail-hours">
            <thead><tr><th>Day</th><th>Hours</th></tr></thead>
            <tbody>
            <?php foreach ($days as $i => $day): $h = $by[$i] ?? []; ?>
                <tr>
                    <td><?= $day ?></td>
                    <td>
                        <?php if (!empty($h['is_closed'])): ?>Closed
                        <?php elseif (!empty($h['opens_at']) && !empty($h['closes_at'])): ?><?= $esc(substr((string) $h['opens_at'], 0, 5)) ?> – <?= $esc(substr((string) $h['closes_at'], 0, 5)) ?>
                        <?php else: ?>Not configured<?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="restaurant-detail-section-title"><h2>Restaurant Management</h2></div>
    <div class="restaurant-detail-links">
        <a class="btn btn-primary" href="<?= $base ?>/restaurant/orders?restaurant_id=<?= (int) $record['id'] ?>">Orders</a>
        <a class="btn btn-primary" href="<?= $base ?>/restaurant/menu?restaurant_id=<?= (int) $record['id'] ?>">Menu Management</a>
        <a class="btn btn-primary" href="<?= $base ?>/restaurant/coupons">Coupons</a>
        <a class="btn btn-secondary" href="<?= $base ?>/restaurant/categories">Categories</a>
        <a class="btn btn-secondary" href="<?= $base ?>/restaurant/items">Menu Items</a>
        <a class="btn btn-secondary" href="<?= $base ?>/restaurant/variants">Variants</a>
        <a class="btn btn-secondary" href="<?= $base ?>/restaurant/modifiers">Modifier Groups</a>
    </div>
</div>

<div class="card">
    <div class="restaurant-detail-section-title"><h2>Danger Zone</h2></div>
    <p>Deleting the restaurant moves the restaurant business and profile to Trash. It does not permanently remove the record.</p>
    <form method="post" action="<?= $base ?>/restaurant/restaurants/<?= (int) $record['id'] ?>/delete" onsubmit="return confirm('Move this restaurant to Trash? You can restore it later.');">
        <button class="btn btn-secondary" type="submit">Move Restaurant to Trash</button>
    </form>
</div>
