<?php
/* Shared desktop MemberApp navigation. Hidden on phones; mobile bottom navigation remains unchanged. */
$memberDesktopBasePath = rtrim((string)config('app.base_path'), '/');
$memberDesktopUri = (string)($_SERVER['REQUEST_URI'] ?? '');
$memberDesktopPath = parse_url($memberDesktopUri, PHP_URL_PATH) ?: '';
$memberDesktopEsc = static fn(mixed $v): string => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$memberDesktopExploreActive = str_starts_with($memberDesktopPath, $memberDesktopBasePath . '/member/tourism')
    || str_starts_with($memberDesktopPath, $memberDesktopBasePath . '/member/taxi')
    || str_starts_with($memberDesktopPath, $memberDesktopBasePath . '/member/commercial-rental')
    || str_starts_with($memberDesktopPath, $memberDesktopBasePath . '/member/fresh-food')
    || str_starts_with($memberDesktopPath, $memberDesktopBasePath . '/member/restaurants');
$memberDesktopBookingsActive = str_starts_with($memberDesktopPath, $memberDesktopBasePath . '/member/bookings');
$memberDesktopProfileActive = str_starts_with($memberDesktopPath, $memberDesktopBasePath . '/member/profile');
$memberDesktopHomeActive = $memberDesktopPath === $memberDesktopBasePath . '/member' || $memberDesktopPath === $memberDesktopBasePath . '/member/';
?>
<header class="md-global-header">
  <a class="mdgh-brand" href="<?= $memberDesktopEsc($memberDesktopBasePath) ?>/member">
    <span class="mdgh-logo">🌿</span>
    <span><strong>ManipurApp</strong><small>People · Places · Possibilities</small></span>
  </a>
  <nav class="mdgh-nav" aria-label="Member navigation">
    <a class="<?= $memberDesktopHomeActive ? 'active' : '' ?>" href="<?= $memberDesktopEsc($memberDesktopBasePath) ?>/member"><span>⌂</span>Home</a>
    <a class="<?= $memberDesktopExploreActive ? 'active' : '' ?>" href="<?= $memberDesktopEsc($memberDesktopBasePath) ?>/member/tourism"><span>⌖</span>Explore</a>
    <a class="<?= $memberDesktopBookingsActive ? 'active' : '' ?>" href="<?= $memberDesktopEsc($memberDesktopBasePath) ?>/member/bookings"><span>▣</span>Bookings</a>
    <a class="<?= $memberDesktopProfileActive ? 'active' : '' ?>" href="<?= $memberDesktopEsc($memberDesktopBasePath) ?>/member/profile"><span>♙</span>Profile</a>
  </nav>
  <div class="mdgh-right">
    <span class="mdgh-location">⌖ Imphal, Manipur</span>
    <a class="mdgh-bell" href="<?= $memberDesktopEsc($memberDesktopBasePath) ?>/member/notifications" aria-label="Notifications">♧</a>
    <a class="mdgh-user" href="<?= $memberDesktopEsc($memberDesktopBasePath) ?>/member/profile">Member</a>
  </div>
</header>
