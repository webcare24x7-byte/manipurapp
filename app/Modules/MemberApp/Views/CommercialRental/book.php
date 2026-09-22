<?php
declare(strict_types=1);
include __DIR__ . '/../Shared/member-desktop.css.php';
include __DIR__ . '/../Shared/member-desktop-header.php';

$basePath=rtrim((string)config('app.base_path'),'/');
$vehicle=is_array($vehicle??null)?$vehicle:[];
$member=is_array($member??null)?$member:[];
$q=is_array($query??null)?$query:[];
function maCrB(mixed $v):string{return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');}
function maCrBM(mixed $v):string{$n=(float)$v;return $n>0?'₹'.number_format($n,0):'Quote on request';}
$name=trim(($vehicle['name']??'')); if($name===''){$name=trim(($vehicle['make']??'').' '.($vehicle['model']??''));}
if($name==='')$name='Commercial Vehicle';
$img=!empty($vehicle['photo_path'])?$basePath.'/'.ltrim((string)$vehicle['photo_path'],'/'):''; 
$defaultName=trim(implode(' ',array_filter([$member['first_name']??'',$member['middle_name']??'',$member['last_name']??''])));
?>
<style>
.ma-crb{max-width:620px;margin:auto;padding:14px 14px 105px;color:#14251f}.ma-crb *{box-sizing:border-box}.ma-crb a{text-decoration:none}
.ma-crb-head{display:flex;align-items:center;gap:12px;padding:6px 2px 15px}.ma-crb-back{width:40px;height:40px;border-radius:50%;display:grid;place-items:center;background:#f1f6f3;color:#163d33;font-size:25px}.ma-crb-head h1{margin:0;font-size:22px}.ma-crb-head p{margin:3px 0;color:#70817b;font-size:11px}
.ma-crb-vehicle{display:grid;grid-template-columns:145px 1fr;gap:14px;background:#fff;border:1px solid #e0ebe6;border-radius:21px;padding:11px;box-shadow:0 8px 24px rgba(20,57,47,.06)}.ma-crb-photo{height:125px;border-radius:16px;background:#edf4f1;display:grid;place-items:center;font-size:48px;overflow:hidden}.ma-crb-photo img{width:100%;height:100%;object-fit:cover}.ma-crb-vehicle h2{margin:3px 0;font-size:18px}.ma-crb-provider{color:#087d64;font-weight:800;font-size:11px}.ma-crb-meta{color:#687972;font-size:11px;line-height:1.7;margin-top:7px}.ma-crb-price{font-size:17px;font-weight:900;margin-top:6px}
.ma-crb-form{margin-top:14px;background:#fff;border:1px solid #e0ebe6;border-radius:21px;padding:16px}.ma-crb-form h2{margin:0;font-size:18px}.ma-crb-form>p{font-size:11px;color:#72817b;line-height:1.5;margin:4px 0 13px}.ma-crb-label{display:block;font-size:11px;font-weight:900;color:#53665f;margin:12px 0 6px}.ma-crb-input,.ma-crb-textarea{width:100%;padding:13px;border:1px solid #dce7e2;border-radius:13px;background:#fbfdfc;color:#172a24;font:inherit;font-size:13px}.ma-crb-textarea{min-height:80px;resize:vertical}.ma-crb-row{display:grid;grid-template-columns:1fr 1fr;gap:9px}.ma-crb-loc{display:grid;grid-template-columns:1fr auto;gap:7px}.ma-crb-loc button{border:1px solid #cfe8df;background:#eaf7f2;color:#087d64;border-radius:13px;padding:0 12px;font-weight:900}.ma-crb-check{display:flex;align-items:center;gap:8px;font-size:12px;color:#4f625b;margin-top:11px}.ma-crb-check input{width:17px;height:17px;accent-color:#087d64}.ma-crb-note{margin-top:12px;padding:11px 12px;background:#fff8e8;border:1px solid #efdfb7;border-radius:13px;color:#725d2b;font-size:11px;line-height:1.5}.ma-crb-submit{width:100%;border:0;background:#087d64;color:#fff;border-radius:14px;padding:14px;margin-top:14px;font-weight:900;font-size:14px}.ma-crb-error{background:#fff0ef;border:1px solid #f1ceca;color:#9b3d35;border-radius:14px;padding:12px;font-size:12px;margin-bottom:12px}
.ma-crb-nav{position:fixed;left:50%;bottom:10px;transform:translateX(-50%);width:min(540px,calc(100% - 20px));background:rgba(255,255,255,.96);border:1px solid #e1ebe7;box-shadow:0 10px 30px rgba(0,0,0,.12);border-radius:22px;display:grid;grid-template-columns:repeat(4,1fr);padding:7px;z-index:30}.ma-crb-nav a{text-decoration:none;color:#73827c;text-align:center;padding:8px 3px;font-size:11px;font-weight:700}.ma-crb-nav a.active{color:#087d64}.ma-crb-nav i{display:block;font-style:normal;font-size:20px;margin-bottom:2px}
@media(max-width:500px){.ma-crb-vehicle{grid-template-columns:105px 1fr}.ma-crb-photo{height:105px}.ma-crb-row{grid-template-columns:1fr}}
</style>
<div class="ma-crb">
<header class="ma-crb-head"><a class="ma-crb-back" href="<?=maCrB($basePath)?>/member/commercial-rental">‹</a><div><h1>Request a vehicle</h1><p>Tell the provider what you need.</p></div></header>

<section class="ma-crb-vehicle">
 <div class="ma-crb-photo"><?php if($img):?><img src="<?=maCrB($img)?>" alt="<?=maCrB($name)?>"><?php else:?>🚚<?php endif;?></div>
 <div><h2><?=maCrB($name)?></h2><div class="ma-crb-provider"><?=maCrB($vehicle['provider_name']??'Local provider')?></div><div class="ma-crb-meta"><?=maCrB($vehicle['category_name']??'Commercial vehicle')?><?php if(!empty($vehicle['capacity'])):?><br>Capacity: <?=maCrB($vehicle['capacity'])?><?php endif;?><?php if(!empty($vehicle['operator_included'])):?><br>✓ Operator available<?php endif;?></div><div class="ma-crb-price"><?=maCrB(maCrBM($vehicle['rate']??0))?></div></div>
</section>

<form class="ma-crb-form" method="post" action="<?=maCrB($basePath)?>/member/commercial-rental/book">
<h2>Booking details</h2><p>This creates a rental request. The provider can review the job and confirm the final price.</p>
<?php if(!empty($error)):?><div class="ma-crb-error"><?=maCrB($error)?></div><?php endif;?>
<input type="hidden" name="_token" value="<?=maCrB($csrf??'')?>"><input type="hidden" name="vehicle_id" value="<?=maCrB($vehicle['id']??'')?>">
<label class="ma-crb-label">Your name</label><input class="ma-crb-input" name="customer_name" value="<?=maCrB($q['customer_name']??$defaultName)?>" required>
<label class="ma-crb-label">Contact phone</label><input class="ma-crb-input" name="customer_phone" value="<?=maCrB($q['customer_phone']??($member['phone']??''))?>" type="tel" inputmode="tel" required>

<label class="ma-crb-label">Pickup / work location</label>
<div class="ma-crb-loc"><input class="ma-crb-input" id="crb_pickup" name="pickup_address" value="<?=maCrB($q['pickup_address']??'')?>" placeholder="Where should the vehicle come?" required><button type="button" id="crb_loc">⌖</button></div>
<input type="hidden" id="crb_lat" name="pickup_lat" value="<?=maCrB($q['pickup_lat']??'')?>"><input type="hidden" id="crb_lng" name="pickup_lng" value="<?=maCrB($q['pickup_lng']??'')?>">

<label class="ma-crb-label">Destination / delivery location <span style="font-weight:500;color:#8a9892">(optional)</span></label>
<input class="ma-crb-input" name="destination_address" value="<?=maCrB($q['destination_address']??'')?>" placeholder="Where is the load / work going?">

<div class="ma-crb-row">
 <div><label class="ma-crb-label">Start</label><input class="ma-crb-input" type="datetime-local" name="start_at" value="<?=maCrB($q['start_at']??'')?>" required></div>
 <div><label class="ma-crb-label">End</label><input class="ma-crb-input" type="datetime-local" name="end_at" value="<?=maCrB($q['end_at']??'')?>"></div>
</div>

<label class="ma-crb-label">What is the vehicle needed for?</label>
<textarea class="ma-crb-textarea" name="purpose" placeholder="Example: construction materials, shifting household items, site work..." required><?=maCrB($q['purpose']??'')?></textarea>
<label class="ma-crb-check"><input type="checkbox" name="operator_required" value="1" <?=!empty($q['operator_required'])?'checked':''?>> I need an operator / driver</label>
<label class="ma-crb-label">Additional notes <span style="font-weight:500;color:#8a9892">(optional)</span></label>
<textarea class="ma-crb-textarea" name="customer_notes" placeholder="Load details, access instructions, special requirements..."><?=maCrB($q['customer_notes']??'')?></textarea>

<div class="ma-crb-note">No online payment is taken at this stage. Your request is sent as <strong>Requested</strong>; the provider/admin can contact you and confirm the quotation.</div>
<button class="ma-crb-submit" type="submit">Send rental request →</button>
</form>

<nav class="ma-crb-nav"><a href="<?=maCrB($basePath)?>/member"><i>⌂</i>Home</a><a class="active" href="<?=maCrB($basePath)?>/member/commercial-rental"><i>🚚</i>Services</a><a href="<?=maCrB($basePath)?>/member/bookings"><i>▣</i>Bookings</a><a href="<?=maCrB($basePath)?>/member/profile"><i>♙</i>Profile</a></nav>
</div>
<script>
(()=>{
 const b=document.getElementById('crb_loc'); if(!b||!navigator.geolocation)return;
 b.addEventListener('click',()=>{b.textContent='…';navigator.geolocation.getCurrentPosition(p=>{
  document.getElementById('crb_lat').value=p.coords.latitude.toFixed(7);
  document.getElementById('crb_lng').value=p.coords.longitude.toFixed(7);
  b.textContent='✓';
 },()=>{b.textContent='⌖';alert('Location permission was not granted. Please enter the pickup area manually.');},{enableHighAccuracy:true,timeout:10000});});
})();
</script>
<?php include __DIR__ . '/../Shared/member-desktop-footer.php'; ?>
