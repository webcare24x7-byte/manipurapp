<?php declare(strict_types=1); ?>

<div class="page-header"><div><h1>Edit Taxi Service</h1><p>Update taxi service information.</p></div><div class="page-actions"><a href="<?= config('app.base_path') ?>/taxi/services/<?= (int) ($record['id'] ?? 0) ?>" class="btn btn-secondary">Back</a></div></div>
<?php if (!empty($error)): ?><div class="alert alert-error"><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
<form method="POST" action="<?= config('app.base_path') ?>/taxi/services/<?= (int) ($record['id'] ?? 0) ?>"><?php require __DIR__ . '/form.php'; ?><div class="page-actions"><a href="<?= config('app.base_path') ?>/taxi/services/<?= (int) ($record['id'] ?? 0) ?>" class="btn btn-secondary">Cancel</a><button type="submit" class="btn btn-primary">Update Taxi Service</button></div></form>
