<?php include __DIR__ . '/../Shared/member-desktop.css.php';
include __DIR__ . '/../Shared/member-desktop-header.php'; ?>
<?php require __DIR__.'/_style.php'; $sources=$tripSources??[]; ?>
<div class="ma-tour">
<header class="ma-tour-head">
  <a class="ma-tour-brand" href="<?=$basePath?>/member/tourism"><span class="ma-tour-mark">🗺️</span><span><strong>My Trip Plans</strong><small>Build your Manipur journey</small></span></a>
  <a class="ma-tour-back" href="<?=$basePath?>/member/tourism">← Explore Manipur</a>
</header>
<section class="ma-trip-hero">
  <div><span class="ma-trip-kicker">YOUR PERSONAL ITINERARY</span><h1>Plan your Manipur journey</h1><p>Save destinations, stays, experiences, festivals, guides and local services into one day-by-day plan.</p></div>
  <div class="ma-trip-hero-icon">✦</div>
</section>
<section class="ma-tour-support" id="tour-support">
  <div class="ma-tour-support-icon">☎️</div>
  <div class="ma-tour-support-copy"><strong>Need help planning your Manipur trip?</strong><p>If you need support, call our <b>ManipurApp Tour expert / Consultant.</b> Speak with our team for help with destinations, stays, transport, food, guides and your itinerary.</p></div>
  <a class="ma-tour-support-btn" href="<?=$basePath?>/member/tourism">Speak with our team</a>
</section>
<section class="ma-tour-panel ma-trip-create"><div><h2>Start a new trip</h2><p>Give your journey a name. You can build the itinerary after creating it.</p></div>
<?php if(!empty($error)):?><div class="ma-trip-error"><?=maTourEsc($error)?></div><?php endif;?>
<form class="ma-tour-form" method="post" action="<?=$basePath?>/member/tourism/trips">
<input type="hidden" name="_token" value="<?=maTourEsc($csrf)?>">
<input name="title" required maxlength="220" placeholder="Trip name — e.g. 5 Days Around Manipur">
<textarea name="description" maxlength="2000" placeholder="Optional note — what would you like to experience?"></textarea>
<div class="ma-tour-form-inline"><input type="date" name="start_date" aria-label="Start date"><input type="date" name="end_date" aria-label="End date"><button class="ma-tour-btn" style="background:var(--green);color:#fff">Create trip plan</button></div>
</form></section>
<section class="ma-tour-section"><div class="ma-tour-section-head"><div><h2>Your saved journeys</h2><p>Open a trip to arrange its days and add places or services.</p></div></div>
<?php if(!$trips):?><div class="ma-tour-empty">You have no trip plans yet. Create your first Manipur journey above.</div><?php else:?>
<div class="ma-tour-grid">
<?php foreach($trips as $t): $status=(string)($t['status']??'Draft'); ?>
<a class="ma-tour-card ma-trip-card" href="<?=$basePath?>/member/tourism/trips/<?=intval($t['id'])?>">
<div class="ma-tour-media"><span class="ma-trip-card-icon">🧭</span><span class="ma-tour-pill"><?=maTourEsc($status)?></span></div>
<div class="ma-tour-card-body"><h3><?=maTourEsc($t['title'])?></h3><p><?php if(!empty($t['start_date'])):?><?=maTourEsc($t['start_date'])?><?php endif;?><?php if(!empty($t['end_date'])):?> → <?=maTourEsc($t['end_date'])?><?php endif;?></p><div class="ma-tour-meta"><span>📌 <?=intval($t['item_count']??0)?> planned items</span><span>Open plan →</span></div></div>
</a>
<?php endforeach;?></div><?php endif;?></section>
</div>
<nav class="ma-bottom-nav" aria-label="Primary navigation">
 <a class="ma-nav-item" href="<?=maTourEsc($basePath)?>/member">⌂<span>Home</span></a>
 <a class="ma-nav-item" href="<?=maTourEsc($basePath)?>/member/tourism">⌖<span>Explore</span></a>
 <a class="ma-nav-item" href="<?=maTourEsc($basePath)?>/member/bookings">▣<span>Bookings</span></a>
 <a class="ma-nav-item active" href="<?=maTourEsc($basePath)?>/member/tourism/trips">♡<span>Trips</span></a>
 <a class="ma-nav-item" href="<?=maTourEsc($basePath)?>/member/profile">●<span>Profile</span></a>
</nav>
<?php $aiScope = 'TOURISM'; $aiContext = "Help me with my Manipur trip plan"; include __DIR__ . '/../AI/ask.php'; ?>
