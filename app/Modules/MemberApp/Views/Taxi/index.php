<?php
declare(strict_types=1);
include __DIR__ . '/../Shared/member-desktop.css.php';
include __DIR__ . '/../Shared/member-desktop-header.php';

$basePath = rtrim((string)config('app.base_path'), '/');
$services = is_array($services ?? null) ? $services : [];
$member = is_array($member ?? null) ? $member : null;
function maTaxiEsc(mixed $v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function maMode(string $m): string { return match(strtoupper($m)){ 'PER_RIDE'=>'Per Ride','PER_KM'=>'Per KM','PER_DAY'=>'Per Day / Rental','PER_KM_WITH_MINIMUM'=>'Per KM with Minimum','CUSTOM_QUOTE'=>'Custom Quote',default=>$m}; }
function maMoney(mixed $v): string { return '₹'.number_format((float)$v, 2); }
?>
<style>
.ma-taxi{max-width:560px;margin:0 auto;padding:14px 14px 100px;font-family:inherit;color:#14251f}.ma-taxi *{box-sizing:border-box}.ma-taxi-head{display:flex;align-items:center;gap:12px;padding:6px 2px 16px}.ma-back{width:40px;height:40px;border:0;border-radius:50%;background:#f1f5f3;font-size:23px;color:#123b31}.ma-taxi-head h1{margin:0;font-size:25px}.ma-taxi-head p{margin:3px 0 0;color:#718078;font-size:13px}.ma-place{margin-left:auto;background:#eaf7f2;border:1px solid #d4eee5;border-radius:22px;padding:10px 13px;font-weight:700;color:#08785f}.ma-hero{border-radius:25px;overflow:hidden;background:linear-gradient(145deg,#0c806a,#145341);padding:24px 20px;color:#fff;min-height:205px;position:relative}.ma-hero:after{content:'';position:absolute;right:-35px;bottom:-50px;width:210px;height:210px;border-radius:50%;background:rgba(255,255,255,.09)}.ma-hero h2{font-size:31px;line-height:1.05;margin:0 0 8px;max-width:300px}.ma-hero p{max-width:290px;line-height:1.5;margin:0;color:#dff4ed}.ma-pills{display:flex;gap:8px;overflow:auto;margin:14px 0 18px;padding-bottom:2px}.ma-pill{border:1px solid #dfeae5;background:#fff;padding:10px 14px;border-radius:22px;white-space:nowrap;font-weight:700;color:#456057}.ma-pill.active{background:#087d64;color:#fff;border-color:#087d64}.ma-card{background:#fff;border:1px solid #e6eeea;border-radius:20px;padding:16px;margin-top:12px;box-shadow:0 6px 20px rgba(17,55,44,.06)}.ma-card h3{margin:0 0 5px;font-size:16px}.ma-card small{color:#73827c}.ma-rule{margin-top:10px;padding:10px 11px;background:#f5faf8;border-radius:13px;font-size:12px;color:#49665d;line-height:1.55}.ma-rule b{color:#126d59}.ma-form{margin-top:16px}.ma-label{display:block;font-size:12px;font-weight:800;color:#53655f;margin:12px 0 6px}.ma-input{width:100%;padding:14px;border:1px solid #dce7e2;border-radius:14px;background:#fbfdfc;font:inherit;color:#14251f}.ma-grid2{display:grid;grid-template-columns:1fr 1fr;gap:10px}.ma-trip{display:grid;grid-template-columns:1fr 1fr;background:#f0f5f3;border-radius:15px;padding:4px}.ma-trip label{padding:11px;text-align:center;border-radius:12px;font-weight:700;color:#566b64;cursor:pointer}.ma-trip input{display:none}.ma-trip input:checked+span{display:block;background:#087d64;color:#fff;padding:11px;border-radius:12px;margin:-11px}.ma-location-row{position:relative}.ma-loc-btn{position:absolute;right:7px;top:7px;border:0;background:#eaf7f2;color:#087d64;border-radius:10px;padding:9px;font-weight:700}.ma-submit{width:100%;border:0;background:#087d64;color:#fff;border-radius:15px;padding:15px;font-size:16px;font-weight:800;margin-top:17px}.ma-note{font-size:12px;color:#718078;line-height:1.5;margin:10px 2px}.ma-nav{position:fixed;left:50%;bottom:10px;transform:translateX(-50%);width:min(540px,calc(100% - 20px));background:rgba(255,255,255,.96);border:1px solid #e1ebe7;box-shadow:0 10px 30px rgba(0,0,0,.12);border-radius:22px;display:grid;grid-template-columns:repeat(4,1fr);padding:7px;z-index:30}.ma-nav a{text-decoration:none;color:#73827c;text-align:center;padding:8px 3px;font-size:11px;font-weight:700}.ma-nav a.active{color:#087d64}.ma-nav i{display:block;font-style:normal;font-size:20px;margin-bottom:2px}
@media(max-width:430px){.ma-grid2{grid-template-columns:1fr}.ma-hero h2{font-size:27px}}

.leaflet-css-note{}
.ma-location-actions{display:flex;gap:7px;margin-top:7px}.ma-loc-btn,.ma-map-btn{position:static;flex:1;border:1px solid #d6e7e0;background:#eaf7f2;color:#087d64;border-radius:10px;padding:9px 10px;font-weight:800;font-size:12px;cursor:pointer}.ma-map-btn{background:#087d64;color:#fff;border-color:#087d64}.ma-coordinates{margin-top:6px;padding:8px 10px;background:#f6faf8;border:1px dashed #d5e5df;border-radius:10px;font-size:11px;color:#61766e}.ma-coordinates strong{color:#173f35}.ma-map-modal{position:fixed;inset:0;background:rgba(10,28,23,.58);display:none;align-items:flex-end;justify-content:center;z-index:10000;padding:12px;pointer-events:none}.ma-map-modal.open{display:flex;pointer-events:auto}.ma-map-sheet{position:relative;z-index:10001;width:min(560px,100%);max-height:calc(100vh - 24px);max-height:calc(100dvh - 24px);display:flex;flex-direction:column;background:#fff;border-radius:24px 24px 14px 14px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.28)}.ma-map-head{position:relative;z-index:10003;display:flex;align-items:center;justify-content:space-between;flex:0 0 auto;padding:14px 16px;border-bottom:1px solid #e8efec;background:#fff}.ma-map-head h3{margin:0;font-size:17px}.ma-map-head p{margin:3px 0 0;font-size:11px;color:#718078}.ma-map-close{position:relative;z-index:10004;border:0;background:#eef5f2;width:36px;height:36px;border-radius:50%;font-size:20px;cursor:pointer;pointer-events:auto;touch-action:manipulation}.ma-map{position:relative;z-index:1;flex:1 1 auto;min-height:260px;width:100%;height:390px;background:#edf3f1}.ma-map-help{position:relative;z-index:10003;flex:0 0 auto;padding:10px 14px;font-size:11px;color:#687a74;background:#fff}.ma-map-confirm{position:relative;z-index:10004;display:block;width:calc(100% - 28px);flex:0 0 auto;margin:0 14px 14px;border:0;background:#087d64;color:#fff;border-radius:14px;padding:14px;font-weight:900;font-size:15px;cursor:pointer;pointer-events:auto;touch-action:manipulation;-webkit-tap-highlight-color:transparent}.ma-map-confirm:disabled{opacity:.55;cursor:not-allowed}.ma-pin-readout{position:relative;z-index:10003;flex:0 0 auto;padding:0 14px 12px;font-size:11px;color:#50675f;background:#fff}.ma-pin-readout b{color:#123d33}.ma-taxi-pin{background:transparent!important;border:0!important}.ma-taxi-pin-dot{position:absolute;left:8px;top:2px;width:18px;height:18px;border-radius:50% 50% 50% 0;background:#e53935;transform:rotate(-45deg);box-shadow:0 2px 5px rgba(0,0,0,.28)}.ma-taxi-pin-dot:after{content:'';position:absolute;left:6px;top:6px;width:6px;height:6px;border-radius:50%;background:#fff}.ma-taxi-pin-point{position:absolute;left:14px;top:27px;width:6px;height:8px;background:#e53935;border-radius:0 0 5px 5px;transform:rotate(0deg);box-shadow:0 2px 3px rgba(0,0,0,.18)}.ma-map .leaflet-control{z-index:500}.ma-map .leaflet-pane{z-index:400}@media(max-width:430px){.ma-map{height:auto;min-height:260px}}
</style>
<div class="ma-taxi">
  <header class="ma-taxi-head"><a class="ma-back" href="<?=maTaxiEsc($basePath)?>/member">‹</a><div><h1>Taxi</h1><p>Travel your way · local &amp; Northeast</p></div><span class="ma-place">📍 Imphal</span></header>
  <section class="ma-hero"><h2>Book a Taxi</h2><p>Choose a service, tell us your trip details, then select an available taxi.</p></section>
  <div class="ma-pills" id="serviceFilters">
    <button class="ma-pill active" type="button" data-filter="">All</button><button class="ma-pill" type="button" data-filter="Outstation">Outstation</button><button class="ma-pill" type="button" data-filter="Airport Transfer">Airport</button><button class="ma-pill" type="button" data-filter="Local">Local</button><button class="ma-pill" type="button" data-filter="Scheduled">Scheduled</button>
  </div>
  <form class="ma-form" method="get" action="<?=maTaxiEsc($basePath)?>/member/taxi/vehicles" id="taxiSearchForm">
    <label class="ma-label">Choose Taxi Service</label>
    <div id="serviceCards">
    <?php foreach($services as $i=>$service): ?>
      <label class="ma-card service-card" data-type="<?=maTaxiEsc($service['service_type'])?>" style="display:block;cursor:pointer">
        <input type="radio" name="service_id" value="<?=maTaxiEsc($service['id'])?>" <?= $i===0?'checked':'' ?> required style="accent-color:#087d64;float:right">
        <h3><?=maTaxiEsc($service['name'])?></h3><small><?=maTaxiEsc($service['service_type'])?> · <?=maMode((string)$service['pricing_mode'])?></small>
        <div class="ma-rule"><b>Pricing:</b> <?php if($service['pricing_mode']==='PER_RIDE'): ?><?=maMoney($service['base_fare'])?> base · <?=number_format((float)$service['included_km'],2)?> km included<?php elseif($service['pricing_mode']==='PER_KM'||$service['pricing_mode']==='PER_KM_WITH_MINIMUM'): ?><?=maMoney($service['base_fare'])?> base · <?=maMoney($service['per_km'])?>/km<?php elseif($service['pricing_mode']==='PER_DAY'): ?><?=maMoney($service['daily_rate'])?>/day<?php else: ?>Manual quote<?php endif; ?><?php if((float)$service['extra_km_rate']>0): ?> · <?=maMoney($service['extra_km_rate'])?>/km extra<?php endif; ?><?php if((float)$service['minimum_fare']>0): ?> · min <?=maMoney($service['minimum_fare'])?><?php endif; ?></div>
      </label>
    <?php endforeach; ?>
    <?php if(!$services): ?><div class="ma-card">No active taxi services are available right now.</div><?php endif; ?>
    </div>
    <label class="ma-label">Trip</label>
    <div class="ma-trip"><label><input type="radio" name="trip_type" value="ROUND_TRIP" checked><span>Round Trip</span></label><label><input type="radio" name="trip_type" value="ONE_WAY"><span>One Way</span></label></div>
    <label class="ma-label">Pickup Location</label>
    <div class="ma-location-row"><input class="ma-input" name="pickup_address" id="pickup_address" placeholder="e.g. Imphal, Manipur" required></div>
    <div class="ma-location-actions"><button class="ma-map-btn" type="button" data-map-target="pickup">📍 Select on Map</button><button class="ma-loc-btn" type="button" data-geo="pickup">Use my location</button></div>
    <div class="ma-coordinates" id="pickup_coordinates">Coordinates: <strong>Not selected</strong></div>
    <input type="hidden" name="pickup_lat" id="pickup_lat"><input type="hidden" name="pickup_lng" id="pickup_lng">

    <label class="ma-label">Destination</label>
    <div class="ma-location-row"><input class="ma-input" name="destination_address" id="destination_address" placeholder="e.g. Imphal Airport" required></div>
    <div class="ma-location-actions"><button class="ma-map-btn" type="button" data-map-target="destination">📍 Select on Map</button><button class="ma-loc-btn" type="button" data-geo="destination">Use my location</button></div>
    <div class="ma-coordinates" id="destination_coordinates">Coordinates: <strong>Not selected</strong></div>
    <input type="hidden" name="destination_lat" id="destination_lat"><input type="hidden" name="destination_lng" id="destination_lng">
    <div class="ma-grid2"><div><label class="ma-label">Pickup Date</label><input class="ma-input" type="date" name="pickup_date" required></div><div><label class="ma-label">Pickup Time</label><input class="ma-input" type="time" name="pickup_time" required></div></div>
    <div id="returnFields"><div class="ma-grid2"><div><label class="ma-label">Return Date</label><input class="ma-input" type="date" name="return_date"></div><div><label class="ma-label">Return Time</label><input class="ma-input" type="time" name="return_time"></div></div></div>
    <label class="ma-label">Trip Purpose</label><select class="ma-input" name="trip_purpose"><option>Leisure</option><option>Business</option><option>Family</option><option>Other</option></select>
    <label class="ma-label">Additional Requirements <small>(optional)</small></label><textarea class="ma-input" name="notes" rows="3" placeholder="Driver stay, extra stops, child seat, etc."></textarea>
    <p class="ma-note">We show service pricing rules before you choose. After you confirm a booking, the route distance and ETA are calculated in the background and the rough estimated fare is added to your booking.</p>
    <button class="ma-submit" type="submit">Search Available Taxis →</button>
  </form>
</div>

<div class="ma-map-modal" id="memberTaxiMapModal" aria-hidden="true">
  <div class="ma-map-sheet" role="dialog" aria-modal="true" aria-labelledby="memberTaxiMapTitle">
    <div class="ma-map-head">
      <div><h3 id="memberTaxiMapTitle">Select Location</h3><p>Tap the map or drag the pin to choose the exact point.</p></div>
      <button class="ma-map-close" type="button" id="memberTaxiMapClose" aria-label="Close map">×</button>
    </div>
    <div class="ma-map" id="memberTaxiMap"></div>
    <div class="ma-pin-readout" id="memberTaxiPinReadout">Coordinates: <b>Not selected</b></div>
    <div class="ma-map-help">Your selected coordinates will be sent with the taxi search so the route can be calculated accurately after booking.</div>
    <button class="ma-map-confirm" type="button" id="memberTaxiMapConfirm">Use This Location</button>
  </div>
</div>

<?php include __DIR__ . '/../Shared/restaurant-cart-widget.php'; ?>
<?php include __DIR__ . '/../Shared/member-notification-widget.php'; ?>
<nav class="ma-nav"><a href="<?=maTaxiEsc($basePath)?>/member"><i>⌂</i>Home</a><a class="active" href="<?=maTaxiEsc($basePath)?>/member/taxi"><i>🚕</i>Services</a><a href="<?=maTaxiEsc($basePath)?>/member/bookings"><i>▣</i>Bookings</a><a href="<?=maTaxiEsc($basePath)?>/member/profile"><i>♙</i>Profile</a></nav>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIINfQ3p4f2QfM4L1M4lQ8M5k6k3X9m1p2M=" crossorigin="">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
(()=>{
  const f=document.getElementById('serviceFilters'),cards=[...document.querySelectorAll('.service-card')];
  f?.addEventListener('click',e=>{const b=e.target.closest('[data-filter]');if(!b)return;document.querySelectorAll('.ma-pill').forEach(x=>x.classList.remove('active'));b.classList.add('active');const q=b.dataset.filter;cards.forEach(c=>c.style.display=!q||c.dataset.type===q?'block':'none')});

  const ret=document.getElementById('returnFields');
  document.querySelectorAll('input[name=trip_type]').forEach(r=>r.addEventListener('change',()=>{ret.style.display=document.querySelector('input[name=trip_type]:checked').value==='ROUND_TRIP'?'block':'none'}));

  const modal=document.getElementById('memberTaxiMapModal');
  const closeBtn=document.getElementById('memberTaxiMapClose');
  const confirmBtn=document.getElementById('memberTaxiMapConfirm');
  const title=document.getElementById('memberTaxiMapTitle');
  const readout=document.getElementById('memberTaxiPinReadout');
  let map=null,marker=null,target=null,pending=null;
  const pinIcon=L.divIcon({className:'ma-taxi-pin',html:'<span class="ma-taxi-pin-dot"></span><span class="ma-taxi-pin-point"></span>',iconSize:[34,42],iconAnchor:[17,40],popupAnchor:[0,-40]});
  const imphal=[24.8170,93.9368];

  function setCoords(which,lat,lng){
    document.getElementById(which+'_lat').value=Number(lat).toFixed(7);
    document.getElementById(which+'_lng').value=Number(lng).toFixed(7);
    document.getElementById(which+'_coordinates').innerHTML='Coordinates: <strong>'+Number(lat).toFixed(7)+', '+Number(lng).toFixed(7)+'</strong>';
  }
  function ensureMap(){
    if(map)return;
    map=L.map('memberTaxiMap',{zoomControl:true}).setView(imphal,12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19,attribution:'© OpenStreetMap contributors'}).addTo(map);
    map.on('click',e=>placePending(e.latlng.lat,e.latlng.lng));
  }
  function placePending(lat,lng){
    pending={lat,lng};
    confirmBtn.disabled=false;
    confirmBtn.textContent='Use This Location';
    if(!marker){marker=L.marker([lat,lng],{draggable:true,icon:pinIcon,title:'Selected location',keyboard:true}).addTo(map);marker.on('dragend',()=>{const p=marker.getLatLng();placePending(p.lat,p.lng,false)});}
    else marker.setLatLng([lat,lng]);
    readout.innerHTML='Coordinates: <b>'+Number(lat).toFixed(7)+', '+Number(lng).toFixed(7)+'</b>';
  }
  function openMap(which){
    target=which;
    confirmBtn.disabled=true;
    confirmBtn.textContent='Select a location on the map';title.textContent=which==='pickup'?'Select Pickup Location':'Select Destination';modal.classList.add('open');modal.setAttribute('aria-hidden','false');
    ensureMap();
    setTimeout(()=>map.invalidateSize(),80);
    const lat=parseFloat(document.getElementById(which+'_lat').value),lng=parseFloat(document.getElementById(which+'_lng').value);
    if(Number.isFinite(lat)&&Number.isFinite(lng)){map.setView([lat,lng],15);placePending(lat,lng)}else{map.setView(imphal,12);pending=null;if(marker){marker.remove();marker=null}readout.innerHTML='Coordinates: <b>Not selected</b>';}
  }
  function closeMap(){modal.classList.remove('open');modal.setAttribute('aria-hidden','true');target=null;pending=null;confirmBtn.disabled=true;confirmBtn.textContent='Select a location on the map'}
  document.querySelectorAll('[data-map-target]').forEach(b=>b.addEventListener('click',()=>openMap(b.dataset.mapTarget)));
  closeBtn.addEventListener('click',closeMap);
  modal.addEventListener('click',e=>{if(e.target===modal)closeMap()});
  document.addEventListener('keydown',e=>{if(e.key==='Escape'&&modal.classList.contains('open'))closeMap()});
  confirmBtn.addEventListener('click',()=>{if(!target||!pending){alert('Please tap the map or drag the pin to select a location.');return}setCoords(target,pending.lat,pending.lng);closeMap()});

  document.querySelectorAll('[data-geo]').forEach(btn=>btn.addEventListener('click',()=>{
    if(!navigator.geolocation){alert('Location is not available. Please select the point on the map.');return}
    const which=btn.dataset.geo;
    btn.disabled=true;btn.textContent='Locating…';
    navigator.geolocation.getCurrentPosition(pos=>{
      const lat=pos.coords.latitude,lng=pos.coords.longitude;
      setCoords(which,lat,lng);
      if(which==='pickup'&&!document.getElementById('pickup_address').value)document.getElementById('pickup_address').value='Current location';
      btn.disabled=false;btn.textContent='Use my location';
    },()=>{alert('Could not access your location. Please select the point on the map.');btn.disabled=false;btn.textContent='Use my location';},{enableHighAccuracy:true,timeout:10000,maximumAge:30000});
  }));

  document.getElementById('taxiSearchForm')?.addEventListener('submit',e=>{
    const fields=['pickup','destination'];
    for(const which of fields){if(!document.getElementById(which+'_lat').value||!document.getElementById(which+'_lng').value){e.preventDefault();alert('Please select '+(which==='pickup'?'pickup':'destination')+' location on the map or use your location.');return;}}
  });
})();
</script>
