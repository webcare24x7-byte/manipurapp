<?php
declare(strict_types=1);
$results = is_array($results ?? null) ? $results : [];
$search = (string) ($search ?? '');
$basePath = rtrim((string) config('app.base_path'), '/');
?>
<div class="gform-intro">
  <span class="gform-badge">BUSINESS OWNER / VENDOR</span>
  <h2>Welcome, Business Owner / Vendor</h2>
  <p>Find your business workspace to continue to your ManipurApp management account.</p>
</div>

<div class="gcard">
<?php if (!empty($error)): ?><div class="gerror"><?= htmlspecialchars((string)$error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
<form method="POST" action="<?= htmlspecialchars($basePath) ?>/login">
  <div class="gfield">
    <label class="glabel" for="church">Business / Workspace name</label>
    <input class="ginput" type="text" id="church" name="church" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>" placeholder="Enter your business or workspace name" required autofocus autocomplete="organization">
  </div>
  <button class="gsubmit" type="submit">Find My Workspace</button>
</form>

<?php if (!empty($results)): ?>
<div class="gresults">
  <h3>Select your workspace</h3>
  <?php foreach ($results as $tenant): ?>
    <a class="gresult" href="<?= htmlspecialchars($basePath) ?>/c/<?= urlencode((string)($tenant['slug'] ?? '')) ?>/login">
      <span><strong><?= htmlspecialchars((string)($tenant['name'] ?? 'Business Workspace'), ENT_QUOTES, 'UTF-8') ?></strong><small><?= htmlspecialchars((string)($tenant['slug'] ?? ''), ENT_QUOTES, 'UTF-8') ?></small></span><span>→</span>
    </a>
  <?php endforeach; ?>
</div>
<?php elseif ($search !== '' && empty($error)): ?>
<div class="gresults"><h3>No workspace found</h3><p style="margin:0;color:#71827c;font-size:10px;">No business workspace matched <strong><?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?></strong>. Check the name and try again.</p></div>
<?php endif; ?>
</div>
<p class="ghelp">Need a new business workspace? <a href="<?= htmlspecialchars($basePath) ?>/register">Register as a Business Owner / Vendor</a></p>
