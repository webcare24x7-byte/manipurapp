<?php
declare(strict_types=1);

$base = config('app.base_path');
$r = $old ?? [];
$selectedId = (int) ($r['product_id'] ?? ($selectedProduct['id'] ?? 0));
?>

<div class="page-header">
    <div>
        <h1>Adjust Fresh Food Inventory</h1>
        <p>Add opening stock, purchases, returns, sales, waste, damage or manual adjustments.</p>
    </div>

    <div class="page-actions">
        <a class="btn btn-secondary" href="<?= $base ?>/fresh-food/inventory">Back to Inventory</a>
    </div>
</div>

<?php if (!empty($error)): ?>
    <div class="card error-box"><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<form method="post" action="<?= $base ?>/fresh-food/inventory/adjust">
    <div class="card">
        <div class="form-grid">
            <div class="form-group full-width">
                <label for="inventory-product">Product *</label>
                <select id="inventory-product" name="product_id" required>
                    <option value="">Select product</option>
                    <?php foreach (($products ?? []) as $product): ?>
                        <option
                            value="<?= (int) $product['id'] ?>"
                            data-unit="<?= htmlspecialchars((string) $product['unit'], ENT_QUOTES, 'UTF-8') ?>"
                            data-stock="<?= htmlspecialchars((string) $product['current_quantity'], ENT_QUOTES, 'UTF-8') ?>"
                            <?= $selectedId === (int) $product['id'] ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars((string) $product['business_name'], ENT_QUOTES, 'UTF-8') ?> —
                            <?= htmlspecialchars((string) $product['name'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Current Stock</label>
                <input id="current-stock" type="text" readonly value="<?= $selectedProduct ? number_format((float) $selectedProduct['current_quantity'], 3) . ' ' . $selectedProduct['unit'] : '—' ?>">
            </div>

            <div class="form-group">
                <label for="movement-type">Movement Type *</label>
                <select id="movement-type" name="movement_type" required>
                    <?php foreach (['OPENING','PURCHASE','RETURN','SALE','ADJUSTMENT','WASTE','DAMAGE'] as $type): ?>
                        <option value="<?= $type ?>" <?= (($r['movement_type'] ?? 'OPENING') === $type) ? 'selected' : '' ?>><?= $type ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="quantity-change">Quantity Change *</label>
                <input id="quantity-change" type="number" name="quantity_change" step="0.001" required value="<?= htmlspecialchars((string) ($r['quantity_change'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                <small id="quantity-help">Use a positive quantity for stock coming in.</small>
            </div>

            <div class="form-group">
                <label for="reference">Reference</label>
                <input id="reference" name="reference" maxlength="200" value="<?= htmlspecialchars((string) ($r['reference'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="Invoice, purchase ref, etc.">
            </div>

            <div class="form-group full-width">
                <label for="notes">Notes</label>
                <textarea id="notes" name="notes" rows="3" placeholder="Optional inventory note"><?= htmlspecialchars((string) ($r['notes'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
        </div>

        <div class="page-actions">
            <button class="btn btn-primary" type="submit">Update Inventory</button>
            <a class="btn btn-secondary" href="<?= $base ?>/fresh-food/inventory">Cancel</a>
        </div>
    </div>
</form>

<style>
.form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}
.full-width{grid-column:1/-1}
.form-group label{display:block;font-weight:600;margin-bottom:7px}
.form-group input,.form-group select,.form-group textarea{width:100%;box-sizing:border-box}
.form-group small{display:block;margin-top:6px;opacity:.7}
.error-box{margin-bottom:16px}
@media(max-width:700px){.form-grid{grid-template-columns:1fr}.full-width{grid-column:auto}}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const product = document.getElementById('inventory-product');
    const current = document.getElementById('current-stock');
    const type = document.getElementById('movement-type');
    const quantity = document.getElementById('quantity-change');
    const help = document.getElementById('quantity-help');

    function updateProduct() {
        const option = product.options[product.selectedIndex];
        if (!option || !option.value) {
            current.value = '—';
            return;
        }
        current.value = option.dataset.stock + ' ' + option.dataset.unit;
    }

    function updateHelp() {
        const value = type.value;
        if (['SALE', 'WASTE', 'DAMAGE'].includes(value)) {
            help.textContent = 'Enter a positive quantity; it will be deducted from stock.';
            quantity.min = '0.001';
        } else if (value === 'ADJUSTMENT') {
            help.textContent = 'Positive adds stock; negative deducts stock.';
            quantity.removeAttribute('min');
        } else {
            help.textContent = 'Enter a positive quantity to add to stock.';
            quantity.min = '0.001';
        }
    }

    product.addEventListener('change', updateProduct);
    type.addEventListener('change', updateHelp);
    updateProduct();
    updateHelp();
});
</script>
