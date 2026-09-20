<?php
declare(strict_types=1);
$old = is_array($old ?? null) ? $old : [];
$basePath = rtrim((string) config('app.base_path'), '/');
$oldValue = static fn(string $key): string => htmlspecialchars((string)($old[$key] ?? ''), ENT_QUOTES, 'UTF-8');
?>
<div class="gform-intro">
  <span class="gform-badge">BUSINESS OWNER / VENDOR</span>
  <h2>Welcome, Business Owner / Vendor</h2>
  <p>Create your ManipurApp workspace once, then manage multiple businesses and services from one connected account.</p>
</div>
<div class="gcard">
<?php if (!empty($error)): ?><div class="gerror"><?= htmlspecialchars((string)$error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
<form method="POST" action="<?= htmlspecialchars($basePath) ?>/register">
  <div class="gfield"><label class="glabel" for="church_name">Business / Workspace name</label><input class="ginput" id="church_name" name="church_name" value="<?= $oldValue('church_name') ?>" required autocomplete="organization"></div>
  <div class="gfield"><label class="glabel" for="slug">Workspace URL</label><input class="ginput" id="slug" name="slug" value="<?= $oldValue('slug') ?>" placeholder="your-business" required autocomplete="off"></div>
  <div class="gfield"><label class="glabel" for="admin_name">Owner / Administrator name</label><input class="ginput" id="admin_name" name="admin_name" value="<?= $oldValue('admin_name') ?>" required autocomplete="name"></div>
  <div class="gfield"><label class="glabel" for="email">Owner / Administrator email</label><input class="ginput" type="email" id="email" name="email" value="<?= $oldValue('email') ?>" required autocomplete="email"></div>
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:11px">
    <div class="gfield"><label class="glabel" for="password">Password</label><input class="ginput" type="password" id="password" name="password" required autocomplete="new-password"></div>
    <div class="gfield"><label class="glabel" for="password_confirmation">Confirm password</label><input class="ginput" type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password"></div>
  </div>
  <button class="gsubmit" type="submit">Create Business Account</button>
</form>
</div>
<p class="ghelp">Already have an account? <a href="<?= htmlspecialchars($basePath) ?>/login">Find your workspace and sign in</a></p>
