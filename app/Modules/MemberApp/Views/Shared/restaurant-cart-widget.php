<?php
declare(strict_types=1);
$rcwBase = rtrim((string) config('app.base_path'), '/');
$rcwCart = $_SESSION['memberapp_restaurant_cart'] ?? [];
$rcwItems = is_array($rcwCart['items'] ?? null) ? $rcwCart['items'] : [];
$rcwCount = 0;
foreach ($rcwItems as $rcwLine) {
    if (is_array($rcwLine)) $rcwCount += max(0, (int)($rcwLine['quantity'] ?? 0));
}
$rcwVisible = $rcwCount > 0;
?>
<style>
.ma-global-cart{position:fixed;right:max(14px,calc((100vw - 540px)/2 + 14px));bottom:78px;z-index:45;display:<?= $rcwVisible ? 'flex' : 'none' ?>;align-items:center;gap:8px;text-decoration:none;background:#087d64;color:#fff;border-radius:999px;padding:9px 13px 9px 10px;box-shadow:0 8px 24px rgba(8,125,100,.28);font-size:10px;font-weight:900;border:2px solid rgba(255,255,255,.9)}
.ma-global-cart-icon{width:27px;height:27px;border-radius:50%;display:grid;place-items:center;background:#fff;color:#087d64;font-size:14px;position:relative}.ma-global-cart-count{position:absolute;right:-5px;top:-6px;min-width:18px;height:18px;padding:0 4px;border-radius:99px;background:#ef5b4d;color:#fff;border:2px solid #fff;display:grid;place-items:center;font-size:8px;font-weight:950}.ma-global-cart-text{line-height:1.05}.ma-global-cart-text small{display:block;opacity:.8;font-size:8px;margin-top:2px;font-weight:700}.ma-global-cart-top{display:none}
@media(min-width:700px){.ma-global-cart{right:calc((100vw - 560px)/2 + 14px)}}
</style>
<a class="ma-global-cart" href="<?=htmlspecialchars($rcwBase.'/member/restaurant/cart',ENT_QUOTES,'UTF-8')?>" aria-label="Open food cart with <?=$rcwCount?> item<?=($rcwCount===1?'':'s')?>">
    <span class="ma-global-cart-icon" aria-hidden="true">🛒<b class="ma-global-cart-count"><?=$rcwCount?></b></span>
    <span class="ma-global-cart-text">View Cart<small><?=$rcwCount?> item<?=($rcwCount===1?'':'s')?> in cart</small></span>
</a>
