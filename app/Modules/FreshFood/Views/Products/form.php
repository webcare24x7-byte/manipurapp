<?php declare(strict_types=1);$r=$record??$old??[];$base=config('app.base_path'); ?><div class="card"><div class="form-grid"><div class="form-group"><label>Fresh Food Business *</label><select id="ff-business" name="fresh_food_id" required><option value="">Select business</option><?php foreach(($businesses??[]) as $b): ?><option value="<?= (int)$b['id'] ?>" <?= ((int)($r['fresh_food_id']??0)===(int)$b['id'])?'selected':'' ?>><?= htmlspecialchars((string)$b['business_name'],ENT_QUOTES,'UTF-8') ?></option><?php endforeach; ?></select></div><div class="form-group"><label>Category</label><select id="ff-category" name="category_id"><option value="">No category</option><?php foreach(($categories??[]) as $c): ?><option value="<?= (int)$c['id'] ?>" <?= ((int)($r['category_id']??0)===(int)$c['id'])?'selected':'' ?>><?= htmlspecialchars((string)$c['name'],ENT_QUOTES,'UTF-8') ?></option><?php endforeach; ?></select></div><div class="form-group"><label>Product Name *</label><input name="name" required value="<?= htmlspecialchars((string)($r['name']??''),ENT_QUOTES,'UTF-8') ?>"></div><div class="form-group"><label>Price (₹) *</label><input id="product-price" type="number" step="0.01" min="0" name="price" required value="<?= htmlspecialchars((string)($r['price']??'0.00'),ENT_QUOTES,'UTF-8') ?>"></div>
<div class="form-group"><label>Discount Type</label><select id="discount-type" name="discount_type"><option value="NONE" <?= (($r['discount_type']??'NONE')==='NONE')?'selected':'' ?>>No Discount</option><option value="PERCENT" <?= (($r['discount_type']??'')==='PERCENT')?'selected':'' ?>>Percentage (%)</option><option value="FLAT" <?= (($r['discount_type']??'')==='FLAT')?'selected':'' ?>>Flat (₹)</option></select></div>
<div class="form-group"><label>Discount Value</label><input id="discount-value" type="number" step="0.01" min="0" name="discount_value" value="<?= htmlspecialchars((string)($r['discount_value']??'0.00'),ENT_QUOTES,'UTF-8') ?>"><small id="discount-help">Select a discount type.</small></div>
<div class="form-group"><label>Effective Selling Price</label><input id="effective-price" type="text" readonly value="₹<?= number_format((float)($r['price']??0),2) ?>"></div><div class="form-group"><label>Unit</label><select name="unit"><?php foreach(['piece','kg','gram','litre','ml','pack','dozen','box'] as $u): ?><option <?= (($r['unit']??'piece')===$u)?'selected':'' ?>><?= $u ?></option><?php endforeach; ?></select></div><div class="form-group"><label>Minimum Order Quantity</label><input type="number" step="0.001" min="0.001" name="min_order_quantity" value="<?= htmlspecialchars((string)($r['min_order_quantity']??'1.000'),ENT_QUOTES,'UTF-8') ?>"></div><div class="form-group"><label>Increment Quantity</label><input type="number" step="0.001" min="0.001" name="increment_quantity" value="<?= htmlspecialchars((string)($r['increment_quantity']??'1.000'),ENT_QUOTES,'UTF-8') ?>"></div><div class="form-group"><label>Sort Order</label><input type="number" name="sort_order" value="<?= (int)($r['sort_order']??0) ?>"></div><div class="form-group full-width"><label>Description</label><textarea name="description" rows="3"><?= htmlspecialchars((string)($r['description']??''),ENT_QUOTES,'UTF-8') ?></textarea></div><div class="form-group full-width"><label><input type="checkbox" name="is_variable_weight" value="1" <?= !empty($r['is_variable_weight'])?'checked':'' ?>> Variable weight product</label><br><label><input type="checkbox" name="is_available" value="1" <?= !empty($r['is_available'])||!array_key_exists('is_available',$r)?'checked':'' ?>> Available for sale</label></div><div class="form-group"><label>Status</label><select name="status"><option <?= (($r['status']??'Active')==='Active')?'selected':'' ?>>Active</option><option <?= (($r['status']??'')==='Inactive')?'selected':'' ?>>Inactive</option></select></div></div></div><style>.form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}.full-width{grid-column:1/-1}.form-group label{display:block;font-weight:600;margin-bottom:7px}.form-group input,.form-group select,.form-group textarea{width:100%;box-sizing:border-box}@media(max-width:700px){.form-grid{grid-template-columns:1fr}.full-width{grid-column:auto}}</style>

<script>
document.addEventListener('DOMContentLoaded', function(){
 const price=document.getElementById('product-price'), type=document.getElementById('discount-type'), value=document.getElementById('discount-value'), effective=document.getElementById('effective-price'), help=document.getElementById('discount-help');
 function calc(){ const p=Math.max(0,parseFloat(price.value)||0), d=Math.max(0,parseFloat(value.value)||0), t=type.value; let discount=0; if(t==='PERCENT') discount=Math.min(p,p*d/100); else if(t==='FLAT') discount=Math.min(p,d); effective.value='₹'+(p-discount).toFixed(2); help.textContent=t==='PERCENT'?'Enter a percentage from 0 to 100.':t==='FLAT'?'Enter a flat amount not greater than the price.':'No discount will be applied.'; value.disabled=t==='NONE'; }
 [price,type,value].forEach(e=>e.addEventListener('input',calc)); [price,type,value].forEach(e=>e.addEventListener('change',calc)); calc();
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const businessSelect = document.getElementById('ff-business');
    const categorySelect = document.getElementById('ff-category');

    if (!businessSelect || !categorySelect) {
        return;
    }

    const basePath = <?= json_encode($base, JSON_UNESCAPED_SLASHES) ?>;
    const selectedCategoryId = <?= json_encode((string)($r['category_id'] ?? '')) ?>;

    async function loadCategories(freshFoodId, preserveSelection = false) {
        categorySelect.innerHTML = '<option value="">Loading categories...</option>';
        categorySelect.disabled = true;

        if (!freshFoodId) {
            categorySelect.innerHTML = '<option value="">No category</option>';
            categorySelect.disabled = false;
            return;
        }

        try {
            const response = await fetch(
                basePath + '/fresh-food/products/categories/' + encodeURIComponent(freshFoodId),
                {
                    method: 'GET',
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin'
                }
            );

            if (!response.ok) {
                throw new Error('Unable to load categories.');
            }

            const categories = await response.json();
            categorySelect.innerHTML = '<option value="">No category</option>';

            if (Array.isArray(categories)) {
                categories.forEach(function (category) {
                    const option = document.createElement('option');
                    option.value = category.id;
                    option.textContent = category.name;

                    if (preserveSelection && String(category.id) === String(selectedCategoryId)) {
                        option.selected = true;
                    }

                    categorySelect.appendChild(option);
                });
            }
        } catch (error) {
            console.error(error);
            categorySelect.innerHTML = '<option value="">Unable to load categories</option>';
        } finally {
            categorySelect.disabled = false;
        }
    }

    businessSelect.addEventListener('change', function () {
        loadCategories(this.value, false);
    });

    if (businessSelect.value) {
        loadCategories(businessSelect.value, true);
    }
});
</script>
