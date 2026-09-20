<?php
declare(strict_types=1);
$base = config('app.base_path');
$o = $order;
$esc = static fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$next = [
    'PENDING' => ['ACCEPTED','REJECTED','CANCELLED'],
    'ACCEPTED' => ['PREPARING','CANCELLED'],
    'PREPARING' => ['READY','CANCELLED'],
    'READY' => $o['order_type'] === 'DELIVERY' ? ['ASSIGNED','COMPLETED'] : ['COMPLETED','CANCELLED'],
    'ASSIGNED' => ['OUT_FOR_DELIVERY'],
    'OUT_FOR_DELIVERY' => ['DELIVERED'],
    'DELIVERED' => ['COMPLETED'],
    'COMPLETED' => [], 'CANCELLED' => [], 'REJECTED' => [],
][$o['status']] ?? [];
$history = is_array($o['status_history'] ?? null) ? $o['status_history'] : [];
?>
<div class="page-header">
    <div><h1>Order <?= $esc($o['order_no']) ?></h1><p><?= $esc($o['restaurant_name']) ?> · <?= $esc($o['order_type']) ?></p></div>
    <div class="page-actions"><a class="btn btn-secondary" href="<?= $base ?>/restaurant/orders">Back to Orders</a></div>
</div>

<div class="order-grid">
    <div class="card">
        <h2>Order Status</h2>
        <div class="big-status"><?= str_replace('_',' ',$esc($o['status'])) ?></div>
        <?php if ($next): ?>
        <form method="post" action="<?= $base ?>/restaurant/orders/<?= (int)$o['id'] ?>/status" class="status-form">
            <label>Move to
                <select name="status"><?php foreach ($next as $s): ?><option value="<?= $s ?>"><?= str_replace('_',' ',$s) ?></option><?php endforeach; ?></select>
            </label>
            <label>Reason <small>(required for reject/cancel)</small><textarea name="reason" rows="3"></textarea></label>
            <button class="btn btn-primary" type="submit">Update Order</button>
        </form>
        <?php endif; ?>
    </div>

    <div class="card">
        <h2>Customer</h2>
        <p><strong><?= $esc($o['customer_name']) ?></strong></p>
        <p><?= $esc($o['customer_phone'] ?? '—') ?></p>
        <p><?= $esc($o['customer_email'] ?? '—') ?></p>
        <hr>
        <?php if ($o['order_type'] === 'DELIVERY'): ?>
            <p><strong>Delivery Address</strong><br>
            <?= nl2br($esc($o['delivery_address'] ?? '')) ?><br>
            <?= $esc($o['delivery_city'] ?? '') ?>, <?= $esc($o['delivery_district'] ?? '') ?>, <?= $esc($o['delivery_state'] ?? '') ?> <?= $esc($o['delivery_postal_code'] ?? '') ?></p>
        <?php else: ?>
            <div class="pickup-message">
                <strong>Customer Pickup</strong>
                <span>The customer has chosen to pick up this order from the restaurant. No delivery address is required.</span>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <h2>Items</h2>
    <?php foreach ($o['items'] as $item): ?>
    <div class="order-line">
        <div>
            <strong><?= $esc($item['item_name']) ?></strong>
            <?php if ($item['variant_name']): ?><div>Variant: <?= $esc($item['variant_name']) ?></div><?php endif; ?>
            <?php if ((float)($item['discount_amount'] ?? 0) > 0): ?><div class="modifier">Item discount: <?= $item['discount_type'] === 'FLAT' ? '₹'.number_format((float)$item['discount_value'],2) : number_format((float)$item['discount_value'],2).'%' ?> off</div><?php endif; ?>
            <?php foreach ($item['modifiers'] as $m): ?><div class="modifier"><?= $esc($m['group_name']) ?>: <?= $esc($m['option_name']) ?><?php if ((float)$m['price_adjustment'] != 0): ?> (+₹<?= number_format((float)$m['price_adjustment'],2) ?>)<?php endif; ?></div><?php endforeach; ?>
        </div>
        <div><?= (int)$item['quantity'] ?> × ₹<?= number_format((float)$item['unit_price'],2) ?><br><strong>₹<?= number_format((float)$item['line_total'],2) ?></strong></div>
    </div>
    <?php endforeach; ?>
    <div class="totals">
        <div>Original subtotal <b>₹<?= number_format((float)($o['gross_subtotal'] ?? $o['subtotal']),2) ?></b></div>
        <div>Item discounts <b>− ₹<?= number_format((float)($o['item_discount'] ?? 0),2) ?></b></div>
        <div>Subtotal <b>₹<?= number_format((float)$o['subtotal'],2) ?></b></div>
        <div>Coupon<?php if (!empty($o['coupon_code'])): ?> (<?= $esc($o['coupon_code']) ?>)<?php endif; ?> <b>− ₹<?= number_format((float)($o['coupon_discount'] ?? 0),2) ?></b></div>
        <div>Delivery fee <b>₹<?= number_format((float)$o['delivery_fee'],2) ?></b></div>
        <div class="final-total">Total <strong>₹<?= number_format((float)$o['total'],2) ?></strong></div>
    </div>
</div>

<div class="card">
    <div class="section-heading"><div><h2>Status History</h2><p>Every order status transition is recorded chronologically.</p></div></div>
    <?php if ($history): ?>
    <div class="timeline">
        <?php foreach ($history as $index => $h): ?>
        <div class="timeline-row">
            <div class="timeline-dot"></div>
            <div class="timeline-body">
                <div class="timeline-top">
                    <strong><?= str_replace('_',' ',$esc($h['to_status'])) ?></strong>
                    <time><?= $esc($h['created_at']) ?></time>
                </div>
                <div class="timeline-meta">
                    <?php if (!empty($h['from_status'])): ?><?= $esc(str_replace('_',' ',$h['from_status'])) ?> → <?php endif; ?><?= $esc(str_replace('_',' ',$h['to_status'])) ?>
                    <?php if (!empty($h['changed_by_name'])): ?> · by <?= $esc(trim((string)$h['changed_by_name'])) ?><?php endif; ?>
                </div>
                <?php if (!empty($h['note'])): ?><div class="timeline-note"><?= nl2br($esc($h['note'])) ?></div><?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?><div class="empty-state">No status history is available for this order.</div><?php endif; ?>
</div>

<?php if (!empty($o['customer_note'])): ?><div class="card"><h2>Customer Note</h2><p><?= nl2br($esc($o['customer_note'])) ?></p></div><?php endif; ?>

<style>
.order-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}.big-status{font-size:26px;font-weight:800;margin:10px 0 20px}.status-form{display:grid;gap:12px}.status-form label{display:grid;gap:6px;font-weight:700}.status-form small{font-weight:400;color:#64748b}.status-form select,.status-form textarea{padding:9px}.order-line{display:flex;justify-content:space-between;gap:20px;padding:14px 0;border-bottom:1px solid #eee}.modifier{font-size:13px;color:#64748b}.totals{margin-top:16px;display:grid;gap:8px}.totals div{display:flex;justify-content:space-between;gap:16px}.totals .final-total{border-top:1px solid #e5e7eb;padding-top:12px;margin-top:4px;font-size:16px}.totals .final-total strong{font-size:21px}.pickup-message{padding:14px;border:1px solid #dbeafe;background:#eff6ff;border-radius:10px;color:#1e40af}.pickup-message strong,.pickup-message span{display:block}.pickup-message span{font-size:13px;margin-top:4px}.section-heading{display:flex;justify-content:space-between}.section-heading h2{margin-bottom:4px}.section-heading p{margin:0;color:#64748b;font-size:13px}.timeline{position:relative;margin-top:18px}.timeline:before{content:"";position:absolute;left:9px;top:10px;bottom:10px;width:2px;background:#e5e7eb}.timeline-row{display:grid;grid-template-columns:20px 1fr;gap:12px;position:relative;padding-bottom:18px}.timeline-row:last-child{padding-bottom:0}.timeline-dot{width:20px;height:20px;border-radius:50%;background:#fff;border:4px solid #111827;z-index:1}.timeline-body{border:1px solid #e5e7eb;border-radius:10px;padding:12px}.timeline-top{display:flex;justify-content:space-between;gap:12px}.timeline-top strong{font-size:14px}.timeline-top time{font-size:12px;color:#64748b}.timeline-meta{font-size:12px;color:#64748b;margin-top:3px}.timeline-note{font-size:13px;margin-top:7px;color:#334155}@media(max-width:700px){.order-grid{grid-template-columns:1fr}.timeline-top{display:grid;gap:3px}.order-line{gap:10px}}
</style>
