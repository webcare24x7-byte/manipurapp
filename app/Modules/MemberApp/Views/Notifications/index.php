<?php
declare(strict_types=1);
include __DIR__ . '/../Shared/member-desktop.css.php';
include __DIR__ . '/../Shared/member-desktop-header.php';

$basePath = rtrim((string) config('app.base_path'), '/');
$list = is_array($notifications ?? null) ? $notifications : [];
function mnEsc(mixed $v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function mnDt(?string $v): string { if(!$v)return '—'; try{return (new DateTime($v))->format('d M Y · h:i A');}catch(Throwable){return $v;} }
?>
<style>
.mn-page{max-width:560px;margin:auto;padding:13px 13px 94px;color:#14251f}.mn-head{display:flex;gap:10px;align-items:center;margin-bottom:10px}.mn-head a{font-size:27px;text-decoration:none;color:#173c32}.mn-head h1{margin:0;font-size:21px}.mn-head p{margin:2px 0;color:#76857f;font-size:10px}.mn-card{display:block;text-decoration:none;color:inherit;background:#fff;border:1px solid #e0ebe6;border-radius:14px;padding:11px 12px;margin:7px 0;box-shadow:0 4px 13px rgba(15,60,45,.04)}.mn-top{display:flex;gap:8px;align-items:center}.mn-icon{width:29px;height:29px;border-radius:10px;background:#eaf7f2;display:grid;place-items:center;flex:none}.mn-title{font-size:12px;font-weight:900}.mn-time{margin-left:auto;font-size:8px;color:#82908a;white-space:nowrap}.mn-message{margin:7px 0 0 37px;font-size:10px;color:#596d65;line-height:1.45}.mn-empty{margin-top:20px;background:#f3f8f5;border-radius:15px;padding:27px;text-align:center;color:#65756e;font-size:11px}.mn-empty b{display:block;color:#173f34;font-size:16px;margin-bottom:4px}.mn-nav{position:fixed;left:50%;bottom:6px;transform:translateX(-50%);width:min(540px,calc(100% - 14px));background:rgba(255,255,255,.97);border:1px solid #e1ebe7;box-shadow:0 7px 22px rgba(0,0,0,.1);border-radius:18px;display:grid;grid-template-columns:repeat(4,1fr);padding:4px;z-index:30}.mn-nav a{text-decoration:none;color:#73827c;text-align:center;padding:5px 3px;font-size:9px;font-weight:700}.mn-nav i{display:block;font-style:normal;font-size:17px}
</style>
<div class="mn-page">
<header class="mn-head"><a href="<?=mnEsc($basePath)?>/member">‹</a><div><h1>Notifications</h1><p>Updates from your bookings and orders</p></div></header>
<?php if(!$list): ?><div class="mn-empty"><b>You're all caught up</b>No new booking or order updates yet.</div>
<?php else: foreach($list as $n): $source=(string)($n['source']??''); ?><a class="mn-card" href="<?=mnEsc($basePath.(string)($n['action_url']??'/member'))?>"><div class="mn-top"><span class="mn-icon"><?= $source==='taxi' ? '🚕' : '🍛' ?></span><span class="mn-title"><?=mnEsc($n['title']??'Update')?></span><span class="mn-time"><?=mnEsc(mnDt($n['created_at']??null))?></span></div><div class="mn-message"><?=mnEsc($n['message']??'')?></div></a><?php endforeach; endif; ?>
</div>
<nav class="mn-nav"><a href="<?=mnEsc($basePath)?>/member"><i>⌂</i>Home</a><a href="<?=mnEsc($basePath)?>/member/taxi"><i>🚕</i>Services</a><a href="<?=mnEsc($basePath)?>/member/bookings"><i>▣</i>Bookings</a><a href="<?=mnEsc($basePath)?>/member/profile"><i>♙</i>Profile</a></nav>
<?php include __DIR__ . '/../Shared/member-desktop-footer.php'; ?>
