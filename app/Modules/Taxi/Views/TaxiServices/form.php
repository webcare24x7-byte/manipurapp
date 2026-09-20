<?php
declare(strict_types=1);
$data = $record ?? $old ?? [];
$pricingMode = (string) ($data['pricing_mode'] ?? 'PER_RIDE');
$esc = static fn(mixed $v): string => htmlspecialchars((string) ($v ?? ''), ENT_QUOTES, 'UTF-8');
?>

<style>
.taxi-service-guide{margin-bottom:18px;padding:20px;border:1px solid #bfdbfe;border-radius:14px;background:#eff6ff;color:#1e3a8a}.taxi-service-guide h2{margin:0 0 7px;font-size:18px;color:#1e3a8a}.taxi-service-guide p{margin:0 0 12px;font-size:13px;line-height:1.6}.taxi-guide-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:10px;margin-top:12px}.taxi-guide-item{padding:12px;border-radius:10px;background:#fff;border:1px solid #dbeafe;font-size:12px;line-height:1.55}.taxi-guide-item strong{display:block;margin-bottom:3px;color:#0f172a}.taxi-pricing-guide{margin-top:18px;padding:16px;border:1px solid #d1fae5;border-radius:13px;background:#f0fdf4}.taxi-pricing-guide h3{margin:0 0 6px;color:#166534;font-size:16px}.taxi-pricing-guide p{margin:0 0 10px;color:#475569;font-size:12px;line-height:1.55}.taxi-example{padding:12px;border-radius:10px;background:#fff;border:1px solid #bbf7d0;color:#334155;font-size:12px;line-height:1.65}.taxi-warning{margin-top:10px;padding:10px 12px;border-radius:9px;background:#fffbeb;border:1px solid #fde68a;color:#92400e;font-size:12px;line-height:1.5}.form-help{display:block;margin-top:5px;color:#64748b;font-size:11px;line-height:1.45}.pricing-field-hint{display:block;margin-top:5px;color:#475569;font-size:11px;line-height:1.45}@media(max-width:760px){.taxi-guide-grid{grid-template-columns:1fr}}
</style>

<div class="taxi-service-guide">
    <h2>How to Configure a Taxi Service</h2>
    <p>This service is the pricing product that staff and customers will select when making a taxi booking. Configure the pricing rules carefully because the selected service determines how the booking fare is calculated from the committed road distance and ETA.</p>
    <div class="taxi-guide-grid">
        <div class="taxi-guide-item"><strong>Per Ride</strong>Fixed base fare. If Included KM is configured and the trip exceeds it, Extra KM Rate is charged for the additional distance.</div>
        <div class="taxi-guide-item"><strong>Per KM</strong>Base Fare + road distance × Per KM. If Per Minute is configured, ETA is also used for a time charge.</div>
        <div class="taxi-guide-item"><strong>Per Day / Rental</strong>Rental days × Daily Rate. If Included KM and Extra KM Rate are configured, excess distance is added after the included distance.</div>
        <div class="taxi-guide-item"><strong>Per KM with Minimum</strong>Base Fare + distance charge + optional time charge, then the Minimum Fare is enforced as the lowest possible fare.</div>
        <div class="taxi-guide-item"><strong>Custom Quote</strong>No automatic fare. Distance/ETA can still be estimated, but an approved manual quote is required.</div>
        <div class="taxi-guide-item"><strong>Minimum Fare</strong>This is a floor, not a maximum. If the calculated fare is ₹700 and minimum is ₹400, the fare remains ₹700.</div>
    </div>
</div>

<div class="card"><div class="form-grid">
    <div class="form-group"><label for="vendor_id">Taxi Business <span class="required">*</span></label><select id="vendor_id" name="vendor_id" required><option value="">Select taxi business...</option><?php foreach (($vendors ?? []) as $vendor): ?><option value="<?= (int) $vendor['id'] ?>" <?= (string) ($data['vendor_id'] ?? '') === (string) $vendor['id'] ? 'selected' : '' ?>><?= $esc($vendor['business_name']) ?></option><?php endforeach; ?></select><small class="form-help">The business that owns and offers this taxi service.</small></div>
    <div class="form-group"><label for="name">Service Name <span class="required">*</span></label><input type="text" id="name" name="name" value="<?= $esc(($data['name'] ?? '')) ?>" required><small class="form-help">Customer-facing name, e.g. Airport Transfer, Local Taxi, or Outstation SUV.</small></div>
    <div class="form-group"><label for="code">Service Code <span class="required">*</span></label><input type="text" id="code" name="code" value="<?= $esc(($data['code'] ?? '')) ?>" required><small class="form-help">Unique internal code used to identify this service.</small></div>
    <div class="form-group"><label for="service_type">Service Type <span class="required">*</span></label><select id="service_type" name="service_type" required><option value="">Select...</option><?php foreach (['Local','Airport Transfer','Outstation','Scheduled','Other'] as $type): ?><option value="<?= $esc($type) ?>" <?= ($data['service_type'] ?? '') === $type ? 'selected' : '' ?>><?= $esc($type) ?></option><?php endforeach; ?></select><small class="form-help">Describes the purpose of the service. It does not by itself determine the fare; Pricing Mode and pricing values do.</small></div>
    <div class="form-group full-width"><label for="description">Description</label><textarea id="description" name="description" rows="4"><?= $esc(($data['description'] ?? '')) ?></textarea><small class="form-help">Explain what the service includes or where it is normally used.</small></div>

    <div class="form-group full-width"><label for="pricing_mode">Pricing Mode</label><select id="pricing_mode" name="pricing_mode"><option value="PER_RIDE" <?= $pricingMode === 'PER_RIDE' ? 'selected' : '' ?>>Per Ride</option><option value="PER_KM" <?= $pricingMode === 'PER_KM' ? 'selected' : '' ?>>Per KM</option><option value="PER_DAY" <?= $pricingMode === 'PER_DAY' ? 'selected' : '' ?>>Per Day / Rental</option><option value="PER_KM_WITH_MINIMUM" <?= $pricingMode === 'PER_KM_WITH_MINIMUM' ? 'selected' : '' ?>>Per KM with Minimum</option><option value="CUSTOM_QUOTE" <?= $pricingMode === 'CUSTOM_QUOTE' ? 'selected' : '' ?>>Custom Quote</option></select><small class="form-help">Choose how the fare is calculated. The booking page will show these rules to staff and, later, to customers.</small></div>

    <div class="form-group"><label for="base_fare">Base Fare</label><input type="number" id="base_fare" name="base_fare" value="<?= $esc(($data['base_fare'] ?? '')) ?>" step="0.01" min="0"><small class="form-help">Starting/fixed amount used by Per Ride, Per KM, and Per KM with Minimum calculations.</small></div>
    <div class="form-group"><label for="included_km">Included KM</label><input type="number" id="included_km" name="included_km" value="<?= $esc(($data['included_km'] ?? '')) ?>" step="0.01" min="0"><small class="form-help">Distance included in the Base Fare or rental package before Extra KM Rate applies. Example: 15 km included.</small></div>
    <div class="form-group"><label for="per_km">Per KM</label><input type="number" id="per_km" name="per_km" value="<?= $esc(($data['per_km'] ?? '')) ?>" step="0.01" min="0"><small class="form-help">Distance charge used by Per KM and Per KM with Minimum modes.</small></div>
    <div class="form-group"><label for="per_minute">Per Minute</label><input type="number" id="per_minute" name="per_minute" value="<?= $esc(($data['per_minute'] ?? '')) ?>" step="0.01" min="0"><small class="form-help">Optional time charge. When configured for a distance-based mode, committed ETA is multiplied by this rate.</small></div>
    <div class="form-group"><label for="minimum_fare">Minimum Fare</label><input type="number" id="minimum_fare" name="minimum_fare" value="<?= $esc(($data['minimum_fare'] ?? '')) ?>" step="0.01" min="0"><small class="form-help">Lowest fare allowed. It never caps a higher calculated fare.</small></div>
    <div class="form-group"><label for="daily_rate">Daily Rate</label><input type="number" id="daily_rate" name="daily_rate" value="<?= $esc(($data['daily_rate'] ?? '')) ?>" step="0.01" min="0"><small class="form-help">Used for outstation / rental services such as ₹2,500 per day.</small></div>
    <div class="form-group"><label for="extra_km_rate">Extra KM Rate</label><input type="number" id="extra_km_rate" name="extra_km_rate" value="<?= $esc(($data['extra_km_rate'] ?? '')) ?>" step="0.01" min="0"><small class="form-help">Applied to kilometres beyond Included KM for Per Ride or rental pricing. If Included KM is exceeded, configure this rate or use Custom Quote.</small></div>
    <div class="form-group"><label for="status">Status</label><select id="status" name="status"><option value="">Select...</option><option value="Active" <?= ($data['status'] ?? '') === 'Active' ? 'selected' : '' ?>>Active</option><option value="Inactive" <?= ($data['status'] ?? '') === 'Inactive' ? 'selected' : '' ?>>Inactive</option></select></div>
</div>

<div class="taxi-pricing-guide">
    <h3>Live Pricing Guide</h3>
    <p id="pricing_guide_text"></p>
    <div class="taxi-example" id="pricing_example"></div>
    <div class="taxi-warning" id="pricing_warning"></div>
</div>
</div>

<script>
(function(){
 const $=id=>document.getElementById(id);
 const money=v=>'₹'+Number(v||0).toLocaleString('en-IN',{minimumFractionDigits:2,maximumFractionDigits:2});
 const num=id=>Math.max(0,Number($(id)?.value||0));
 function update(){
   const mode=$('pricing_mode')?.value||'PER_RIDE', base=num('base_fare'), inc=num('included_km'), km=num('per_km'), min=num('minimum_fare'), day=num('daily_rate'), extra=num('extra_km_rate'), minute=num('per_minute');
   let text='', example='', warning='';
   if(mode==='PER_RIDE'){
     text='Per Ride means the Base Fare is charged for trips within the Included KM. If the route exceeds Included KM, the excess kilometres are charged using Extra KM Rate.';
     const sampleKm=inc>0?inc+10:30, excess=inc>0?10:0, total=base+excess*extra;
     example=`Example: Base Fare ${money(base)}, Included KM ${inc.toFixed(2)} km. A ${sampleKm.toFixed(2)} km trip is ${inc>0?money(base)+' + (10 × '+money(extra)+'/km) = '+money(total):money(base)+' because no Included KM limit is configured.'}.`;
     if(inc>0&&extra<=0) warning='Included KM is configured. If a booking exceeds it, an Extra KM Rate must be configured or the booking should use Custom Quote.';
   } else if(mode==='PER_KM'){
     text='Per KM uses Base Fare + road distance × Per KM. If Per Minute is configured, committed ETA also adds a time charge.';
     example=`Example at 10 km / 30 min: ${money(base)} + (10 × ${money(km)}/km)${minute>0?' + (30 × '+money(minute)+'/min)':''} = ${money(base+10*km+30*minute)}.`;
   } else if(mode==='PER_KM_WITH_MINIMUM'){
     text='Per KM with Minimum calculates the distance/time fare first, then applies Minimum Fare as a floor. A higher calculated fare is never reduced to the minimum.';
     const raw=base+10*km+30*minute, result=Math.max(raw,min);
     example=`Example at 10 km / 30 min: calculated ${money(raw)}; minimum ${money(min)}; result ${money(result)}.`;
   } else if(mode==='PER_DAY'){
     text='Per Day / Rental uses rental days × Daily Rate. If Included KM is configured and total route distance exceeds it, Extra KM Rate is added to the excess distance.';
     const total=day+Math.max(0,10-inc)*extra;
     example=`Example: 1 day = ${money(day)}. If Included KM is ${inc.toFixed(2)} km and the trip is 10 km, excess distance is ${Math.max(0,10-inc).toFixed(2)} km.`;
     if(inc>0&&extra<=0) warning='Included KM is configured. Configure Extra KM Rate if excess distance should be charged automatically.';
   } else {
     text='Custom Quote disables automatic fare calculation. Staff must enter an approved quote after reviewing the booking.';
     example='Distance and ETA can still be estimated and stored for operational planning, but they do not create an automatic customer fare.';
   }
   $('pricing_guide_text').textContent=text; $('pricing_example').textContent=example; $('pricing_warning').textContent=warning; $('pricing_warning').style.display=warning?'block':'none';
 }
 ['pricing_mode','base_fare','included_km','per_km','per_minute','minimum_fare','daily_rate','extra_km_rate'].forEach(id=>$(id)?.addEventListener('input',update)); update();
})();
</script>
