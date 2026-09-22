<?php include __DIR__ . '/../Shared/member-desktop.css.php';
include __DIR__ . '/../Shared/member-desktop-header.php'; ?>
<?php
require __DIR__.'/_style.php';
$t=$trip;
$sources=$tripSources??[];
$labels=['DESTINATION'=>'Destinations','STAY'=>'Places to stay','PACKAGE'=>'Tour packages','EXPERIENCE'=>'Experiences','EVENT'=>'Events & festivals','GUIDE'=>'Local guides','RESTAURANT'=>'Restaurants','TAXI'=>'Taxi services','RENTAL'=>'Vehicle rentals','FRESH_FOOD'=>'Fresh food & groceries'];
$icons=['DESTINATION'=>'📍','STAY'=>'🏨','PACKAGE'=>'🧳','EXPERIENCE'=>'✨','EVENT'=>'🎉','GUIDE'=>'🧭','RESTAURANT'=>'🍛','TAXI'=>'🚕','RENTAL'=>'🚐','FRESH_FOOD'=>'🥬'];
$itemsByDay=[];
foreach(($t['items']??[]) as $i){$itemsByDay[(int)$i['day_number']][]=$i;}
$maxDay=max(1,count($itemsByDay),1);
if(!empty($t['start_date'])&&!empty($t['end_date'])){try{$maxDay=max($maxDay,(int)((new DateTime($t['start_date']))->diff(new DateTime($t['end_date']))->days)+1);}catch(Throwable $e){}}
$money=static function($v):string{ $n=(float)$v; return '₹'.number_format($n,2); };
?>
<div class="ma-tour ma-trip-page">
<header class="ma-tour-head"><a class="ma-tour-brand" href="<?=$basePath?>/member/tourism/trips"><span class="ma-tour-mark">🗺️</span><span><strong><?=maTourEsc($t['title'])?></strong><small><?=maTourEsc($t['status']??'Draft')?> trip plan</small></span></a><a class="ma-tour-back" href="<?=$basePath?>/member/tourism/trips">← My trips</a></header>
<section class="ma-trip-hero ma-trip-detail-hero"><div><span class="ma-trip-kicker">MY MANIPUR JOURNEY</span><h1><?=maTourEsc($t['title'])?></h1><p><?=nl2br(maTourEsc((string)($t['description']??'Plan your days, places and local services.')))?></p><div class="ma-trip-summary"><?php if(!empty($t['start_date'])):?><span>📅 <?=maTourEsc($t['start_date'])?></span><?php endif;?><?php if(!empty($t['end_date'])):?><span>→ <?=maTourEsc($t['end_date'])?></span><?php endif;?><span>📌 <?=count($t['items']??[])?> items</span></div></div><div class="ma-trip-hero-icon">🧭</div></section>

<section class="ma-tour-support" id="tour-support">
  <div class="ma-tour-support-icon">☎️</div>
  <div class="ma-tour-support-copy"><strong>Need help planning your Manipur trip?</strong><p>If you need support, call our <b>ManipurApp Tour expert / Consultant.</b> Speak with our team for help with destinations, stays, transport, food, guides and your itinerary.</p></div>
  <a class="ma-tour-support-btn" href="<?=$basePath?>/member/tourism">Speak with our team</a>
</section>

<?php if(isset($_GET['error'])):?><div class="ma-trip-error"><?=maTourEsc((string)$_GET['error'])?></div><?php endif;?>
<section class="ma-trip-district"><form class="ma-tour-location-filter" method="get"><label>📍 Add options from<select name="district" onchange="this.form.submit()"><option value="">All Manipur</option><?php foreach(($districts??[]) as $row): ?><option value="<?=maTourEsc($row['district'])?>" <?=($district??'')===$row['district']?'selected':''?>><?=maTourEsc($row['district'])?></option><?php endforeach; ?></select></label><button type="submit">Filter options</button><?php if(!empty($district)): ?><a class="clear" href="<?=$basePath?>/member/tourism/trips/<?=intval($t['id'])?>" style="text-decoration:none;padding:10px 14px;border-radius:11px">Clear</a><?php endif; ?></form><p class="ma-trip-source-note">Choose a district to focus the add-to-trip list on local destinations, stays, food, taxi, rentals and experiences.</p></section>
<section class="ma-trip-builder">
  <div class="ma-trip-builder-head"><div><span class="ma-trip-kicker">BUILD YOUR ITINERARY</span><h2>Add something to your trip</h2><p>Choose an item and we'll show useful details before you add it. Transport routes, prices, rooms, menus, products and vehicle information can be planned here.</p></div></div>
  <form class="ma-trip-add" method="post" action="<?=$basePath?>/member/tourism/trips/<?=intval($t['id'])?>/items" id="tripAddForm">
    <input type="hidden" name="_token" value="<?=maTourEsc($csrf)?>">
    <input type="hidden" name="title" id="tripTitle"><input type="hidden" name="district" value="<?=maTourEsc($district??'')?>">
    <div class="ma-trip-add-grid">
      <label>Day<select name="day_number" required><?php for($d=1;$d<=$maxDay+7;$d++):?><option value="<?=$d?>">Day <?=$d?></option><?php endfor;?></select></label>
      <label>Category<select name="item_type" id="tripType" required><option value="">Choose what to add</option><?php foreach($labels as $key=>$label):?><option value="<?=$key?>"><?=$icons[$key]?> <?=$label?></option><?php endforeach;?></select></label>
      <label class="ma-trip-item-field">Item<select name="item_id" id="tripItem" required disabled><option value="">Choose a category first</option></select></label>
    </div>

    <div class="ma-trip-smart" id="tripSmart" hidden>
      <div class="ma-trip-smart-head"><div><span class="ma-trip-kicker">SMART DETAILS</span><h3 id="smartTitle">Choose an item</h3></div><span id="smartBadge" class="ma-trip-smart-badge"></span></div>
      <div id="smartContent" class="ma-trip-smart-content"></div>
    </div>

    <div class="ma-trip-add-bottom"><input name="notes" id="tripNotes" maxlength="1000" placeholder="Optional note — e.g. morning visit, lunch stop, pickup point"><button class="ma-tour-btn" style="background:var(--green);color:#fff">＋ Add to itinerary</button></div>
  </form>
  <div class="ma-trip-source-note">💡 <strong>Planning only:</strong> adding a service to your trip does not make a booking. When you're ready, use the existing Taxi, Restaurant, Fresh Food or Vehicle Rental booking flow.</div>
</section>

<section class="ma-trip-itinerary"><div class="ma-trip-builder-head"><div><span class="ma-trip-kicker">YOUR ROUTE</span><h2>Day-by-day itinerary</h2></div></div>
<?php if(!$itemsByDay):?><div class="ma-tour-empty">Your itinerary is empty. Start by adding a destination, experience, stay or service above.</div><?php else:?>
<?php for($day=1;$day<=$maxDay;$day++): if(empty($itemsByDay[$day])) continue; ?><div class="ma-trip-day"><div class="ma-trip-day-label"><span>DAY</span><strong><?=$day?></strong></div><div class="ma-trip-day-items"><?php foreach($itemsByDay[$day] as $i): $type=(string)$i['item_type']; ?><article class="ma-trip-item"><div class="ma-trip-item-icon"><?=$icons[$type]??'•'?></div><div class="ma-trip-item-main"><div class="ma-trip-item-top"><span class="ma-trip-item-type"><?=maTourEsc($labels[$type]??$type)?></span></div><h3><?=maTourEsc((string)($i['title']?:'Planned item'))?></h3><?php if(!empty($i['notes'])):?><p><?=nl2br(maTourEsc($i['notes']))?></p><?php endif;?></div><form method="post" action="<?=$basePath?>/member/tourism/trips/<?=intval($t['id'])?>/items/remove" onsubmit="return confirm('Remove this item from your trip?');"><input type="hidden" name="_token" value="<?=maTourEsc($csrf)?>"><input type="hidden" name="item_id" value="<?=intval($i['id'])?>"><button class="ma-trip-remove" title="Remove">×</button></form></article><?php endforeach;?></div></div><?php endfor;?><?php endif;?></section>
</div>

<nav class="ma-bottom-nav" aria-label="Primary navigation">
 <a class="ma-nav-item" href="<?=maTourEsc($basePath)?>/member">⌂<span>Home</span></a>
 <a class="ma-nav-item" href="<?=maTourEsc($basePath)?>/member/tourism">⌖<span>Explore</span></a>
 <a class="ma-nav-item" href="<?=maTourEsc($basePath)?>/member/bookings">▣<span>Bookings</span></a>
 <a class="ma-nav-item ma-nav-ilp" href="<?= maTourEsc($basePath) ?>/member/ilp">▤<span>ILP</span></a>
 <a class="ma-nav-item ma-nav-ai" href="<?= maTourEsc($basePath) ?>/member#ask-ai">✦<span>Ask AI</span></a>
 <a class="ma-nav-item active" href="<?=maTourEsc($basePath)?>/member/tourism/trips">♡<span>Trips</span></a>
 <a class="ma-nav-item" href="<?=maTourEsc($basePath)?>/member/profile">●<span>Profile</span></a>
</nav>

<script>
(()=>{
const data=<?=json_encode($sources,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)?>;
const labels=<?=json_encode($labels,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)?>;
const icons=<?=json_encode($icons,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)?>;
const type=document.getElementById('tripType'), item=document.getElementById('tripItem'), title=document.getElementById('tripTitle'), notes=document.getElementById('tripNotes'), smart=document.getElementById('tripSmart'), smartTitle=document.getElementById('smartTitle'), smartBadge=document.getElementById('smartBadge'), smartContent=document.getElementById('smartContent');
const esc=v=>String(v??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c]));
const money=v=>{const n=Number(v||0);return '₹'+n.toLocaleString('en-IN',{minimumFractionDigits:2,maximumFractionDigits:2});};
const num=v=>Number(v||0);
let selected=null;
function meta(x){return [x.district||x.city||x.category_name||x.service_type||x.experience_type||''].filter(Boolean).join(' · ');}
function renderList(){
  item.innerHTML='<option value="">Choose an item</option>';
  const rows=data[type.value]||[];
  item.disabled=!rows.length;
  if(!rows.length){item.innerHTML='<option value="">No active items available</option>';smart.hidden=true;return;}
  rows.forEach(x=>{const o=document.createElement('option');o.value=x.id;o.textContent=(x.title||x.name||x.full_name||'Item')+(meta(x)?' · '+meta(x):'');o.dataset.title=x.title||x.name||x.full_name||'Item';item.appendChild(o);});
  smart.hidden=true;
}
function addRouteFields(kind){
 return `<div class="smart-route-grid"><label>Pickup / start<input id="smartPickup" placeholder="Enter pickup or starting point"></label><label>Destination<input id="smartDestination" placeholder="Enter destination"></label></div>`;
}
function renderSmart(){
 const rows=data[type.value]||[]; selected=rows.find(x=>String(x.id)===String(item.value))||null;
 if(!selected){smart.hidden=true;return;}
 smart.hidden=false; smartTitle.textContent=selected.title||selected.name||selected.full_name||'Selected item'; smartBadge.textContent=(icons[type.value]||'•')+' '+(labels[type.value]||type.value);
 let html='';
 const t=type.value;
 if(t==='TAXI'){
   const pricing=selected.pricing_mode||'Standard';
   html+=`<div class="smart-price-row"><strong>${pricing}</strong><span>Minimum ${money(selected.minimum_fare)}</span></div>`;
   html+=`<div class="smart-price-grid"><div><small>Base fare</small><b>${money(selected.base_fare)}</b></div><div><small>Per km</small><b>${money(selected.per_km)}</b></div><div><small>Per minute</small><b>${money(selected.per_minute)}</b></div><div><small>Daily rate</small><b>${money(selected.daily_rate)}</b></div></div>`;
   html+=addRouteFields('TAXI');
   html+=`<div class="smart-route-grid"><label>Estimated distance (km)<input id="smartDistance" type="number" min="0" step="0.1" placeholder="e.g. 12"></label><div class="smart-estimate" id="smartEstimate">Enter distance for an indicative fare.</div></div>`;
   html+=`<p class="smart-help">Pickup and destination are saved into your trip note. Actual taxi fare and booking are confirmed through the existing Taxi service.</p>`;
 } else if(t==='RENTAL'){
   html+=`<div class="smart-vehicle"><div class="smart-vehicle-main"><strong>${esc([selected.make,selected.model,selected.model_year].filter(Boolean).join(' ')||selected.title)}</strong><span>${esc(selected.category_name||selected.body_type||'Commercial vehicle')} · ${esc(selected.seating_capacity||selected.capacity||'—')} capacity</span></div><div class="smart-vehicle-price"><b>${money(selected.rate)}</b><small>${esc(selected.rate_type||'rate')}</small></div></div>`;
   html+=`<div class="smart-detail-grid"><span>Minimum rental <b>${money(selected.minimum_rental)}</b></span><span>Operator <b>${selected.operator_included?'Included':'Optional / not included'}</b></span><span>Provider <b>${esc(selected.provider_name||'—')}</b></span></div>`;
   html+=addRouteFields('RENTAL');
   html+=`<label class="smart-full-field">Rental purpose<input id="smartPurpose" placeholder="e.g. sightseeing, group transfer, event"></label>`;
   if(selected.description||selected.service_notes) html+=`<p class="smart-help">${esc(selected.description||selected.service_notes||'')}</p>`;
 } else if(t==='STAY'){
   html+=`<div class="smart-price-row"><strong>${esc(selected.stay_type||'Stay')}</strong><span>From ${money(selected.starting_price_per_night)}/night</span></div>`;
   if(Array.isArray(selected.rooms)&&selected.rooms.length){html+=`<div class="smart-options"><strong>Choose a room to plan</strong>`;selected.rooms.forEach((r,i)=>{html+=`<label class="smart-option"><input type="radio" name="planner_room" value="${esc(r.id)}" data-label="${esc(r.name)}" data-price="${esc(r.price_per_night)}" ${i===0?'checked':''}><span><b>${esc(r.name)}</b><small>${esc(r.bed_type||'Room')} · up to ${esc(r.max_guests||'—')} guests</small></span><strong>${money(r.price_per_night)}/night</strong></label>`;});html+='</div>';} else html+=`<p class="smart-help">Room types are not currently configured for this stay.</p>`;
 } else if(t==='RESTAURANT'){
   html+=`<div class="smart-price-row"><strong>${esc(selected.cuisine_type||'Restaurant')}</strong><span>${selected.delivery_available?'🚚 Delivery ':''}${selected.pickup_available?'🏪 Pickup':''}</span></div>`;
   if(Array.isArray(selected.menu_items)&&selected.menu_items.length){html+=`<div class="smart-food-head"><strong>Available food</strong><span>Select dishes you may want to eat</span></div><div class="smart-food-list">`;selected.menu_items.forEach(m=>{const price=Number(m.price||0);const disc=Number(m.discount_value||0);let final=price;if(m.discount_type==='PERCENT')final=price-(price*disc/100);else if(m.discount_type==='FLAT')final=price-disc;html+=`<label class="smart-food"><input type="checkbox" value="${esc(m.name)}" data-price="${final}"><span><b>${esc(m.name)}</b><small>${esc(m.category_name||'Menu item')} ${m.is_veg?'· Veg':''}</small></span><strong>${money(final)}</strong></label>`;});html+='</div>';} else html+=`<p class="smart-help">No active menu items are currently listed.</p>`;
 } else if(t==='FRESH_FOOD'){
   html+=`<div class="smart-price-row"><strong>Fresh local store</strong><span>${selected.delivery_available?'🚚 Delivery ':''}${selected.pickup_available?'🏪 Pickup':''}</span></div>`;
   if(Array.isArray(selected.products)&&selected.products.length){html+=`<div class="smart-food-head"><strong>Available fresh food & groceries</strong><span>Select products you may want</span></div><div class="smart-food-list">`;selected.products.forEach(p=>{let final=Number(p.price||0),disc=Number(p.discount_value||0);if(p.discount_type==='PERCENT')final=final-(final*disc/100);else if(p.discount_type==='FLAT')final=final-disc;const available=Number(p.current_quantity||0)>0&&Number(p.is_available??1)!==0;html+=`<label class="smart-food ${available?'':'is-unavailable'}"><input type="checkbox" value="${esc(p.name)}" data-price="${final}" ${available?'':'disabled'}><span><b>${esc(p.name)}</b><small>${esc(p.category_name||'Fresh food')} · ${esc(p.unit||'unit')}</small></span><strong>${money(final)}</strong></label>`;});html+='</div>';} else html+=`<p class="smart-help">No active products are currently listed.</p>`;
 } else if(t==='PACKAGE'){
   const price=selected.discount_price&&Number(selected.discount_price)>0?selected.discount_price:selected.base_price;html+=`<div class="smart-price-row"><strong>${esc(selected.duration_days||'—')} days / ${esc(selected.duration_nights||'—')} nights</strong><span>${money(price)}</span></div><p class="smart-help">${esc(selected.description||'Build this package into your itinerary and review its included destinations before booking.')}</p>`;
 } else if(t==='GUIDE'){
   html+=`<div class="smart-price-row"><strong>${esc(selected.full_name||selected.name||'Local guide')}</strong><span>${money(selected.price_per_day)}/day</span></div><div class="smart-detail-grid"><span>Languages <b>${esc(selected.languages||'—')}</b></span><span>Experience <b>${esc(selected.experience_years||0)} years</b></span><span>Specialization <b>${esc(selected.specializations||'—')}</b></span></div>`;
 } else if(t==='EXPERIENCE'){
   html+=`<div class="smart-price-row"><strong>${esc(selected.experience_type||'Experience')}</strong><span>${money(selected.price)}</span></div><div class="smart-detail-grid"><span>Duration <b>${esc(selected.duration_minutes||'—')} min</b></span><span>Max participants <b>${esc(selected.max_participants||'—')}</b></span><span>Meeting point <b>${esc(selected.meeting_point||'—')}</b></span></div>`;
 } else if(t==='EVENT'){
   html+=`<div class="smart-price-row"><strong>${esc(selected.event_type||'Event / festival')}</strong><span>${esc(selected.ticket_info||'Check event details')}</span></div><div class="smart-detail-grid"><span>When <b>${esc(selected.start_at||'—')}</b></span><span>Venue <b>${esc(selected.venue||'—')}</b></span><span>District <b>${esc(selected.district||selected.city||'—')}</b></span></div>`;
 } else if(t==='DESTINATION'){
   html+=`<div class="smart-price-row"><strong>${esc(selected.district||selected.city||'Manipur')}</strong><span>Suggested stay ${esc(selected.suggested_duration||'—')}</span></div><p class="smart-help">${esc(selected.description||'Add this destination to your route, then add nearby stays, experiences, food and transport.')}</p>`;
 }
 smartContent.innerHTML=html;
 if(t==='TAXI') document.getElementById('smartDistance')?.addEventListener('input',updateTaxiEstimate);
}
function updateTaxiEstimate(){const d=num(document.getElementById('smartDistance')?.value);const base=num(selected?.base_fare),pk=num(selected?.per_km),min=num(selected?.minimum_fare);const estimate=Math.max(min,base+(d*pk));const box=document.getElementById('smartEstimate');if(box)box.innerHTML=d>0?`Indicative fare <b>${money(estimate)}</b> for ~${d.toFixed(1)} km.`:'Enter distance for an indicative fare.';}
function collectDetails(){
 if(!selected)return '';
 const t=type.value, bits=[];
 if(t==='TAXI'||t==='RENTAL'){const p=document.getElementById('smartPickup')?.value.trim(),d=document.getElementById('smartDestination')?.value.trim();if(p)bits.push('Pickup: '+p);if(d)bits.push('Destination: '+d);}
 if(t==='TAXI'){const km=document.getElementById('smartDistance')?.value.trim();if(km)bits.push('Estimated distance: '+km+' km');const est=document.getElementById('smartEstimate')?.querySelector('b')?.textContent;if(est)bits.push('Indicative fare: '+est);}
 if(t==='RENTAL'){const purpose=document.getElementById('smartPurpose')?.value.trim();if(purpose)bits.push('Purpose: '+purpose);bits.push('Rate: '+money(selected.rate)+' / '+(selected.rate_type||'rate'));if(selected.minimum_rental)bits.push('Minimum rental: '+money(selected.minimum_rental));}
 if(t==='STAY'){const r=document.querySelector('input[name="planner_room"]:checked');if(r)bits.push('Room: '+r.dataset.label+' — '+money(r.dataset.price)+'/night');}
 if(t==='RESTAURANT'){const chosen=[...document.querySelectorAll('.smart-food input[type="checkbox"]:checked')].map(x=>x.value);if(chosen.length)bits.push('Food choices: '+chosen.join(', '));}
 if(t==='FRESH_FOOD'){const chosen=[...document.querySelectorAll('.smart-food input[type="checkbox"]:checked')].map(x=>x.value);if(chosen.length)bits.push('Fresh food choices: '+chosen.join(', '));}
 return bits.join(' · ');
}
type.addEventListener('change',renderList);
item.addEventListener('change',()=>{title.value=item.options[item.selectedIndex]?.dataset.title||'';renderSmart();});
document.getElementById('tripAddForm')?.addEventListener('submit',()=>{const extra=collectDetails();if(extra){notes.value=notes.value.trim()?notes.value.trim()+' · '+extra:extra;}});
})();
</script>
<?php include __DIR__ . '/../Shared/member-desktop-footer.php'; ?>
