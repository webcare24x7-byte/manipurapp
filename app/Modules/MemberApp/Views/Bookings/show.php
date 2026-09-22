<?php
declare(strict_types=1);
include __DIR__ . '/../Shared/member-desktop.css.php';
include __DIR__ . '/../Shared/member-desktop-header.php';


$basePath = rtrim((string) config('app.base_path'), '/');
$b = is_array($booking ?? null) ? $booking : [];
$csrfToken = (string) ($csrf ?? '');

function maSe(mixed $v): string { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); }
function maSm(mixed $v): string { return '₹' . number_format((float) $v, 2); }
function maDt(?string $v, string $fallback = '—'): string {
    if (!$v) return $fallback;
    try { return (new DateTime($v))->format('d M Y · h:i A'); } catch (Throwable) { return $v; }
}

$displayDistance = $b['total_distance_km'] ?? $b['distance_km'] ?? null;
$displayEta = $b['total_eta_minutes'] ?? $b['eta_minutes'] ?? null;
$committed = !empty($b['route_estimate_committed_at']);
$fare = (float) ($b['fare'] ?? 0);
$mode = strtoupper((string) ($b['pricing_mode'] ?? ''));
$fareResult = is_array($b['fare_result'] ?? null) ? $b['fare_result'] : null;
$items = is_array($fareResult['line_items'] ?? null) ? $fareResult['line_items'] : [];
$tripType = strtoupper((string) ($b['trip_type'] ?? 'ONE_WAY'));
$status = (string) ($b['status'] ?? 'Pending');
$history = is_array($b['status_history'] ?? null) ? $b['status_history'] : [];

$statusNotes = [
    'Pending' => 'Your booking has been received. The taxi business is reviewing your request.',
    'Confirmed' => 'Your booking has been confirmed. A driver and vehicle will be assigned next.',
    'Assigned' => 'A driver and vehicle have been assigned to your booking.',
    'Driver Arrived' => 'Your driver has arrived at the pickup location.',
    'In Progress' => 'Your driver has started the trip. Have a safe journey.',
    'Completed' => 'Your taxi trip has been completed.',
    'Cancelled' => 'This booking was cancelled. Route and fare processing has been stopped.',
    'No Show' => 'This booking was marked as No Show by the taxi business.',
];
$driverName = trim((string) ($b['driver_name'] ?? ''));
$vehicleName = trim((string) (($b['make'] ?? '') . ' ' . ($b['model'] ?? '')));
$vehicleName = $vehicleName !== '' ? $vehicleName : ((string) ($b['vehicle_type'] ?? 'Taxi'));
?>
<style>
.ma-bshow{max-width:560px;margin:auto;padding:11px 12px 92px;color:#14251f}.ma-bshow *{box-sizing:border-box}
.bs-head{display:flex;gap:9px;align-items:center;margin-bottom:7px}.bs-head a{font-size:26px;text-decoration:none;color:#173c32;line-height:1}.bs-head h1{margin:0;font-size:20px}.bs-head p{margin:1px 0;color:#75847e;font-size:9px}
.bs-status{margin:8px 0;background:#eaf7f2;color:#087d64;border-radius:13px;padding:10px 12px}.bs-status strong{display:block;font-size:14px}.bs-status small{display:block;margin-top:2px;color:#5f756c;font-size:10px;line-height:1.4}
.bs-status.confirmed{background:#eef7ff;color:#145b8c}.bs-status.assigned{background:#f2efff;color:#5b43a4}.bs-status.arrived{background:#fff7e8;color:#8a5c00}.bs-status.progress{background:#eaf3ff;color:#145b8c}.bs-status.completed{background:#eaf7f2;color:#087d64}.bs-status.cancelled,.bs-status.noshow{background:#fff0f0;color:#a33b3b}
.bs-card{background:#fff;border:1px solid #e1ebe6;border-radius:14px;padding:10px;margin:7px 0}.bs-card h2{font-size:12px;margin:0 0 7px}
.bs-grid{display:grid;grid-template-columns:1fr 1fr;gap:6px}.bs-box{background:#f5f9f7;border-radius:9px;padding:7px 8px;font-size:10px;min-width:0}.bs-box span{display:block;color:#7a8983;font-size:8px;margin-bottom:2px}.bs-box b{display:block;line-height:1.35}
.bs-box.full{grid-column:1/-1}.bs-route{display:grid;grid-template-columns:15px 1fr;gap:6px}.bs-dot{color:#087d64;font-size:9px;padding-top:2px}.bs-route div{padding-bottom:7px;border-bottom:1px solid #edf2f0}.bs-route div:last-child{border:0;padding-bottom:0}.bs-label{font-size:8px;color:#7b8984;text-transform:uppercase;font-weight:800}.bs-value{font-size:11px;font-weight:800;margin-top:2px;line-height:1.35}
.bs-service-title{font-size:14px;font-weight:900}.bs-service-sub{font-size:9px;color:#72827b;margin-top:1px}.bs-rule-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:5px;margin-top:7px}.bs-rule{background:#f7faf8;border-radius:8px;padding:6px 7px}.bs-rule span{display:block;font-size:8px;color:#7a8983}.bs-rule b{display:block;font-size:10px;margin-top:1px}.bs-rule-note{margin-top:7px;background:#f3f8f5;border-radius:9px;padding:7px 8px;color:#5f7169;font-size:9px;line-height:1.4}
.bs-fare{font-size:22px;font-weight:950;color:#087d64}.bs-fare small{display:block;color:#73837c;font-size:9px;font-weight:600;line-height:1.4;margin-top:3px}.bs-fare-list{margin-top:7px;border-top:1px solid #edf2f0}.bs-fare-row{display:flex;justify-content:space-between;gap:7px;padding:5px 0;border-bottom:1px solid #edf2f0;font-size:9px}.bs-fare-row span:first-child{color:#61736b;line-height:1.35}.bs-fare-row b{white-space:nowrap}.bs-fare-row small{font-size:8px;color:#8a9892}.bs-fare-row.total{font-size:11px;font-weight:900;border-bottom:0;padding-bottom:0}
.bs-driver{display:flex;align-items:center;gap:9px}.bs-driver-photo{width:42px;height:42px;border-radius:50%;object-fit:cover;background:#edf4f1;border:1px solid #dbe8e3}.bs-driver-placeholder{display:flex;align-items:center;justify-content:center;font-size:19px}.bs-driver-name{font-size:12px;font-weight:900}.bs-driver-meta{font-size:9px;color:#71817a;margin-top:2px}.bs-phone{margin-left:auto;text-decoration:none;background:#eaf7f2;color:#087d64;border-radius:9px;padding:7px 9px;font-size:10px;font-weight:800}
.bs-timeline{position:relative;padding-left:20px}.bs-timeline:before{content:"";position:absolute;left:5px;top:5px;bottom:5px;width:1px;background:#dbe7e2}.bs-event{position:relative;padding:0 0 9px}.bs-event:last-child{padding-bottom:0}.bs-event:before{content:"";position:absolute;left:-18px;top:3px;width:8px;height:8px;border-radius:50%;background:#b7c7c1;border:2px solid #fff;box-shadow:0 0 0 1px #cbdad4}.bs-event.current:before{background:#087d64;box-shadow:0 0 0 1px #087d64}.bs-event-title{font-size:10px;font-weight:850}.bs-event-time{font-size:8px;color:#7a8983;margin-top:1px}.bs-event-note{font-size:8px;color:#87948f;margin-top:1px}
.bs-support{background:#fff8e9;border-color:#efdfb6}.bs-support p{margin:0;color:#6f6250;font-size:9px;line-height:1.45}
.bs-processing{background:#fff8e9;border-color:#efdfb6}.bs-btn{width:100%;border:0;border-radius:10px;padding:10px;font-weight:800;font-size:11px}.bs-danger{background:#fff0f0;color:#a33b3b}
.bs-nav{position:fixed;left:50%;bottom:6px;transform:translateX(-50%);width:min(540px,calc(100% - 14px));background:rgba(255,255,255,.97);border:1px solid #e1ebe7;box-shadow:0 7px 22px rgba(0,0,0,.1);border-radius:18px;display:grid;grid-template-columns:repeat(4,1fr);padding:4px;z-index:30}.bs-nav a{text-decoration:none;color:#73827c;text-align:center;padding:5px 3px;font-size:9px;font-weight:700}.bs-nav a.active{color:#087d64}.bs-nav i{display:block;font-style:normal;font-size:17px;margin-bottom:0}
@media(min-width:430px){.bs-rule-grid{grid-template-columns:repeat(3,1fr)}}
</style>

<div class="ma-bshow">
<header class="bs-head"><a href="<?=maSe($basePath)?>/member/bookings">‹</a><div><h1>Booking Details</h1><p><?=maSe($b['booking_no']??'')?></p></div></header>

<?php
$statusClass = match($status) {
    'Confirmed' => 'confirmed', 'Assigned' => 'assigned', 'Driver Arrived' => 'arrived',
    'In Progress' => 'progress', 'Completed' => 'completed', 'Cancelled' => 'cancelled', 'No Show' => 'noshow', default => ''
};
?>
<div class="bs-status <?=maSe($statusClass)?>" id="statusBox">
    <strong id="statusText"><?=maSe($status)?></strong>
    <small id="statusNote"><?=maSe($statusNotes[$status] ?? 'Your booking status has been updated.')?></small>
</div>

<section class="bs-card">
<h2>Booking &amp; Trip Time</h2>
<div class="bs-grid">
    <div class="bs-box"><span>Booked on</span><b id="bookedOn"><?=maDt($b['created_at']??null)?></b></div>
    <div class="bs-box"><span>Trip</span><b><?=maSe($tripType==='ROUND_TRIP'?'Round Trip':'One Way')?></b></div>
    <div class="bs-box"><span><?=($b['booking_type']??'Immediate')==='Scheduled'?'Scheduled trip':'Trip time'?></span><b id="tripTime"><?=($b['booking_type']??'Immediate')==='Scheduled'?maDt($b['scheduled_at']??null):'Immediate'?></b></div>
    <?php if(!empty($b['return_scheduled_at'])):?><div class="bs-box"><span>Return</span><b id="returnTime"><?=maDt($b['return_scheduled_at'])?></b></div><?php endif;?>
    <?php if($status==='Cancelled'):?><div class="bs-box full"><span>Cancelled on</span><b id="cancelledOn"><?=maDt($b['cancelled_at']??null)?></b></div><?php endif;?>
</div>
</section>

<section class="bs-card"><h2>Trip</h2><div class="bs-route">
<span class="bs-dot">●</span><div><span class="bs-label">Pickup</span><div class="bs-value"><?=maSe($b['pickup_address']??'')?></div></div>
<span class="bs-dot">●</span><div><span class="bs-label">Destination</span><div class="bs-value"><?=maSe($b['destination_address']??'')?></div></div>
</div></section>

<section class="bs-card" id="assignmentCard" <?=($driverName || !empty($b['vehicle_id'])) ? '' : 'hidden'?>>
<h2>Assigned Driver &amp; Vehicle</h2>
<div id="assignmentContent">
<?php if($driverName): ?>
<div class="bs-driver">
<?php if(!empty($b['driver_photo_path'])): ?><img class="bs-driver-photo" src="<?=maSe($basePath.'/'.ltrim((string)$b['driver_photo_path'],'/'))?>" alt="Driver"><?php else: ?><div class="bs-driver-photo bs-driver-placeholder">👤</div><?php endif;?>
<div><div class="bs-driver-name"><?=maSe($driverName)?></div><div class="bs-driver-meta">Assigned driver<?php if(!empty($b['driver_phone'])):?> · <?=maSe($b['driver_phone'])?><?php endif;?></div></div>
<?php if(!empty($b['driver_phone'])):?><a class="bs-phone" href="tel:<?=maSe($b['driver_phone'])?>">Call</a><?php endif;?>
</div>
<?php endif;?>
<?php if($driverName && !empty($b['vehicle_id'])):?><div style="height:6px"></div><?php endif;?>
<?php if(!empty($b['vehicle_id'])): ?>
<div class="bs-grid">
<div class="bs-box"><span>Vehicle</span><b><?=maSe($vehicleName)?></b></div>
<?php if(!empty($b['registration_no'])):?><div class="bs-box"><span>Registration</span><b><?=maSe($b['registration_no'])?></b></div><?php endif;?>
<div class="bs-box"><span>Seats</span><b><?=maSe($b['seating_capacity']??'—')?></b></div>
</div>
<?php endif;?>
</div>
</section>

<section class="bs-card">
<h2>Selected Taxi Service</h2>
<div class="bs-service-title"><?=maSe($b['service_name']??'Taxi')?></div>
<div class="bs-service-sub"><?=maSe($b['service_type']??'')?> · <?=maSe($fareResult['pricing_mode_label']??($b['pricing_mode']??''))?></div>
<div class="bs-rule-grid">
<?php foreach([
    ['Base Fare',$b['base_fare']??0,'money'],['Per KM',$b['per_km']??0,'money'],['Per Minute',$b['per_minute']??0,'money'],
    ['Minimum Fare',$b['minimum_fare']??0,'money'],['Included KM',$b['included_km']??0,'km'],['Extra KM Rate',$b['extra_km_rate']??0,'money'],['Daily Rate',$b['daily_rate']??0,'money']
] as [$label,$value,$type]): ?>
<div class="bs-rule"><span><?=maSe($label)?></span><b><?= $type==='km' ? number_format((float)$value,2).' km' : maSm($value)?></b></div>
<?php endforeach;?>
</div>
<div class="bs-rule-note">These are the fare rules configured by this taxi business for this service. Your booking uses this service's rules; automatic pricing is calculated by the system.</div>
</section>

<section class="bs-card">
<h2>Route &amp; Fare</h2>
<div class="bs-grid">
<div class="bs-box"><span>Estimated road distance</span><b id="distance"><?=($displayDistance!==null&&$displayDistance!=='')?maSe(number_format((float)$displayDistance,2).' km'):'Calculating…'?></b></div>
<div class="bs-box"><span>Estimated driving time</span><b id="eta"><?=($displayEta!==null&&$displayEta!=='')?maSe((string)$displayEta.' min'):'Calculating…'?></b></div>
</div>
<div style="margin-top:8px">
<div class="bs-fare" id="fare"><?= $fare>0?maSm($fare):($mode==='CUSTOM_QUOTE'?'Quote pending':'Calculating…') ?><small id="fareNote"><?= $fare>0?'Estimated fare based on the selected taxi service pricing rules.':($mode==='CUSTOM_QUOTE'?'This service uses Custom Quote pricing. Our team will confirm the fare.':'The route estimate is being processed behind the scenes.')?></small></div>
<div class="bs-fare-list" id="fareBreakdown" <?=empty($items)?'hidden':''?>>
<?php foreach($items as $item): ?><div class="bs-fare-row"><span><?=maSe($item['label']??'Fare component')?><?php if(!empty($item['meta'])):?><br><small><?=maSe($item['meta'])?></small><?php endif;?></span><b><?=maSm($item['amount']??0)?></b></div><?php endforeach;?>
<div class="bs-fare-row total"><span>Total Estimated Fare</span><b><?= $fare>0?maSm($fare):'—'?></b></div>
</div></div>
</section>

<?php if($history): ?>
<section class="bs-card"><h2>Booking Timeline</h2><div class="bs-timeline" id="timeline">
<?php foreach($history as $event): ?>
<div class="bs-event <?php if (($event['new_status'] ?? '') === $status): ?>current<?php endif; ?>">
<div class="bs-event-title"><?=maSe($event['new_status']??'Status updated')?></div>
<div class="bs-event-time"><?=maDt($event['created_at']??null)?></div>
<?php if(!empty($event['notes'])):?><div class="bs-event-note"><?=maSe($event['notes'])?></div><?php endif;?>
</div>
<?php endforeach;?>
</div></section>
<?php else: ?>
<section class="bs-card" id="timelineCard" hidden><h2>Booking Timeline</h2><div class="bs-timeline" id="timeline"></div></section>
<?php endif; ?>

<section class="bs-card bs-support" id="routeSupport" hidden><h2>Route information unavailable</h2><p>We could not determine the pickup or destination coordinates for this booking. Please contact ManipurApp Support so we can help complete your booking.</p></section>

<?php if(in_array($status,['Pending','Confirmed'],true)):?><section class="bs-card" id="cancelCard"><button class="bs-btn bs-danger" id="cancelBtn" type="button">Cancel Booking</button></section><?php endif;?>
</div>

<?php include __DIR__ . '/../Shared/restaurant-cart-widget.php'; ?>
<?php include __DIR__ . '/../Shared/member-notification-widget.php'; ?>
<nav class="bs-nav"><a href="<?=maSe($basePath)?>/member"><i>⌂</i>Home</a><a href="<?=maSe($basePath)?>/member/taxi"><i>🚕</i>Services</a><a class="active" href="<?=maSe($basePath)?>/member/bookings"><i>▣</i>Bookings</a><a href="<?=maSe($basePath)?>/member/profile"><i>♙</i>Profile</a></nav>

<script>
(()=>{
const initialStatus=<?=json_encode($status)?>,token=<?=json_encode($csrfToken)?>,id=<?=json_encode((int)($b['id']??0))?>,base=<?=json_encode($basePath)?>;
let routeController=null,pollTimer=null,routeStarted=false,terminal=['Completed','Cancelled','No Show'];
const statusNotes=<?=json_encode($statusNotes)?>;
const money=v=>'₹'+Number(v||0).toLocaleString('en-IN',{minimumFractionDigits:2,maximumFractionDigits:2});
const esc=s=>String(s??'').replace(/[&<>"']/g,m=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]));
function formatDt(v){if(!v)return '—';const d=new Date(String(v).replace(' ','T'));if(Number.isNaN(d.getTime()))return String(v);return d.toLocaleDateString('en-GB',{day:'2-digit',month:'short',year:'numeric'})+' · '+d.toLocaleTimeString('en-US',{hour:'2-digit',minute:'2-digit'});}
function statusClass(s){return ({Confirmed:'confirmed',Assigned:'assigned','Driver Arrived':'arrived','In Progress':'progress',Completed:'completed',Cancelled:'cancelled','No Show':'noshow'})[s]||'';}
function renderTimeline(history,status){
 const card=document.getElementById('timelineCard'),el=document.getElementById('timeline');
 if(!el)return;
 const rows=Array.isArray(history)?history:[];
 if(!rows.length){if(card)card.hidden=true;return;}
 if(card)card.hidden=false;
 el.innerHTML=rows.map(x=>'<div class="bs-event '+(x.new_status===status?'current':'')+'"><div class="bs-event-title">'+esc(x.new_status||'Status updated')+'</div><div class="bs-event-time">'+esc(formatDt(x.created_at))+'</div>'+(x.notes?'<div class="bs-event-note">'+esc(x.notes)+'</div>':'')+'</div>').join('');
}
function renderAssignment(b){
 const card=document.getElementById('assignmentCard'),el=document.getElementById('assignmentContent');
 if(!card||!el)return;
 const driverName=String(b.driver_name||'').trim();
 const hasVehicle=!!b.vehicle_id;
 if(!driverName&&!hasVehicle){card.hidden=true;el.innerHTML='';return;}
 card.hidden=false;
 let html='';
 if(driverName){
   const photo=String(b.driver_photo_path||'').trim();
   const phone=String(b.driver_phone||'').trim();
   html+='<div class="bs-driver">';
   html+=photo?'<img class="bs-driver-photo" src="'+esc(base+'/'+photo.replace(/^\/+/,''))+'" alt="Driver">':'<div class="bs-driver-photo bs-driver-placeholder">👤</div>';
   html+='<div><div class="bs-driver-name">'+esc(driverName)+'</div><div class="bs-driver-meta">Assigned driver'+(phone?' · '+esc(phone):'')+'</div></div>';
   if(phone)html+='<a class="bs-phone" href="tel:'+esc(phone)+'">Call</a>';
   html+='</div>';
 }
 if(driverName&&hasVehicle)html+='<div style="height:6px"></div>';
 if(hasVehicle){
   const vehicleName=[b.make,b.model].filter(Boolean).join(' ').trim()||String(b.vehicle_type||'Taxi');
   html+='<div class="bs-grid"><div class="bs-box"><span>Vehicle</span><b>'+esc(vehicleName)+'</b></div>';
   if(b.registration_no)html+='<div class="bs-box"><span>Registration</span><b>'+esc(b.registration_no)+'</b></div>';
   html+='<div class="bs-box"><span>Seats</span><b>'+esc(b.seating_capacity||'—')+'</b></div></div>';
 }
 el.innerHTML=html;
}
function apply(d){
 const b=d.booking||{};
 const s=b.status||initialStatus;
 document.getElementById('statusText').textContent=s;
 const box=document.getElementById('statusBox');box.className='bs-status '+statusClass(s);
 document.getElementById('statusNote').textContent=statusNotes[s]||'Your booking status has been updated.';
 renderAssignment(b);
 if(b.created_at)document.getElementById('bookedOn').textContent=formatDt(b.created_at);
 if((b.booking_type||'Immediate')==='Scheduled'&&b.scheduled_at)document.getElementById('tripTime').textContent=formatDt(b.scheduled_at);
 if(document.getElementById('returnTime')&&b.return_scheduled_at)document.getElementById('returnTime').textContent=formatDt(b.return_scheduled_at);
 if(document.getElementById('cancelledOn')&&b.cancelled_at)document.getElementById('cancelledOn').textContent=formatDt(b.cancelled_at);
 const dist=b.total_distance_km??b.distance_km??null,eta=b.total_eta_minutes??b.eta_minutes??null;
 if(dist!==null&&dist!=='')document.getElementById('distance').textContent=Number(dist).toFixed(2)+' km';
 if(eta!==null&&eta!=='')document.getElementById('eta').textContent=eta+' min';
 if(Number(b.fare)>0){document.getElementById('fare').firstChild.textContent=money(b.fare);document.getElementById('fareNote').textContent='Estimated fare based on the selected taxi service pricing rules.';}
 if(b.fare_result)renderFare(b.fare_result,b);
 renderTimeline(b.status_history||[],s);
 if(s==='Cancelled'){
   document.getElementById('cancelCard')?.remove();
   if(routeController){routeController.abort();routeController=null;}
 }
}
function renderFare(fr,b){
 const el=document.getElementById('fareBreakdown');if(!el)return;
 const items=Array.isArray(fr.line_items)?fr.line_items:[];
 let html=items.map(x=>'<div class="bs-fare-row"><span>'+esc(x.label||'Fare component')+(x.meta?'<br><small>'+esc(x.meta)+'</small>':'')+'</span><b>'+money(x.amount)+'</b></div>').join('');
 html+='<div class="bs-fare-row total"><span>Total Estimated Fare</span><b>'+((Number(b.fare)>0)?money(b.fare):'—')+'</b></div>';
 el.innerHTML=html;el.hidden=false;
}
async function refreshStatus(){
 try{const r=await fetch(base+'/member/taxi/bookings/'+id+'/status',{headers:{Accept:'application/json'},cache:'no-store'});if(!r.ok)return null;const j=await r.json();if(j.success&&j.data){apply({booking:j.data});return j.data;}}
 catch(e){} return null;
}
async function processRoute(){
 if(routeStarted)return;routeStarted=true;
 const current=await refreshStatus();
 if(current&&(current.status==='Cancelled'||current.route_estimate_committed_at))return;
 routeController=new AbortController();
 try{
  const r=await fetch(base+'/member/taxi/bookings/'+id+'/process-route',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:new URLSearchParams({_token:token}),signal:routeController.signal});
  const j=await r.json();if(j.success)apply(j.data);else{
   const msg=j.message||'Route calculation could not be completed.';
   document.getElementById('statusNote').textContent=msg;
   if(/coordinate/i.test(msg)){const box=document.getElementById('routeSupport');box.hidden=false;box.querySelector('p').textContent=msg;}
  }
 }catch(e){if(e?.name!=='AbortError')document.getElementById('statusNote').textContent='Route calculation is temporarily unavailable. Your booking is still received.';}
 finally{routeController=null;}
}
function startPolling(){
 if(pollTimer)return;
 pollTimer=setInterval(async()=>{
   const b=await refreshStatus();
   if(b&&terminal.includes(b.status)){clearInterval(pollTimer);pollTimer=null;}
 },5000);
}
if(initialStatus!=='Cancelled'){processRoute();if(!terminal.includes(initialStatus))startPolling();}
document.getElementById('cancelBtn')?.addEventListener('click',async()=>{
 if(!confirm('Cancel this booking?'))return;
 const c=document.getElementById('cancelBtn');c.disabled=true;c.textContent='Cancelling…';
 routeController?.abort();routeController=null;
 try{
  const r=await fetch(base+'/member/bookings/'+id+'/cancel',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:new URLSearchParams({_token:token})});
  const j=await r.json();
  if(j.success){apply({booking:j.data});}
  else{c.disabled=false;c.textContent='Cancel Booking';alert(j.message||'Unable to cancel booking.');}
 }catch(e){c.disabled=false;c.textContent='Cancel Booking';alert('Unable to cancel booking right now. Please try again.');}
});
})();
</script>
<?php include __DIR__ . '/../Shared/member-desktop-footer.php'; ?>
