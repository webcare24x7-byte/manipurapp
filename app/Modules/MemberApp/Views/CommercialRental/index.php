<?php
declare(strict_types=1);
include __DIR__ . '/../Shared/member-desktop.css.php';
include __DIR__ . '/../Shared/member-desktop-header.php';

$basePath = rtrim((string)config('app.base_path'), '/');
$categories = is_array($categories ?? null) ? $categories : [];
$vehicles = is_array($vehicles ?? null) ? $vehicles : [];
$q = is_array($query ?? null) ? $query : [];
function maCrEsc(mixed $v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function maCrMoney(mixed $v): string {
    $n = (float)$v;
    return $n > 0 ? '₹' . number_format($n, 0) : 'Quote';
}
function maCrRate(array $v): string {
    $type = strtoupper((string)($v['rate_type'] ?? ''));
    return match ($type) {
        'DAILY' => maCrMoney($v['rate']) . ' / day',
        'HOURLY' => maCrMoney($v['rate']) . ' / hour',
        'PER_TRIP' => maCrMoney($v['rate']) . ' / trip',
        default => ((float)($v['rate'] ?? 0) > 0 ? maCrMoney($v['rate']) : 'Request a quote'),
    };
}
?>
<style>
.ma-cr{max-width:760px;margin:auto;padding:14px 14px 105px;color:#14251f}
.ma-cr *{box-sizing:border-box}.ma-cr a{text-decoration:none}
.ma-cr-head{display:flex;align-items:center;gap:12px;padding:6px 2px 16px}
.ma-cr-back{width:40px;height:40px;border-radius:50%;display:grid;place-items:center;background:#f1f6f3;color:#163d33;font-size:25px}
.ma-cr-head h1{margin:0;font-size:24px;letter-spacing:-.6px}.ma-cr-head p{margin:3px 0;color:#70817b;font-size:12px}
.ma-cr-place{margin-left:auto;background:#eaf7f2;border:1px solid #d4eee5;color:#087d64;border-radius:20px;padding:9px 12px;font-size:11px;font-weight:800}
.ma-cr-hero{position:relative;overflow:hidden;border-radius:24px;background:linear-gradient(135deg,#875f0a,#c58c19 58%,#e8c35d);color:#fff;padding:22px 20px;min-height:175px;box-shadow:0 15px 35px rgba(127,91,13,.16)}
.ma-cr-hero:after{content:'';position:absolute;width:190px;height:190px;right:-55px;bottom:-80px;border-radius:50%;background:rgba(255,255,255,.14)}
.ma-cr-hero h2{margin:0 0 7px;font-size:29px;line-height:1.05;max-width:420px}.ma-cr-hero p{margin:0;max-width:430px;color:#fff9e6;font-size:12px;line-height:1.55}
.ma-cr-badges{display:flex;gap:7px;flex-wrap:wrap;margin-top:15px}.ma-cr-badge{background:rgba(255,255,255,.16);border:1px solid rgba(255,255,255,.2);padding:7px 9px;border-radius:99px;font-size:10px;font-weight:800}
.ma-cr-search{margin-top:16px;background:#fff;border:1px solid #e2ebe7;border-radius:22px;padding:15px;box-shadow:0 9px 25px rgba(20,57,47,.06)}
.ma-cr-label{display:block;font-size:11px;font-weight:900;color:#53665f;margin:0 0 7px}
.ma-cr-row{display:grid;grid-template-columns:1fr 1fr;gap:9px}.ma-cr-input,.ma-cr-select{width:100%;padding:13px;border:1px solid #dce7e2;border-radius:13px;background:#fbfdfc;color:#172a24;font:inherit;font-size:13px}
.ma-cr-loc{display:grid;grid-template-columns:1fr auto;gap:7px}.ma-cr-loc button{border:1px solid #cfe8df;background:#eaf7f2;color:#087d64;border-radius:13px;padding:0 12px;font-weight:900}
.ma-cr-check{display:flex;align-items:center;gap:8px;margin:12px 0 0;font-size:12px;color:#4e635b}.ma-cr-check input{accent-color:#087d64;width:17px;height:17px}
.ma-cr-submit{width:100%;border:0;background:#087d64;color:#fff;border-radius:14px;padding:14px;margin-top:12px;font-weight:900;font-size:14px}
.ma-cr-section{margin-top:25px}.ma-cr-section h2{margin:0;font-size:19px}.ma-cr-section p{margin:4px 0 12px;color:#72817b;font-size:11px}
.ma-cr-chips{display:flex;gap:8px;overflow:auto;padding-bottom:3px}.ma-cr-chip{white-space:nowrap;border:1px solid #dce7e2;background:#fff;border-radius:20px;padding:9px 12px;color:#4c625a;font-weight:800;font-size:11px}.ma-cr-chip.active{background:#087d64;border-color:#087d64;color:#fff}
.ma-cr-card{background:#fff;border:1px solid #e0ebe6;border-radius:21px;padding:11px;margin:11px 0;box-shadow:0 7px 22px rgba(20,57,47,.055)}
.ma-cr-card-top{display:grid;grid-template-columns:145px 1fr;gap:13px}.ma-cr-photo{height:125px;border-radius:16px;background:linear-gradient(145deg,#eef3ef,#dbe9e3);display:grid;place-items:center;font-size:48px;overflow:hidden}.ma-cr-photo img{width:100%;height:100%;object-fit:cover}
.ma-cr-card h3{margin:2px 0 3px;font-size:17px}.ma-cr-provider{font-size:11px;color:#087d64;font-weight:800}.ma-cr-meta{margin-top:6px;color:#64766e;font-size:11px;line-height:1.7}
.ma-cr-price{font-size:17px;font-weight:900;margin-top:7px}.ma-cr-distance{display:inline-block;margin-left:6px;padding:4px 7px;border-radius:99px;background:#f1f7f4;color:#527067;font-size:10px;font-weight:800}
.ma-cr-actions{display:flex;gap:8px;margin-top:10px}.ma-cr-btn{flex:1;text-align:center;border-radius:12px;padding:11px;font-size:12px;font-weight:900}.ma-cr-btn.primary{background:#087d64;color:#fff}.ma-cr-btn.secondary{background:#eef6f3;color:#087d64}
.ma-cr-empty{background:#fff8e8;border:1px solid #efdfb7;border-radius:17px;padding:15px;font-size:12px;color:#6f5a2d;line-height:1.5}
.ma-cr-nav{position:fixed;left:50%;bottom:10px;transform:translateX(-50%);width:min(540px,calc(100% - 20px));background:rgba(255,255,255,.96);border:1px solid #e1ebe7;box-shadow:0 10px 30px rgba(0,0,0,.12);border-radius:22px;display:grid;grid-template-columns:repeat(4,1fr);padding:7px;z-index:30}.ma-cr-nav a{text-decoration:none;color:#73827c;text-align:center;padding:8px 3px;font-size:11px;font-weight:700}.ma-cr-nav a.active{color:#087d64}.ma-cr-nav i{display:block;font-style:normal;font-size:20px;margin-bottom:2px}
@media(max-width:560px){.ma-cr-card-top{grid-template-columns:115px 1fr}.ma-cr-photo{height:110px}.ma-cr-row{grid-template-columns:1fr}}
</style>

<div class="ma-cr">
<header class="ma-cr-head">
    <a class="ma-cr-back" href="<?=maCrEsc($basePath)?>/member" aria-label="Back">‹</a>
    <div><h1>Commercial Rental</h1><p>Vehicles &amp; equipment for work, moving and projects.</p></div>
    <span class="ma-cr-place">📍 Imphal</span>
</header>

<section class="ma-cr-hero">
    <h2>Find the right vehicle for the job.</h2>
    <p>Request pickups, mini trucks, trucks, tractors, JCBs, tankers and other commercial vehicles from local providers.</p>
    <div class="ma-cr-badges"><span class="ma-cr-badge">🚚 Work vehicles</span><span class="ma-cr-badge">📍 Local providers</span><span class="ma-cr-badge">💬 Quote on request</span></div>
</section>

<form class="ma-cr-search" method="get" action="<?=maCrEsc($basePath)?>/member/commercial-rental">
    <label class="ma-cr-label" for="pickup_address">Where do you need the vehicle?</label>
    <div class="ma-cr-loc">
        <input class="ma-cr-input" id="pickup_address" name="pickup_address" value="<?=maCrEsc($q['pickup_address']??'')?>" placeholder="e.g. Imphal, Patsoi" autocomplete="street-address">
        <button type="button" id="crUseLocation" title="Use my location">⌖</button>
    </div>
    <input type="hidden" id="pickup_lat" name="pickup_lat" value="<?=maCrEsc($q['pickup_lat']??'')?>">
    <input type="hidden" id="pickup_lng" name="pickup_lng" value="<?=maCrEsc($q['pickup_lng']??'')?>">
    <div class="ma-cr-row" style="margin-top:9px">
        <select class="ma-cr-select" name="category_id">
            <option value="">All vehicle types</option>
            <?php foreach($categories as $c): ?>
                <option value="<?=maCrEsc($c['id'])?>" <?=((string)($q['category_id']??'')===(string)$c['id'])?'selected':''?>><?=maCrEsc($c['name'])?></option>
            <?php endforeach; ?>
        </select>
        <select class="ma-cr-select" name="sort" aria-label="Sort vehicles">
            <option value="nearest" <?=(($q['sort']??'nearest')==='nearest')?'selected':''?>>Nearest first</option>
            <option value="latest" <?=(($q['sort']??'')==='latest')?'selected':''?>>Recently added</option>
        </select>
    </div>
    <label class="ma-cr-check"><input type="checkbox" name="operator_required" value="1" <?=!empty($q['operator_required'])?'checked':''?>> I need an operator / driver</label>
    <button class="ma-cr-submit" type="submit">Search available vehicles →</button>
</form>

<section class="ma-cr-section">
    <h2>Available commercial vehicles</h2>
    <p><?=count($vehicles)?> vehicle<?=count($vehicles)===1?'':'s'?> available to request</p>

    <?php if(!$vehicles): ?>
        <div class="ma-cr-empty"><strong>No matching vehicles right now.</strong><br>Try another vehicle type or remove the operator filter. New local vehicles can be added by providers at any time.</div>
    <?php endif; ?>

    <?php foreach($vehicles as $v):
        $img = !empty($v['photo_path']) ? $basePath.'/'.ltrim((string)$v['photo_path'],'/') : '';
        $model = trim(($v['make']??'').' '.($v['model']??''));
        $name = trim((string)($v['name']??'')) ?: ($model ?: 'Commercial Vehicle');
    ?>
    <article class="ma-cr-card">
        <div class="ma-cr-card-top">
            <div class="ma-cr-photo">
                <?php if($img): ?><img src="<?=maCrEsc($img)?>" alt="<?=maCrEsc($name)?>"><?php else: ?>🚚<?php endif; ?>
            </div>
            <div>
                <h3><?=maCrEsc($name)?></h3>
                <div class="ma-cr-provider"><?=maCrEsc($v['provider_name']??'Local provider')?></div>
                <div class="ma-cr-meta">
                    <?=maCrEsc($v['category_name']??'Commercial vehicle')?><br>
                    <?php if(!empty($v['capacity'])): ?>Capacity: <?=maCrEsc($v['capacity'])?><br><?php endif; ?>
                    <?php if(!empty($v['operator_included'])): ?>✓ Operator available<?php else: ?>✓ Vehicle rental<?php endif; ?>
                    <?php if(!empty($v['provider_city'])): ?><br>📍 <?=maCrEsc($v['provider_city'])?><?php endif; ?>
                </div>
                <div class="ma-cr-price"><?=maCrEsc(maCrRate($v))?><?php if(($v['distance_km']??null)!==null): ?><span class="ma-cr-distance"><?=maCrEsc($v['distance_km'])?> km away</span><?php endif; ?></div>
            </div>
        </div>
        <div class="ma-cr-actions">
            <a class="ma-cr-btn secondary" href="<?=maCrEsc($basePath)?>/member/commercial-rental?category_id=<?=maCrEsc($v['category_id']??'')?>">View similar</a>
            <a class="ma-cr-btn primary" href="<?=maCrEsc($basePath)?>/member/commercial-rental/book?vehicle_id=<?=maCrEsc($v['id'])?>">Request this vehicle →</a>
        </div>
    </article>
    <?php endforeach; ?>
</section>

<nav class="ma-cr-nav">
    <a href="<?=maCrEsc($basePath)?>/member"><i>⌂</i>Home</a>
    <a class="active" href="<?=maCrEsc($basePath)?>/member/commercial-rental"><i>🚚</i>Services</a>
    <a href="<?=maCrEsc($basePath)?>/member/bookings"><i>▣</i>Bookings</a>
    <a href="<?=maCrEsc($basePath)?>/member/profile"><i>♙</i>Profile</a>
</nav>
</div>
<script>
(()=>{
 const btn=document.getElementById('crUseLocation');
 if(!btn || !navigator.geolocation) return;
 btn.addEventListener('click',()=>{
   btn.textContent='…';
   navigator.geolocation.getCurrentPosition(pos=>{
      document.getElementById('pickup_lat').value=pos.coords.latitude.toFixed(7);
      document.getElementById('pickup_lng').value=pos.coords.longitude.toFixed(7);
      btn.textContent='✓';
   },()=>{btn.textContent='⌖'; alert('Location permission was not granted. You can enter the pickup area manually.');},{enableHighAccuracy:true,timeout:10000});
 });
})();
</script>
<?php include __DIR__ . '/../Shared/member-desktop-footer.php'; ?>
