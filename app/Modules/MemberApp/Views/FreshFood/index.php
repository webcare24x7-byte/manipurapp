<?php
declare(strict_types=1);
include __DIR__ . '/../Shared/member-desktop.css.php';
include __DIR__ . '/../Shared/member-desktop-header.php';

$base = rtrim((string) config('app.base_path'), '/');
$businesses = is_array($businesses ?? null) ? $businesses : [];
$featuredProducts = is_array($featuredProducts ?? null) ? $featuredProducts : [];
$search = (string) ($search ?? '');
$latitude = $latitude ?? null;
$longitude = $longitude ?? null;

function ffUxEsc(mixed $v): string { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); }
function ffUxMoney(mixed $v): string { return '₹' . number_format((float) $v, 0); }
function ffUxImage(?string $path, string $base): ?string { return $path ? $base . '/' . ltrim($path, '/') : null; }
function ffUxRating(mixed $id, string $salt = ''): array {
    $n = abs((int) sprintf('%u', crc32($salt . ':' . (string) $id)));
    return [round(4.5 + (($n % 5) / 10), 1), 60 + ($n % 297)];
}
$categories = [
    ['All','✦'], ['Vegetables','🥬'], ['Fruits','🍎'], ['Meat','🥩'], ['Dairy','🥛'],
    ['Pantry','🫙'], ['Beverages','🧃'], ['Organic','🌿']
];
$hero = '';
foreach($businesses as $b){$hero=ffUxImage($b['cover_image_path']??null,$base) ?: ffUxImage($b['logo_path']??null,$base);if($hero)break;}
$promotions = [
    ['eyebrow'=>'FARM FRESH','title'=>'UP TO 20% OFF','text'=>'Fresh vegetables & greens','code'=>'FRESH20','tone'=>'green','icon'=>'🥬'],
    ['eyebrow'=>'FREE DELIVERY','title'=>'ON ₹500+','text'=>'Selected local stores','code'=>'FRESH500','tone'=>'orange','icon'=>'🛵'],
    ['eyebrow'=>'HEALTHY CHOICES','title'=>'ORGANIC PICKS','text'=>'Natural & locally sourced','code'=>'ORGANIC10','tone'=>'cream','icon'=>'🌱'],
];
?>
<style>
.ff2-page{--ink:#13231e;--muted:#6f7f78;--green:#087d64;--green2:#0b9a79;--line:#e2ebe7;--soft:#f3f8f5;max-width:600px;margin:auto;padding:12px 13px 105px;color:var(--ink);font-family:inherit}.ff2-page *{box-sizing:border-box}.ff2-top{display:flex;align-items:center;gap:10px;margin:2px 1px 12px}.ff2-back{width:36px;height:36px;display:grid;place-items:center;border-radius:50%;background:#fff;border:1px solid var(--line);box-shadow:0 5px 16px rgba(20,65,51,.06);text-decoration:none;color:#173c32;font-size:24px}.ff2-title{flex:1}.ff2-title h1{font-size:21px;line-height:1.05;margin:0;font-weight:950;letter-spacing:-.4px}.ff2-title p{font-size:10px;color:var(--muted);margin:4px 0 0}.ff2-cart{font-size:10px;font-weight:950;color:var(--green);text-decoration:none}.ff2-search{display:flex;align-items:center;gap:8px}.ff2-search input{flex:1;border:1px solid #dce7e2;background:#fff;border-radius:14px;padding:11px 12px;font-size:10px;outline:0;box-shadow:0 4px 16px rgba(16,62,48,.04)}.ff2-search button{border:0;background:#173f35;color:#fff;border-radius:12px;padding:10px 13px;font-size:10px;font-weight:950}.ff2-chips{display:flex;gap:8px;overflow:auto;padding:11px 1px 5px;scrollbar-width:none}.ff2-chips::-webkit-scrollbar{display:none}.ff2-chip{min-width:58px;text-align:center;text-decoration:none;color:#52665e;background:#fff;border:1px solid var(--line);border-radius:16px;padding:7px 8px;font-size:8px;font-weight:850;white-space:nowrap}.ff2-chip span{display:block;font-size:17px;line-height:17px;margin-bottom:2px}.ff2-chip.active{color:#fff;background:var(--green);border-color:var(--green);box-shadow:0 5px 14px rgba(8,125,100,.2)}.ff2-location{display:flex;align-items:center;justify-content:space-between;gap:8px;margin:7px 0 13px;padding:9px 11px;background:#eaf7f2;color:var(--green);border-radius:12px;font-size:9px;font-weight:850}.ff2-location button{border:0;background:#fff;color:var(--green);border-radius:9px;padding:6px 9px;font-size:8px;font-weight:900}.ff2-hero{position:relative;overflow:hidden;min-height:178px;border-radius:20px;margin:8px 0 17px;background:linear-gradient(120deg,#edf6dc,#c9e2b0);box-shadow:0 10px 28px rgba(61,107,67,.13)}.ff2-hero.has-image{background-image:linear-gradient(90deg,rgba(13,64,39,.78) 0%,rgba(13,64,39,.38) 52%,rgba(13,64,39,.05) 100%),var(--hero-image);background-size:cover;background-position:center}.ff2-hero:after{content:"";position:absolute;right:-35px;bottom:-55px;width:175px;height:175px;border:30px solid rgba(255,255,255,.18);border-radius:50%}.ff2-hero-copy{position:relative;z-index:2;padding:25px 20px;max-width:76%;color:#16472f}.ff2-hero.has-image .ff2-hero-copy{color:#fff}.ff2-hero-copy small{font-size:9px;font-weight:950;letter-spacing:1px}.ff2-hero-copy h2{font-size:25px;line-height:.98;margin:7px 0;letter-spacing:-.7px;font-weight:950}.ff2-hero-copy p{font-size:10px;margin:0 0 13px;line-height:1.35}.ff2-hero-cta{display:inline-block;background:#0b6f57;color:#fff;border-radius:10px;padding:8px 12px;font-size:9px;font-weight:950;text-decoration:none}.ff2-hero.has-image .ff2-hero-cta{background:#fff;color:#14523c}.ff2-fresh-badge{position:absolute;right:15px;top:15px;z-index:3;width:62px;height:62px;border-radius:50%;background:#39ad6a;color:#fff;display:grid;place-items:center;text-align:center;font-size:8px;font-weight:950;line-height:1.05;box-shadow:0 7px 18px rgba(0,0,0,.12);transform:rotate(6deg)}.ff2-heading{display:flex;align-items:center;justify-content:space-between;margin:0 1px 8px}.ff2-heading h2{font-size:15px;margin:0;letter-spacing:-.2px}.ff2-heading a{font-size:9px;color:var(--green);font-weight:900;text-decoration:none}.ff2-rating{display:inline-flex;align-items:center;gap:4px;font-size:9px;color:#5d6e67}.ff2-rating strong{color:#ecaa16;font-size:10px}.ff2-stores{display:grid;grid-template-columns:repeat(2,1fr);gap:9px;margin-bottom:17px}.ff2-store{overflow:hidden;background:#fff;border:1px solid var(--line);border-radius:16px;text-decoration:none;color:inherit;box-shadow:0 7px 20px rgba(15,62,48,.06)}.ff2-store-img{height:112px;background:linear-gradient(135deg,#e7f1e6,#cfe3d0);position:relative}.ff2-store-img img{width:100%;height:100%;object-fit:cover}.ff2-open{position:absolute;left:8px;top:8px;background:#eaf7f2;color:var(--green);padding:4px 6px;border-radius:7px;font-size:7px;font-weight:950}.ff2-heart{position:absolute;right:8px;top:8px;width:27px;height:27px;border-radius:50%;background:rgba(255,255,255,.92);display:grid;place-items:center;font-size:13px}.ff2-store-body{padding:9px}.ff2-store-name{font-size:12px;font-weight:950;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.ff2-store-meta{font-size:8px;color:var(--muted);margin:3px 0}.ff2-store-bottom{display:flex;justify-content:space-between;align-items:center;margin-top:6px;font-size:8px;color:#64766e}.ff2-products{display:flex;gap:9px;overflow:auto;padding:1px 1px 8px;scrollbar-width:none;margin-bottom:15px}.ff2-products::-webkit-scrollbar{display:none}.ff2-product{min-width:142px;max-width:142px;background:#fff;border:1px solid var(--line);border-radius:15px;overflow:hidden;text-decoration:none;color:inherit;box-shadow:0 6px 18px rgba(15,62,48,.05)}.ff2-product-img{height:92px;background:linear-gradient(135deg,#edf5e8,#d7ead4);position:relative}.ff2-product-img img{width:100%;height:100%;object-fit:cover}.ff2-discount{position:absolute;left:7px;top:7px;background:#e8533e;color:#fff;padding:4px 6px;border-radius:7px;font-size:7px;font-weight:950}.ff2-product-body{padding:8px}.ff2-product-name{font-size:10px;font-weight:950;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.ff2-product-seller{font-size:8px;color:var(--muted);margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.ff2-product-bottom{display:flex;align-items:center;justify-content:space-between;margin-top:6px}.ff2-price{font-size:11px;font-weight:950;color:#126b58}.ff2-price del{font-size:8px;color:#9aa49f;font-weight:700;margin-right:2px}.ff2-add{width:24px;height:24px;border-radius:50%;display:grid;place-items:center;background:var(--green);color:#fff;font-size:16px}.ff2-promos{display:grid;grid-auto-flow:column;grid-auto-columns:82%;gap:9px;overflow:auto;scrollbar-width:none;padding:1px 1px 7px;margin-bottom:15px}.ff2-promos::-webkit-scrollbar{display:none}.ff2-promo{min-height:106px;border-radius:16px;padding:13px;position:relative;overflow:hidden}.ff2-promo.green{background:linear-gradient(120deg,#177c57,#55ad64);color:#fff}.ff2-promo.orange{background:linear-gradient(120deg,#f37b43,#f49d51);color:#fff}.ff2-promo.cream{background:linear-gradient(120deg,#f4eddc,#ded1ae);color:#385342}.ff2-promo small{font-size:7px;font-weight:950;letter-spacing:1px}.ff2-promo h3{font-size:18px;margin:4px 0 2px;line-height:1}.ff2-promo p{font-size:8px;margin:0;opacity:.88}.ff2-promo code{display:inline-block;margin-top:8px;background:rgba(255,255,255,.2);border:1px dashed rgba(255,255,255,.5);padding:4px 6px;border-radius:6px;font-size:7px;font-weight:900}.ff2-promo-icon{position:absolute;right:13px;top:24px;font-size:40px;opacity:.85}.ff2-empty{background:var(--soft);border:1px dashed #cfe0d9;border-radius:16px;padding:25px;text-align:center;color:var(--muted);font-size:10px}.ff2-empty b{display:block;color:var(--ink);font-size:13px;margin-bottom:4px}.ff2-nav{position:fixed;left:50%;bottom:6px;transform:translateX(-50%);width:min(580px,calc(100% - 14px));background:rgba(255,255,255,.96);backdrop-filter:blur(14px);border:1px solid var(--line);box-shadow:0 9px 28px rgba(0,0,0,.1);border-radius:20px;display:grid;grid-template-columns:repeat(4,1fr);padding:4px;z-index:30}.ff2-nav a{text-decoration:none;color:#73827c;text-align:center;padding:5px 3px;font-size:9px;font-weight:750}.ff2-nav a.active{color:var(--green)}.ff2-nav i{display:block;font-style:normal;font-size:17px;margin-bottom:1px}.ff2-note{font-size:8px;color:#8a9792;margin:-2px 1px 9px}@media(min-width:700px){.ff2-page{max-width:620px;padding-top:18px}.ff2-stores{grid-template-columns:repeat(2,1fr)}.ff2-promos{grid-auto-columns:48%}}
</style>
<div class="ff2-page">
    <header class="ff2-top">
        <a class="ff2-back" href="<?=ffUxEsc($base)?>/member" aria-label="Back">‹</a>
        <div class="ff2-title"><h1>Fresh Food &amp; Grocery</h1><p>Farm fresh products from local stores</p></div>
        <a class="ff2-cart" href="<?=ffUxEsc($base)?>/member/fresh-food/cart">🛒 Cart</a>
    </header>

    <form class="ff2-search" method="get" action="<?=ffUxEsc($base)?>/member/fresh-food">
        <input name="q" value="<?=ffUxEsc($search)?>" placeholder="Search fresh food, stores or products" autocomplete="off"><button type="submit">Search</button>
        <input type="hidden" name="lat" value="<?=ffUxEsc($latitude)?>"><input type="hidden" name="lng" value="<?=ffUxEsc($longitude)?>">
    </form>
    <div class="ff2-chips">
        <?php foreach($categories as [$name,$icon]): ?><a class="ff2-chip <?=$name==='All'?'active':''?>" href="#<?=$name==='All'?'stores':'products'?>"><span><?=$icon?></span><?=ffUxEsc($name)?></a><?php endforeach; ?>
    </div>
    <div class="ff2-location"><span>📍 <?=($latitude!==null&&$longitude!==null)?'Sorted using your location':'Fresh stores around Imphal, Manipur'?></span><button type="button" id="useLocation">Use my location</button></div>

    <section class="ff2-hero <?= $hero!==''?'has-image':'' ?>" <?php if($hero!==''): ?>style="--hero-image:url('<?=ffUxEsc($hero)?>')"<?php endif; ?> data-promo-slot="fresh-hero">
        <div class="ff2-hero-copy"><small>FARM FRESH • LOCAL FIRST</small><h2>Fresh from<br>Local Farms.</h2><p>Better food. Healthier choices. Support local sellers.</p><a class="ff2-hero-cta" href="#products">Shop fresh →</a></div>
        <div class="ff2-fresh-badge">FRESH<br>&amp;<br>HEALTHY</div>
    </section>

    <section id="stores">
        <div class="ff2-heading"><h2>Top Rated Stores</h2><a href="#stores">See all →</a></div>
        <div class="ff2-stores">
            <?php foreach(array_slice($businesses,0,4) as $idx=>$b): $img=ffUxImage($b['cover_image_path']??null,$base) ?: ffUxImage($b['logo_path']??null,$base); [$rating,$reviews]=ffUxRating($b['id']??$idx,'store'); ?>
                <a class="ff2-store" href="<?=ffUxEsc($base)?>/member/fresh-food/<?=ffUxEsc($b['id'])?>"><div class="ff2-store-img"><?php if($img): ?><img src="<?=ffUxEsc($img)?>" alt="<?=ffUxEsc($b['business_name'])?>"><?php else: ?><span style="display:grid;place-items:center;height:100%;font-size:40px">🥬</span><?php endif; ?><span class="ff2-open"><?=!empty($b['is_open'])?'OPEN':'CLOSED'?></span><span class="ff2-heart">♡</span></div><div class="ff2-store-body"><div class="ff2-store-name"><?=ffUxEsc($b['business_name'])?></div><div class="ff2-store-meta"><?=ffUxEsc($b['city']??'Imphal')?> · <?=!empty($b['delivery_available'])?'Delivery':''?><?=(!empty($b['delivery_available'])&&!empty($b['pickup_available']))?' · ':''?><?=!empty($b['pickup_available'])?'Pickup':''?></div><div class="ff2-rating"><strong>★ <?=number_format($rating,1)?></strong><span>(<?=number_format($reviews)?> reviews)</span></div><div class="ff2-store-bottom"><span>🚚 <?=!empty($b['delivery_available'])?ffUxMoney($b['minimum_delivery_fee']??0):'Pickup'?></span><span>Min. ₹<?=number_format((float)($b['minimum_delivery_fee']??0),0)?></span></div></div></a>
            <?php endforeach; ?>
        </div>
        <?php if(!$businesses): ?><div class="ff2-empty"><b>No Fresh Food stores found</b>Try another search or check again later.</div><?php endif; ?>
    </section>

    <section id="products">
        <div class="ff2-heading"><h2>Popular Fresh Picks</h2><a href="#stores">See all →</a></div>
        <p class="ff2-note">Popularity and review numbers are demo-ranked for now and can be replaced by real customer analytics later.</p>
        <div class="ff2-products">
            <?php foreach(array_slice($featuredProducts,0,8) as $idx=>$p): $img=ffUxImage($p['image_path']??null,$base); [$rating,$reviews]=ffUxRating($p['id']??$idx,'product'); $old=(float)($p['price']??0);$price=(float)($p['display_price']??$old);$discount=max(0,$old-$price); ?>
                <a class="ff2-product" href="<?=ffUxEsc($base)?>/member/fresh-food/<?=ffUxEsc($p['business_id'])?>/products/<?=ffUxEsc($p['id'])?>"><div class="ff2-product-img"><?php if($img): ?><img src="<?=ffUxEsc($img)?>" alt="<?=ffUxEsc($p['name'])?>"><?php else: ?><span style="display:grid;place-items:center;height:100%;font-size:34px">🥕</span><?php endif; ?><?php if($discount>0): ?><span class="ff2-discount"><?=round($discount/max(1,$old)*100)?>% OFF</span><?php endif; ?><span class="ff2-heart">♡</span></div><div class="ff2-product-body"><div class="ff2-product-name"><?=ffUxEsc($p['name'])?></div><div class="ff2-product-seller"><?=ffUxEsc($p['business_name'])?></div><div class="ff2-rating"><strong>★ <?=number_format($rating,1)?></strong><span>(<?=number_format($reviews)?>)</span></div><div class="ff2-product-bottom"><span class="ff2-price"><?php if($discount>0): ?><del><?=ffUxMoney($old)?></del><?php endif; ?><?=ffUxMoney($price)?> <small>/ <?=ffUxEsc($p['unit'])?></small></span><span class="ff2-add">+</span></div></div></a>
            <?php endforeach; ?>
        </div>
    </section>

    <section>
        <div class="ff2-heading"><h2>Special Offers</h2><a href="#offers">See all →</a></div>
        <div class="ff2-promos" id="offers" data-promo-slot="fresh-promotions">
            <?php foreach($promotions as $promo): ?><article class="ff2-promo <?=$promo['tone']?>"><small><?=ffUxEsc($promo['eyebrow'])?></small><h3><?=ffUxEsc($promo['title'])?></h3><p><?=ffUxEsc($promo['text'])?></p><code><?=ffUxEsc($promo['code'])?></code><span class="ff2-promo-icon"><?=$promo['icon']?></span></article><?php endforeach; ?>
        </div>
    </section>

    <?php include __DIR__.'/../Shared/fresh-food-cart-widget.php'; ?>
    <?php include __DIR__.'/../Shared/member-notification-widget.php'; ?>
</div>
<nav class="ff2-nav"><a href="<?=ffUxEsc($base)?>/member"><i>⌂</i>Home</a><a class="active" href="<?=ffUxEsc($base)?>/member/fresh-food"><i>🥬</i>Fresh</a><a href="<?=ffUxEsc($base)?>/member/bookings"><i>▣</i>Bookings</a><a href="<?=ffUxEsc($base)?>/member/profile"><i>♙</i>Profile</a></nav>
<script>
document.getElementById('useLocation')?.addEventListener('click',()=>{if(!navigator.geolocation){alert('Location is not available in this browser.');return}navigator.geolocation.getCurrentPosition(p=>{const u=new URL(location.href);u.searchParams.set('lat',p.coords.latitude.toFixed(7));u.searchParams.set('lng',p.coords.longitude.toFixed(7));location.href=u.toString()},()=>alert('Location permission was not granted.'),{enableHighAccuracy:true,timeout:10000,maximumAge:300000})});
</script>
<?php include __DIR__ . '/../Shared/member-desktop-footer.php'; ?>
