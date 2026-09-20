<?php declare(strict_types=1);
include __DIR__ . '/../Shared/member-desktop.css.php';
include __DIR__ . '/../Shared/member-desktop-header.php';
 $basePath=rtrim((string)config('app.base_path'),'/'); $message=(string)($message??'Booking not found.'); function maQe(mixed $v):string{return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');} ?>
<div style="max-width:520px;margin:40px auto;padding:24px;font-family:inherit;text-align:center"><div style="font-size:48px">📋</div><h1>Booking</h1><p style="color:#687972"><?=maQe($message)?></p><a href="<?=maQe($basePath)?>/member/bookings" style="display:inline-block;background:#087d64;color:#fff;text-decoration:none;padding:13px 18px;border-radius:13px;font-weight:800">My Bookings</a></div>

<?php include __DIR__ . '/../Shared/member-notification-widget.php'; ?>
