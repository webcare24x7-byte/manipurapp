<?php include __DIR__ . '/../Shared/member-desktop.css.php'; ?>
<?php include __DIR__ . '/../Shared/member-desktop-header.php'; ?>
<?php require __DIR__.'/_style.php'; ?>
<div class="ma-tour">
<header class="ma-tour-head">
  <a class="ma-tour-brand" href="<?=maTourEsc($basePath)?>/member"><span class="ma-tour-mark">🏔️</span><span><strong>Explore Manipur</strong><small>Discover • Stay • Experience</small></span></a>
  <a class="ma-tour-back" href="<?=maTourEsc($basePath)?>/member">← ManipurApp</a>
</header>
<?php $aiScope = 'TOURISM'; $aiContext = ''; $aiTriggerTitle = 'Visiting Manipur? Let us help you plan.'; $aiTriggerSubtitle = 'Ask about stays, places, experiences, food and local travel.'; $aiTriggerLabel = 'Plan with AI →'; $aiDialogTitle = 'Plan your Manipur visit'; include __DIR__ . '/../AI/ask.php'; ?>
<section class="ma-tour-hero">
 <div class="ma-tour-copy">
  <span class="ma-tour-kicker">Your Manipur journey starts here</span>
  <h1>Discover a place worth remembering.</h1>
  <p>Explore destinations, stays, local guides, experiences and festivals — then bring everything together into your own trip plan.</p>
  <form class="ma-tour-search" method="get" action="<?=maTourEsc($basePath)?>/member/tourism">
   <input name="q" value="<?=maTourEsc($search??'')?>" placeholder="Search places, experiences, stays or festivals…" aria-label="Search tourism">
   <button class="ma-tour-btn">Explore →</button>
  </form>
  <?php $districtAction=maTourEsc($basePath).'/member/tourism'; ?>
  <form class="ma-tour-location-filter" method="get" action="<?=$districtAction?>">
   <input type="hidden" name="q" value="<?=maTourEsc($search??'')?>">
   <label>📍 Explore by district
    <select name="district" onchange="this.form.submit()"><option value="">All Manipur</option><?php foreach(($districts??[]) as $row): ?><option value="<?=maTourEsc($row['district'])?>" <?=($district??'')===$row['district']?'selected':''?>><?=maTourEsc($row['district'])?></option><?php endforeach; ?></select>
   </label><button type="submit">Apply</button><?php if(!empty($district)): ?><a class="clear" href="<?=$districtAction?>" style="text-decoration:none;padding:10px 14px;border-radius:11px">Clear</a><?php endif; ?>
  </form>
 </div>
</section>

<section class="ma-tour-section">
 <div class="ma-tour-categories">
  <a class="ma-tour-cat" href="<?=maTourEsc($basePath)?>/member/tourism/destinations"><span>📍</span>Destinations</a>
  <a class="ma-tour-cat" href="<?=maTourEsc($basePath)?>/member/tourism/stays"><span>🏡</span>Stays</a>
  <a class="ma-tour-cat" href="<?=maTourEsc($basePath)?>/member/tourism/packages"><span>🧳</span>Packages</a>
  <a class="ma-tour-cat" href="<?=maTourEsc($basePath)?>/member/tourism/guides"><span>🧭</span>Guides</a>
  <a class="ma-tour-cat" href="<?=maTourEsc($basePath)?>/member/tourism/experiences"><span>✨</span>Experiences</a>
  <a class="ma-tour-cat" href="<?=maTourEsc($basePath)?>/member/tourism/events"><span>🎉</span>Events</a>
 </div>
</section>

<?php if(!empty($destinations)): ?>
<section class="ma-tour-section"><div class="ma-tour-section-head"><div><h2>Places to discover</h2><p>Start with the landscapes, towns and cultural places that make Manipur unique.</p></div><a class="ma-tour-see" href="<?=maTourEsc($basePath)?>/member/tourism/destinations">View all →</a></div>
<div class="ma-tour-grid"><?php foreach(array_slice($destinations,0,6) as $d): ?><a class="ma-tour-card" href="<?=maTourEsc($basePath)?>/member/tourism/destinations/<?=intval($d['id'])?>">
<div class="ma-tour-media"><?php if(maTourImg($d['cover_image_path']??'')): ?><img src="<?=maTourEsc($d['cover_image_path'])?>" alt="<?=maTourEsc($d['name'])?>"><?php else: ?>🏞️<?php endif; ?><span class="ma-tour-pill"><?=maTourEsc($d['district']??'Manipur')?></span></div>
<div class="ma-tour-card-body"><h3><?=maTourEsc($d['name'])?></h3><p><?=maTourEsc(mb_strimwidth((string)($d['description']??''),0,105,'…','UTF-8'))?></p><div class="ma-tour-meta"><span><?=maTourEsc($d['suggested_duration']??'Explore')?></span><?php if(!empty($d['review_rating'])):?><span>★ <?=maTourEsc($d['review_rating'])?></span><?php endif;?></div></div></a><?php endforeach;?></div></section>
<?php endif; ?>

<?php if(!empty($experiences)): ?>
<section class="ma-tour-section"><div class="ma-tour-section-head"><div><h2>Things to experience</h2><p>Go beyond sightseeing.</p></div><a class="ma-tour-see" href="<?=maTourEsc($basePath)?>/member/tourism/experiences">All experiences →</a></div>
<div class="ma-tour-grid"><?php foreach(array_slice($experiences,0,3) as $e): ?><a class="ma-tour-card" href="<?=maTourEsc($basePath)?>/member/tourism/experiences/<?=intval($e['id'])?>"><div class="ma-tour-media"><?php if(maTourImg($e['cover_image_path']??'')): ?><img src="<?=maTourEsc($e['cover_image_path'])?>" alt="<?=maTourEsc($e['title'])?>"><?php else: ?>✨<?php endif;?></div><div class="ma-tour-card-body"><h3><?=maTourEsc($e['title'])?></h3><p><?=maTourEsc(mb_strimwidth((string)($e['description']??''),0,110,'…','UTF-8'))?></p><div class="ma-tour-meta"><span><?=maTourEsc($e['experience_type']??'Experience')?></span><?php if(isset($e['price'])):?><span>₹<?=number_format((float)$e['price'],0)?></span><?php endif;?></div></div></a><?php endforeach;?></div></section>
<?php endif; ?>

<?php if(!empty($events)): ?>
<section class="ma-tour-section"><div class="ma-tour-section-head"><div><h2>Events & festivals</h2><p>See what's happening across Manipur.</p></div><a class="ma-tour-see" href="<?=maTourEsc($basePath)?>/member/tourism/events">See calendar →</a></div>
<div class="ma-tour-grid"><?php foreach(array_slice($events,0,3) as $e): ?><a class="ma-tour-card" href="<?=maTourEsc($basePath)?>/member/tourism/events/<?=intval($e['id'])?>"><div class="ma-tour-media"><?php if(maTourImg($e['cover_image_path']??'')): ?><img src="<?=maTourEsc($e['cover_image_path'])?>" alt="<?=maTourEsc($e['title'])?>"><?php else: ?>🎉<?php endif;?></div><div class="ma-tour-card-body"><h3><?=maTourEsc($e['title'])?></h3><p><?=maTourEsc(date('d M Y',strtotime((string)$e['start_at'])))?> · <?=maTourEsc($e['venue']??$e['district']??'Manipur')?></p></div></a><?php endforeach;?></div></section>
<?php endif; ?>

<section class="ma-tour-section"><div class="ma-tour-panel" style="background:linear-gradient(135deg,#eff9f4,#fff);border-color:#cfe5dc"><h2>Plan your own Manipur journey</h2><p>Save destinations, stays, experiences, events and guides into a day-by-day itinerary.</p><div class="ma-tour-action"><a href="<?=maTourEsc($basePath)?>/member/tourism/trips">🗺️ Open Trip Planner</a><a class="light" href="<?=maTourEsc($basePath)?>/member/tourism/destinations">Start exploring</a></div></div></section>

<?php include __DIR__.'/../Shared/member-notification-widget.php'; ?>
<nav class="ma-bottom-nav" aria-label="Primary navigation">
 <a class="ma-nav-item" href="<?=maTourEsc($basePath)?>/member">⌂<span>Home</span></a>
 <a class="ma-nav-item active" href="<?=maTourEsc($basePath)?>/member/tourism">⌖<span>Explore</span></a>
 <a class="ma-nav-item" href="<?=maTourEsc($basePath)?>/member/bookings">▣<span>Bookings</span></a>
 <a class="ma-nav-item ma-nav-ilp" href="<?= maTourEsc($basePath) ?>/member/ilp">▤<span>ILP</span></a>
 <a class="ma-nav-item ma-nav-ai" href="<?= maTourEsc($basePath) ?>/member#ask-ai">✦<span>Ask AI</span></a>
 <a class="ma-nav-item" href="<?=maTourEsc($basePath)?>/member/tourism/trips">♡<span>Trips</span></a>
 <a class="ma-nav-item" href="<?=maTourEsc($basePath)?>/member/profile">●<span>Profile</span></a>
</nav>
</div>
<?php include __DIR__ . '/../Shared/member-desktop-footer.php'; ?>
