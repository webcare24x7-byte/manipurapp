<?php
declare(strict_types=1);
include __DIR__ . '/../Shared/member-desktop.css.php';
include __DIR__ . '/../Shared/member-desktop-header.php';
$basePath = rtrim((string)config('app.base_path'), '/');
$query = trim((string)($query ?? ''));
$results = is_array($results ?? null) ? $results : [];
$total = (int)($total ?? 0);
$esc = static fn(mixed $v): string => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$money = static fn(mixed $v): string => '₹' . number_format((float)$v, 0);
$groups = [
  'restaurants' => ['🍛','Food & Restaurants'], 'destinations'=>['⛰️','Destinations'], 'stays'=>['🏨','Stays & Homestays'],
  'packages'=>['🧭','Tour Packages'], 'guides'=>['🧑‍🏫','Local Guides'], 'experiences'=>['✨','Experiences'],
  'events'=>['🎉','Events & Festivals'], 'fresh_food'=>['🥬','Fresh Food'], 'taxi'=>['🚕','Taxi'], 'rentals'=>['🚚','Commercial Rentals'],
];
?>
<style>
.ma-search-page{max-width:1440px;margin:0 auto;padding:28px 32px 110px;color:#10241f;background:#f6faf8;min-height:calc(100vh - 72px)}
.ma-search-wrap{max-width:1180px;margin:0 auto}.ma-search-head{margin:18px 0 22px}.ma-search-head h1{margin:0;font-size:30px;letter-spacing:-1px}.ma-search-head p{margin:7px 0;color:#70817b;font-size:13px}
.ma-search-form{display:flex;gap:10px;background:#fff;border:1px solid #d8e5e0;border-radius:18px;padding:7px;box-shadow:0 10px 28px rgba(24,65,53,.07)}
.ma-search-form input{flex:1;border:0;outline:0;padding:12px 14px;font:inherit;font-size:14px}.ma-search-form button{border:0;border-radius:13px;background:#087d64;color:#fff;font-weight:900;padding:0 24px;cursor:pointer}
.ma-search-summary{margin:18px 0;color:#61756e;font-size:12px}.ma-search-group{margin:28px 0}.ma-search-group-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:12px}.ma-search-group-head h2{margin:0;font-size:18px}.ma-search-group-head span{font-size:11px;color:#7c8d87}
.ma-search-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px}.ma-result{display:block;background:#fff;border:1px solid #e0eae6;border-radius:16px;padding:15px;color:#10241f;text-decoration:none;box-shadow:0 7px 18px rgba(28,63,53,.045);transition:.18s}.ma-result:hover{transform:translateY(-2px);box-shadow:0 12px 25px rgba(28,63,53,.08);border-color:#c6ddd4}.ma-result-icon{width:42px;height:42px;border-radius:13px;background:#eaf7f2;display:grid;place-items:center;font-size:21px;margin-bottom:10px}.ma-result strong{display:block;font-size:13px;line-height:1.25}.ma-result small{display:block;margin-top:5px;color:#72827d;font-size:10px;line-height:1.35}.ma-result .price{margin-top:8px;color:#087d64;font-weight:900;font-size:11px}.ma-empty{padding:42px 20px;text-align:center;background:#fff;border:1px dashed #cbdcd5;border-radius:20px;color:#6f817a}.ma-empty strong{display:block;color:#20352f;margin-bottom:6px}
@media(max-width:899px){.md-global-header{display:none!important}.ma-search-page{min-height:100vh;padding:18px 14px 100px}.ma-search-head{margin-top:4px}.ma-search-head h1{font-size:25px}.ma-search-form{border-radius:15px}.ma-search-form button{padding:0 16px}.ma-search-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:10px}.ma-result{padding:12px;border-radius:14px}.ma-result strong{font-size:12px}}
@media(min-width:900px){.ma-search-page+.ma-bottom-nav{display:none}}
</style>
<div class="ma-search-page">
  <div class="ma-search-wrap">
    <div class="ma-search-head">
      <h1><?= $query!=='' ? 'Search results' : 'Search ManipurApp' ?></h1>
      <p><?= $query!=='' ? 'Showing matches across food, travel, rides, stays and local services.' : 'Search across the services available in ManipurApp.' ?></p>
    </div>
    <form class="ma-search-form" action="<?= $esc($basePath) ?>/member/search" method="get">
      <span style="display:grid;place-items:center;padding:0 5px;font-size:21px">⌕</span>
      <input name="q" value="<?= $esc($query) ?>" placeholder="Search food, places, rides, stays, services..." autocomplete="off" autofocus>
      <button type="submit">Search</button>
    </form>

    <?php if($query===''): ?>
      <div class="ma-empty" style="margin-top:22px"><strong>What are you looking for?</strong>Try “pork”, “Loktak”, “homestay”, “taxi”, “restaurant” or a service name.</div>
    <?php elseif($total===0): ?>
      <div class="ma-empty" style="margin-top:22px"><strong>No results found for “<?= $esc($query) ?>”</strong>Try a broader word, another spelling, or search by service type.</div>
    <?php else: ?>
      <div class="ma-search-summary">Found <?= $total ?> matching result<?= $total===1?'':'s' ?> for <strong>“<?= $esc($query) ?>”</strong></div>
      <?php foreach($groups as $key=>$meta): $rows=$results[$key]??[]; if(!$rows) continue; ?>
        <section class="ma-search-group">
          <div class="ma-search-group-head"><h2><?= $meta[0] ?> <?= $esc($meta[1]) ?></h2><span><?= count($rows) ?> result<?= count($rows)===1?'':'s' ?></span></div>
          <div class="ma-search-grid">
          <?php foreach($rows as $row):
            $name='';$sub='';$href='#';$price=null;
            switch($key){
              case 'restaurants': $name=$row['business_name']??'Restaurant';$sub=trim(($row['cuisine_type']??'Local cuisine').' • '.($row['city']??$row['district']??''));$href=$basePath.'/member/restaurants/'.(int)$row['id'];break;
              case 'destinations': $name=$row['name']??'Destination';$sub=trim(($row['district']??'').' • '.($row['city']??''));$href=$basePath.'/member/tourism/destinations/'.(int)$row['id'];break;
              case 'stays': $name=$row['name']??'Stay';$sub=trim(($row['district']??'').' • '.($row['city']??''));$price=$row['starting_price_per_night']??$row['room_price']??null;$href=$basePath.'/member/tourism/stays/'.(int)$row['id'];break;
              case 'packages': $name=$row['title']??'Tour Package';$sub=trim(($row['duration_days']??'').' days • '.($row['provider_name']??''));$price=$row['discount_price']??$row['base_price']??null;$href=$basePath.'/member/tourism/packages/'.(int)$row['id'];break;
              case 'guides': $name=$row['full_name']??$row['name']??'Local Guide';$sub=trim(($row['district']??'').' • '.($row['languages']??''));$price=$row['price_per_day']??null;$href=$basePath.'/member/tourism/guides/'.(int)$row['id'];break;
              case 'experiences': $name=$row['title']??'Experience';$sub=trim(($row['experience_type']??'Experience').' • '.($row['destination_name']??''));$price=$row['price']??null;$href=$basePath.'/member/tourism/experiences/'.(int)$row['id'];break;
              case 'events': $name=$row['title']??'Event';$sub=trim(($row['district']??'').' • '.($row['venue']??''));$href=$basePath.'/member/tourism/events/'.(int)$row['id'];break;
              case 'fresh_food': $name=$row['business_name']??'Fresh Food Store';$sub=trim(($row['district']??'').' • '.($row['city']??''));$href=$basePath.'/member/fresh-food/'.(int)$row['id'];break;
              case 'taxi': $name=$row['name']??'Taxi Service';$sub=trim(($row['service_type']??'Taxi').' • '.($row['business_name']??''));$href=$basePath.'/member/taxi?service_id='.(int)$row['id'];break;
              case 'rentals': $name=$row['name']??trim(($row['make']??'').' '.($row['model']??''));$sub=trim(($row['category_name']??'Vehicle').' • '.($row['provider_name']??''));$price=$row['rate']??null;$href=$basePath.'/member/commercial-rental/book?vehicle_id='.(int)$row['id'];break;
            }
          ?>
            <a class="ma-result" href="<?= $esc($href) ?>"><div class="ma-result-icon"><?= $meta[0] ?></div><strong><?= $esc($name) ?></strong><small><?= $esc($sub) ?></small><?php if($price!==null&&$price!==''): ?><div class="price"><?= $money($price) ?></div><?php endif; ?></a>
          <?php endforeach; ?>
          </div>
        </section>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>
<nav class="ma-bottom-nav" aria-label="Primary navigation">
  <a class="ma-nav-item" href="<?= $esc($basePath) ?>/member">⌂<span>Home</span></a>
  <a class="ma-nav-item active" href="<?= $esc($basePath) ?>/member/search">⌕<span>Search</span></a>
  <a class="ma-nav-item" href="<?= $esc($basePath) ?>/member/bookings">▣<span>Bookings</span></a>
  <a class="ma-nav-item" href="<?= $esc($basePath) ?>/member/profile">♙<span>Profile</span></a>
</nav>
