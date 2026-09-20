<?php
declare(strict_types=1);
include __DIR__ . '/../Shared/member-desktop.css.php';
include __DIR__ . '/../Shared/member-desktop-header.php';

$basePath=rtrim((string)config('app.base_path'),'/');
$b=is_array($booking??null)?$booking:[];
function maCrCE(mixed $v):string{return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');}
$status=(string)($b['status']??'REQUESTED');
$steps=['REQUESTED','CONTACTING','QUOTED','CONFIRMED','IN_PROGRESS','COMPLETED'];
$current=array_search($status,$steps,true); $current=$current===false?0:$current;
?>
<style>
.ma-crc{max-width:620px;margin:auto;padding:18px 14px 105px;color:#14251f}.ma-crc *{box-sizing:border-box}.ma-crc a{text-decoration:none}
.ma-crc-head{display:flex;align-items:center;gap:12px;padding:4px 2px 16px}.ma-crc-back{width:40px;height:40px;border-radius:50%;display:grid;place-items:center;background:#f1f6f3;color:#163d33;font-size:25px}.ma-crc-head h1{margin:0;font-size:22px}.ma-crc-head p{margin:3px 0;color:#70817b;font-size:11px}
.ma-crc-success{text-align:center;background:#eaf7f2;border:1px solid #d4eee5;border-radius:23px;padding:24px 18px}.ma-crc-icon{width:60px;height:60px;border-radius:50%;display:grid;place-items:center;background:#087d64;color:#fff;font-size:30px;margin:0 auto 12px}.ma-crc-success h2{margin:0;font-size:23px}.ma-crc-success p{margin:7px auto 0;max-width:430px;color:#557168;font-size:12px;line-height:1.55}.ma-crc-no{display:inline-block;margin-top:13px;background:#fff;padding:8px 11px;border-radius:10px;font-weight:900;font-size:12px;color:#087d64}
.ma-crc-card{background:#fff;border:1px solid #e0ebe6;border-radius:20px;padding:16px;margin-top:13px}.ma-crc-card h3{margin:0 0 12px;font-size:16px}.ma-crc-grid{display:grid;grid-template-columns:1fr 1fr;gap:9px}.ma-crc-item{background:#f7faf8;border-radius:13px;padding:10px;font-size:11px;color:#6d7d77}.ma-crc-item b{display:block;color:#203c34;font-size:12px;margin-top:3px}
.ma-crc-status{display:grid;gap:0;margin-top:8px}.ma-crc-step{display:grid;grid-template-columns:25px 1fr;gap:9px;position:relative;padding-bottom:15px}.ma-crc-dot{width:22px;height:22px;border-radius:50%;background:#dce9e4;color:#60746d;display:grid;place-items:center;font-size:10px;font-weight:900;z-index:2}.ma-crc-step.active .ma-crc-dot{background:#087d64;color:#fff}.ma-crc-step:after{content:'';position:absolute;left:10px;top:22px;width:2px;height:calc(100% - 14px);background:#e2ebe7}.ma-crc-step:last-child:after{display:none}.ma-crc-step strong{font-size:12px}.ma-crc-step small{display:block;color:#7b8984;font-size:10px;margin-top:2px}
.ma-crc-actions{display:flex;gap:8px;margin-top:13px}.ma-crc-btn{flex:1;text-align:center;padding:12px;border-radius:13px;font-size:12px;font-weight:900}.primary{background:#087d64;color:#fff}.secondary{background:#eef6f3;color:#087d64}
.ma-crc-nav{position:fixed;left:50%;bottom:10px;transform:translateX(-50%);width:min(540px,calc(100% - 20px));background:rgba(255,255,255,.96);border:1px solid #e1ebe7;box-shadow:0 10px 30px rgba(0,0,0,.12);border-radius:22px;display:grid;grid-template-columns:repeat(4,1fr);padding:7px;z-index:30}.ma-crc-nav a{text-decoration:none;color:#73827c;text-align:center;padding:8px 3px;font-size:11px;font-weight:700}.ma-crc-nav a.active{color:#087d64}.ma-crc-nav i{display:block;font-style:normal;font-size:20px;margin-bottom:2px}
@media(max-width:480px){.ma-crc-grid{grid-template-columns:1fr}}
</style>
<div class="ma-crc">
<header class="ma-crc-head"><a class="ma-crc-back" href="<?=maCrCE($basePath)?>/member/commercial-rental">‹</a><div><h1>Rental request</h1><p>Keep this request number for reference.</p></div></header>
<section class="ma-crc-success"><div class="ma-crc-icon">✓</div><h2>Request sent</h2><p>Your vehicle request has been received. A provider/admin can review the job, contact you and confirm the quotation.</p><span class="ma-crc-no"><?=maCrCE($b['request_no']??'')?></span></section>
<section class="ma-crc-card"><h3>Request summary</h3><div class="ma-crc-grid">
<div class="ma-crc-item">Vehicle<b><?=maCrCE($b['vehicle_name']??'Commercial vehicle')?></b></div>
<div class="ma-crc-item">Provider<b><?=maCrCE($b['provider_name']??'Local provider')?></b></div>
<div class="ma-crc-item">Pickup / work location<b><?=maCrCE($b['pickup_address']??'—')?></b></div>
<div class="ma-crc-item">Destination<b><?=maCrCE($b['destination_address']??'—')?></b></div>
<div class="ma-crc-item">Start<b><?=maCrCE($b['start_at']??'—')?></b></div>
<div class="ma-crc-item">Status<b><?=maCrCE($status)?></b></div>
</div></section>
<section class="ma-crc-card"><h3>Request progress</h3><div class="ma-crc-status">
<?php foreach($steps as $i=>$step): ?>
<div class="ma-crc-step <?=$i<=$current?'active':''?>"><span class="ma-crc-dot"><?=($i<=$current?'✓':$i+1)?></span><div><strong><?=maCrCE(str_replace('_',' ',$step))?></strong><small><?=match($step){'REQUESTED'=>'Request received','CONTACTING'=>'Provider is reviewing your request','QUOTED'=>'Quotation can be confirmed here','CONFIRMED'=>'Vehicle and booking confirmed','IN_PROGRESS'=>'Rental is underway','COMPLETED'=>'Rental completed',default=>''}?></small></div></div>
<?php endforeach; ?>
</div></section>
<div class="ma-crc-actions"><a class="ma-crc-btn secondary" href="<?=maCrCE($basePath)?>/member/commercial-rental">Find another vehicle</a><a class="ma-crc-btn primary" href="<?=maCrCE($basePath)?>/member/bookings">My bookings →</a></div>
<nav class="ma-crc-nav"><a href="<?=maCrCE($basePath)?>/member"><i>⌂</i>Home</a><a class="active" href="<?=maCrCE($basePath)?>/member/commercial-rental"><i>🚚</i>Services</a><a href="<?=maCrCE($basePath)?>/member/bookings"><i>▣</i>Bookings</a><a href="<?=maCrCE($basePath)?>/member/profile"><i>♙</i>Profile</a></nav>
</div>
