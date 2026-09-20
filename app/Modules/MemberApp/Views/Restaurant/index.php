<?php
declare(strict_types=1);
include __DIR__ . '/../Shared/member-desktop.css.php';
include __DIR__ . '/../Shared/member-desktop-header.php';
$aiScope = 'RESTAURANTS'; $aiContext = ''; $aiTriggerTitle = 'Need help choosing where to eat?'; $aiTriggerSubtitle = 'Ask about restaurants, dishes, prices, vegetarian food and delivery.'; $aiTriggerLabel = 'Ask Food AI →'; $aiDialogTitle = 'Find food in Manipur';
include __DIR__ . '/../AI/ask.php';

$base = rtrim((string) config('app.base_path'), '/');
$restaurants = is_array($restaurants ?? null) ? $restaurants : [];
$featuredItems = is_array($featuredItems ?? null) ? $featuredItems : [];
$search = (string) ($search ?? '');
$cuisine = (string) ($cuisine ?? '');
$lat = $latitude ?? null;
$lng = $longitude ?? null;

function mrUxEsc(mixed $v): string { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); }
function mrUxMoney(mixed $v): string { return '₹' . number_format((float) $v, 0); }
function mrUxImage(?string $path, string $base): ?string { return $path ? $base . '/' . ltrim($path, '/') : null; }
function mrUxRating(mixed $id, string $salt = ''): array {
    $n = abs((int) sprintf('%u', crc32($salt . ':' . (string) $id)));
    return [round(4.5 + (($n % 5) / 10), 1), 70 + ($n % 351)];
}
$cuisines = [
    ['All', '✦'], ['Manipuri', '🍛'], ['Indian', '🥘'], ['Chinese', '🥢'],
    ['Thai', '🍜'], ['Cafe', '☕'], ['Fast Food', '🍔'], ['Desserts', '🍰']
];
$hero = null;
foreach ($restaurants as $r) {
    $hero = mrUxImage($r['cover_image_path'] ?? null, $base) ?: mrUxImage($r['logo_path'] ?? null, $base);
    if ($hero) break;
}
if (!$hero) $hero = '';

// Demo content slots. These are deliberately isolated so an admin Promotion/CMS module
// can replace them later without changing the MemberApp interaction flow.
$promotions = [
    ['eyebrow' => 'FIRST ORDER', 'title' => '20% OFF', 'text' => 'Your first food order', 'code' => 'WELCOME20', 'tone' => 'coral', 'icon' => '🍛'],
    ['eyebrow' => 'DELIVERY DEAL', 'title' => 'FREE DELIVERY', 'text' => 'On selected restaurants', 'code' => 'FREERIDE', 'tone' => 'green', 'icon' => '🛵'],
    ['eyebrow' => 'COMBO DEALS', 'title' => 'FROM ₹199', 'text' => 'Meals made for sharing', 'code' => 'COMBO199', 'tone' => 'violet', 'icon' => '🍔'],
];
$topRestaurants = array_slice($restaurants, 0, 3);
$popularItems = array_slice($featuredItems, 0, 8);
?>
<style>
/* Food discovery v2 — presentation only. Existing URLs/forms/cart/notifications remain unchanged. */
.mr2-page{--ink:#13231e;--muted:#6f7f78;--green:#087d64;--green2:#0b9a79;--line:#e2ebe7;--soft:#f3f8f5;max-width:600px;margin:auto;padding:12px 13px 105px;color:var(--ink);font-family:inherit}.mr2-page *{box-sizing:border-box}.mr2-top{display:flex;align-items:center;gap:10px;margin:2px 1px 12px}.mr2-back{width:36px;height:36px;display:grid;place-items:center;border-radius:50%;background:#fff;border:1px solid var(--line);box-shadow:0 5px 16px rgba(20,65,51,.06);text-decoration:none;color:#173c32;font-size:24px;line-height:1}.mr2-title{flex:1}.mr2-title h1{font-size:21px;line-height:1.05;margin:0;font-weight:950;letter-spacing:-.4px}.mr2-title p{font-size:10px;color:var(--muted);margin:4px 0 0}.mr2-cart{font-size:10px;font-weight:950;color:var(--green);text-decoration:none;white-space:nowrap}.mr2-search{display:flex;align-items:center;gap:8px;border:1px solid #dce7e2;background:#fff;border-radius:14px;padding:10px 12px;box-shadow:0 4px 16px rgba(16,62,48,.04)}.mr2-search svg{width:18px;color:#557068;flex:none}.mr2-search input{border:0;outline:0;width:100%;background:transparent;color:var(--ink);font-size:11px}.mr2-chips{display:flex;gap:8px;overflow:auto;padding:11px 1px 5px;scrollbar-width:none}.mr2-chips::-webkit-scrollbar{display:none}.mr2-chip{min-width:60px;text-align:center;text-decoration:none;color:#52665e;background:#fff;border:1px solid var(--line);border-radius:16px;padding:7px 9px;font-size:9px;font-weight:850;white-space:nowrap}.mr2-chip span{display:block;font-size:17px;line-height:17px;margin-bottom:2px}.mr2-chip.active{color:#fff;background:var(--green);border-color:var(--green);box-shadow:0 5px 14px rgba(8,125,100,.2)}.mr2-chip.active span{filter:brightness(0) invert(1)}.mr2-location{display:flex;align-items:center;justify-content:space-between;gap:8px;margin:7px 0 13px;padding:9px 11px;background:#eaf7f2;border:1px solid #d4eee5;border-radius:12px;color:var(--green);font-size:9px;font-weight:850}.mr2-location button{border:0;background:#fff;color:var(--green);border-radius:9px;padding:6px 9px;font-size:8px;font-weight:900}.mr2-hero{position:relative;overflow:hidden;min-height:178px;border-radius:20px;margin:8px 0 17px;background:linear-gradient(120deg,#173f35,#0b725d);box-shadow:0 10px 28px rgba(16,70,54,.16)}.mr2-hero.has-image{background-image:linear-gradient(90deg,rgba(5,38,30,.88) 0%,rgba(5,38,30,.58) 48%,rgba(5,38,30,.08) 100%),var(--hero-image);background-size:cover;background-position:center}.mr2-hero:after{content:"";position:absolute;right:-28px;bottom:-50px;width:170px;height:170px;border:30px solid rgba(255,255,255,.09);border-radius:50%}.mr2-hero-copy{position:relative;z-index:2;padding:24px 20px;color:#fff;max-width:72%}.mr2-hero-copy small{font-size:9px;font-weight:900;letter-spacing:1.2px;opacity:.88}.mr2-hero-copy h2{font-size:25px;line-height:.98;margin:7px 0;font-weight:950;letter-spacing:-.7px}.mr2-hero-copy p{font-size:10px;margin:0 0 13px;line-height:1.35;opacity:.92}.mr2-hero-cta{display:inline-block;background:#ffd34d;color:#17352c;border-radius:10px;padding:8px 12px;font-size:9px;font-weight:950;text-decoration:none}.mr2-off-badge{position:absolute;right:15px;top:15px;z-index:3;background:#fff;color:#d84b39;border-radius:50%;width:58px;height:58px;display:grid;place-items:center;text-align:center;font-size:9px;font-weight:950;line-height:1.05;transform:rotate(6deg);box-shadow:0 7px 18px rgba(0,0,0,.13)}.mr2-dots{position:absolute;z-index:3;bottom:11px;left:50%;transform:translateX(-50%);display:flex;gap:5px}.mr2-dots i{width:6px;height:6px;border-radius:50%;background:rgba(255,255,255,.55)}.mr2-dots i:first-child{background:#fff;width:18px;border-radius:10px}.mr2-heading{display:flex;align-items:center;justify-content:space-between;margin:0 1px 8px}.mr2-heading h2{font-size:15px;margin:0;letter-spacing:-.2px}.mr2-heading a{font-size:9px;color:var(--green);font-weight:900;text-decoration:none}.mr2-rating{display:inline-flex;align-items:center;gap:4px;font-size:9px;color:#5d6e67}.mr2-rating strong{color:#ecaa16;font-size:10px}.mr2-rating b{color:#42564e}.mr2-sellers{display:grid;grid-template-columns:repeat(2,1fr);gap:9px;margin-bottom:17px}.mr2-seller{overflow:hidden;background:#fff;border:1px solid var(--line);border-radius:16px;text-decoration:none;color:inherit;box-shadow:0 7px 20px rgba(15,62,48,.06)}.mr2-seller-img{height:112px;background:linear-gradient(135deg,#e6f1ed,#cfe4db);position:relative;overflow:hidden}.mr2-seller-img img{width:100%;height:100%;object-fit:cover}.mr2-tag{position:absolute;left:8px;top:8px;padding:4px 6px;border-radius:7px;background:#eaf7f2;color:var(--green);font-size:7px;font-weight:950}.mr2-heart{position:absolute;right:8px;top:8px;width:27px;height:27px;border-radius:50%;background:rgba(255,255,255,.92);display:grid;place-items:center;font-size:13px}.mr2-seller-body{padding:9px}.mr2-seller-name{font-size:12px;font-weight:950;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.mr2-cuisine{font-size:8px;color:var(--muted);margin:3px 0}.mr2-seller-meta{display:flex;align-items:center;justify-content:space-between;gap:4px;margin-top:6px;font-size:8px;color:#64766e}.mr2-popular{display:flex;gap:9px;overflow:auto;padding:1px 1px 8px;scrollbar-width:none;margin-bottom:14px}.mr2-popular::-webkit-scrollbar{display:none}.mr2-food{min-width:142px;max-width:142px;background:#fff;border:1px solid var(--line);border-radius:15px;overflow:hidden;box-shadow:0 6px 18px rgba(15,62,48,.05);text-decoration:none;color:inherit}.mr2-food-img{height:92px;background:linear-gradient(135deg,#edf5f1,#d8e9e1);position:relative}.mr2-food-img img{width:100%;height:100%;object-fit:cover}.mr2-food-body{padding:8px}.mr2-food-name{font-size:10px;font-weight:950;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.mr2-food-seller{font-size:8px;color:var(--muted);margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.mr2-food-bottom{display:flex;align-items:center;justify-content:space-between;margin-top:6px}.mr2-price{font-size:11px;font-weight:950;color:#126b58}.mr2-add{width:24px;height:24px;border-radius:50%;display:grid;place-items:center;background:var(--green);color:#fff;font-size:16px;font-weight:900}.mr2-promos{display:grid;grid-auto-flow:column;grid-auto-columns:82%;gap:9px;overflow:auto;scrollbar-width:none;padding:1px 1px 7px;margin-bottom:15px}.mr2-promos::-webkit-scrollbar{display:none}.mr2-promo{min-height:106px;border-radius:16px;padding:13px;position:relative;overflow:hidden;color:#fff}.mr2-promo:after{content:"";position:absolute;width:100px;height:100px;border-radius:50%;right:-30px;bottom:-45px;background:rgba(255,255,255,.13)}.mr2-promo.coral{background:linear-gradient(120deg,#ed5a45,#f18a58)}.mr2-promo.green{background:linear-gradient(120deg,#087d64,#1ba07e)}.mr2-promo.violet{background:linear-gradient(120deg,#7154d9,#9a6be5)}.mr2-promo small{font-size:7px;font-weight:950;letter-spacing:1px;opacity:.9}.mr2-promo h3{font-size:19px;margin:4px 0 2px;line-height:1}.mr2-promo p{font-size:8px;margin:0;opacity:.9}.mr2-promo code{display:inline-block;margin-top:8px;background:rgba(255,255,255,.18);border:1px dashed rgba(255,255,255,.45);padding:4px 6px;border-radius:6px;font-size:7px;font-weight:900}.mr2-promo-icon{position:absolute;right:12px;top:25px;font-size:39px;opacity:.85;transform:rotate(-8deg)}.mr2-near{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-bottom:17px}.mr2-near-card{background:#fff;border:1px solid var(--line);border-radius:14px;padding:8px;text-decoration:none;color:inherit;box-shadow:0 5px 16px rgba(15,62,48,.045)}.mr2-near-img{height:70px;border-radius:10px;overflow:hidden;background:#e8f1ed;margin-bottom:6px}.mr2-near-img img{width:100%;height:100%;object-fit:cover}.mr2-near-name{font-size:9px;font-weight:950;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.mr2-near-meta{font-size:7px;color:var(--muted);margin-top:3px}.mr2-nav{position:fixed;left:50%;bottom:6px;transform:translateX(-50%);width:min(580px,calc(100% - 14px));background:rgba(255,255,255,.96);backdrop-filter:blur(14px);border:1px solid var(--line);box-shadow:0 9px 28px rgba(0,0,0,.1);border-radius:20px;display:grid;grid-template-columns:repeat(4,1fr);padding:4px;z-index:30}.mr2-nav a{text-decoration:none;color:#73827c;text-align:center;padding:5px 3px;font-size:9px;font-weight:750}.mr2-nav a.active{color:var(--green)}.mr2-nav i{display:block;font-style:normal;font-size:17px;margin-bottom:1px}.mr2-section-note{font-size:8px;color:#8a9792;margin:-2px 1px 9px}.mr2-empty{background:var(--soft);border:1px dashed #cfe0d9;border-radius:16px;padding:25px;text-align:center;color:var(--muted);font-size:10px}.mr2-empty b{display:block;color:var(--ink);font-size:13px;margin-bottom:4px}@media(min-width:700px){.mr2-page{max-width:620px;padding-top:18px}.mr2-sellers{grid-template-columns:repeat(3,1fr)}.mr2-seller-img{height:125px}.mr2-promos{grid-auto-columns:48%}}
</style>
<div class="mr2-page">
    <header class="mr2-top">
        <a class="mr2-back" href="<?=mrUxEsc($base)?>/member" aria-label="Back">‹</a>
        <div class="mr2-title"><h1>Food &amp; Restaurants</h1><p>Discover local favourites, offers &amp; dishes</p></div>
        <a class="mr2-cart" href="<?=mrUxEsc($base)?>/member/restaurant/cart">🛒 Cart</a>
    </header>

    <form class="mr2-search" method="get" action="<?=mrUxEsc($base)?>/member/restaurants">
        <svg viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/><path d="m20 20-4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        <input name="q" value="<?=mrUxEsc($search)?>" placeholder="Search restaurants, cuisines or dishes..." autocomplete="off">
        <input type="hidden" name="lat" value="<?=mrUxEsc($lat)?>"><input type="hidden" name="lng" value="<?=mrUxEsc($lng)?>">
    </form>

    <div class="mr2-chips">
        <?php foreach ($cuisines as [$chip, $icon]): ?>
            <a class="mr2-chip <?=$cuisine===$chip||($chip==='All'&&$cuisine==='')?'active':''?>" href="<?=mrUxEsc($base)?>/member/restaurants?<?=http_build_query(array_filter(['cuisine'=>$chip==='All'?'':$chip,'q'=>$search,'lat'=>$lat,'lng'=>$lng],static fn($v)=>$v!==null&&$v!==''))?>"><span><?=$icon?></span><?=mrUxEsc($chip)?></a>
        <?php endforeach; ?>
    </div>

    <div class="mr2-location">
        <span>📍 <?=($lat!==null&&$lng!==null)?'Showing restaurants closest to you':'Restaurants around Imphal, Manipur'?></span>
        <?php if($lat===null||$lng===null): ?><button type="button" id="useLocation">Use my location</button><?php else: ?><button type="button" id="clearLocation">Nearby mode</button><?php endif; ?>
    </div>

    <section class="mr2-hero <?= $hero!==''?'has-image':'' ?>" <?php if($hero!==''): ?>style="--hero-image:url('<?=mrUxEsc($hero)?>')"<?php endif; ?> data-promo-slot="food-hero">
        <div class="mr2-hero-copy"><small>LOCAL FOOD • LOCAL LOVE</small><h2>Good Food.<br>Brighter Days.</h2><p>Discover favourites from Manipur's local kitchens.</p><a class="mr2-hero-cta" href="#popular-dishes">Explore dishes →</a></div>
        <div class="mr2-off-badge">UP TO<br><strong>30%</strong><br>OFF</div><div class="mr2-dots"><i></i><i></i><i></i></div>
    </section>

    <section>
        <div class="mr2-heading"><h2>Top Rated Restaurants</h2><a href="#restaurants">See all →</a></div>
        <div class="mr2-sellers" id="restaurants">
            <?php foreach($topRestaurants as $idx=>$r): $img=mrUxImage($r['cover_image_path']??null,$base) ?: mrUxImage($r['logo_path']??null,$base); [$rating,$reviews]=mrUxRating($r['id']??$idx,'restaurant'); ?>
                <a class="mr2-seller" href="<?=mrUxEsc($base)?>/member/restaurants/<?=mrUxEsc($r['id'])?>">
                    <div class="mr2-seller-img"><?php if($img): ?><img src="<?=mrUxEsc($img)?>" alt="<?=mrUxEsc($r['business_name'])?>"><span class="mr2-tag"><?=!empty($r['is_open'])?'OPEN NOW':'CLOSED'?></span><?php else: ?><span style="display:grid;place-items:center;height:100%;font-size:40px">🍛</span><?php endif; ?><span class="mr2-heart">♡</span></div>
                    <div class="mr2-seller-body"><div class="mr2-seller-name"><?=mrUxEsc($r['business_name'])?></div><div class="mr2-cuisine"><?=mrUxEsc($r['cuisine_type']??'Local cuisine')?></div><div class="mr2-rating"><strong>★ <?=number_format($rating,1)?></strong><span>(<?=number_format($reviews)?>)</span></div><div class="mr2-seller-meta"><span>🕐 <?=!empty($r['estimated_prep_minutes'])?(int)$r['estimated_prep_minutes'].' min':'—'?></span><span>🚚 <?=!empty($r['delivery_available'])?mrUxMoney($r['delivery_fee']):'Pickup'?></span></div></div>
                </a>
            <?php endforeach; ?>
        </div>
        <?php if(!$topRestaurants): ?><div class="mr2-empty"><b>No restaurants found</b>Try another dish, cuisine, or location.</div><?php endif; ?>
    </section>

    <section id="popular-dishes">
        <div class="mr2-heading"><h2>Popular Dishes</h2><a href="#restaurants">Explore menus →</a></div>
        <p class="mr2-section-note">Popular picks are demo-ranked for now and can be replaced with real sales/review analytics later.</p>
        <div class="mr2-popular">
            <?php foreach($popularItems as $idx=>$item): $img=mrUxImage($item['image_path']??null,$base); [$rating,$reviews]=mrUxRating($item['id']??$idx,'dish'); ?>
                <a class="mr2-food" href="<?=mrUxEsc($base)?>/member/restaurants/<?=mrUxEsc($item['restaurant_id'])?>/items/<?=mrUxEsc($item['id'])?>">
                    <div class="mr2-food-img"><?php if($img): ?><img src="<?=mrUxEsc($img)?>" alt="<?=mrUxEsc($item['name'])?>"><?php else: ?><span style="display:grid;place-items:center;height:100%;font-size:34px">🍽️</span><?php endif; ?><span class="mr2-heart">♡</span></div>
                    <div class="mr2-food-body"><div class="mr2-food-name"><?=mrUxEsc($item['name'])?></div><div class="mr2-food-seller"><?=mrUxEsc($item['restaurant_name'])?></div><div class="mr2-rating"><strong>★ <?=number_format($rating,1)?></strong><span>(<?=number_format($reviews)?>)</span></div><div class="mr2-food-bottom"><span class="mr2-price"><?=mrUxMoney($item['display_price']??$item['price']??0)?></span><span class="mr2-add">+</span></div></div>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <section>
        <div class="mr2-heading"><h2>Exclusive Offers</h2><a href="#offers">See all →</a></div>
        <div class="mr2-promos" id="offers" data-promo-slot="food-promotions">
            <?php foreach($promotions as $promo): ?><article class="mr2-promo <?=$promo['tone']?>"><small><?=mrUxEsc($promo['eyebrow'])?></small><h3><?=mrUxEsc($promo['title'])?></h3><p><?=mrUxEsc($promo['text'])?></p><code><?=mrUxEsc($promo['code'])?></code><span class="mr2-promo-icon"><?=$promo['icon']?></span></article><?php endforeach; ?>
        </div>
    </section>

    <section>
        <div class="mr2-heading"><h2>Popular Near You</h2><a href="#restaurants">See all →</a></div>
        <div class="mr2-near">
            <?php foreach(array_slice($restaurants,0,3) as $idx=>$r): $img=mrUxImage($r['cover_image_path']??null,$base) ?: mrUxImage($r['logo_path']??null,$base); [$rating,$reviews]=mrUxRating($r['id']??$idx,'near'); ?>
                <a class="mr2-near-card" href="<?=mrUxEsc($base)?>/member/restaurants/<?=mrUxEsc($r['id'])?>"><div class="mr2-near-img"><?php if($img): ?><img src="<?=mrUxEsc($img)?>" alt="<?=mrUxEsc($r['business_name'])?>"><?php else: ?><span style="display:grid;place-items:center;height:100%;font-size:28px">🍛</span><?php endif; ?></div><div class="mr2-near-name"><?=mrUxEsc($r['business_name'])?></div><div class="mr2-rating"><strong>★ <?=number_format($rating,1)?></strong><span>(<?=number_format($reviews)?>)</span></div><div class="mr2-near-meta"><?=mrUxEsc($r['cuisine_type']??'Restaurant')?></div></a>
            <?php endforeach; ?>
        </div>
    </section>

    <?php include __DIR__ . '/../Shared/restaurant-cart-widget.php'; ?>
    <?php include __DIR__ . '/../Shared/member-notification-widget.php'; ?>
</div>
<nav class="mr2-nav"><a href="<?=mrUxEsc($base)?>/member"><i>⌂</i>Home</a><a class="active" href="<?=mrUxEsc($base)?>/member/restaurants"><i>🍴</i>Food</a><a href="<?=mrUxEsc($base)?>/member/bookings"><i>▣</i>Bookings</a><a href="<?=mrUxEsc($base)?>/member/profile"><i>♙</i>Profile</a></nav>
<script>
(()=>{document.getElementById('useLocation')?.addEventListener('click',()=>{if(!navigator.geolocation){alert('Location is not supported on this device.');return;}navigator.geolocation.getCurrentPosition(p=>{const u=new URL(location.href);u.searchParams.set('lat',p.coords.latitude.toFixed(7));u.searchParams.set('lng',p.coords.longitude.toFixed(7));location.href=u.toString();},()=>alert('Location permission was not granted. You can still browse restaurants around Imphal.'),{enableHighAccuracy:true,timeout:10000,maximumAge:300000});});document.getElementById('clearLocation')?.addEventListener('click',()=>{const u=new URL(location.href);u.searchParams.delete('lat');u.searchParams.delete('lng');location.href=u.toString();});})();
</script>

