<?php
declare(strict_types=1);
$tenant = is_array($tenant ?? null) ? $tenant : [];
$basePath = rtrim((string) config('app.base_path'), '/');
$slug = (string) ($tenant['slug'] ?? '');
?>
<div class="gform-intro">
  <span class="gform-badge">BUSINESS OWNER / VENDOR</span>
  <h2>Welcome, Business Owner / Vendor</h2>
  <p>Sign in to <strong><?= htmlspecialchars((string)($tenant['name'] ?? 'Your Business Workspace'), ENT_QUOTES, 'UTF-8') ?></strong> with your owner, administrator or staff account.</p>
</div>
<div class="gcard">
<?php if (!empty($error)): ?><div class="gerror"><?= htmlspecialchars((string)$error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
<form method="POST" action="<?= htmlspecialchars($basePath) ?>/c/<?= urlencode($slug) ?>/login">
  <div class="gfield"><label class="glabel" for="email">Email</label><input class="ginput" type="email" id="email" name="email" required autocomplete="username" autofocus></div>
  <div class="gfield"><label class="glabel" for="password">Password</label><input class="ginput" type="password" id="password" name="password" required autocomplete="current-password"></div>
  <button class="gsubmit" type="submit">Sign In</button>
</form>
</div>
<p class="ghelp"><a href="<?= htmlspecialchars($basePath) ?>/login">← Choose a different workspace</a></p>
