<?php
declare(strict_types=1);
include __DIR__ . '/../Shared/member-desktop.css.php';
include __DIR__ . '/../Shared/member-desktop-header.php';

$basePath=rtrim((string)config('app.base_path'),'/');
function maCrErr(mixed $v):string{return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');}
?>
<div style="max-width:560px;margin:50px auto;padding:25px;font-family:inherit;text-align:center">
<div style="font-size:48px">🚚</div><h1><?=maCrErr($title??'Commercial Rental')?></h1><p style="color:#687972;line-height:1.6"><?=maCrErr($message??'Something went wrong.')?></p>
<a href="<?=maCrErr($basePath)?>/member/commercial-rental" style="display:inline-block;padding:12px 18px;background:#087d64;color:#fff;border-radius:13px;text-decoration:none;font-weight:800">Back to commercial rental</a>
</div>
