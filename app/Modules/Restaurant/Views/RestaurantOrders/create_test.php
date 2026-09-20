<?php

declare(strict_types=1);
$base = config('app.base_path');
$esc = static fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$old = $old ?? [];
$oldItems = is_array($old['items'] ?? null) ? $old['items'] : [];
?>
<div class="page-header">
    <div>
        <h1>Create Test Customer Order</h1>
        <p>Admin-side customer ordering simulator. Build the order exactly like a customer: restaurant → menu → item options → cart → coupon → delivery/pickup → place order.</p>
    </div>
    <div class="page-actions">
        <a class="btn btn-secondary" href="<?= $base ?>/restaurant/orders">Back</a>
        <a class="btn btn-secondary" href="<?= $base ?>/restaurant/coupons">Manage Coupons</a>
    </div>
</div>

<?php if (!empty($error)): ?>
<div class="card error-card">✕ <?= $esc($error) ?></div>
<?php endif; ?>

<div class="customer-order-layout">
    <main>
        <div class="card order-context">
            <div class="form-grid">
                <div class="form-group">
                    <label>Restaurant <span class="required">*</span></label>
                    <select id="restaurantPicker" name="restaurant_id" form="testOrderForm" required>
                        <?php foreach ($restaurants as $r): ?>
                            <option value="<?= (int)$r['id'] ?>" <?= $restaurant_id === (int)$r['id'] ? 'selected' : '' ?>><?= $esc($r['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Customer / Member <span class="required">*</span></label>
                    <select name="member_id" form="testOrderForm" required>
                        <option value="">Select customer...</option>
                        <?php foreach ($members as $m): ?>
                            <?php $memberName = trim((string)$m['first_name'].' '.(string)$m['last_name']); ?>
                            <option value="<?= (int)$m['id'] ?>" <?= (int)($old['member_id'] ?? 0) === (int)$m['id'] ? 'selected' : '' ?>><?= $esc($memberName) ?><?= !empty($m['phone']) ? ' · '.$esc($m['phone']) : '' ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <?php if ($restaurant_id > 0): ?>
        <div class="card">
            <div class="menu-heading">
                <div>
                    <h2>Restaurant Menu</h2>
                    <p>Sold-out items remain visible so the admin test follows the real customer menu.</p>
                </div>
                <span id="menuCount" class="muted"></span>
            </div>
            <div id="categoryTabs" class="category-tabs"></div>
            <div id="menuGrid" class="menu-grid"></div>
        </div>

        <div class="card configurator" id="configurator" hidden>
            <div class="config-header">
                <div>
                    <span class="eyebrow">Configure item</span>
                    <h2 id="configTitle">Item</h2>
                    <p id="configDescription"></p>
                </div>
                <button type="button" class="icon-button" id="closeConfig" aria-label="Close">×</button>
            </div>
            <div id="configPrice" class="config-price"></div>
            <div id="variantBox"></div>
            <div id="modifierBox"></div>
            <div class="config-footer">
                <label class="qty-control">Quantity <input id="configQty" type="number" min="1" max="50" value="1"></label>
                <button type="button" class="btn btn-primary" id="addToCart">Add to Cart</button>
            </div>
        </div>
        <?php else: ?>
            <div class="card empty-state">Select a restaurant to load its menu.</div>
        <?php endif; ?>
    </main>

    <aside class="cart-column">
        <div class="card cart-card">
            <div class="cart-header"><div><h2>Order Cart</h2><p id="cartCount">0 items</p></div></div>
            <div id="cartLines" class="cart-lines"><div class="empty-cart">Your cart is empty.</div></div>

            <form method="post" action="<?= $base ?>/restaurant/orders/create-test" id="testOrderForm">
                <input type="hidden" name="restaurant_id" value="<?= (int)$restaurant_id ?>">
                <div id="hiddenItems"></div>

                <div class="coupon-box">
                    <label>Coupon Code</label>
                    <div class="coupon-row">
                        <input type="text" id="couponCode" name="coupon_code" value="<?= $esc($old['coupon_code'] ?? '') ?>" placeholder="e.g. WELCOME50" autocomplete="off">
                        <button type="button" class="btn btn-secondary" id="applyCoupon">Apply</button>
                    </div>
                    <div id="couponMessage" class="coupon-message"></div>
                    <?php if (!empty($coupons)): ?>
                        <div class="available-coupons"><strong>Available coupons:</strong> <?php foreach ($coupons as $c): ?><button type="button" class="coupon-chip" data-code="<?= $esc($c['code']) ?>"><?= $esc($c['code']) ?></button><?php endforeach; ?></div>
                    <?php endif; ?>
                </div>

                <div id="minimumOrderNotice" class="minimum-order-notice" role="status"></div>

                <div class="order-totals">
                    <div><span>Original subtotal</span><b id="grossSubtotal">₹0.00</b></div>
                    <div><span>Item discounts</span><b id="itemDiscount">− ₹0.00</b></div>
                    <div><span>Subtotal</span><b id="netSubtotal">₹0.00</b></div>
                    <div><span>Coupon discount</span><b id="couponDiscount">− ₹0.00</b></div>
                    <div id="deliveryRow"><span>Delivery fee</span><b id="deliveryFee">₹0.00</b></div>
                    <div class="grand-total"><span>Total</span><strong id="grandTotal">₹0.00</strong></div>
                </div>

                <div class="checkout-section">
                    <h3>Fulfilment</h3>
                    <div class="order-type-toggle">
                        <label><input type="radio" name="order_type" value="DELIVERY" <?= ($old['order_type'] ?? 'DELIVERY') !== 'PICKUP' ? 'checked' : '' ?>> Delivery</label>
                        <label><input type="radio" name="order_type" value="PICKUP" <?= ($old['order_type'] ?? '') === 'PICKUP' ? 'checked' : '' ?>> Pickup</label>
                    </div>
                    <div id="fulfilmentMessage" class="fulfilment-message"></div>
                    <div id="deliveryFields" class="delivery-fields">
                        <label>Delivery Address *<input name="delivery_address" value="<?= $esc($old['delivery_address'] ?? '') ?>"></label>
                        <div class="two"><label>City<input name="delivery_city" value="<?= $esc($old['delivery_city'] ?? 'Imphal') ?>"></label><label>District<input name="delivery_district" value="<?= $esc($old['delivery_district'] ?? 'Imphal West') ?>"></label></div>
                        <div class="two"><label>State<input name="delivery_state" value="<?= $esc($old['delivery_state'] ?? 'Manipur') ?>"></label><label>Postal Code<input name="delivery_postal_code" value="<?= $esc($old['delivery_postal_code'] ?? '795001') ?>"></label></div>
                    </div>
                    <label>Customer Note<textarea name="customer_note" rows="3"><?= $esc($old['customer_note'] ?? '') ?></textarea></label>
                </div>

                <button class="btn btn-primary place-order" type="submit">Place Test Customer Order</button>
            </form>
        </div>
    </aside>
</div>

<script>
(() => {
    const base = <?= json_encode($base) ?>;
    const menu = <?= json_encode($menu ?? [], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) ?>;
    const oldCart = <?= json_encode($oldItems, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) ?>;
    const oldCoupon = <?= json_encode((string)($old['coupon_code'] ?? '')) ?>;
    const restaurantId = <?= (int)$restaurant_id ?>;
    const money = n => '₹' + Number(n || 0).toFixed(2);
    const esc = s => String(s ?? '').replace(/[&<>'"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#039;','"':'&quot;'}[c]));
    const discountAmount = (price, type, value) => type === 'FLAT' ? Math.min(price, Number(value || 0)) : Math.min(price, price * Number(value || 0) / 100);
    const salePrice = (price, item) => Math.max(0, Number(price) - discountAmount(Number(price), item.discount_type || 'PERCENT', item.discount_value || 0));

    let activeCategory = 'ALL';
    let currentItem = null;
    let cart = [];
    let coupon = { code: oldCoupon, discount: 0 };

    function categories() {
        const names = [...new Set(menu.map(i => i.category_name || 'Uncategorized'))];
        return names;
    }

    function renderTabs() {
        const el = document.getElementById('categoryTabs');
        if (!el) return;
        const cats = categories();
        el.innerHTML = '<button type="button" class="category-tab active" data-cat="ALL">All</button>' + cats.map(c => `<button type="button" class="category-tab" data-cat="${esc(c)}">${esc(c)}</button>`).join('');
        el.querySelectorAll('.category-tab').forEach(btn => btn.addEventListener('click', () => {
            activeCategory = btn.dataset.cat;
            el.querySelectorAll('.category-tab').forEach(b => b.classList.toggle('active', b === btn));
            renderMenu();
        }));
    }

    function renderMenu() {
        const grid = document.getElementById('menuGrid');
        if (!grid) return;
        const list = activeCategory === 'ALL' ? menu : menu.filter(i => (i.category_name || 'Uncategorized') === activeCategory);
        document.getElementById('menuCount').textContent = `${list.length} menu item${list.length === 1 ? '' : 's'}`;
        grid.innerHTML = list.map(i => {
            const sold = !Number(i.is_available);
            const hasDiscount = Number(i.discount_value || 0) > 0;
            const basePrice = Number(i.price || 0);
            const effective = salePrice(basePrice, i);
            const discountText = i.discount_type === 'FLAT' ? `₹${Number(i.discount_value).toFixed(2)} off` : `${Number(i.discount_value).toFixed(2)}% off`;
            const variants = (i.variants || []).length;
            const modifiers = (i.modifier_groups || []).length;
            return `<article class="menu-card ${sold ? 'sold-out' : ''}">
                <div class="menu-card-top"><span class="veg-dot ${Number(i.is_veg) ? 'veg' : 'nonveg'}"></span><span class="category-label">${esc(i.category_name || 'Uncategorized')}</span></div>
                <h3>${esc(i.name)}</h3>
                <p>${esc(i.description || '')}</p>
                <div class="price-line">${hasDiscount ? `<del>${money(basePrice)}</del><strong>${money(effective)}</strong><span class="discount-badge">${esc(discountText)}</span>` : `<strong>${money(basePrice)}</strong>`}</div>
                <div class="menu-meta">${variants ? `${variants} variant${variants > 1 ? 's' : ''}` : 'Base price'}${modifiers ? ` · ${modifiers} option group${modifiers > 1 ? 's' : ''}` : ''}</div>
                <button type="button" class="btn ${sold ? 'btn-disabled' : 'btn-primary'} configure-btn" data-id="${i.id}" ${sold ? 'disabled' : ''}>${sold ? 'Sold Out' : 'Configure & Add'}</button>
            </article>`;
        }).join('') || '<div class="empty-state">No menu items in this category.</div>';
        grid.querySelectorAll('.configure-btn:not([disabled])').forEach(b => b.addEventListener('click', () => openConfigurator(Number(b.dataset.id))));
    }

    function openConfigurator(id) {
        currentItem = menu.find(i => Number(i.id) === id);
        if (!currentItem) return;
        document.getElementById('configurator').hidden = false;
        document.getElementById('configTitle').textContent = currentItem.name;
        document.getElementById('configDescription').textContent = currentItem.description || '';
        document.getElementById('configQty').value = 1;
        const variants = currentItem.variants || [];
        const groups = currentItem.modifier_groups || [];
        const basePrice = variants.length ? Math.min(...variants.filter(v => Number(v.is_available)).map(v => Number(v.price))) : Number(currentItem.price);
        document.getElementById('configPrice').innerHTML = `${currentItem.discount_value > 0 ? '<span>Item discount applied</span> ' : ''}<strong>${money(salePrice(basePrice, currentItem))}</strong> <small>starting price</small>`;
        document.getElementById('variantBox').innerHTML = variants.length ? `<fieldset><legend>Variant ${variants.some(v => Number(v.is_available)) ? '' : '— none available'}</legend>${variants.map((v,n) => `<label class="option-row ${Number(v.is_available) ? '' : 'disabled'}"><input type="radio" name="config_variant" value="${v.id}" ${n === variants.findIndex(x => Number(x.is_available)) ? 'checked' : ''} ${Number(v.is_available) ? '' : 'disabled'}><span>${esc(v.name)}</span><b>${money(salePrice(Number(v.price), currentItem))}</b>${Number(currentItem.discount_value || 0) > 0 ? `<del>${money(v.price)}</del>` : ''}${!Number(v.is_available) ? '<em>Sold out</em>' : ''}</label>`).join('')}</fieldset>` : '';
        document.getElementById('modifierBox').innerHTML = groups.map(g => {
            const type = g.selection_type === 'MULTIPLE' ? 'checkbox' : 'radio';
            const rule = g.is_required ? `Required${g.min_selections ? ` · min ${g.min_selections}` : ''}${g.max_selections !== null ? ` · max ${g.max_selections}` : ''}` : `Optional${g.max_selections !== null ? ` · max ${g.max_selections}` : ''}`;
            return `<fieldset><legend>${esc(g.name)} <small>${rule}</small></legend>${(g.options || []).map(o => `<label class="option-row ${Number(o.is_available) ? '' : 'disabled'}"><input type="${type}" name="config_modifiers" value="${o.id}" ${Number(o.is_available) ? '' : 'disabled'}><span>${esc(o.name)}</span><b>${Number(o.price_adjustment) ? (Number(o.price_adjustment) > 0 ? '+' : '') + money(o.price_adjustment) : 'Included'}</b>${!Number(o.is_available) ? '<em>Unavailable</em>' : ''}</label>`).join('')}</fieldset>`;
        }).join('');
        document.getElementById('configurator').scrollIntoView({behavior:'smooth', block:'start'});
    }

    function selectedModifiers() {
        return [...document.querySelectorAll('#modifierBox input[name="config_modifiers"]:checked')].map(x => Number(x.value));
    }

    function addCurrentItem() {
        if (!currentItem) return;
        const qty = Math.max(1, Math.min(50, Number(document.getElementById('configQty').value || 1)));
        const variant = document.querySelector('#variantBox input[name="config_variant"]:checked');
        const variantId = variant ? Number(variant.value) : null;
        const optionIds = selectedModifiers().sort((a,b) => a-b);
        const key = [currentItem.id, variantId || 0, ...optionIds].join(':');
        const existing = cart.find(x => x.key === key);
        if (existing) existing.quantity = Math.min(50, existing.quantity + qty);
        else cart.push({key, item_id:Number(currentItem.id), variant_id:variantId, modifier_option_ids:optionIds, quantity:qty});
        renderCart();
        document.getElementById('configurator').hidden = true;
    }

    function itemPricing(line) {
        const item = menu.find(i => Number(i.id) === Number(line.item_id));
        let base = Number(item?.price || 0), variantName = null;
        if (line.variant_id) { const v=(item?.variants||[]).find(v=>Number(v.id)===Number(line.variant_id)); if(v){base=Number(v.price);variantName=v.name;} }
        const itemDiscount=discountAmount(base,item?.discount_type||'PERCENT',item?.discount_value||0);
        let modifierTotal=0, modifierNames=[];
        for(const oid of line.modifier_option_ids||[]){for(const g of (item?.modifier_groups||[])){const o=(g.options||[]).find(o=>Number(o.id)===Number(oid));if(o){modifierTotal+=Number(o.price_adjustment||0);modifierNames.push(`${g.name}: ${o.name}`);}}}
        const originalUnit=base+modifierTotal, unit=base-itemDiscount+modifierTotal;
        return {item,variantName,modifierNames,originalUnit,unit,itemDiscount,originalLine:originalUnit*line.quantity,lineTotal:unit*line.quantity};
    }

    function renderCart() {
        const lines=document.getElementById('cartLines');
        const hidden=document.getElementById('hiddenItems');
        let gross=0,net=0,itemDisc=0,count=0;
        lines.innerHTML=cart.map((line,index)=>{const p=itemPricing(line);gross+=p.originalLine;net+=p.lineTotal;itemDisc+=p.itemDiscount*line.quantity;count+=line.quantity;return `<div class="cart-line"><div><strong>${esc(p.item.name)}</strong>${p.variantName?`<small>Variant: ${esc(p.variantName)}</small>`:''}${p.modifierNames.map(x=>`<small>${esc(x)}</small>`).join('')}<div class="line-price">${p.itemDiscount>0?`<del>${money(p.originalUnit)}</del> `:''}${money(p.unit)}</div></div><div class="line-actions"><button type="button" data-action="minus" data-index="${index}">−</button><span>${line.quantity}</span><button type="button" data-action="plus" data-index="${index}">+</button><button type="button" class="remove" data-action="remove" data-index="${index}">×</button></div></div>`}).join('') || '<div class="empty-cart">Your cart is empty.</div>';
        lines.querySelectorAll('button[data-action]').forEach(btn=>btn.addEventListener('click',()=>{const i=Number(btn.dataset.index),a=btn.dataset.action;if(a==='minus')cart[i].quantity--;if(a==='plus')cart[i].quantity++;if(a==='remove'||cart[i].quantity<=0)cart.splice(i,1);renderCart();}));
        hidden.innerHTML=cart.map((line,i)=>`<input type="hidden" name="items[${i}][item_id]" value="${line.item_id}"><input type="hidden" name="items[${i}][variant_id]" value="${line.variant_id||''}"><input type="hidden" name="items[${i}][quantity]" value="${line.quantity}">${(line.modifier_option_ids||[]).map(oid=>`<input type="hidden" name="items[${i}][modifier_option_ids][]" value="${oid}">`).join('')}`).join('');
        document.getElementById('cartCount').textContent=`${count} item${count===1?'':'s'}`;
        updateTotals(gross,itemDisc,net);
    }

    // Restaurant pricing shown here is only a live preview. Final pricing is always recalculated server-side.
    const restaurantConfig = <?= json_encode($restaurant_id > 0 ? ($restaurant ?? []) : [], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) ?>;
    function updateTotals(gross,itemDisc,net) {
        const type=document.querySelector('input[name="order_type"]:checked')?.value || 'DELIVERY';
        const minimum=Number(restaurantConfig.minimum_order_amount||0);
        const free=restaurantConfig.free_delivery_above===null||restaurantConfig.free_delivery_above===undefined||restaurantConfig.free_delivery_above===''?null:Number(restaurantConfig.free_delivery_above);
        const deliveryFeeAmount=Number(restaurantConfig.delivery_fee||0);
        const delivery=type==='DELIVERY' && deliveryFeeAmount>0 && (free===null || net<free) ? deliveryFeeAmount : 0;
        const cd=Math.min(Number(coupon.discount||0),net);
        const meetsMinimum=net>=minimum;
        const notice=document.getElementById('minimumOrderNotice');
        if (notice) {
            if (!cart.length) {
                notice.className='minimum-order-notice neutral';
                notice.textContent=minimum>0 ? `Minimum order: ${money(minimum)}. Add items to begin.` : 'Add items to begin your order.';
            } else if (!meetsMinimum) {
                notice.className='minimum-order-notice warning';
                notice.innerHTML=`Minimum order is <strong>${money(minimum)}</strong>. Please add <strong>${money(minimum-net)}</strong> more before placing this order.`;
            } else {
                notice.className='minimum-order-notice success';
                notice.innerHTML=`✓ Minimum order reached (${money(minimum)}).`;
                if (type==='DELIVERY' && free!==null) {
                    if (net>=free) notice.innerHTML+=` <span>Free delivery applies.</span>`;
                    else notice.innerHTML+=` <span>Delivery fee: ${money(deliveryFeeAmount)} · Free delivery above ${money(free)}.</span>`;
                }
            }
        }
        document.getElementById('grossSubtotal').textContent=money(gross);
        document.getElementById('itemDiscount').textContent='− '+money(itemDisc);
        document.getElementById('netSubtotal').textContent=money(net);
        document.getElementById('couponDiscount').textContent='− '+money(cd);
        document.getElementById('deliveryFee').textContent=money(delivery);
        document.getElementById('grandTotal').textContent=money(Math.max(0,net+delivery-cd));
        document.getElementById('deliveryRow').style.display=type==='DELIVERY'?'flex':'none';
        const submit=document.querySelector('.place-order');
        if(submit) submit.disabled=!cart.length || !meetsMinimum;
    }

    async function applyCoupon() {
        const code=document.getElementById('couponCode').value.trim().toUpperCase();
        const msg=document.getElementById('couponMessage');
        if(!code){coupon={code:'',discount:0};msg.textContent='';renderCart();return;}
        const net=cart.reduce((sum,l)=>sum+itemPricing(l).lineTotal,0);
        const fd=new FormData();fd.append('restaurant_id',restaurantId);fd.append('coupon_code',code);fd.append('subtotal',net.toFixed(2));
        msg.textContent='Checking coupon…';
        try { const r=await fetch(base+'/restaurant/orders/test-coupon',{method:'POST',body:fd});const data=await r.json();if(!data.success)throw new Error(data.message||'Coupon could not be applied.');coupon={code:data.data.code,discount:Number(data.data.discount)};msg.innerHTML=`<span class="success">✓ ${esc(data.data.code)} applied — ${money(data.data.discount)} off</span>`;document.getElementById('couponCode').value=data.data.code;renderCart();}
        catch(e){coupon={code:'',discount:0};msg.innerHTML=`<span class="error-text">${esc(e.message)}</span>`;renderCart();}
    }

    document.getElementById('addToCart')?.addEventListener('click',addCurrentItem);
    document.getElementById('closeConfig')?.addEventListener('click',()=>document.getElementById('configurator').hidden=true);
    document.getElementById('applyCoupon')?.addEventListener('click',applyCoupon);
    document.querySelectorAll('.coupon-chip').forEach(b=>b.addEventListener('click',()=>{document.getElementById('couponCode').value=b.dataset.code;applyCoupon();}));
    document.querySelectorAll('input[name="order_type"]').forEach(r=>r.addEventListener('change',()=>{
        const type=document.querySelector('input[name="order_type"]:checked')?.value || 'DELIVERY';
        const fields=document.getElementById('deliveryFields');
        const message=document.getElementById('fulfilmentMessage');
        if(fields) fields.style.display=type==='DELIVERY'?'grid':'none';
        if(message) message.innerHTML=type==='DELIVERY' ? 'Delivery fee is calculated automatically according to this restaurant's delivery settings.' : 'Pickup selected — the customer will collect this order from the restaurant.';
        updateTotals(cart.reduce((s,l)=>s+itemPricing(l).originalLine,0),cart.reduce((s,l)=>s+itemPricing(l).itemDiscount*l.quantity,0),cart.reduce((s,l)=>s+itemPricing(l).lineTotal,0));
    }));
    document.getElementById('restaurantPicker')?.addEventListener('change',e=>{location.href=base+'/restaurant/orders/create-test?restaurant_id='+encodeURIComponent(e.target.value);});

    renderTabs(); renderMenu(); renderCart();
    const initialType=document.querySelector('input[name="order_type"]:checked')?.value || 'DELIVERY';
    const initialFields=document.getElementById('deliveryFields');
    const initialMessage=document.getElementById('fulfilmentMessage');
    if(initialFields) initialFields.style.display=initialType==='DELIVERY'?'grid':'none';
    if(initialMessage) initialMessage.innerHTML=initialType==='DELIVERY' ? 'Delivery fee is calculated automatically according to this restaurant's delivery settings.' : 'Pickup selected — the customer will collect this order from the restaurant.';
    if(oldCoupon) applyCoupon();
})();
</script>

<style>
.customer-order-layout{display:grid;grid-template-columns:minmax(0,1.55fr) minmax(360px,.75fr);gap:18px;align-items:start}.customer-order-layout main{min-width:0}.cart-column{position:sticky;top:18px}.order-context{margin-bottom:18px}.menu-heading,.cart-header,.config-header{display:flex;justify-content:space-between;gap:16px;align-items:flex-start}.menu-heading h2,.cart-header h2,.config-header h2{margin:0 0 4px}.menu-heading p,.cart-header p,.config-header p{margin:0;color:#64748b;font-size:13px}.category-tabs{display:flex;gap:8px;overflow:auto;padding:16px 0 4px}.category-tab{border:1px solid #e2e8f0;background:#fff;border-radius:999px;padding:8px 14px;white-space:nowrap;cursor:pointer}.category-tab.active{background:#111827;color:#fff;border-color:#111827}.menu-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px;margin-top:16px}.menu-card{border:1px solid #e5e7eb;border-radius:14px;padding:15px;display:flex;flex-direction:column;min-height:245px}.menu-card.sold-out{opacity:.62;background:#f8fafc}.menu-card-top{display:flex;justify-content:space-between;align-items:center}.veg-dot{width:10px;height:10px;border:1px solid #16a34a;display:inline-block;border-radius:2px}.veg-dot.nonveg{border-color:#dc2626}.category-label,.menu-meta,.muted{font-size:12px;color:#64748b}.menu-card h3{margin:10px 0 5px}.menu-card p{color:#64748b;font-size:13px;min-height:38px;margin:0 0 10px}.price-line{display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-top:auto}.price-line strong{font-size:18px}.price-line del{color:#94a3b8}.discount-badge{font-size:11px;background:#ecfdf5;color:#047857;padding:4px 7px;border-radius:999px;font-weight:700}.menu-meta{margin:8px 0 12px}.btn-disabled{background:#e2e8f0;color:#64748b;border:0;cursor:not-allowed}.configurator{margin-top:18px}.config-header{margin-bottom:12px}.eyebrow{font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:#64748b}.icon-button{border:0;background:#f1f5f9;border-radius:50%;width:34px;height:34px;font-size:22px;cursor:pointer}.config-price{padding:10px 12px;background:#f8fafc;border-radius:10px;margin-bottom:10px}.config-price strong{font-size:20px}.config-price span{font-size:12px;color:#047857}.configurator fieldset{border:1px solid #e2e8f0;border-radius:12px;padding:12px;margin:12px 0}.configurator legend{font-weight:800;padding:0 5px}.configurator legend small{font-weight:400;color:#64748b}.option-row{display:grid;grid-template-columns:auto 1fr auto auto;gap:9px;align-items:center;padding:9px;border-radius:8px}.option-row:hover{background:#f8fafc}.option-row.disabled{opacity:.5}.option-row em{font-size:11px;color:#b91c1c}.option-row del{font-size:11px;color:#94a3b8}.config-footer{display:flex;justify-content:space-between;align-items:end;gap:12px}.qty-control{display:grid;gap:5px;font-weight:700}.qty-control input{width:90px}.cart-card{overflow:hidden}.cart-lines{display:grid;gap:2px;margin:0 -4px}.empty-cart{padding:24px 4px;text-align:center;color:#94a3b8}.cart-line{display:flex;justify-content:space-between;gap:10px;padding:12px 4px;border-bottom:1px solid #eef2f7}.cart-line>div:first-child{min-width:0}.cart-line strong{display:block}.cart-line small{display:block;color:#64748b;font-size:11px;margin-top:2px}.line-price{font-size:12px;margin-top:5px}.line-price del{color:#94a3b8}.line-actions{display:flex;align-items:center;gap:6px;align-self:center}.line-actions button{width:27px;height:27px;border:1px solid #e2e8f0;background:#fff;border-radius:6px;cursor:pointer}.line-actions .remove{color:#b91c1c}.minimum-order-notice{margin-top:14px;padding:11px 12px;border-radius:10px;font-size:12px;line-height:1.5}.minimum-order-notice.warning{background:#fff7ed;color:#9a3412;border:1px solid #fed7aa}.minimum-order-notice.success{background:#ecfdf5;color:#047857;border:1px solid #a7f3d0}.minimum-order-notice.neutral{background:#f8fafc;color:#64748b;border:1px solid #e2e8f0}.minimum-order-notice span{display:block;margin-top:3px}.fulfilment-message{font-size:12px;color:#64748b;margin:8px 0 10px}.coupon-box{border-top:1px solid #e5e7eb;margin-top:12px;padding-top:14px}.coupon-box>label{font-weight:800;font-size:13px}.coupon-row{display:flex;gap:8px;margin-top:7px}.coupon-row input{min-width:0;flex:1;text-transform:uppercase}.coupon-message{min-height:20px;font-size:12px;margin-top:6px}.success{color:#047857}.error-text{color:#b91c1c}.available-coupons{font-size:11px;color:#64748b;margin-top:7px}.coupon-chip{border:0;background:#eef2ff;color:#3730a3;border-radius:999px;padding:4px 7px;margin:2px;cursor:pointer}.order-totals{display:grid;gap:8px;padding:16px 0;border-top:1px solid #e5e7eb;margin-top:14px}.order-totals>div{display:flex;justify-content:space-between;gap:12px;font-size:13px}.order-totals .grand-total{border-top:1px solid #e5e7eb;padding-top:12px;margin-top:4px;font-size:17px}.grand-total strong{font-size:21px}.checkout-section{border-top:1px solid #e5e7eb;padding-top:15px}.checkout-section h3{margin:0 0 10px}.order-type-toggle{display:flex;gap:10px;margin-bottom:12px}.order-type-toggle label{border:1px solid #e2e8f0;border-radius:9px;padding:9px 12px;cursor:pointer}.delivery-fields{display:grid;gap:10px}.delivery-fields label,.checkout-section>label{display:grid;gap:5px;font-size:12px;font-weight:700}.two{display:grid;grid-template-columns:1fr 1fr;gap:10px}.place-order{width:100%;margin-top:14px}.error-card{color:#991b1b;border-color:#fecaca}.required{color:#dc2626}@media(max-width:1050px){.customer-order-layout{grid-template-columns:1fr}.cart-column{position:static}}@media(max-width:650px){.menu-grid{grid-template-columns:1fr}.two{grid-template-columns:1fr}.config-footer{align-items:stretch;flex-direction:column}.customer-order-layout{display:block}.cart-column{margin-top:18px}}
</style>
